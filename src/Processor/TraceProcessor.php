<?php

namespace Hulq\PayDistribute\Processor;

use Hulq\PayDistribute\Support\Trace;

class TraceProcessor
{
    public function __invoke(array $record)
    {
        $record['extra']['trace_id'] = Trace::getTraceId();
        $record['extra']['ip'] = $_SERVER['REMOTE_ADDR'] ?? 'cli';
        $record['extra']['uri'] = $_SERVER['REQUEST_URI'] ?? 'cli';
        $record['extra']['method'] = $_SERVER['REQUEST_METHOD'] ?? 'CLI';

        return $record;
    }
}