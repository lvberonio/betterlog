<?php
/**
 * Custom Log Manager
 *
 * @date      08/3/17
 * @author    lvberonio
 * @copyright 2019 Incube8.sg
 */

namespace Lvberonio\Betterlog\Log;

use Illuminate\Log\LogManager;

class BaseLogManager extends LogManager
{
    /**
     * Attempt to get the log from the local cache.
     *
     * @param string $name
     * @return \Psr\Log\LoggerInterface
     */
    protected function get($name)
    {
        try {
            return $this->channels[$name] ?? with($this->resolve($name), function ($logger) use ($name) {
                    return $this->channels[$name] = $this->tap($name, new Writer($logger, $this->app['events']));
                });
        } catch (\Throwable $e) {
            return tap($this->createEmergencyLogger(), function ($logger) use ($e) {
                $logger->emergency('Unable to create configured logger. Using emergency logger.', [
                    'exception' => $e,
                ]);
            });
        }
    }
}