<?php

namespace PhpRabbitMq\Lib\MessageBus\works;
use PhpRabbitMq\Lib\MessageBus\base\AbstractWork;

class OrderCancel extends AbstractWork
{
    public function handler($message) {
//        dump("发送时间:".$message["created_at"]);
        var_dump("消费时间:".date("Y-m-d H:i:s"));
    }
}