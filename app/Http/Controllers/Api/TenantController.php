<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Stancl\Tenancy\Database\Models\Tenant;

class TenantController extends Controller
{
    /**
     * Display a listing of tenants.
     */
    public function index()
    {
        $tenants = Tenant::with('domains')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($tenants);
    }

    /**
     * Store a newly created tenant.
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
            ],
        ]);

        // Check subdomain availability
        $domain = $validated['subdomain'] . '.' . config('app.domain', 'localhost');
        if (Tenant::whereHas('domains', function ($query) use ($domain) {
            $query->where('domain', $domain);
        })->exists()) {
            return response()->json([
                'message' => 'This subdomain is already taken.',
                'errors' => ['subdomain' => ['The subdomain is already in use.']]
            ], 422);
        }

        // Create tenant
        $tenant = Tenant::create([
            'id' => Str::uuid()->toString(),
            'company_name' => $validated['company_name'],
            'subdomain' => $validated['subdomain'],
        ]);

        // Create domain
        $tenant->domains()->create([
            'domain' => $domain,
        ]);

        return response()->json([
            'message' => 'Tenant created successfully',
            'tenant' => $tenant->load('domains'),
        ], 201);
    }

    /**
     * Display the specified tenant.
     */
    public function show(string $id)
    {
        $tenant = Tenant::with('domains')->findOrFail($id);

        return response()->json($tenant);
    }

    /**
     * Update the specified tenant.
     */
    public function update(Request $request, string $id)
    {
        $tenant = Tenant::findOrFail($id);

        $validated = $request->validate([
            'company_name' => ['sometimes', 'required', 'string', 'max:255'],
            'subdomain' => [
                'sometimes',
                'required',
                'string',
                'max:63',
                'regex:/^[a-z0-9][a-z0-9-]*[a-z0-9]$/',
            ],
        ]);

        // If subdomain is being updated
        if (isset($validated['subdomain']) && $validated['subdomain'] !== $tenant->subdomain) {
            $domain = $validated['subdomain'] . '.' . config('app.domain', 'localhost');

            // Check availability
            if (Tenant::whereHas('domains', function ($query) use ($domain) {
                $query->where('domain', $domain);
            })->exists()) {
                return response()->json([
                    'message' => 'This subdomain is already taken.',
                    'errors' => ['subdomain' => ['The subdomain is already in use.']]
                ], 422);
            }

            // Update domain
            $tenant->domains()->delete();
            $tenant->domains()->create([
                'domain' => $domain,
            ]);
        }

        $tenant->update($validated);

        return response()->json([
            'message' => 'Tenant updated successfully',
            'tenant' => $tenant->load('domains'),
        ]);
    }

    /**
     * Remove the specified tenant.
     */
    public function destroy(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->delete();

        return response()->json([
            'message' => 'Tenant deleted successfully',
        ]);
    }
}
