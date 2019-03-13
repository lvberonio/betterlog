<?php
/**
 * Configure Logging Class
 *
 * @author    Lee
 * @copyright 2019 incube8.sg
 */

namespace Lvberonio\Betterlog;

use Lvberonio\Betterlog\Log\Writer;
use Illuminate\Log\LogServiceProvider as BaseConfigureLogging;
use Monolog\Logger as Monolog;

class ConfigureLogging extends BaseConfigureLogging
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('log', function () {
            return $this->createLogger();
        });
    }

    /**
     * Create the logger.
     *
     * @return \App\Services\Betterlog\Log\Writer
     */
    public function createLogger()
    {
        $log = new Writer(
            new Monolog($this->channel()), $this->app['events']
        );

        if ($this->app->hasMonologConfigurator()) {
            call_user_func($this->app->getMonologConfigurator(), $log->getMonolog());
        } else {
            $this->configureHandler($log);
        }

        return $log;
    }
}