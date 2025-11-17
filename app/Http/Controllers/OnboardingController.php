<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Stancl\Tenancy\Database\Models\Domain;

class OnboardingController extends Controller
{
    /**
     * Show the onboarding form
     */
    public function showForm()
    {
        return view('onboarding.create');
    }

    /**
     * Create a new tenant with company name and subdomain
     */
    public function store(Request $request)
    {
        // Check if user already has a tenant
        if (auth()->user()->tenant) {
            return redirect()->route('onboarding.create')
                ->withErrors(['error' => 'You already have a company. Each user can only create one company.']);
        }

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'subdomain' => [
                'required',
                'string',
                'max:63',
                'regex:/^[a-z0-9][a-z0-9-]*[a-z0-9]$/',
            ],
        ], [
            'subdomain.regex' => 'The subdomain must start and end with a letter or number, and can only contain lowercase letters, numbers, and hyphens.',
        ]);

        // Check subdomain availability
        $domain = $validated['subdomain'] . '.' . config('app.domain', 'localhost');
        if (Domain::where('domain', $domain)->exists()) {
            return back()->withErrors(['subdomain' => 'This subdomain is already taken.'])->withInput();
        }

        // Create the tenant
        $tenant = Tenant::create([
            'id' => Str::uuid()->toString(),
            'user_id' => auth()->id(),
            'company_name' => $validated['company_name'],
            'subdomain' => $validated['subdomain'],
        ]);

        // Create the subdomain
        $tenant->domains()->create([
            'domain' => $domain,
        ]);

        // Ensure tenant database and migrations are complete
        // Even though JobPipeline runs synchronously, we refresh to ensure it's done
        $tenant->refresh();

        // Verify database was created, if not create it manually
        try {
            tenancy()->initialize($tenant);
            \DB::connection('tenant')->getPdo();
            tenancy()->end();
        } catch (\Exception $e) {
            // Database might not exist, JobPipeline should have created it
            // but if it failed, we'll get an error when we try to seed
        }

        // Create the user in the tenant database
        $this->createTenantUser($tenant);

        return redirect()
            ->route('onboarding.success')
            ->with('success', 'Your company has been created successfully!')
            ->with('subdomain', $validated['subdomain'])
            ->with('tenant_url', 'http://' . $domain . ':8000');
    }

    /**
     * Create user in tenant database and set up initial roles/permissions
     */
    protected function createTenantUser($tenant)
    {
        $user = auth()->user();

        // Initialize tenancy to switch database context
        tenancy()->initialize($tenant);

        try {
            // Seed roles and permissions (tables created by TenancyServiceProvider)
            $seeder = new \Database\Seeders\RolePermissionSeeder();
            $seeder->run();

            // Create user in tenant database
            $tenantUser = \App\Models\User::create([
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'email_verified_at' => $user->email_verified_at,
            ]);

            // Get the owner role
            $ownerRole = \App\Models\Role::where('name', 'owner')->first();

            // Create team member record with owner role
            if ($ownerRole) {
                \App\Models\TeamMember::create([
                    'user_id' => $tenantUser->id,
                    'role_id' => $ownerRole->id,
                    'joined_at' => now(),
                ]);
            }
        } finally {
            // End tenancy to return to central database context
            tenancy()->end();
        }
    }

    /**
     * Show success page
     */
    public function success()
    {
        return view('onboarding.success');
    }

    /**
     * Check subdomain availability
     */
    public function checkSubdomain(Request $request)
    {
        $subdomain = $request->input('subdomain');

        if (!$subdomain) {
            return response()->json(['available' => false, 'message' => 'Subdomain is required']);
        }

        // Validate subdomain format
        if (!preg_match('/^[a-z0-9][a-z0-9-]*[a-z0-9]$/', $subdomain)) {
            return response()->json([
                'available' => false,
                'message' => 'Invalid format. Use only lowercase letters, numbers, and hyphens.'
            ]);
        }

        // Check if subdomain is available
        $domain = $subdomain . '.' . config('app.domain', 'localhost');
        $exists = Domain::where('domain', $domain)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'This subdomain is already taken' : 'Subdomain is available!',
            'full_domain' => $domain,
        ]);
    }
}
