<?php

namespace PhpRabbitMq\Lib\MessageBus;

class EventType
{
    /**
     * 激活 (快应用把加桌定义为激活)
     */
    public const Activate = 'active';

    /**
     * 注册
     */
    public const Register = 'active_register';

    /**
     * 付费
     */
    public const Payment = 'active_pay';

    /**
     * 关键行为
     */
    public const GAME_ADDICTION = 'game_addiction';

    /**
     * 付费2
     */
    public const SupplyPayment = 392;

    /**
     * 用户留存到次日
     */
    public const MemberKeptSecondDay = 6;
}