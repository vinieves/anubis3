<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuração de Facebook Pixels
    |--------------------------------------------------------------------------
    |
    | IDs dos pixels do Facebook para cada checkout do sistema.
    | Isso permite tracking separado e A/B testing entre checkouts.
    |
    */

    'checkout' => [
        'pixel_id' => env('PIXEL_CHECKOUT', ''),
        'conversion_api_token' => env('PIXEL_CHECKOUT_TOKEN', ''),
        'enabled' => env('PIXEL_CHECKOUT_ENABLED', false),
    ],

    'pay' => [
        'pixel_id' => env('PIXEL_PAY', ''),
        'conversion_api_token' => env('PIXEL_PAY_TOKEN', ''),
        'enabled' => env('PIXEL_PAY_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações Globais
    |--------------------------------------------------------------------------
    */
    
    'test_mode' => env('PIXEL_TEST_MODE', false),
    
    // IDs de eventos de teste (opcional)
    'test_event_code' => env('PIXEL_TEST_EVENT_CODE', ''),
];

