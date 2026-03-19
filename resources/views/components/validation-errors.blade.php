{{-- Affichage global des erreurs de validation (session flash) — visible même si une vue oublie @error --}}
@if (isset($errors) && $errors->any())
    <div class="validation-errors-banner mb-6 rounded-lg border-2 border-red-400 bg-red-50 p-4 shadow-sm" role="alert" data-validation-errors>
        <p class="font-semibold text-red-900 mb-2 flex items-center gap-2">
            <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            {{ $title ?? 'Veuillez corriger les erreurs suivantes :' }}
        </p>
        <ul class="list-disc list-inside text-sm text-red-800 space-y-1">
            @foreach ($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
