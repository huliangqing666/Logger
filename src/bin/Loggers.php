<?php
namespace Hulq\PayDistribute\bin;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Hulq\PayDistribute\Formatter\JsonReadableFormatter;
use Hulq\PayDistribute\Processor\TraceProcessor;

class Loggers
{
    protected static $logger;

    protected static function getLogger(): Logger
    {
        if (!self::$logger) {

            $logger = new Logger('pay');

            // ===== 日志文件 =====
            $handler = new StreamHandler(
                __DIR__ . '/../../runtime/logs/pay.log',
                Logger::DEBUG
            );

            $handler->setFormatter(new JsonReadableFormatter());

            $logger->pushHandler($handler);

            // ✅ 加 Processor（核心）
            $logger->pushProcessor(new TraceProcessor());

            self::$logger = $logger;
        }

        return self::$logger;
    }

    public static function info($message, array $context = [])
    {
        if (is_array($message) && empty($context)) {
            return self::getLogger()->info('', $message);
        }

        return self::getLogger()->info($message, $context);
    }

    public static function error($message, array $context = [])
    {
        if (is_array($message) && empty($context)) {
            return self::getLogger()->error('', $message);
        }

        return self::getLogger()->error($message, $context);
    }
}