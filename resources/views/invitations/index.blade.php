@extends('layouts.app')

@section('title', 'Pending Invitations')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('team.index') }}" class="text-indigo-600 hover:text-indigo-900">← Back to Team</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="py-10">
        <header>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold leading-tight text-gray-900">
                    Pending Invitations
                </h1>
            </div>
        </header>

        <main>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <!-- Success Message -->
                    @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Invitations List -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Pending Invitations ({{ $invitations->count() }})
                            </h3>
                        </div>
                        <ul class="divide-y divide-gray-200">
                            @forelse($invitations as $invitation)
                            <li class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $invitation->email }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                Invited as {{ $invitation->role_name }}
                                            </p>
                                        </div>
                                        <div class="mt-1">
                                            <p class="text-xs text-gray-500">
                                                Invited by {{ $invitation->inviter->name ?? 'Unknown' }}
                                                • {{ $invitation->created_at->diffForHumans() }}
                                                • Expires {{ $invitation->expires_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-shrink-0 flex space-x-2">
                                        <form action="{{ route('invitations.resend', $invitation) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                Resend
                                            </button>
                                        </form>
                                        <form action="{{ route('invitations.destroy', $invitation) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this invitation?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1 border border-red-300 shadow-sm text-xs font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                Cancel
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                            @empty
                            <li class="px-4 py-8 text-center text-gray-500">
                                No pending invitations.
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
