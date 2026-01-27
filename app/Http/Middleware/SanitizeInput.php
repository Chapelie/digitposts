<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SanitizeInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();

        // Sanitize string inputs
        array_walk_recursive($input, function (&$value) {
            if (is_string($value)) {
                // Nettoyer les balises HTML sauf pour les champs autorisés (description, notes, etc.)
                $value = strip_tags($value);
                // Échapper les caractères spéciaux
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        });

        // Remplacer les inputs sanitized
        $request->merge($input);

        return $next($request);
    }
}
