@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Activités</h1>
            <p class="text-gray-500">Toutes les formations et événements de la plateforme</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour au dashboard
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activité</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créateur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inscriptions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($activities as $activity)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($activity->feedable && $activity->feedable->file)
                                    <img src="{{ asset('storage/' . $activity->feedable->file) }}" alt="" class="h-10 w-10 rounded-lg object-cover mr-3">
                                @else
                                    <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900 max-w-xs truncate">
                                        {{ $activity->feedable->title ?? 'Sans titre' }}
                                    </div>
                                    <div class="text-xs text-gray-500">ID: {{ substr($activity->id, 0, 8) }}...</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($activity->feedable_type === 'App\Models\Training')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Formation</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Événement</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $activity->user->firstname ?? '' }} {{ $activity->user->lastname ?? '' }}</div>
                            <div class="text-xs text-gray-500">{{ $activity->user->email ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $activity->registrations_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($activity->status === 'publiée')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Publiée</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Brouillon</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $activity->created_at->format('d/m/Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Aucune activité trouvée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $activities->links() }}
    </div>
</div>
@endsection
