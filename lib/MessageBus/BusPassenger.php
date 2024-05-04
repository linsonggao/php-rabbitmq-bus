<?php

namespace PhpRabbitMq\Lib\MessageBus;

use PhpRabbitMq\Lib\Instance;
use PhpRabbitMq\Lib\MessageBus\works\OrderCancel;

/**
 * rabbitMq的交换机队列规则class
 */
class BusPassenger
{
    use Instance;
    //交换机
    private array $bus;
    //交换机的配置信息以及绑定的队列
    private array $busInfo;
    //队列对应的class对用works目录下的class
    private array $works = [];

    public function __construct()
    {
        $this->registerBus();
        $this->registerWork();


        //不存在的handler会自动绑定
        //自动模式会遵循大驼峰命名
        //比如member-login-cpa-callback对应MemberLoginCpaCallback
        $this->autoRegisterHanders();
    }

    /**
     * 注册交换机跟队列
     * 1对多绑定，1个事件发送到多个队列
     * @return void
     */
    private function registerBus()
    {
        //用户注册
        $this->defineBus(BusEnum::MEMBER_LOGIN);
        $this->defineBusInfo(BusEnum::MEMBER_LOGIN,BusEnum::MEMBER_LOGIN_CPA_CALL_BACK);
        $this->defineBusInfo(BusEnum::MEMBER_LOGIN,BusEnum::MEMBER_LOGIN_CPA_CALL_BACK_DATA);

        //用户激活
        $this->defineBus(BusEnum::USER_ACTIVATE);
        $this->defineBusInfo(BusEnum::USER_ACTIVATE,BusEnum::USER_ACTIVATE_CALLBACK);

        //订单回传
        $this->defineBus(BusEnum::ORDER_RETURN);
        $this->defineBusInfo(BusEnum::ORDER_RETURN,BusEnum::ORDER_RETURN_CALLBACK);

        //取消订单
        $this->defineBus(BusEnum::ORDER_CREATE);
        $this->defineDelayedBusInfo(BusEnum::ORDER_CREATE,BusEnum::ORDER_CANCEL,900);//15分钟自动取消订单

    }
    /**
     * 手动绑定事件绑定类
     * @return void
     */
    private function registerWork()
    {
        // Core
        $this->registeHandler(BusEnum::ORDER_CANCEL, OrderCancel::class);
    }

    /**
     * 自动注册器，默认处理规则
     * @return void
     */
    private function autoRegisterHanders()
    {
        foreach ($this->getBus() as $bus_name){
            $busInfo = $this->getBusInfo($bus_name);
            foreach ($busInfo['passengers'] as $passenger) {
                if(!isset($this->works[$passenger])) {
                    $psArr = explode("-",$passenger);
                    $class = '';
                    foreach ($psArr as $psStr)
                    {
                        $class .= ucfirst($psStr);
                    }
                    $this->registeHandler($passenger,'PhpRabbitMq\\Lib\\MessageBus\\works\\'.$class);
                }
            }
        }
    }

    /**
     * 注册处理器
     * @param $command
     * @param $handlerClass
     * @return void
     */
    public function registeHandler($command, $handlerClass)
    {
        $this->works[$command] = [
            'class' => $handlerClass,
        ];
    }

    /**
     * 获取所有works
     * @return array
     */
    public function getWork()
    {
        return $this->works;
    }

    /**
     * 获取单个bus详情
     * @param $bus_name
     * @return mixed
     */
    public function getBusInfo($bus_name)
    {
        if(!isset($this->busInfo[$bus_name])) {
            dd("不存在的bus:".$bus_name);
        }
        return $this->busInfo[$bus_name];
    }

    /**
     * 获取所有bus
     * @return \Generator
     */
    public function getBus(): \Generator
    {
        foreach ($this->bus as $bus => $entry) {
            yield $bus;
        }
    }
    /**
     * 注册一个命令-
     *
     * @param string $command
     *
     * @return void
     */
    public function defineBus(string $command)
    {
        $this->bus[$command] = [
            'delay' => false,
            'delay_dynamic' => false,
            'delay_seconds' => 0,
        ];
    }
    /**
     * 注册命令和事件绑定关系-
     *
     * @param string $command
     * @param string $queue
     *
     * @return void
     */
    public function defineBusInfo(string $command, string $queue)
    {
        $queues = $this->busInfo[$command]["passengers"] ??[];
        $queues[]= $queue;
        $this->busInfo[$command] = [
            'delay' => false,
            'delay_dynamic' => false,
            'delay_seconds' => 0,
            'delay_seconds_arr' => [],
            'passengers' => $queues
        ];
    }
    /**
     * 注册一个有延迟需求的命令，参数 $delay_second 表示默认延迟时间。
     *
     * @param string $command
     * @param int $delay_seconds
     *
     * @return void
     */
    public function defineDelayedBusInfo(string $command, string $queue,int $delay_seconds = 0)
    {
        $queues = $this->busInfo[$command]["passenger"] ??[];
        $queues[]= $queue;

        $delay_seconds_arr = $this->busInfo[$command]["delay_seconds_arr"] ??[];
        $delay_seconds_arr[$queue]= $delay_seconds;

        $delay_arr = $this->busInfo[$command]["delay_arr"] ??[];
        $delay_arr[$queue]= true;

        $this->busInfo[$command] = [
            'delay' => true,
            'delay_dynamic' => false,
            'delay_arr' => $delay_arr,
            'delay_seconds_arr' => $delay_seconds_arr,
            'passengers' => $queues
        ];
    }
}