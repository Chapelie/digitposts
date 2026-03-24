@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Modifier l’utilisateur</h1>
            <p class="text-gray-500">{{ $user->email }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.users') }}" class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">Retour à la liste</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-800">{{ session('error') }}</div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                <input type="text" name="firstname" value="{{ old('firstname', $user->firstname) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" />
                @error('firstname') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                <input type="text" name="lastname" value="{{ old('lastname', $user->lastname) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" />
                @error('lastname') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" />
                @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" />
                @error('phone') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Organisation</label>
                <input type="text" name="organization" value="{{ old('organization', $user->organization) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" />
                @error('organization') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    <option value="user" @selected(old('role', $user->role) === 'user')>Utilisateur</option>
                    <option value="admin" @selected(old('role', $user->role) === 'admin')>Administrateur</option>
                </select>
                @error('role') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2 flex flex-wrap gap-3">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Enregistrer
                </button>
            </div>
        </form>

        @if($user->id !== auth()->id())
            <div class="mt-10 pt-8 border-t border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Zone sensible</h2>
                <p class="text-sm text-gray-600 mb-4">La suppression est définitive. Elle est refusée si l’utilisateur a encore des campagnes.</p>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Supprimer l’utilisateur
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
