<?php

namespace app\lib\MessageBus\works;
use app\common\model\book\Book;
use app\common\model\click\UserCallback;
use app\common\model\user\User;
use app\common\model\click\UserCallbackLog;
use app\common\model\promotion\PromotionLink;
use app\common\model\recycle_report\RecycleReport;
use app\lib\Instance;
use app\lib\MessageBus\base\AbstractWork;

//用户注册回传信息获取
class MemberLoginCallBackData extends AbstractWork
{
    public function handler($message) {
        dump("用户callback打印开始 用户ID-".$message['user_id'].date("Y-m-d H:i:s"),$message);
    }

}