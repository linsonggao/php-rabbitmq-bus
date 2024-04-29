<?php


namespace app\lib\MessageBus\base;


use app\lib\MessageBus\BusPassenger;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Wire\AMQPTable;

abstract class AbstractBus
{
    private const DEFAULT_EXCHANGE = 'default.direct';
    private const DEFAULT_DELAYED_MESSAGE_EXCHANGE = 'default.dmx.direct';
    private const DEAD_LETTER_EXCHANGE = 'dlx.direct';

    /**
     * 消费端 消费端需要保持运行状态实现方式
     **/
    function shutdown(AMQPChannel $channel, $connection)
    {
        $channel->close();
        $connection->close();
    }
    /**
     * @param $channel
     * @param $name bus_name,交换机
     * @return void
     */
    function bindBusPassengers(AMQPChannel $channel, $name, callable $bindDirectBusQueue, callable $bindDelayBusQueue)
    {
        $businfo = BusPassenger::instance()->getBusInfo($name);
        $channel->queue_declare($name, false, true, false, false);
        if (is_array($businfo['delay_seconds_arr']) && count($businfo['delay_seconds_arr']) > 0 ) {
            $this->delete_delay_queue($channel,$name);
            foreach ($businfo['passengers'] as $passenger) {
                if (isset($businfo['delay_arr'][$passenger]) && $businfo['delay_arr'][$passenger]) {
                    $bindDelayBusQueue($businfo,$channel, $passenger, $name);
                } else {
                    $bindDirectBusQueue($businfo,$channel, $name);
                }
            }
        } else {
            $bindDirectBusQueue($businfo,$channel, $name);
        }
    }

    protected static function array2Table(array $array): AMQPTable
    {
        $table = new AMQPTable();
        foreach ($array as $key => $value) {
            $table->set($key, $value);
        }

        return $table;
    }

    protected function ensureBus(AMQPChannel $channel, $bus_name)
    {
        // 默认交换机
        $channel->exchange_declare(self::DEFAULT_EXCHANGE, "direct", false, true, false);
        // 默认延迟消息交换机
        // 如果 send 指定了延迟时间，使用此交换机
        $channel->exchange_declare(self::DEFAULT_DELAYED_MESSAGE_EXCHANGE, "direct", false, true, false);
        // 死信交换机
        $channel->exchange_declare(self::DEAD_LETTER_EXCHANGE, "direct", false, true, false);
        // 事件交换机
        // 事件交换机都是 fanout 类型的，消息被分发复制到每个绑定的队列
        $channel->exchange_declare($bus_name, 'fanout', false, true, false);
        $bindDirectBusQueue = function ($businfo,AMQPChannel $channel, $bus_name) {
            // 不需要延迟（还包含仅动态延迟的队列）
            // 直接把队列绑定到默认交换机和事件交换机
            // routing key 等于原始队列名称
            $channel->queue_bind($bus_name, self::DEFAULT_EXCHANGE, $bus_name);
            //dump("  bound to " . self::DEFAULT_EXCHANGE);
            foreach ($businfo['passengers'] as $passenger) {
                $channel->queue_declare($passenger, false, true, false, false);
                $channel->queue_bind($passenger, $bus_name, $passenger);
                //dump("1111 queue $passenger  bound to $bus_name");
            }
        };
        $bindDelayBusQueue = function ($businfo,AMQPChannel $channel, $passenger, $bus_name) {
            //Message->Exchange--> Delayed Queue ->默认交换机(dlx.direct)->Queue->consumer hander
            $delayedName = $passenger . '.delayed.' . $businfo['delay_seconds_arr'][$passenger];
            // 因为有动态绑定的问题，需要删除之前的延迟队列
            $channel->queue_declare($delayedName, false, true, false, false, false, self::array2Table([
                'x-message-ttl' => 1000 * $businfo['delay_seconds_arr'][$passenger],
                'x-dead-letter-exchange' => self::DEAD_LETTER_EXCHANGE,
                'x-dead-letter-routing-key' => $bus_name,
            ]));

            //dump("  delayed queue $delayedName declared, dead letting exchange " . self::DEAD_LETTER_EXCHANGE);

            // 把延迟队列绑定到事件交换机
            // routing key 等于原始队列名称
            $channel->queue_bind($delayedName, $bus_name, $bus_name);

            self::redisCache()->push($bus_name,$delayedName);

            // 实际消费队列绑定到死信交换机
            $channel->queue_declare($passenger, false, true, false, false);
            $channel->queue_bind($passenger, self::DEAD_LETTER_EXCHANGE, $bus_name);
            //dump("$passenger final  bound to $bus_name");
        };
        $this->bindBusPassengers($channel, $bus_name,$bindDirectBusQueue,$bindDelayBusQueue);
    }

    //因为没有使用动态延迟,如果延迟队列修改了时间请解除历史绑定延迟队列时间，也可以在操作界面上直接删除~~~!!
    ////Message->Exchange--> Delayed Queue ->默认交换机(dlx.direct)->Queue->consumer hander
    public function delete_delay_queue(AMQPChannel $channel, $name){
        $businfo = BusPassenger::instance()->getBusInfo($name);
        $delayedQueues = self::redisCache()->get($name);
        if ($delayedQueues) {
            foreach ($delayedQueues as $delayedQueue) {
                //检查不能误删
                foreach ($businfo['passengers'] as $passenger) {
                    $delayedName = $passenger . '.delayed.' . $businfo['delay_seconds_arr'][$passenger];
                    if($delayedName == $delayedQueue) {
                        //dump("continue success:".$delayedQueue);
                        continue 2;
                    }
                }
                //dump("delete success:".$delayedQueue);
                $channel->queue_delete($delayedQueue);
                $channel->exchange_unbind($name,$delayedQueue);
            }
            self::redisCache()->clear();
        }
    }

    public static function redisCache()
    {
        $cache = Cache::store('redis');
        $cache->handler()->select(2);
        return $cache;
    }

}