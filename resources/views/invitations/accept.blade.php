@extends('layouts.guest')

@section('title', 'Accept Invitation')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Team Invitation</h2>
        </div>

        <!-- Invitation Details -->
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-indigo-900">
                You've been invited to join
            </p>
            <p class="text-lg font-semibold text-indigo-900 mt-1">
                {{ $invitation->tenant->company_name }}
            </p>
            @if($invitation->inviter)
            <p class="text-xs text-indigo-700 mt-2">
                Invited by {{ $invitation->inviter->name }}
            </p>
            @endif
            <p class="text-xs text-indigo-700 mt-1">
                Role: {{ ucfirst($invitation->role_name) }}
            </p>
        </div>

        <!-- Error Message -->
        @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        @auth
        <!-- Authenticated User - Show Accept Button -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-700">
                You are logged in as <strong>{{ auth()->user()->email }}</strong>
            </p>
        </div>

        <form action="{{ route('invitations.accept', $invitation->token) }}" method="POST">
            @csrf
            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Accept Invitation
            </button>
        </form>

        <div class="mt-4 text-center">
            <p class="text-xs text-gray-600">
                Not you? <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-indigo-600 hover:text-indigo-900">Sign out</a> and create a new account
            </p>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
        @else
        <!-- Guest User - Show Login/Register Options -->
        <div class="space-y-3">
            <a href="{{ route('login') }}?redirect={{ route('invitations.show', $invitation->token) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Sign In to Accept
            </a>

            <a href="{{ route('register') }}?redirect={{ route('invitations.show', $invitation->token) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Create Account
            </a>
        </div>

        <div class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-3">
            <p class="text-xs text-gray-600 text-center">
                You'll need to sign in or create an account to accept this invitation
            </p>
        </div>
        @endauth

        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500">
                This invitation expires on {{ $invitation->expires_at->format('F j, Y') }}
            </p>
        </div>
    </div>
</div>
@endsection
