<?php

namespace PhpRabbitMq\Lib\MessageBus\base;

interface BusInterface
{
    /**
     * 发送事件消息
     *
     * @param string $event
     * @param mixed $message
     *
     * @return void
     */
    public function publish($message);

    /**
     * 监听交换机消息、并且分发交换机信息到队列
     *
     * @param string $bus_name
     *
     * @return void
     */
    public function start(string $bus_name);

}