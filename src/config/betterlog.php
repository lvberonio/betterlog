<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enable Betterlog package
    |--------------------------------------------------------------------------
    |
    | Set to true to log using Betterlog.
    |
    */
    'enabled'                   => env('BETTERLOG_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Enable test exception
    |--------------------------------------------------------------------------
    |
    | Set to true to pass through test exception.
    |
    */
    'test_exception'            => env('BETTERLOG_TEST_EXCEPTION', false),

    /*
    |--------------------------------------------------------------------------
    | Log ONLY unhandled exception
    |--------------------------------------------------------------------------
    |
    | Set to true to log ONLY unhandled exceptions to Sentry.
    |
    */
    'unhandled_exceptions_only' => env('BETTERLOG_UNHANDLED_EXCEPTIONS_ONLY', true),

    /*
    |--------------------------------------------------------------------------
    | Optionally log to Sentry
    |--------------------------------------------------------------------------
    |
    | Set to true to optionally allow logging to Sentry when sentry_alert = true
    | parameter exists in the Log facade.
    |
    */
    'optional_logging'          => env('BETTERLOG_OPTIONAL_LOGGING', false),

    /*
    |--------------------------------------------------------------------------
    | Enable Sentry
    |--------------------------------------------------------------------------
    |
    | Set to true to log all logs to sentry.
    |
    */
    'sentry'                    => [
        'enabled' => env('SENTRY_ENABLED', false),
    ],
];