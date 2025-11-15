<?php

namespace Hulq\PayDistribute\Formatter;

use Monolog\Formatter\FormatterInterface;

class ReadableFormatter implements FormatterInterface
{
    /**
     * 格式化日志记录（Laravel风格）
     *
     * @param array $record
     * @return string
     */
    public function format(array $record): string
    {
        // 时间戳和日志级别
        $datetime = $record['datetime']->format('Y-m-d H:i:s');
        $level = strtoupper($record['level_name']);
        $channel = $record['channel'] ?? 'app';
        
        // 构建主日志行（Laravel风格：[时间] 频道.级别: 消息）
        $message = !empty($record['message']) ? $record['message'] : '';
        $logLine = sprintf("[%s] %s.%s: %s", $datetime, $channel, $level, $message);
        
        // 处理上下文信息
        $context = $record['context'] ?? [];
        $contextJson = '';
        $extraJson = '';
        
        // 格式化上下文信息
        if (!empty($context)) {
            // 如果上下文不是空数组，转换为JSON
            $contextJson = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            // 如果JSON是空对象或空数组，则不显示
            if ($contextJson === '[]' || $contextJson === '{}') {
                $contextJson = '';
            }
        }
        
        // 格式化额外信息
        if (!empty($record['extra'])) {
            $extraJson = json_encode($record['extra'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($extraJson === '[]' || $extraJson === '{}') {
                $extraJson = '';
            }
        }
        
        // 组合日志行（Laravel风格：消息后跟上下文和额外信息）
        $parts = [$logLine];
        if (!empty($contextJson)) {
            $parts[] = $contextJson;
        }
        if (!empty($extraJson)) {
            $parts[] = $extraJson;
        }
        
        return implode(' ', $parts) . "\n";
    }
    
    /**
     * 判断是否需要多行显示
     *
     * @param array $context
     * @return bool
     */
    protected function needsMultilineDisplay(array $context): bool
    {
        foreach ($context as $value) {
            if (is_array($value) && !empty($value)) {
                return true;
            }
            if (is_object($value)) {
                return true;
            }
            // 如果单个值很长（超过50个字符），也用多行显示
            if (is_string($value) && mb_strlen($value) > 50) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * 格式化上下文为内联格式（简洁显示）
     *
     * @param array $context
     * @return string
     */
    protected function formatInlineContext(array $context): string
    {
        $items = [];
        foreach ($context as $key => $value) {
            if (is_array($value)) {
                // 数组转换为JSON格式
                $formattedValue = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } elseif (is_bool($value)) {
                $formattedValue = $value ? 'true' : 'false';
            } elseif (is_null($value)) {
                $formattedValue = 'null';
            } elseif (is_object($value)) {
                // 对象也转换为JSON格式
                $formattedValue = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                $formattedValue = (string)$value;
            }
            $items[] = "{$key}: {$formattedValue}";
        }
        return implode(', ', $items);
    }
    
    /**
     * 批量格式化日志记录
     *
     * @param array $records
     * @return string
     */
    public function formatBatch(array $records): string
    {
        $output = [];
        foreach ($records as $record) {
            $output[] = $this->format($record);
        }
        return implode("\n", $output);
    }
    
    /**
     * 格式化上下文数据为易读格式
     *
     * @param array $context
     * @param int $indent
     * @return string
     */
    protected function formatContext(array $context, int $indent = 0): string
    {
        $lines = [];
        $indentStr = str_repeat('  ', $indent);
        
        foreach ($context as $key => $value) {
            if (is_array($value)) {
                if (empty($value)) {
                    $lines[] = sprintf("%s%s: []", $indentStr, $key);
                } elseif ($this->isAssocArray($value)) {
                    $lines[] = sprintf("%s%s:", $indentStr, $key);
                    $lines[] = $this->formatContext($value, $indent + 1);
                } else {
                    // 索引数组，格式化JSON
                    $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                    $indentedJson = $this->indentJson($json, $indentStr);
                    $lines[] = sprintf("%s%s: %s", $indentStr, $key, $indentedJson);
                }
            } elseif (is_object($value)) {
                if (method_exists($value, '__toString')) {
                    $lines[] = sprintf("%s%s: %s", $indentStr, $key, (string)$value);
                } else {
                    $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                    $indentedJson = $this->indentJson($json, $indentStr);
                    $lines[] = sprintf("%s%s: %s", $indentStr, $key, $indentedJson);
                }
            } elseif (is_bool($value)) {
                $lines[] = sprintf("%s%s: %s", $indentStr, $key, $value ? 'true' : 'false');
            } elseif (is_null($value)) {
                $lines[] = sprintf("%s%s: null", $indentStr, $key);
            } else {
                $lines[] = sprintf("%s%s: %s", $indentStr, $key, $value);
            }
        }
        
        return implode("\n", $lines);
    }
    
    /**
     * 检查是否为关联数组
     *
     * @param array $array
     * @return bool
     */
    protected function isAssocArray(array $array): bool
    {
        if (empty($array)) {
            return false;
        }
        return array_keys($array) !== range(0, count($array) - 1);
    }
    
    /**
     * 格式化数组为内联格式（用于请求参数，简洁显示）
     *
     * @param array $array
     * @return string
     */
    protected function formatInlineArray(array $array): string
    {
        $items = [];
        
        foreach ($array as $key => $value) {
            // 格式化键
            if (is_string($key)) {
                $formattedKey = "'{$key}'";
            } else {
                $formattedKey = $key;
            }
            
            // 格式化值
            if (is_string($value)) {
                $formattedValue = "'{$value}'";
            } elseif (is_bool($value)) {
                $formattedValue = $value ? 'true' : 'false';
            } elseif (is_null($value)) {
                $formattedValue = 'null';
            } elseif (is_numeric($value)) {
                $formattedValue = $value;
            } elseif (is_array($value)) {
                $formattedValue = '[...]';
            } else {
                $formattedValue = "'" . (string)$value . "'";
            }
            
            $items[] = "{$formattedKey} => {$formattedValue}";
        }
        
        return '[' . implode(', ', $items) . ']';
    }
    
    /**
     * 格式化数组为易读格式（用于请求参数）
     *
     * @param array $array
     * @param int $indent
     * @return string
     */
    protected function formatArray(array $array, int $indent = 0): string
    {
        $indentStr = str_repeat('  ', $indent);
        $lines = [];
        $lines[] = $indentStr . '[';
        
        $keys = array_keys($array);
        $lastKey = end($keys);
        
        foreach ($array as $key => $value) {
            $isLast = ($key === $lastKey);
            $comma = $isLast ? '' : ',';
            
            // 格式化值
            if (is_string($value)) {
                $formattedValue = "'{$value}'";
            } elseif (is_bool($value)) {
                $formattedValue = $value ? 'true' : 'false';
            } elseif (is_null($value)) {
                $formattedValue = 'null';
            } elseif (is_numeric($value)) {
                $formattedValue = $value;
            } elseif (is_array($value)) {
                if (empty($value)) {
                    $formattedValue = '[]';
                } else {
                    // 格式化嵌套数组
                    $nestedArray = $this->formatArray($value, $indent + 1);
                    // 格式化键
                    if (is_string($key)) {
                        $formattedKey = "'{$key}'";
                    } else {
                        $formattedKey = $key;
                    }
                    // 将嵌套数组的第一行与键合并
                    $nestedLines = explode("\n", $nestedArray);
                    $firstLine = $indentStr . "  {$formattedKey} => " . ltrim($nestedLines[0]);
                    $lines[] = $firstLine;
                    // 添加其余行
                    for ($i = 1; $i < count($nestedLines) - 1; $i++) {
                        $lines[] = $nestedLines[$i];
                    }
                    // 最后一行加上逗号
                    $lastNestedLine = $nestedLines[count($nestedLines) - 1];
                    $lines[] = $lastNestedLine . $comma;
                    continue;
                }
            } else {
                $formattedValue = "'" . (string)$value . "'";
            }
            
            // 格式化键
            if (is_string($key)) {
                $formattedKey = "'{$key}'";
            } else {
                $formattedKey = $key;
            }
            
            $lines[] = $indentStr . "  {$formattedKey} => {$formattedValue}{$comma}";
        }
        
        $lines[] = $indentStr . ']';
        
        return implode("\n", $lines);
    }
    
    /**
     * 缩进JSON字符串
     *
     * @param string $json
     * @param string $indentStr
     * @return string
     */
    protected function indentJson(string $json, string $indentStr): string
    {
        $lines = explode("\n", $json);
        $indentedLines = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed !== '') {
                // 如果JSON已经格式化，需要额外缩进
                $indentedLines[] = $indentStr . '  ' . $line;
            }
        }
        return implode("\n", $indentedLines);
    }
}

