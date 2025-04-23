@extends('layouts.blanklayout')

@section('title', 'Register')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-center text-gray-800 mb-2">Create an account</h1>
            <p class="text-sm text-center text-gray-600">Enter your information to get started</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- Name Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First name</label>
                    <input
                        id="first_name"
                        name="first_name"
                        type="text"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        required
                        autofocus
                    >
                    @error('first_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last name</label>
                    <input
                        id="last_name"
                        name="last_name"
                        type="text"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        required
                    >
                    @error('last_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    placeholder="name@example.com"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required
                >
                @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required
                >
                @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password Field -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required
                >
            </div>

            <!-- User Type Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">I want to</label>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <input id="visitor" name="user_type" type="radio" value="visitor" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" checked>
                        <label for="visitor" class="ml-2 block text-sm text-gray-700">
                            Register for campaigns (Visitor)
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input id="creator" name="user_type" type="radio" value="creator" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                        <label for="creator" class="ml-2 block text-sm text-gray-700">
                            Create campaigns (Creator)
                        </label>
                    </div>
                </div>
                @error('user_type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Create Account
            </button>
        </form>

        <!-- Social Login -->
        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Or continue with</span>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-3">
                <a href="{{ route('login.provider', 'google') }}" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <img src="{{ asset('images/google-icon.png') }}" alt="Google" class="h-5 w-5 mr-2">
                    Google
                </a>
            </div>
        </div>

        <!-- Login Link -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Already have an account?
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Sign in
                </a>
            </p>
        </div>
    </div>
@endsection
