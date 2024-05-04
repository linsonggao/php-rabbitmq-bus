<?php
require "./vendor/autoload.php";
use PhpRabbitMq\Lib\MessageBus\base\AmqpBus;

AmqpBus::instance()->publish([
    "bus_name"=>"member-login",
    "test"=>"测试消息发送成功!!!"
]);