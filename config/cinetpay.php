<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CinetPay API v1 (OAuth + /v1/payment)
    |--------------------------------------------------------------------------
    | Sandbox : https://api.cinetpay.net
    | Production : à confirmer dans votre espace marchand (souvent api.cinetpay.com)
    */
    'base_url' => rtrim(env('CINETPAY_BASE_URL', 'https://api.cinetpay.net'), '/'),

    /** Clé compte (account_key) */
    'api_key' => env('CINETPAY_API_KEY'),

    /** Mot de passe API (account_password) — requis pour l’API v1 */
    'api_password' => env('CINETPAY_API_PASSWORD'),

    /**
     * Ancienne intégration (checkout v2) — conservé si besoin de migration.
     * Non utilisé par l’API v1.
     */
    'site_id' => env('CINETPAY_SITE_ID'),
    'secret_key' => env('CINETPAY_SECRET_KEY'),

    // En local : URL publique (ngrok) pour success_url, failed_url, notify_url
    'payment_base_url' => env('CINETPAY_PAYMENT_BASE_URL'),
];
