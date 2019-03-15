<?php
/**
 * Configure Logging Class
 *
 * @author    Lee
 * @copyright 2019 incube8.sg
 */

namespace Lvberonio\Betterlog;

use Illuminate\Log\LogManager;
use Lvberonio\Betterlog\Log\BaseLogManager;
use Lvberonio\Betterlog\Log\Writer;
use Illuminate\Log\LogServiceProvider as BaseConfigureLogging;

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
            return new BaseLogManager($this->app);
        });
    }
}