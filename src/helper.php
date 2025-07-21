<?php

use  \Hulq\PayDistribute\bin\Loggers;

/**
 * 设置日志文件名称
 */
if (!function_exists('loggers_set_name')) {
    function loggers_set_name(string $name)
    {
        Loggers::getLogger($name);
    }
}

/**
 * 打印普通日志
 */
if (!function_exists('loggers')) {
    function loggers(string $message,array $context = [])
    {
        Loggers::info($message,$context);
    }
}


/**
 * 打印错误日志
 */
if (!function_exists('loggers_error')) {
    function loggers_error(string $message,array $context = [])
    {
        Loggers::error($message,$context);
    }
}








