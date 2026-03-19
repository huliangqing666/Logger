<?php
namespace Hulq\PayDistribute;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Hulq\PayDistribute\Formatter\ReadableFormatter;


// 遗弃
class LogHelper
{
    private static $loggers = [];

    public static function getLogger($channel = 'app'): Logger
    {
        if (!isset(self::$loggers[$channel])) {
            $logger = new Logger($channel);

            $logDir = defined('ROOT_PATH') ? ROOT_PATH . 'runtime/logs/' : __DIR__ . '/../runtime/logs/';
            if (!is_dir($logDir) && !mkdir($logDir, 0755, true) && !is_dir($logDir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $logDir));
            }

            // RotatingFileHandler 会自己生成带日期的日志文件
            $logPath = $logDir . $channel . '.log';
            $handler = new RotatingFileHandler($logPath, 7, Logger::DEBUG);
            $handler->setFormatter(new ReadableFormatter());

            $logger->pushHandler($handler);
            self::$loggers[$channel] = $logger;
        }

        return self::$loggers[$channel];
    }
}