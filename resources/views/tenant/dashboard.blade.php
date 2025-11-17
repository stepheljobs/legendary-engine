@extends('layouts.app')

@section('title', 'Dashboard - ' . $company_name)

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">{{ $company_name }}</h1>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-sm text-gray-500">{{ $subdomain }}.{{ config('app.domain', 'localhost') }}</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="py-10">
        <header>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold leading-tight text-gray-900">
                    Welcome to Your Tenant Dashboard
                </h1>
            </div>
        </header>

        <main>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Success Message -->
                <div class="px-4 py-6 sm:px-0">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="mb-6">
                                <h2 class="text-2xl font-semibold text-gray-800 mb-2">
                                    Tenant Information
                                </h2>
                                <p class="text-gray-600">
                                    This is your isolated tenant workspace powered by multi-tenancy.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Tenant Details -->
                                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg p-6">
                                    <h3 class="text-lg font-semibold text-indigo-900 mb-4">Company Details</h3>
                                    <dl class="space-y-3">
                                        <div>
                                            <dt class="text-sm font-medium text-indigo-700">Company Name</dt>
                                            <dd class="text-base text-indigo-900">{{ $company_name }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-indigo-700">Subdomain</dt>
                                            <dd class="text-base text-indigo-900">{{ $subdomain }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-indigo-700">Tenant ID</dt>
                                            <dd class="text-base text-indigo-900 font-mono text-sm">{{ $tenant->id }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-indigo-700">Created At</dt>
                                            <dd class="text-base text-indigo-900">{{ $tenant->created_at->format('M d, Y H:i') }}</dd>
                                        </div>
                                    </dl>
                                </div>

                                <!-- Features -->
                                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6">
                                    <h3 class="text-lg font-semibold text-green-900 mb-4">Multi-Tenancy Features</h3>
                                    <ul class="space-y-2">
                                        <li class="flex items-start">
                                            <svg class="h-6 w-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span class="text-sm text-green-900">Isolated database per tenant</span>
                                        </li>
                                        <li class="flex items-start">
                                            <svg class="h-6 w-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span class="text-sm text-green-900">Subdomain-based routing</span>
                                        </li>
                                        <li class="flex items-start">
                                            <svg class="h-6 w-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span class="text-sm text-green-900">Automatic tenant context</span>
                                        </li>
                                        <li class="flex items-start">
                                            <svg class="h-6 w-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span class="text-sm text-green-900">Tenant-specific cache & storage</span>
                                        </li>
                                        <li class="flex items-start">
                                            <svg class="h-6 w-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span class="text-sm text-green-900">Easy onboarding flow</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Info Box -->
                            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm text-blue-700">
                                            This is a boilerplate Laravel SaaS application with multi-tenancy.
                                            You can now build your application-specific features on top of this foundation.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-6 flex space-x-4">
                                <a href="/" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Refresh Page
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
