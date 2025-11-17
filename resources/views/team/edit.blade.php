@extends('layouts.app')

@section('title', 'Edit Team Member')

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
                    Edit Team Member Role
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Update the role for {{ $teamMember->user->name }}
                </p>
            </div>
        </header>

        <main>
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <div class="bg-white shadow sm:rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <!-- Member Info -->
                            <div class="mb-6 pb-6 border-b border-gray-200">
                                <div class="flex items-center">
                                    <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-600 font-medium text-lg">
                                            {{ strtoupper(substr($teamMember->user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-base font-medium text-gray-900">
                                            {{ $teamMember->user->name }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $teamMember->user->email }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('team.update', $teamMember) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <!-- Role -->
                                <div>
                                    <label for="role_id" class="block text-sm font-medium text-gray-700">
                                        Role
                                    </label>
                                    <select name="role_id" id="role_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('role_id') border-red-300 @enderror">
                                        @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ $teamMember->role_id === $role->id ? 'selected' : '' }}>
                                            {{ $role->display_name }}
                                            @if($role->description)
                                            - {{ $role->description }}
                                            @endif
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Warning for Owner Role -->
                                @if($teamMember->isOwner())
                                <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-yellow-700">
                                                This member is currently an owner. Ensure there is at least one other owner before changing this role.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Actions -->
                                <div class="mt-6 flex justify-end space-x-3">
                                    <a href="{{ route('team.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Cancel
                                    </a>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Update Role
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
