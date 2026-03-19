<?php

namespace Hulq\PayDistribute\Support;

class Trace
{

    protected static $traceId;

    public static function getTraceId(): string
    {
        if (!self::$traceId) {
            self::$traceId = self::generate();
        }
        return self::$traceId;
    }

    public static function setTraceId(string $traceId)
    {
        self::$traceId = $traceId;
    }

    protected static function generate(): string
    {
        return uniqid('trace_', true);
    }
}