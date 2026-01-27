<?php

return [
    'api_key' => env('CINETPAY_API_KEY'),
    'site_id' => env('CINETPAY_SITE_ID'),
    'secret_key' => env('CINETPAY_SECRET_KEY'),
    'api_url' => env('CINETPAY_API_URL', 'https://api-checkout.cinetpay.com/v2/payment'),
    'check_url' => env('CINETPAY_CHECK_URL', 'https://api-checkout.cinetpay.com/v2/payment/check'),
    // En local : définir l’URL publique (ex. ngrok) pour que CinetPay atteigne return_url et notify_url
    'payment_base_url' => env('CINETPAY_PAYMENT_BASE_URL'),
];
