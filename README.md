## Betterlog

The Betterlog service provider extends the existing Laravel 5.5 monolog writer with the following features:

* Catch Laravel exceptions and ignore the exception to prevent it from breaking the application
* Send to Sentry.io for better error reporting and monitoring

### Installation

***NOTE:*** *Before you begin, make sure to remove Incube8/Debug and/or Incube8/Nolog as this is meant as a replacement.*

Add these lines to your composer.json file:

```
"require": {
    "leeberonio/betterlog": "1.2.2"
},
"repositories": [
    {
        "type": "vcs",
        "url": "ssh://git@stash.infostreamgroup.com:7999/ls/betterlog.git"
    }
],
```

Run the composer update command to download and install Betterlog component:

```
composer update
```

#### Register Service Provider

After composer update is complete, add these lines to `app/Providers/AppServiceProvider.php` in the register() function:

```
// Overrides monolog logging
$this->app->register(\App\Services\Betterlog\ConfigureLogging::class);
```

#### Include Betterlog Required Files

Run this Artisan command to copy required files, namely `config/sentry.php` and `Http/Middleware/SentryContext.php`:

```
php artisan vendor:publish --provider="Incube8\Betterlog\BetterlogServiceProvider" --force
```

### Sentry Config

Before using Sentry, make sure to provide config values to `config/sentry.php`

Add these lines into you .env file

```
SENTRY_ENABLED=true
SENTRY_DSN=<provided_dsn>
```

### Usage

There are multiple ways to start Laravel logging.
No matter which option or method is implemented, Betterlog will continue to work with Sentry.

#### Option 1 - Direct to Sentry

Betterlog sends out data to sentry by providing Exception or if context contains "exception".

```
try {
    // do something
} catch (\Exception $e) {
    \Log::error(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
        'Exception thrown on getting user profile view data for a member', [
        'exception_type' => get_class($e),
        'message'        => $e->getMessage(),
        'code'           => $e->getCode(),
        'line_number'    => $e->getLine(),
        'exception'      => $e,
    ]);
}    
```

#### Option 2 - Using existing Log facade

Useful for existing applications that already use the Log facade.

```
// Typical logging, nothing additional to add
try {
    // do something
} catch (\Exception $e) {
    Log::error(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
        'Exception thrown on getting user profile view data for a member', [
            'exception_type' => get_class($e),
            'message'        => $e->getMessage(),
            'code'           => $e->getCode(),
            'line_number'    => $e->getLine(),
            'profile'        => !empty($profile) ? $profile->id : '',
        ]
    );
    
    throw new ProfileViewException('Exception thrown on getting user profile view data for a member', 40114014);
}

// Extended info logging
    Log::error(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
        'Logging informational logs', [
            'arg1' => 'value',
            'arg2' => 'value',
        ]
    );
    
// Normal info logging
Log::info('Some information here');
```

##### Attaching User Data to Sentry

As this package is already integrated with Laravel, attaching user information is quick and easy.

#### Update the Middleware

Simply update the middleware `app/Http/Middleware/SentryContext` to provide user data to Sentry via its callback function.

##### Attaching the Middleware

Add this middleware class to the `$middleware` in `app/Http/Kernel.php`.

```
protected $middleware = [
    \App\Http\Middleware\AddUserToSentry::class
];
```

When exception is thrown, Betterlog provides user data to Sentry for further information.

### Testing

For testing purposes that exception is thrown to Sentry, add the following code to your command or job:
```
if (config('sentry.enabled_test_exception')) {
    throw new \Exception('Unhandled exception is thrown to test sentry logging');
}
```

Then add this to `.env` file:
```
SENTRY_ENABLED_TEST_EXCEPTION=false
```