<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant;

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
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'subdomain' => [
                'required',
                'string',
                'max:63',
                'regex:/^[a-z0-9][a-z0-9-]*[a-z0-9]$/',
                Rule::unique('domains', 'domain'),
            ],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'subdomain.regex' => 'The subdomain must start and end with a letter or number, and can only contain lowercase letters, numbers, and hyphens.',
            'subdomain.unique' => 'This subdomain is already taken.',
        ]);

        // Create the tenant
        $tenant = Tenant::create([
            'id' => Str::uuid()->toString(),
            'company_name' => $validated['company_name'],
            'subdomain' => $validated['subdomain'],
        ]);

        // Create the subdomain
        $domain = $validated['subdomain'] . '.' . config('app.domain', 'localhost');
        $tenant->domains()->create([
            'domain' => $domain,
        ]);

        // Store user info in tenant data for later registration
        $tenant->update([
            'data' => [
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
            ]
        ]);

        return redirect()
            ->route('onboarding.success')
            ->with('success', 'Your company has been created successfully!')
            ->with('subdomain', $validated['subdomain']);
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
