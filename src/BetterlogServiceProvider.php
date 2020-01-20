<?php
/**
 * Class BetterlogServiceProvider
 *
 * @date      08/3/17
 * @author    lvberonio
 * @copyright 2019 Incube8.sg
 */

namespace Lvberonio\Betterlog;

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
            __DIR__ . '/config/betterlog.php' => config_path('betterlog.php')
        ], 'betterlog_sentry_config');

        // Publish the middleware
        $this->publishes([
            __DIR__ . '/Http/Middleware/SentryContext.php' => app_path('Http/Middleware/SentryContext.php')
        ], 'betterlog_sentry_config');
    }
}