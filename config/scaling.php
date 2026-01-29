<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Limites pour grand volume d'utilisateurs
    |--------------------------------------------------------------------------
    */

    'limits' => [
        'feeds_homepage' => (int) env('SCALE_FEEDS_HOMEPAGE', 200),
        'campaigns_per_creator' => (int) env('SCALE_CAMPAIGNS_PER_CREATOR', 500),
        'admin_pagination' => (int) env('SCALE_ADMIN_PAGINATION', 20),
        'user_registrations_pagination' => (int) env('SCALE_USER_REGISTRATIONS_PAGINATION', 10),
        'user_favorites_pagination' => (int) env('SCALE_USER_FAVORITES_PAGINATION', 12),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate limiting (requêtes / minute)
    |--------------------------------------------------------------------------
    */

    'throttle' => [
        'login' => (int) env('THROTTLE_LOGIN', 5),
        'payments_initiate' => (int) env('THROTTLE_PAYMENTS_INITIATE', 20),
        'subscriptions_initiate' => (int) env('THROTTLE_SUBSCRIPTIONS_INITIATE', 20),
        'inscriptions_store' => (int) env('THROTTLE_INSCRIPTIONS_STORE', 30),
    ],

];
