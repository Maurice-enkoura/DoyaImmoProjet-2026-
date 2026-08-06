<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PayDunya Configuration
    |--------------------------------------------------------------------------
    */

    'master_key' => env('PAYDUNYA_MASTER_KEY'),
    'private_key' => env('PAYDUNYA_PRIVATE_KEY'),
    'public_key' => env('PAYDUNYA_PUBLIC_KEY'),
    'token' => env('PAYDUNYA_TOKEN'),
    
    'mode' => env('PAYDUNYA_MODE', 'test'),
    
    'store' => [
        'name' => env('PAYDUNYA_STORE_NAME', 'DoyaImmo'),
        'url' => env('PAYDUNYA_STORE_URL', 'http://localhost:8000'),
        'logo' => env('PAYDUNYA_STORE_LOGO'),
    ],
    
    'routes' => [
        'callback' => env('PAYDUNYA_CALLBACK_URL', 'http://localhost:8000/paydunya/callback'),
        'cancel' => env('PAYDUNYA_CANCEL_URL', 'http://localhost:8000/paydunya/cancel'),
    ],
];