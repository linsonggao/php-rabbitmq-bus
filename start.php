<?php
require "./vendor/autoload.php";

use PhpRabbitMq\Lib\MessageBus\base\AmqpBus;
ini_set ("memory_limit","-1");
//* 1.用户登录事件监听   php think message_bus --bus member-login
//*
//* 2.订单支付事件监听  php think message_bus --bus order-paid
//*
//* 3.订单创建事件监听  php think message_bus --bus order-create
AmqpBus::instance()->start("member-login");
