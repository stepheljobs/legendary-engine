<?php

use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Central App Routes
|--------------------------------------------------------------------------
|
| These routes are for the central application (non-tenant routes)
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Dashboard - redirects to tenant or onboarding
Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    $user = auth()->user();

    // If user has a tenant, redirect to tenant subdomain
    if ($user->tenant) {
        $domain = $user->tenant->subdomain . '.' . config('app.domain', 'localhost');
        $port = request()->getPort() != 80 && request()->getPort() != 443 ? ':' . request()->getPort() : '';
        return redirect('http://' . $domain . $port);
    }

    // Otherwise, redirect to onboarding
    return redirect()->route('onboarding.create');
})->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Onboarding routes (requires authentication)
Route::middleware('auth')->prefix('onboard')->name('onboarding.')->group(function () {
    Route::get('/', [OnboardingController::class, 'showForm'])->name('create');
    Route::post('/', [OnboardingController::class, 'store'])->name('store');
    Route::get('/success', [OnboardingController::class, 'success'])->name('success');
    Route::get('/check-subdomain', [OnboardingController::class, 'checkSubdomain'])->name('check-subdomain');
});

// Invitation routes (public access for invitation acceptance)
Route::prefix('invitations')->name('invitations.')->group(function () {
    Route::get('/{token}', [\App\Http\Controllers\InvitationController::class, 'show'])->name('show');
    Route::post('/{token}/accept', [\App\Http\Controllers\InvitationController::class, 'accept'])->middleware('auth')->name('accept');
});

// API routes for tenant management
Route::prefix('api/tenants')->name('api.tenants.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\TenantController::class, 'index'])->name('index');
    Route::post('/', [\App\Http\Controllers\Api\TenantController::class, 'store'])->name('store');
    Route::get('/{id}', [\App\Http\Controllers\Api\TenantController::class, 'show'])->name('show');
    Route::put('/{id}', [\App\Http\Controllers\Api\TenantController::class, 'update'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Api\TenantController::class, 'destroy'])->name('destroy');
});

require __DIR__.'/auth.php';
