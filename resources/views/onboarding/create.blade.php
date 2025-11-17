@extends('layouts.app')

@section('title', 'Create Your Company - SaaS Onboarding')

@section('content')
<div class="flex items-center justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Create Your Company
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Welcome, {{ auth()->user()->name }}! Set up your multi-tenant workspace
            </p>
        </div>

        @if ($errors->has('error'))
            <div class="rounded-md bg-red-50 p-4">
                <p class="text-sm text-red-800">{{ $errors->first('error') }}</p>
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('onboarding.store') }}" method="POST" id="onboardingForm">
            @csrf

            <div class="rounded-md shadow-sm space-y-4">
                <!-- Company Name -->
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Company Name
                    </label>
                    <input
                        id="company_name"
                        name="company_name"
                        type="text"
                        required
                        value="{{ old('company_name') }}"
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm @error('company_name') border-red-500 @enderror"
                        placeholder="Acme Corporation"
                    >
                    @error('company_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Subdomain -->
                <div>
                    <label for="subdomain" class="block text-sm font-medium text-gray-700 mb-1">
                        Subdomain
                    </label>
                    <div class="flex rounded-md shadow-sm">
                        <input
                            id="subdomain"
                            name="subdomain"
                            type="text"
                            required
                            value="{{ old('subdomain') }}"
                            class="flex-1 min-w-0 block w-full px-3 py-2 rounded-l-md border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('subdomain') border-red-500 @enderror"
                            placeholder="acme"
                            pattern="[a-z0-9][a-z0-9-]*[a-z0-9]"
                            onkeyup="this.value = this.value.toLowerCase(); checkSubdomain()"
                        >
                        <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                            .{{ config('app.domain', 'localhost') }}
                        </span>
                    </div>
                    <div id="subdomain-feedback" class="mt-1 text-sm hidden"></div>
                    @error('subdomain')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        Use lowercase letters, numbers, and hyphens only
                    </p>
                </div>
            </div>

            <div>
                <button
                    type="submit"
                    id="submitBtn"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Create Company
                </button>
            </div>
        </form>

        <div class="text-center">
            <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                Back to dashboard
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let debounceTimer;
let lastCheckedSubdomain = '';

function checkSubdomain() {
    const subdomain = document.getElementById('subdomain').value.trim().toLowerCase();
    const feedback = document.getElementById('subdomain-feedback');
    const submitBtn = document.getElementById('submitBtn');

    // Clear previous timer
    clearTimeout(debounceTimer);

    // Reset if empty
    if (!subdomain) {
        feedback.classList.add('hidden');
        submitBtn.disabled = false;
        return;
    }

    // Don't check if it's the same as last time
    if (subdomain === lastCheckedSubdomain) {
        return;
    }

    // Show checking message
    feedback.classList.remove('hidden', 'text-red-600', 'text-green-600');
    feedback.classList.add('text-gray-600');
    feedback.textContent = 'Checking availability...';

    // Debounce the API call
    debounceTimer = setTimeout(() => {
        fetch(`{{ route('onboarding.check-subdomain') }}?subdomain=${encodeURIComponent(subdomain)}`)
            .then(response => response.json())
            .then(data => {
                lastCheckedSubdomain = subdomain;
                feedback.classList.remove('hidden', 'text-gray-600');

                if (data.available) {
                    feedback.classList.add('text-green-600');
                    feedback.classList.remove('text-red-600');
                    feedback.textContent = `✓ ${data.message}`;
                    submitBtn.disabled = false;
                } else {
                    feedback.classList.add('text-red-600');
                    feedback.classList.remove('text-green-600');
                    feedback.textContent = `✗ ${data.message}`;
                    submitBtn.disabled = true;
                }
            })
            .catch(error => {
                console.error('Error checking subdomain:', error);
                feedback.classList.add('text-gray-600');
                feedback.textContent = 'Error checking availability';
                submitBtn.disabled = false;
            });
    }, 500);
}
</script>
@endpush
