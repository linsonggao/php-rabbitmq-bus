<?php

namespace PhpRabbitMq\Lib\MessageBus\works;
use PhpRabbitMq\Lib\MessageBus\base\AbstractWork;

class OrderReturnCallback extends AbstractWork
{
    public function handler($message) {
        var_dump("订单回传开始 订单ID".$message['order_id'].date("Y-m-d H:i:s"),$message);
    }

}