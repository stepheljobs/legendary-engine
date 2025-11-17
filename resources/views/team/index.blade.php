@extends('layouts.app')

@section('title', 'Team Members')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/dashboard" class="text-indigo-600 hover:text-indigo-900">← Back to Dashboard</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="py-10">
        <header>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="md:flex md:items-center md:justify-between">
                    <div class="flex-1 min-w-0">
                        <h1 class="text-3xl font-bold leading-tight text-gray-900">
                            Team Members
                        </h1>
                    </div>
                    @if(auth()->user()->hasPermission('invite-members'))
                    <div class="mt-4 flex md:mt-0 md:ml-4">
                        <a href="{{ route('invitations.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Invite Member
                        </a>
                    </div>
                    @endif
                </div>
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

                    <!-- Team Members List -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Active Members ({{ $teamMembers->count() }})
                            </h3>
                        </div>
                        <ul class="divide-y divide-gray-200">
                            @forelse($teamMembers as $member)
                            <li class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center min-w-0 flex-1">
                                        <div class="flex-shrink-0">
                                            <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span class="text-indigo-600 font-medium text-lg">
                                                    {{ strtoupper(substr($member->user->name, 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1 px-4">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    {{ $member->user->name }}
                                                    @if($member->user_id === auth()->id())
                                                    <span class="ml-2 text-xs text-gray-500">(You)</span>
                                                    @endif
                                                </p>
                                                <p class="text-sm text-gray-500 truncate">
                                                    {{ $member->user->email }}
                                                </p>
                                            </div>
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $member->role->name === 'owner' ? 'purple' : ($member->role->name === 'admin' ? 'blue' : 'gray') }}-100 text-{{ $member->role->name === 'owner' ? 'purple' : ($member->role->name === 'admin' ? 'blue' : 'gray') }}-800">
                                                    {{ $member->role->display_name }}
                                                </span>
                                                <span class="ml-2 text-xs text-gray-500">
                                                    Joined {{ $member->joined_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    @if(auth()->user()->hasPermission('manage-members') && $member->user_id !== auth()->id())
                                    <div class="ml-4 flex-shrink-0 flex space-x-2">
                                        <a href="{{ route('team.edit', $member) }}" class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Edit Role
                                        </a>
                                        <form action="{{ route('team.destroy', $member) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this member?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1 border border-red-300 shadow-sm text-xs font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                </div>
                            </li>
                            @empty
                            <li class="px-4 py-8 text-center text-gray-500">
                                No team members found.
                            </li>
                            @endforelse
                        </ul>
                    </div>

                    <!-- Pending Invitations Link -->
                    @if(auth()->user()->hasPermission('invite-members'))
                    <div class="mt-6">
                        <a href="{{ route('invitations.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                            View Pending Invitations →
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
