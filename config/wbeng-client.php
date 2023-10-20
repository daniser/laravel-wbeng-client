<?php

use TTBooking\WBEngine\DTO\Air\Enums\RespondType;

return [

    /*
    |--------------------------------------------------------------------------
    | Default WBEngine Client Connection Name
    |--------------------------------------------------------------------------
    */

    'default' => env('WB_CONNECTION', 'default'),

    'connections' => [

        'default' => [
            'uri' => env('WB_URI'),
            'login' => env('WB_LOGIN'),
            'password' => env('WB_PASSWORD'),
            'salepoint' => null,
            'locale' => env('WB_LOCALE', 'ru'),
            'respondType' => RespondType::JSON,
            'currency' => env('WB_CURRENCY', 'RUB'),
            'id' => 0,
            'provider' => env('WB_PROVIDER', '1H,1S,S7'),
            'context_id' => null,
        ],

    ],

];
