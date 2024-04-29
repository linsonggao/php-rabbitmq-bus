<?php

namespace app\lib\MessageBus\works;
use app\common\model\click\CpaClick;
use app\common\model\click\UserCallback;
use app\common\model\click\UserCallbackLog;
use app\common\model\promotion\PromotionLink;
use app\common\model\recycle_report\RecycleReport;
use app\common\model\return_rule\ReturnRule;
use app\common\model\user\User;
use app\lib\Instance;
use app\lib\MessageBus\base\AbstractWork;
use app\lib\MessageBus\EventType;
use GuzzleHttp\Client;
use think\facade\Log;
//加桌回传
//https://open.oceanengine.com/labels/7/docs/1696710656359439
class UserActivateCallback extends AbstractWork
{
    public function handler($message) {
        dump("用户激活回调打印开始 用户ID-".$message['user_id'].date("Y-m-d H:i:s"),$message);
    }
}