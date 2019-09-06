<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable Sentry
    |--------------------------------------------------------------------------
    |
    | Set to true to enable Sentry.
    |
    */

    'enabled' => env('SENTRY_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Enable log to Sentry
    |--------------------------------------------------------------------------
    |
    | Set to true to send logs to sentry.
    |
    */

    'enabled_log' => env('SENTRY_ENABLED_LOG', false),

    /*
    |--------------------------------------------------------------------------
    | Enable test exception to log in Sentry
    |--------------------------------------------------------------------------
    |
    | Set to true to log all logs to sentry.
    |
    */

    'enabled_test_exception' => env('SENTRY_ENABLED_TEST_EXCEPTION', false),

    /*
    |--------------------------------------------------------------------------
    | Enable Sentry only for unhandled exceptions
    |--------------------------------------------------------------------------
    |
    | Set to true to log ONLY for unhandled exceptions to sentry.
    | Requires sentry.enabled_log = true
    |
    */

    'enabled_unhandled_exceptions_only' => env('SENTRY_ENABLED_UNHANDLED_EXCEPTIONS_ONLY', false),

    /*
    |--------------------------------------------------------------------------
    | Optionally log to Sentry
    |--------------------------------------------------------------------------
    |
    | Set to true to optionally allow logging to Sentry when sentry_alert = true
    | parameter exists in the Log facade.
    | Requires sentry.enabled_log = true
    |
    */

    'enabled_optional_logging' => env('SENTRY_ENABLED_OPTIONAL_LOGGING', false),

    /*
    |--------------------------------------------------------------------------
    | Sentry DSN
    |--------------------------------------------------------------------------
    |
    | Enter the Sentry DSN to log to.
    |
    */

    'dsn' => env('SENTRY_DSN'),

    'public_dsn' => env('SENTRY_PUBLIC_DSN'),

    /*
    |--------------------------------------------------------------------------
    | SQL query bindings
    |--------------------------------------------------------------------------
    |
    | Set to true to capture bindings on SQL queries.
    |
    */

    'breadcrumbs.sql_bindings' => env('SENTRY_SQL_BINDINGS', true),
];
