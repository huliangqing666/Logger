<?php
namespace Hulq\PayDistribute;

use http\Env\Request;
use Monolog\Formatter\JsonFormatter;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

class LogHelper
{
    private static $loggers = [];

    public static function getLogger($channel = 'app')
    {
        if (!isset(self::$loggers[$channel])) {

            $logger = new Logger($channel);

            $logDir = defined('ROOT_PATH') ? ROOT_PATH . 'runtime/logs/' : __DIR__ . '/../runtime/logs/';

            if (!is_dir($logDir)) {

                if (!mkdir($logDir, 0755, true) && !is_dir($logDir)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $logDir));
                }
            }

            $logPath = $logDir . $channel . '.log';

            $logger->pushHandler(new RotatingFileHandler($logPath, 7, Logger::DEBUG));

//            $handler = new RotatingFileHandler($logPath, 7, Logger::DEBUG);
//            $handler->setFormatter(new JsonFormatter()); // JSON 格式
//            $logger->pushHandler($handler);

            self::$loggers[$channel] = $logger;
        }

        return self::$loggers[$channel];
    }
}
