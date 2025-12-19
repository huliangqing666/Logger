<?php
namespace Hulq\PayDistribute;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Hulq\PayDistribute\Formatter\ReadableFormatter;


class LogHelper
{
    /**
     * @var array
     */
    private static $loggers = [];


    /**
     * Func getLogger
     * @author huliangqing
     * @date 2025-07-19
     * @param $channel
     * @return mixed|Logger
     */
    public static function getLogger($channel = 'app')
    {
        if (!isset(self::$loggers[$channel])) {

            $logger = new Logger($channel);


            //$logDir = defined('ROOT_PATH') ? ROOT_PATH . 'runtime/logs/'.date("Ymd").'/' : __DIR__ . '/../runtime/logs/'.date("Ymd").'/';

            $logDir = dirname(__DIR__, 3) . '/runtime/logs/' . date('Ymd') . '/';

            //$logDir = defined('ROOT_PATH') ? ROOT_PATH . 'runtime/logs/' : __DIR__ . '/../runtime/logs/';

            if (!is_dir($logDir)) {

                if (!mkdir($logDir, 0755, true) && !is_dir($logDir)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $logDir));
                }
            }

            $logPath = $logDir . $channel . '-' . date('Y-m-d') . '.log';

            // 创建处理器并使用自定义格式化器
            $handler = new RotatingFileHandler($logPath, 7, Logger::DEBUG);
            $handler->setFormatter(new ReadableFormatter());
            $logger->pushHandler($handler);

            self::$loggers[$channel] = $logger;
        }

        return self::$loggers[$channel];
    }
}
