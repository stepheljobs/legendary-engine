<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TeamMemberController extends Controller
{
    /**
     * Display a listing of team members.
     */
    public function index()
    {
        $teamMembers = TeamMember::with(['user', 'role'])
            ->orderBy('joined_at', 'desc')
            ->get();

        return view('team.index', compact('teamMembers'));
    }

    /**
     * Show the form for editing a team member's role.
     */
    public function edit(TeamMember $teamMember)
    {
        $roles = Role::orderBy('name')->get();

        return view('team.edit', compact('teamMember', 'roles'));
    }

    /**
     * Update a team member's role.
     */
    public function update(Request $request, TeamMember $teamMember)
    {
        $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $newRole = Role::findOrFail($request->role_id);

        // Prevent changing the last owner's role
        if ($teamMember->isOwner()) {
            $ownerCount = TeamMember::whereHas('role', function ($query) {
                $query->where('name', 'owner');
            })->count();

            if ($ownerCount <= 1 && $newRole->name !== 'owner') {
                throw ValidationException::withMessages([
                    'role_id' => 'Cannot change the role of the last owner. Promote another member to owner first.',
                ]);
            }
        }

        // Prevent non-owners from promoting members to owner
        if ($newRole->name === 'owner' && !$request->user()->isOwner()) {
            throw ValidationException::withMessages([
                'role_id' => 'Only owners can promote members to owner.',
            ]);
        }

        $teamMember->update([
            'role_id' => $request->role_id,
        ]);

        return redirect()
            ->route('team.index')
            ->with('success', 'Team member role updated successfully.');
    }

    /**
     * Remove a team member.
     */
    public function destroy(Request $request, TeamMember $teamMember)
    {
        // Prevent removing yourself
        if ($teamMember->user_id === $request->user()->id) {
            throw ValidationException::withMessages([
                'error' => 'You cannot remove yourself from the team.',
            ]);
        }

        // Prevent removing the last owner
        if ($teamMember->isOwner()) {
            $ownerCount = TeamMember::whereHas('role', function ($query) {
                $query->where('name', 'owner');
            })->count();

            if ($ownerCount <= 1) {
                throw ValidationException::withMessages([
                    'error' => 'Cannot remove the last owner. Promote another member to owner first.',
                ]);
            }
        }

        $teamMember->delete();

        return redirect()
            ->route('team.index')
            ->with('success', 'Team member removed successfully.');
    }

    /**
     * Leave the current team (self-removal).
     */
    public function leave(Request $request)
    {
        $teamMember = $request->user()->teamMember;

        if (!$teamMember) {
            abort(403, 'You are not a member of this team.');
        }

        // Prevent leaving if you're the last owner
        if ($teamMember->isOwner()) {
            $ownerCount = TeamMember::whereHas('role', function ($query) {
                $query->where('name', 'owner');
            })->count();

            if ($ownerCount <= 1) {
                throw ValidationException::withMessages([
                    'error' => 'You are the last owner. Please promote another member to owner before leaving.',
                ]);
            }
        }

        $teamMember->delete();

        // End tenancy and redirect to central app
        tenancy()->end();

        return redirect('/dashboard')
            ->with('success', 'You have left the team.');
    }
}
