<?php

// config for Bilfeldt/RequestLogger
return [
    /*
    |--------------------------------------------------------------------------
    | Log Context
    |--------------------------------------------------------------------------
    |
    | When not empty this will add the Unique Request UUID to the log context
    | using the specified key.
    |
    */
    'log_context' => env('REQUEST_LOGGER_CONTEXT', 'request-uuid'), // null means disabled

    /*
    |--------------------------------------------------------------------------
    | Response Header
    |--------------------------------------------------------------------------
    |
    | When not empty this will add the Unique Request UUID to the response
    | headers using the specified key.
    |
    */
    'headers' => [
        'header' => [
            'name' => env('REQUEST_LOGGER_HEADER', 'Request-Id'), // null means disabled,
            'value' => fn() => isset($request)?$request->getUniqueId():null,
        ],
        'version' => [
            'name' => env('REQUEST_LOGGER_VERSION_HEADER', 'App-Version'),
            'value' => fn() => gethostname(),
        ]
    ],


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
        'Authorization',
        'filter.search',
    ],
];
