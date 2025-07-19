<?php

namespace Hulq\PayDistribute\bin;

use Hulq\PayDistribute\LogHelper;
use Psr\Log\LoggerInterface;

class Loggers
{

    /**
     * @var
     */
    protected static  $logger;

    /**
     * Func getLogger
     * @author huliangqing
     * @date 2025-07-19
     * @param $name
     * @return LoggerInterface
     */
    public static function getLogger($name = 'app'): LoggerInterface
    {
        if (!isset(self::$logger)) {
            self::$logger = LogHelper::getLogger($name);
        }
        return self::$logger;
    }

    /**
     * Func info
     * @author huliangqing
     * @date 2025-07-19
     * @param string $message
     * @param array $context
     */

    public static function info(string $message, array $context = []): void
    {
        self::getLogger()->info($message, self::withRequestContext($context));
    }

    /**
     * Func warning
     * @author huliangqing
     * @date 2025-07-19
     * @param string $message
     * @param array $context
     */
    public static function warning(string $message, array $context = []): void
    {
        self::getLogger()->warning($message, self::withRequestContext($context));
    }

    /**
     * Func error
     * @author huliangqing
     * @date 2025-07-19
     * @param string $message
     * @param array $context
     */
    public static function error(string $message, array $context = []): void
    {
        self::getLogger()->error($message, self::withRequestContext($context));
    }

    /**
     * Func debug
     * @author huliangqing
     * @date 2025-07-19
     * @param string $message
     * @param array $context
     */
    public static function debug(string $message, array $context = []): void
    {
        self::getLogger()->debug($message, self::withRequestContext($context));
    }

    /**
     * Func withRequestContext
     * @author huliangqing
     * @date 2025-07-19
     * @param array $context
     * @return array
     */
    protected static function withRequestContext(array $context): array
    {
        return array_merge([
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'cli',
            'uri' => $_SERVER['REQUEST_URI'] ?? 'cli',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'cli',
            'time' => date('Y-m-d H:i:s'),
        ], $context);
    }

}
