<?php


namespace PhpRabbitMq\Lib\MessageBus\base;


use PhpRabbitMq\common\model\click\CpaClick;
use PhpRabbitMq\Lib\Instance;
use PhpRabbitMq\Lib\MessageBus\EventType;
use GuzzleHttp\Client;
use think\Collection;

abstract class AbstractWork
{
    use Instance;

    /**
     * @param $messages
     * @return void
     */
    abstract public function handler(array $message);

    private $url = "https://analytics.oceanengine.com/api/v2/conversion";

    public function callback($content) {
        return 0;
    }
}