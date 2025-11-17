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

    // Team member routes (requires authentication and team membership)
    Route::middleware(['auth', 'team.member'])->group(function () {
        // Team member listing
        Route::get('/team', [\App\Http\Controllers\TeamMemberController::class, 'index'])->name('team.index');

        // Team member management (requires permission)
        Route::middleware('permission:manage-members')->group(function () {
            Route::get('/team/{teamMember}/edit', [\App\Http\Controllers\TeamMemberController::class, 'edit'])->name('team.edit');
            Route::patch('/team/{teamMember}', [\App\Http\Controllers\TeamMemberController::class, 'update'])->name('team.update');
            Route::delete('/team/{teamMember}', [\App\Http\Controllers\TeamMemberController::class, 'destroy'])->name('team.destroy');
        });

        // Leave team (any member can do this)
        Route::post('/team/leave', [\App\Http\Controllers\TeamMemberController::class, 'leave'])->name('team.leave');

        // Invitation routes (requires permission)
        Route::middleware('permission:invite-members')->group(function () {
            Route::get('/invitations', [\App\Http\Controllers\InvitationController::class, 'index'])->name('invitations.index');
            Route::get('/invitations/create', [\App\Http\Controllers\InvitationController::class, 'create'])->name('invitations.create');
            Route::post('/invitations', [\App\Http\Controllers\InvitationController::class, 'store'])->name('invitations.store');
            Route::delete('/invitations/{invitation}', [\App\Http\Controllers\InvitationController::class, 'destroy'])->name('invitations.destroy');
            Route::post('/invitations/{invitation}/resend', [\App\Http\Controllers\InvitationController::class, 'resend'])->name('invitations.resend');
        });
    });
});
