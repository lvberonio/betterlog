<?php
/**
 * Custom Log Writer
 *
 * @date      08/3/17
 * @author    lvberonio
 * @copyright 2019 Incube8.sg
 */

namespace Lvberonio\Betterlog\Log;

use Illuminate\Log\Logger;
use Sentry\Laravel\Facade;
use Sentry\State\Scope;

/**
 * Custom Log Writer
 *
 * Provides some alternatives to the standard laravel log writer.
 */
class Writer extends Logger
{
    /**
     * Ignore debug logs from writing if app_debug is turned off
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function debug($message, array $context = array())
    {
        if (!config('app.debug')) {
            return;
        }

        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Write a message to the log.
     *
     * @param string $level
     * @param \Throwable|\Exception $exception
     * @param array $context
     * @return void
     */
    protected function writeLog($level, $exception, $context)
    {
        try {
            if (config('betterlog.enabled')) {
                if (config('betterlog.unhandled_exceptions_only')
                    || (config('betterlog.optional_logging') && isset($context['sentry_alert']))
                ) {
                    if (is_object($exception)) {
                        // Set default
                        $functionName = $traitName = null;

                        // Get Exception attributes
                        $className  = get_class($exception);
                        $fileName   = $exception->getFile();
                        $lineNumber = $exception->getLine();
                        $errorMsg   = $exception->getMessage();
                        $errorCode  = $exception->getCode();
                    } else {
                        // Split the log message based on formatting.
                        $logData = explode(':', $exception, 6);

                        if (count($logData) === 6) {
                            list($className, $traitName, $fileName, $lineNumber, $functionName, $errorMsg) = $logData;
                        } elseif (count($logData) === 5) {
                            list($className, $traitName, $fileName, $lineNumber, $functionName) = $logData;
                        } else {
                            list($className, $traitName, $fileName, $lineNumber, $functionName, $errorMsg) = [
                                null,
                                null,
                                null,
                                null,
                                null,
                                $logData[0]
                            ];
                        }

                        // Contain Exception context object
                        if (!empty($context['exception'])) {
                            $errorCode = $context['exception']->getCode();
                        } else {
                            $errorCode = $context['code'] ?? null;
                        }
                    }

                    // Override $errorMessage if empty
                    $errorMsg = $errorMsg ?? ($context['message'] ?? $exception);

                    $metaData = [
                        'extras' => [
                            'className'    => $className,
                            'traitName'    => $traitName,
                            'functionName' => $functionName,
                            'fileName'     => $fileName,
                            'lineNumber'   => $lineNumber,
                            'message'      => $errorMsg,
                            'code'         => $errorCode,
                            'details'      => json_encode($context)
                        ],
                        'level' => $level,
                    ];

                    Facade::withScope(function (Scope $scope) use ($metaData) {
                        $scope->setLevel(new \Sentry\Severity($metaData['level']));
                        $scope->setExtras($metaData['extras']);
                    });

                    if (is_object($exception)) {
                        Facade::captureException($exception);
                    } elseif (!empty($context['exception'])) {
                        Facade::captureException($context['exception']);

                        unset($context['exception']);
                    }
                }
            }
        } catch (\Exception $e) {
            // Proceed with normal logging
        }

        try {
            $this->fireLogEvent($level, $message = $this->formatMessage($exception), $context);

            $this->logger->{$level}($message, $context);
        } catch (\Exception $e) {
            // Ignore monolog Exception
        }
    }
}