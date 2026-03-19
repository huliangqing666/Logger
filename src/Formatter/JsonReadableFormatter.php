<?php

namespace Hulq\PayDistribute\Formatter;

use Monolog\Formatter\FormatterInterface;

class JsonReadableFormatter implements FormatterInterface
{
    public function format(array $record): string
    {
        $data = [
            'time' => $record['datetime']->format('Y-m-d H:i:s'),
            'level' => $record['level_name'],
            'channel' => $record['channel'],
            'trace_id' => $record['extra']['trace_id'] ?? '',
            'ip' => $record['extra']['ip'] ?? '',
            'method' => $record['extra']['method'] ?? '',
            'uri' => $record['extra']['uri'] ?? '',
        ];

        // ===== 核心：统一 message + context =====
        if (is_array($record['message'])) {
            $data['data'] = $record['message'];
        } else {
            $data['msg'] = $record['message'];

            if (!empty($record['context'])) {
                $data['data'] = $record['context'];
            }
        }

        return json_encode(
                $data,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES
            ) . "\n";
    }

    public function formatBatch(array $records): string
    {
        return implode("", array_map([$this, 'format'], $records));
    }
}