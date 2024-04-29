<?php

namespace app\lib\MessageBus;

final class BusEnum
{

    public  const MEMBER_LOGIN="member-login";                                      //用户登录bus
    public  const MEMBER_LOGIN_CPA_CALL_BACK="member-login-cpa-call-back";           //用户登录bus:用户登录bus回传queue,默认是巨量
    public  const MEMBER_LOGIN_CPA_CALL_BACK_DATA="member-login-call-back-data";     //用户登录bus:用户登录记录回传的关键信息



    public  const USER_ACTIVATE="user-activate";                                    //用户加桌（激活）
    public  const USER_ACTIVATE_CALLBACK="user-activate-callback";                  //用户激活bus:用户激活bus回传queue,默认是巨量



    public  const ORDER_RETURN="order-return";                                      //订单回传
    public  const ORDER_RETURN_CALLBACK="order-return-callback";                    //订单回传bus:订单回传bus回传queue,默认是巨量




    public  const ORDER_CREATE="order-create";                                      //创建订单

    public  const ORDER_CANCEL="order-create-cancel-order";                         //取消订单

}