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
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | IATA Location Prompter Callback
    |--------------------------------------------------------------------------
    */

    'iata_location_prompter' => null,

];
