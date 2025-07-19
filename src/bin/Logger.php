<?php

namespace Hulq\PayDistribute\bin;

use Hulq\PayDistribute\LogHelper;
use Psr\Log\LoggerInterface;

class Logger
{
    protected static  $logger;

    // 初始化 logger（懒加载）
    public static function getLogger($name = 'app'): LoggerInterface
    {
        if (!isset(self::$logger)) {
            self::$logger = LogHelper::getLogger($name);
        }
        return self::$logger;
    }

    public static function info(string $message, array $context = []): void
    {
        self::getLogger()->info($message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::getLogger()->warning($message, self::withRequestContext($context));
    }

    public static function error(string $message, array $context = []): void
    {
        self::getLogger()->error($message, self::withRequestContext($context));
    }

    public static function debug(string $message, array $context = []): void
    {
        self::getLogger()->debug($message, self::withRequestContext($context));
    }

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
