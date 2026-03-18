<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Après renommage SESSION_COOKIE (ex. digitposts_session_v2), l’ancien cookie
 * peut rester dans le navigateur : deux sessions → CSRF / session incohérents (419).
 * Chaque réponse envoie un Set-Cookie qui expire l’ancien nom.
 */
class ExpireLegacySessionCookie
{
    /** Noms de cookies de session à faire expirer (anciens APP_NAME / renommages). */
    private const LEGACY_NAMES = [
        'digitposts_session',
        'laravel_session',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $current = config('session.cookie');
        $path = config('session.path', '/');
        $domain = config('session.domain');

        foreach (self::LEGACY_NAMES as $name) {
            if ($name === $current) {
                continue;
            }
            $response->withoutCookie($name, $path, $domain);
        }

        return $response;
    }
}
