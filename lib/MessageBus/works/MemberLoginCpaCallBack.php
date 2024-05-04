<?php

namespace PhpRabbitMq\Lib\MessageBus\works;
use PhpRabbitMq\Lib\MessageBus\base\AbstractWork;
//用户注册回传
class MemberLoginCpaCallBack extends AbstractWork
{
    public function handler($message) {
        var_dump("用户注册回调打印开始 用户ID-".date("Y-m-d H:i:s"),$message);
    }

}