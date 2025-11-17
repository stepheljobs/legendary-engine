<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Role;
use App\Models\TeamMember;
use App\Models\User;
use App\Notifications\TeamInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvitationController extends Controller
{
    /**
     * Show the invitation form.
     */
    public function create()
    {
        // Get available roles for the invitation
        $roles = Role::where('name', '!=', 'owner')
            ->orderBy('name')
            ->get();

        return view('invitations.create', compact('roles'));
    }

    /**
     * Send a new invitation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role_name' => ['required', 'string', 'exists:roles,name'],
        ]);

        $email = strtolower($request->email);
        $tenantId = tenant('id');

        // Check if user is already a team member
        $existingMember = TeamMember::whereHas('user', function ($query) use ($email) {
            $query->where('email', $email);
        })->first();

        if ($existingMember) {
            throw ValidationException::withMessages([
                'email' => 'This user is already a team member.',
            ]);
        }

        // Check if there's already a pending invitation
        $existingInvitation = Invitation::forTenant($tenantId)
            ->forEmail($email)
            ->pending()
            ->first();

        if ($existingInvitation) {
            throw ValidationException::withMessages([
                'email' => 'An invitation has already been sent to this email address.',
            ]);
        }

        // Create the invitation
        $invitation = Invitation::createInvitation(
            tenantId: $tenantId,
            email: $email,
            roleName: $request->role_name,
            invitedBy: $request->user()->id
        );

        // Send invitation email
        try {
            // Find or create a temporary notification target
            // Note: In production, you might want to use a queued notification
            \Illuminate\Support\Facades\Notification::route('mail', $email)
                ->notify(new TeamInvitation($invitation));
        } catch (\Exception $e) {
            // Log the error but don't fail the invitation creation
            \Log::error('Failed to send invitation email: ' . $e->getMessage());
        }

        return redirect()
            ->route('team.index')
            ->with('success', 'Invitation sent successfully!');
    }

    /**
     * Show pending invitations for the current tenant.
     */
    public function index()
    {
        $invitations = Invitation::forTenant(tenant('id'))
            ->pending()
            ->with('inviter')
            ->latest()
            ->get();

        return view('invitations.index', compact('invitations'));
    }

    /**
     * Show the invitation acceptance page.
     */
    public function show(string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->isAccepted()) {
            return redirect()->route('login')->with('error', 'This invitation has already been accepted.');
        }

        if ($invitation->isExpired()) {
            return redirect()->route('login')->with('error', 'This invitation has expired.');
        }

        return view('invitations.accept', compact('invitation'));
    }

    /**
     * Accept an invitation.
     */
    public function accept(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->isAccepted()) {
            return redirect()->route('login')->with('error', 'This invitation has already been accepted.');
        }

        if ($invitation->isExpired()) {
            return redirect()->route('login')->with('error', 'This invitation has expired.');
        }

        $user = $request->user();
        $email = $user?->email ?? $request->input('email');

        // Check if invitation email matches
        if (strtolower($invitation->email) !== strtolower($email)) {
            return back()->with('error', 'This invitation was sent to a different email address.');
        }

        try {
            DB::beginTransaction();

            // If user is not authenticated, they need to register/login first
            if (!$user) {
                session(['pending_invitation' => $token]);
                return redirect()->route('register')->with('email', $invitation->email);
            }

            // Switch to tenant context
            $tenant = $invitation->tenant;

            // Initialize tenancy for this operation
            tenancy()->initialize($tenant);

            // Check if user already exists in tenant database
            $tenantUser = User::where('email', $user->email)->first();

            if (!$tenantUser) {
                // Create user in tenant database
                $tenantUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $user->password,
                    'email_verified_at' => $user->email_verified_at,
                ]);
            }

            // Get the role for this invitation
            $role = Role::where('name', $invitation->role_name)->firstOrFail();

            // Create team member record
            TeamMember::create([
                'user_id' => $tenantUser->id,
                'role_id' => $role->id,
                'joined_at' => now(),
            ]);

            // Mark invitation as accepted
            tenancy()->end();
            $invitation->markAsAccepted($user->id);

            DB::commit();

            // Redirect to tenant subdomain
            $domain = $tenant->domains->first();
            $url = 'http://' . $domain->domain . '/dashboard';

            return redirect($url)->with('success', 'Welcome to the team!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to accept invitation: ' . $e->getMessage());

            return back()->with('error', 'Failed to accept invitation. Please try again.');
        }
    }

    /**
     * Cancel/delete an invitation.
     */
    public function destroy(Invitation $invitation)
    {
        // Verify the invitation belongs to the current tenant
        if ($invitation->tenant_id !== tenant('id')) {
            abort(403);
        }

        $invitation->delete();

        return redirect()
            ->route('invitations.index')
            ->with('success', 'Invitation cancelled successfully.');
    }

    /**
     * Resend an invitation.
     */
    public function resend(Invitation $invitation)
    {
        // Verify the invitation belongs to the current tenant
        if ($invitation->tenant_id !== tenant('id')) {
            abort(403);
        }

        if ($invitation->isAccepted()) {
            return back()->with('error', 'This invitation has already been accepted.');
        }

        // Update expiration date
        $invitation->update([
            'expires_at' => now()->addDays(7),
        ]);

        // Resend invitation email
        try {
            \Illuminate\Support\Facades\Notification::route('mail', $invitation->email)
                ->notify(new TeamInvitation($invitation));
        } catch (\Exception $e) {
            \Log::error('Failed to resend invitation email: ' . $e->getMessage());
        }

        return back()->with('success', 'Invitation resent successfully!');
    }
}
