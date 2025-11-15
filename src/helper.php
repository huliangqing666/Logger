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
 * @param string|array $message 日志消息或上下文数组
 * @param array $context 上下文信息（当第一个参数是字符串时使用）
 */
if (!function_exists('loggers')) {
    function loggers($message, array $context = [])
    {
        Loggers::info($message, $context);
    }
}


/**
 * 打印错误日志
 * @param string|array $message 日志消息或上下文数组
 * @param array $context 上下文信息（当第一个参数是字符串时使用）
 */
if (!function_exists('loggers_error')) {
    function loggers_error($message, array $context = [])
    {
        Loggers::error($message, $context);
    }
}

