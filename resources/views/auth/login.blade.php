@extends('layouts.blanklayout')

@php
    $seoTitle = 'Login';
@endphp

@section('content')
    <div class="bg-white shadow-md rounded-lg p-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-center text-gray-800 mb-2">Connectez-vous à votre compte!</h1>
            <p class="text-sm text-center text-gray-600">Entrez vos identifiants pour accéder à votre compte</p>
        </div>

        <!-- Messages d'erreur et de succès -->
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email or Phone Input -->
            <div>
                <label for="identifier" class="block text-sm font-medium text-gray-700 mb-1">
                    Email ou Numéro de téléphone
                </label>
                <input
                    id="identifier"
                    name="identifier"
                    type="text"
                    placeholder="email@example.com ou +226 XX XX XX XX"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required
                    autofocus
                    value="{{ old('identifier') }}"
                >
                @error('identifier')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Vous pouvez vous connecter avec votre email ou votre numéro de téléphone</p>
            </div>

            <!-- Password Input -->
            <div>
                <div class="flex items-center justify-between mb-1">
{{--                    <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>--}}
{{--                    <a href="{{ route('password.request') }}" class="text-xs text-indigo-600 hover:underline">--}}
{{--                        Forgot password?--}}
{{--                    </a>--}}
                </div>
                <div class="relative">
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        required
                    >
                    <button
                        type="button"
                        id="toggle-password"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700"
                        aria-label="Afficher le mot de passe"
                    >
                        <!-- Eye icon -->
                        <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Eye off icon -->
                        <svg id="icon-eye-off" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.269-2.943-9.543-7a10.056 10.056 0 012.234-3.592m3.09-2.272A9.956 9.956 0 0112 5c4.478 0 8.269 2.943 9.543 7a10.06 10.06 0 01-4.132 5.411M15 12a3 3 0 00-3-3m0 0a3 3 0 012.12.879M9.88 9.88A3 3 0 009 12a3 3 0 004.12 2.12M3 3l18 18" />
                        </svg>
                    </button>
                </div>
                @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Se Connecter
            </button>
        </form>

        <!-- Forgot Password Link -->
        <div class="mt-4 text-center">
            <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                Mot de passe oublié ?
            </a>
        </div>

        <!-- Social Login -->
        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Ou continuer avec</span>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-3">
                <a href="{{ \Illuminate\Support\Facades\Route::has('login.provider') ? route('login.provider', 'google') : '#' }}" class="w-full inline-flex items-center justify-center py-3 px-4 border-2 border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-colors">
                    <svg class="h-6 w-6 mr-3" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span class="font-semibold">Continuer avec Google</span>
                </a>
            </div>
        </div>

        <!-- Registration Link -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Pas de compte?
                <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    S'inscrire
                </a>
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('password');
            const btn = document.getElementById('toggle-password');
            const iconEye = document.getElementById('icon-eye');
            const iconEyeOff = document.getElementById('icon-eye-off');

            if (!input || !btn || !iconEye || !iconEyeOff) return;

            btn.addEventListener('click', function () {
                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                iconEye.classList.toggle('hidden', isHidden);
                iconEyeOff.classList.toggle('hidden', !isHidden);
                btn.setAttribute('aria-label', isHidden ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
            });
        });
    </script>
@endsection
