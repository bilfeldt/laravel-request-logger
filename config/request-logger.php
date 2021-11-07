<?php
// config for Bilfeldt/RequestLogger
return [
    'log_context' => env('REQUEST_LOGGER_CONTEXT', 'request-uuid'), // null means disabled
    'header' => env('REQUEST_LOGGER_HEADER', 'Request-Id'), // null means disabled

    'log_method' => [
//        '*',
//        'GET',
//        'HEAD',
//        'POST',
//        'PUT',
//        'DELETE',
//        'PATCH',
    ],

    'log_status' => [
//        '5**',
//        '4**',
//        '3**',
//        '2**',
    ],

    'default' => 'model',

    'drivers' => [
        'model' => [
            'class' => \Bilfeldt\RequestLogger\Models\RequestLog::class,
            'prune' => env('REQUEST_LOGGER_MODEL_PRUNE', 30), // Number of days keep records
        ],
    ],

    'filters' => [
        'password',
        'password_confirm',
        'apikey',
        'Authorization',
        'filter.search',
    ],
];
