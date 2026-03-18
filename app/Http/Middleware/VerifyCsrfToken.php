<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Webhooks CinetPay : POST sans token CSRF (serveur → serveur).
     * Important : ce middleware doit remplacer ValidateCsrfToken dans bootstrap/app.php,
     * pas être ajouté en plus — sinon ces URLs restent bloquées par le 1er CSRF.
     *
     * @var array<int, string>
     */
    protected $except = [
        'payments/notify',
        'subscriptions/notify',
    ];
}
