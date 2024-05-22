<?php
require "./vendor/autoload.php";
use PhpRabbitMq\Lib\MessageBus\base\AmqpBus;

$data = [
    "bus_name"=>"member-login",
    "test"=>"测试消息发送成功!!!"
];
var_dump("生产消息");
AmqpBus::instance()->publish($data);