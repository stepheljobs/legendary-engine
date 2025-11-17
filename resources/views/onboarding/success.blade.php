@extends('layouts.app')

@section('title', 'Success - Company Created')

@section('content')
<div class="flex items-center justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
                <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Company Created Successfully!
            </h2>

            @if(session('success'))
                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-md">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('tenant_url'))
                <div class="mt-6 p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600 mb-2">Your company is now accessible at:</p>
                    <a href="{{ session('tenant_url') }}"
                       class="text-lg font-semibold text-indigo-600 hover:text-indigo-500 break-all">
                        {{ session('subdomain') }}.{{ config('app.domain', 'localhost') }}
                    </a>
                </div>
            @endif

            <div class="mt-8 space-y-3">
                @if(session('tenant_url'))
                    <a href="{{ session('tenant_url') }}"
                       class="w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Go to Your Dashboard
                    </a>
                @endif

                <a href="{{ route('home') }}"
                   class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Back to Home
                </a>
            </div>
        </div>

        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-blue-900 mb-2">Next Steps:</h3>
            <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                <li>Access your tenant dashboard</li>
                <li>Configure your company settings</li>
                <li>Invite team members</li>
                <li>Start using your SaaS application</li>
            </ul>
        </div>
    </div>
</div>
@endsection
