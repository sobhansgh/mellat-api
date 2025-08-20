<?php

return [
    'terminal_id' => env('MELLAT_API_TERMINAL_ID'),
    'username'    => env('MELLAT_API_USERNAME'),
    'password'    => env('MELLAT_API_PASSWORD'),

    // Example: https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl
    'wsdl'        => env('MELLAT_API_WSDL', 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl'),

    // When true, you pass TOMAN to initiate(); it will be converted to RIAL for Mellat
    'convert_to_rial' => env('MELLAT_API_CONVERT_TO_RIAL', true),

    // Your API callback (server endpoint) for Mellat to POST back to
    'callback_url' => env('MELLAT_API_CALLBACK_URL', '/api/payments/mellat-api/callback'),

    // Routes prefix & middleware for published routes
    'route' => [
        'prefix' => 'api/payments/mellat-api',
        'middleware' => ['api'],
    ],
];
