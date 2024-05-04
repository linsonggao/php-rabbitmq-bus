<?php

namespace PhpRabbitMq\Lib\MessageBus\works;
use PhpRabbitMq\Lib\MessageBus\base\AbstractWork;
class UserActivateCallback extends AbstractWork
{
    public function handler($message) {
        var_dump("用户激活回调打印开始 用户ID-".$message['user_id'].date("Y-m-d H:i:s"),$message);
    }
}