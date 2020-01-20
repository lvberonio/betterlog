<?php
/**
 * Configure Logging Class
 *
 * @date      08/3/17
 * @author    lvberonio
 * @copyright 2019 Incube8.sg
 */

namespace Lvberonio\Betterlog;

use Lvberonio\Betterlog\Log\BaseLogManager;
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