# php-rabbitmq-bus
## 这是一个基于amqp封装的rabbitmq独立的代码包
配置文件lib/MessageBus/rabbitmq.php
```phpregexp
<?php
return [
    'AMQP' => [
        'host' => "localhost",
        'port'=>"5672",
        'login'=>"admin",
        'password'=>"123456",
        'vhost'=>"my_vhost"
    ],
];
```
使用方法
## 1.开启监听demo
php start.php
## 2.生产demo
php produce.php
