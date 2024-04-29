<?php


namespace app\lib\MessageBus\base;


use app\common\model\click\CpaClick;
use app\lib\Instance;
use app\lib\MessageBus\EventType;
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
        $result = curlPost($this->url,$content);
        $result = json_decode($result,true);
        return $result;
    }
}