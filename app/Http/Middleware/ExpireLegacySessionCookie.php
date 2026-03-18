<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * 1) Anciens noms de cookie (digitposts_session → digitposts_session_v2).
 * 2) Doublons même nom (ex. deux digitposts_session_v2) : host-only vs Domain=.site.com.
 *    Le navigateur peut envoyer les deux dans un ordre variable → PHP ne garde qu’une valeur
 *    (souvent la dernière) → ordre différent entre GET et POST → 419 sur logout, etc.
 * On expire les variantes « mauvais domaine » pour ne garder qu’une session cohérente.
 */
class ExpireLegacySessionCookie
{
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

        $this->expireDuplicateSessionScopes($response, $request, $current, $path);

        return $response;
    }

    /**
     * Si SESSION_DOMAIN est vide, Laravel envoie un cookie « host-only ».
     * Un second cookie avec Domain=.exemple.com crée un doublon : on l’expire.
     */
    private function expireDuplicateSessionScopes(Response $response, Request $request, string $cookieName, string $path): void
    {
        $configured = config('session.domain');
        if ($configured !== null && $configured !== '') {
            return;
        }

        $host = $request->getHost();
        if ($host === 'localhost' || str_ends_with($host, '.local') || filter_var($host, FILTER_VALIDATE_IP)) {
            return;
        }

        $apex = $this->apexHost($host);
        if ($apex === null) {
            return;
        }

        $secure = $this->sessionCookieSecure($request);
        $httpOnly = (bool) config('session.http_only', true);
        $sameSite = config('session.same_site', 'lax');
        $sameSite = is_string($sameSite) ? strtolower($sameSite) : 'lax';

        // Cookie « large » (tous sous-domaines) : à retirer si la config est host-only
        $response->headers->setCookie(new Cookie(
            $cookieName,
            '',
            1,
            $path,
            '.'.$apex,
            $secure,
            $httpOnly,
            false,
            $sameSite
        ));
    }

    private function sessionCookieSecure(Request $request): bool
    {
        $v = config('session.secure');
        if ($v !== null) {
            return filter_var($v, FILTER_VALIDATE_BOOLEAN);
        }

        return $request->secure();
    }

    /**
     * Domaine « racine » pour expirer les cookies Domain=.site.com (évite faux positifs sur app.site.com).
     */
    private function apexHost(string $host): ?string
    {
        if (preg_match('/^www\.(.+)$/i', $host, $m)) {
            return $m[1];
        }
        if (substr_count($host, '.') === 1) {
            return $host;
        }

        return null;
    }
}
