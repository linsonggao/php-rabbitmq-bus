<?php

namespace PhpRabbitMq\Lib\MessageBus\works;
use PhpRabbitMq\Lib\MessageBus\base\AbstractWork;

//用户注册回传信息获取
class MemberLoginCallBackData extends AbstractWork
{
    public function handler($message) {
        var_dump("用户callback打印开始 用户ID-".date("Y-m-d H:i:s"),$message);
    }

}