<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Daily limit for rating article by specific IP address
    |--------------------------------------------------------------------------
    |
    */

    'daily_limit' => env('ARTICLE_RATE_DAILY_LIMIT'),
    'hourly_api_throttle' => env('HOURLY_API_THROTTLE'),
];
