<?php

namespace app\lib\MessageBus\works;

use app\common\model\order\Order as OrderModel;
use app\lib\Instance;
use app\lib\MessageBus\base\AbstractWork;
use think\facade\Log;

class OrderCancel extends AbstractWork
{
    public function handler($message) {
//        dump("发送时间:".$message["created_at"]);
        dump("消费时间:".date("Y-m-d H:i:s"));
    }
}