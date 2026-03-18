<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Http\Request;

class VerifyCsrfToken extends Middleware
{
    /**
     * URIs exclues (webhooks CinetPay : POST sans token CSRF).
     *
     * @var array<int, string>
     */
    protected $except = [
        'payments/notify',
        'subscriptions/notify',
    ];

    /**
     * Doit exclure cette requête du contrôle CSRF.
     * Chemins + noms de route : plus robuste si l’URL change ou sous-dossier.
     */
    protected function inExceptArray($request): bool
    {
        return $this->shouldPassWithoutCsrf($request)
            || parent::inExceptArray($request);
    }

    protected function shouldPassWithoutCsrf(Request $request): bool
    {
        if ($request->routeIs('payments.notify', 'subscriptions.notify')) {
            return true;
        }

        $path = trim($request->path(), '/');

        return str_ends_with($path, 'payments/notify')
            || str_ends_with($path, 'subscriptions/notify');
    }
}
