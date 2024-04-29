<?php

namespace app\lib\MessageBus\works;
use app\common\model\click\CpaClick;
use app\common\model\click\UserCallback;
use app\common\model\click\UserCallbackLog;
use app\common\model\return_rule\ReturnRule;
use app\common\model\user\User;
use app\lib\Instance;
use app\lib\MessageBus\base\AbstractWork;
use app\lib\MessageBus\EventType;
use GuzzleHttp\Client;
use think\facade\Log;

//https://event-manager.oceanengine.com/docs/8650/quickapp/

//用户注册回传
class MemberLoginCpaCallBack extends AbstractWork
{
    public function handler($message) {
        dump("用户注册回调打印开始 用户ID-".$message['user_id'].date("Y-m-d H:i:s"),$message);
    }

}