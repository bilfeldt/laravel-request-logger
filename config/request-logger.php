<?php

// config for Bilfeldt/RequestLogger
return [
    /*
    |--------------------------------------------------------------------------
    | Enable logging
    |--------------------------------------------------------------------------
    |
    | These settings can be used to conditionally enable/disable logging.
    |
    */
    'log_methods' => [
        // '*',
        // 'GET',
        // 'HEAD',
        // 'POST',
        // 'PUT',
        // 'DELETE',
        // 'PATCH',
    ],
    'log_statuses' => [
        // '*',
        // '5**',
        // '4**',
        // '3**',
        // '2**',
    ],
    'ignore_paths' => [
        'telescope-api*',
        'horizon*',
        'nova-api*',
        'livewire*',
    ],
    'disable_robots_tracking' => false, // require 'jaybizzle/crawler-detect' to be installed

    /*
    |--------------------------------------------------------------------------
    | Default Driver
    |--------------------------------------------------------------------------
    |
    | This is the default driver used when non is specified when enabling logging.
    |
    */
    'default' => 'model',

    /*
    |--------------------------------------------------------------------------
    | Log Drivers
    |--------------------------------------------------------------------------
    |
    | The following array lists the "drivers" that can be used for logging.
    | Since the package implements the Manager class 3rd party packages can
    | dynamically register new drivers from a Service Provider.
    |
    */
    'drivers' => [
        'model' => [
            'class' => \Bilfeldt\RequestLogger\Models\RequestLog::class,
            'prune' => env('REQUEST_LOGGER_MODEL_PRUNE', 30), // Number of days keep records
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    |
    | Certain personal or sensitive data can be filtered out of the logs by
    | specifying them here.
    |
    */
    'filters' => [
        'password',
        'password_confirm',
        'apikey',
        'api_token',
        'Authorization',
        'filter.search',
    ],
];
