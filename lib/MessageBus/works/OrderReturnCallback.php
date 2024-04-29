<?php

namespace app\lib\MessageBus\works;
use app\api\service\RecycleReportService;
use app\common\logic\OrderCallbackLogic;
use app\common\model\activity\RechargeActivity;
use app\common\model\callback\OrderCallback;
use app\common\model\click\CpaClick;
use app\common\model\click\UserCallback;
use app\common\model\media_account\MediaAccount;
use app\common\model\order\Order;
use app\common\model\promotion\PromotionLink;
use app\common\model\return_rule\ReturnRule;
use app\common\model\user\User;
use app\lib\Instance;
use app\lib\MessageBus\base\AbstractWork;
use app\lib\MessageBus\EventType;
use GuzzleHttp\Client;
use think\facade\Log;

class OrderReturnCallback extends AbstractWork
{
    public function handler($message) {
        dump("订单回传开始 订单ID".$message['order_id'].date("Y-m-d H:i:s"),$message);
    }

}