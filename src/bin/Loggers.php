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
     * @param string $name
     * @return LoggerInterface
     *@author huliangqing
     * @date 2025-07-19
     */
    public static function getLogger(string $name = 'app'): LoggerInterface
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
     * @param string|array $message 日志消息或上下文数组
     * @param array $context 上下文信息（当第一个参数是字符串时使用）
     */

    public static function info($message, array $context = []): void
    {
        if (is_array($message)) {
            // 如果第一个参数是数组，将其作为上下文，消息为空或从数组中提取
            $logMessage = '';
            $finalContext = self::withRequestContext($message);
        } else {
            // 如果第一个参数是字符串，正常处理
            $logMessage = $message;
            $finalContext = self::withRequestContext($context);
        }
        
        self::getLogger()->info($logMessage, $finalContext);
    }

    /**
     * Func warning
     * @author huliangqing
     * @date 2025-07-19
     * @param string|array $message 日志消息或上下文数组
     * @param array $context 上下文信息（当第一个参数是字符串时使用）
     */
    public static function warning($message, array $context = []): void
    {
        if (is_array($message)) {
            $logMessage = '';
            $finalContext = self::withRequestContext($message);
        } else {
            $logMessage = $message;
            $finalContext = self::withRequestContext($context);
        }
        
        self::getLogger()->warning($logMessage, $finalContext);
    }

    /**
     * Func error
     * @author huliangqing
     * @date 2025-07-19
     * @param string|array $message 日志消息或上下文数组
     * @param array $context 上下文信息（当第一个参数是字符串时使用）
     */
    public static function error($message, array $context = []): void
    {
        if (is_array($message)) {
            $logMessage = '';
            $finalContext = self::withRequestContext($message);
        } else {
            $logMessage = $message;
            $finalContext = self::withRequestContext($context);
        }
        
        self::getLogger()->error($logMessage, $finalContext);
    }

    /**
     * Func debug
     * @author huliangqing
     * @date 2025-07-19
     * @param string|array $message 日志消息或上下文数组
     * @param array $context 上下文信息（当第一个参数是字符串时使用）
     */
    public static function debug($message, array $context = []): void
    {
        if (is_array($message)) {
            $logMessage = '';
            $finalContext = self::withRequestContext($message);
        } else {
            $logMessage = $message;
            $finalContext = self::withRequestContext($context);
        }
        
        self::getLogger()->debug($logMessage, $finalContext);
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
        ], $context);
    }

}
