@extends('layouts.app')

@section('title', 'Invite Team Member')

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
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold leading-tight text-gray-900">
                    Invite Team Member
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Send an invitation to join your team
                </p>
            </div>
        </header>

        <main>
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <div class="bg-white shadow sm:rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <form action="{{ route('invitations.store') }}" method="POST">
                                @csrf

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">
                                        Email Address
                                    </label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('email') border-red-300 @enderror">
                                    @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Role -->
                                <div class="mt-6">
                                    <label for="role_name" class="block text-sm font-medium text-gray-700">
                                        Role
                                    </label>
                                    <select name="role_name" id="role_name" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('role_name') border-red-300 @enderror">
                                        <option value="">Select a role</option>
                                        @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ old('role_name') === $role->name ? 'selected' : '' }}>
                                            {{ $role->display_name }}
                                            @if($role->description)
                                            - {{ $role->description }}
                                            @endif
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('role_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-sm text-gray-500">
                                        Select the role this member will have in your organization
                                    </p>
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
                                                An invitation email will be sent to this address. The invitation will expire after 7 days.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="mt-6 flex justify-end space-x-3">
                                    <a href="{{ route('team.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Cancel
                                    </a>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Send Invitation
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
