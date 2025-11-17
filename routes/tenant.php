<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        $tenant = tenant();
        return view('tenant.dashboard', [
            'tenant' => $tenant,
            'company_name' => $tenant->company_name,
            'subdomain' => $tenant->subdomain,
        ]);
    })->name('tenant.dashboard');

    Route::get('/dashboard', function () {
        $tenant = tenant();
        return view('tenant.dashboard', [
            'tenant' => $tenant,
            'company_name' => $tenant->company_name,
            'subdomain' => $tenant->subdomain,
        ]);
    })->name('tenant.dashboard');
});
