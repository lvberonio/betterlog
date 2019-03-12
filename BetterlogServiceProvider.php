<?php
/**
 * Class BetterlogServiceProvider
 *
 * @date      08/3/17
 * @author    leeberonio
 * @copyright Copyright (c) Incube8.sg
 */

namespace Incube8\Betterlog;

use Illuminate\Support\ServiceProvider;

/**
 * Class BetterlogServiceProvider
 */
class BetterlogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Publish the config
        $this->publishes([
            __DIR__ . '/config/sentry.php' => config_path('sentry.php')
        ], 'sentry_config');

        // Publish the middleware
        $this->publishes([
            __DIR__ . '/Http/Middleware/SentryContext.php' => app_path('Http/Middleware/AddUserToSentry.php')
        ], 'sentry_config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }
}