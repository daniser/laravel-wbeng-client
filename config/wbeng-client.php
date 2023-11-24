<?php

use TTBooking\WBEngine\DTO\Enums\RespondType;

return [

    /*
    |--------------------------------------------------------------------------
    | Default WBEngine Connection Name
    |--------------------------------------------------------------------------
    */

    'default' => env('WB_CONNECTION', 'default'),

    'connections' => [

        'default' => [
            'uri' => env('WB_URI'),
            'login' => env('WB_LOGIN'),
            'password' => env('WB_PASSWORD'),
            'provider' => env('WB_PROVIDER', ''),
            'salePoint' => null,
            'currency' => env('WB_CURRENCY', 'RUB'),
            'locale' => env('WB_LOCALE', 'ru'),
            'respondType' => RespondType::JSON,
            'legacy' => env('WB_LEGACY', true),
            'serializer' => env('WB_SERIALIZER'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Default WBEngine Storage Name
    |--------------------------------------------------------------------------
    */

    'store' => env('WB_STORAGE', 'eloquent'),

    'stores' => [

        'eloquent' => [
            'model' => TTBooking\WBEngine\Models\State::class,
        ],

        'database' => [
            'table' => 'wbeng_state',
        ],

        'filesystem' => [
            'path' => 'wbeng/state',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Query Middleware
    |--------------------------------------------------------------------------
    */

    'middleware' => [
        TTBooking\WBEngine\Middleware\StoreMiddleware::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | IATA Location Prompter Callback
    |--------------------------------------------------------------------------
    */

    'iata_location_prompter' => null,

];
