<?php

use App\Http\Controllers\OnboardingController;
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

// Onboarding routes
Route::prefix('onboard')->name('onboarding.')->group(function () {
    Route::get('/', [OnboardingController::class, 'showForm'])->name('create');
    Route::post('/', [OnboardingController::class, 'store'])->name('store');
    Route::get('/success', [OnboardingController::class, 'success'])->name('success');
    Route::get('/check-subdomain', [OnboardingController::class, 'checkSubdomain'])->name('check-subdomain');
});

// API routes for tenant management
Route::prefix('api/tenants')->name('api.tenants.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\TenantController::class, 'index'])->name('index');
    Route::post('/', [\App\Http\Controllers\Api\TenantController::class, 'store'])->name('store');
    Route::get('/{id}', [\App\Http\Controllers\Api\TenantController::class, 'show'])->name('show');
    Route::put('/{id}', [\App\Http\Controllers\Api\TenantController::class, 'update'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Api\TenantController::class, 'destroy'])->name('destroy');
});
