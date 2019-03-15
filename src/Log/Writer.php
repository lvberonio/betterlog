<?php
/**
 * Custom Log Writer
 *
 * @author     Lee
 * @copyright  2019 incube8.sg
 */

namespace Lvberonio\Betterlog\Log;

use Illuminate\Log\Logger;

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
     * @param  string $message
     * @param  array $context
     * @return void
     */
    public function debug($message, array $context = array())
    {
        // return if not in debug mode
        if (!config('app.debug')) {
            return;
        }

        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Write a message to the log.
     *
     * @param  string  $level
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    protected function writeLog($level, $message, $context)
    {
        try {
            if (config('sentry.enabled')) {
                if (config('sentry.enabled_unhandled_exceptions_only') ||
                    (config('sentry.enabled_optional_logging') &&
                        isset($context['sentry_alert']) &&
                        $context['sentry_alert'] === true)
                ) {
                    if (is_object($message)) {
                        // exception attributes
                        $className     = get_class($message);
                        $fileName      = $message->getFile();
                        $lineNumber    = $message->getLine();
                        $errorMessage  = $message->getMessage();
                        $functionName  = null;
                        $traitName     = null;
                        $exceptionCode = $message->getCode();
                        $exceptionType = null;
                    } else {
                        // Split the log message to see how it is formatted.
                        $logData = explode(':', $message, 6);

                        if (count($logData) === 6) {
                            list($className, $traitName, $fileName, $lineNumber, $functionName, $errorMessage) =
                                $logData;
                        } elseif (count($logData) === 5) {
                            list($className, $traitName, $fileName, $lineNumber, $functionName) = $logData;
                        } else {
                            list($className, $traitName, $fileName, $lineNumber, $functionName, $errorMessage) = [
                                null,
                                null,
                                null,
                                null,
                                null,
                                $logData[0]
                            ];
                        }

                        if (!empty($context['exception'])) {
                            // context contains the exception object
                            $exceptionType = get_class($context['exception']);
                            $exceptionCode = $context['exception']->getCode();
                        } else {
                            $exceptionType = !empty($context['exception_type'])
                                ? $context['exception_type']
                                : (!empty($errorMessage)
                                    ? $errorMessage
                                    : (!empty($context['message'])
                                        ? $context['message']
                                        : $message
                                    )
                                );
                            $exceptionCode = !empty($context['code'])
                                ? $context['code']
                                : null;
                        }
                    }

                    $errorMessage = !empty($errorMessage)
                        ? $errorMessage
                        : (!empty($context['message'])
                            ? $context['message']
                            : $message
                        );

                    $metaData = [
                        'extra' => [
                            'className'    => $className,
                            'traitName'    => $traitName,
                            'functionName' => $functionName,
                            'fileName'     => $fileName,
                            'lineNumber'   => $lineNumber,
                            'message'      => $errorMessage,
                            'code'         => $exceptionCode,
                            'details'      => json_encode($context)
                        ],
                        'level' => $level,
                    ];

                    // Should only log to Sentry when it is exception
                    if (is_object($message)) {
                        // $message is an exception
                        \Sentry\SentryLaravel\SentryFacade::captureException($message, $metaData);
                    } elseif (!empty($context['exception'])) {
                        \Sentry\SentryLaravel\SentryFacade::captureException($context['exception'], $metaData);

                        unset($context['exception']);
                    }
                }
            }
        } catch (\Exception $e) {
            // do nothing, just continue logging
        }

        try {
            $this->fireLogEvent($level, $message = $this->formatMessage($message), $context);

            $this->logger->{$level}($message, $context);
        } catch (\Exception $e) {
            // ignore monolog exception
        }
    }
}
