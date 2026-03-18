<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Derrière Nginx / Cloudflare : Laravel voit le bon schéma (HTTPS) pour les cookies Secure
        $trusted = env('TRUSTED_PROXIES');
        if ($trusted === '*') {
            $middleware->trustProxies(at: '*');
        } elseif (is_string($trusted) && $trusted !== '' && $trusted !== 'false') {
            $middleware->trustProxies(at: array_map('trim', explode(',', $trusted)));
        }

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
        
        // Remplacer le CSRF du framework : sinon $except dans App\VerifyCsrfToken ne sert à rien
        // (CinetPay POST /payments/notify recevait 419 avant d’atteindre le 2e middleware).
        $middleware->web(replace: [
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class => \App\Http\Middleware\VerifyCsrfToken::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\ExpireLegacySessionCookie::class,
        ]);

        // Rate limiting global pour les routes API
        $middleware->api(prepend: [
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':60,1',
        ]);

        // Rate limiting pour les routes web sensibles
        $middleware->throttleApi('60,1');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
