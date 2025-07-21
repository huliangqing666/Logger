<?php

namespace Hulq\PayDistribute\bin;

class Pay
{
    public $redis;

    public $log;

    public $log_name = 'pay';

    public function __construct()
    {
//        $this->redis = new \Redis();
//
//        $this->redis->connect('127.0.0.1', 6379);
//
//        $this->redis->select(0);

        $this->log = LogHelper::getLogger($this->log_name);
    }

    public function setFlag(string $orderSn, string $flag)
    {
//        $this->log->info("测试");
//        if ($this->redis) {
//            // 设置标志值并指定 5 分钟过期
//            $this->redis->setex($orderSn, 300, $flag);
//        } else {
//            $this->log->info("链接错误");
//        }
    }
}