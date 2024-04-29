<?php


namespace app\lib\MessageBus\base;

use app\lib\Instance;
use app\lib\MessageBus\BusPassenger;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use PhpAmqpLib\Connection\AMQPStreamConnection;


class AmqpBus extends AbstractBus implements BusInterface
{
    use Instance;
    /**
     * 启动
     * @return \think\Response
     */
    public function start($bus_name)
    {
        //dump("ensureBus");
        $param = config('rabbitmq.AMQP');
        //$amqpDetail = config('rabbitmq.direct_queue');
        $connection = new AMQPStreamConnection(
            $param['host'],
            $param['port'],
            $param['login'],
            $param['password'],
            $param['vhost'],
            keepalive: true,
            heartbeat: 3
        );
        //dump("ensureBus");
        /*
         * 创建通道
         */
        $channel = $connection->channel();
        /*
         * 设置消费者（Consumer）客户端同时只处理一条队列
         * 这样是告诉RabbitMQ，再同一时刻，不要发送超过1条消息给一个消费者（Consumer），
         * 直到它已经处理了上一条消息并且作出了响应。这样，RabbitMQ就会把消息分发给下一个空闲的消费者（Consumer）。
         */
        //dump("ensureBus");
        $channel->basic_qos(0, 1, false);
        /*
         * 同样是创建路由和队列，以及绑定路由队列，注意要跟publisher的一致
         * 这里其实可以不用，但是为了防止队列没有被创建所以做的容错处理
         */
        //dump("ensureBus");
        $this->ensureBus($channel, $bus_name);
        //$channel->queue_bind($amqpDetail['queue_name'], $amqpDetail['exchange_name'],$amqpDetail['route_key']);
        /*
            queue: 从哪里获取消息的队列
            consumer_tag: 消费者标识符,用于区分多个客户端
            no_local: 不接收此使用者发布的消息
            no_ack: 设置为true，则使用者将使用自动确认模式。详情请参见.
                        自动ACK：消息一旦被接收，消费者自动发送ACK
                        手动ACK：消息接收后，不会发送ACK，需要手动调用
            exclusive:是否排他，即这个队列只能由一个消费者消费。适用于任务不允许进行并发处理的情况下
            nowait: 不返回执行结果，但是如果排他开启的话，则必须需要等待结果的，如果两个一起开就会报错
            callback: :回调逻辑处理函数,PHP回调 array($this, 'process_message') 调用本对象的process_message方法
        */
        $noAck = $options['no_ack'] ?? true;

        //消费队列～
        $busInfo = BusPassenger::instance()->getBusInfo($bus_name);
        foreach ($busInfo['passengers'] as $queue_name) {
            //$this->queue_name = $queue_name;
            $channel->basic_consume($queue_name, '', false, $noAck, false, false, function (AMQPMessage $msg) use ($queue_name) {
                $messageData = json_decode($msg->body, true);
                //执行对应的class即可
                //todo.
                $handler = BusPassenger::Instance()->getWork()[$queue_name]['class'];

                try {
                    /**@var AbstractWork $handler**/
                    $handler::Instance($handler)->handler($messageData);
                } catch (\Throwable $e) {
                    var_dump("消息消费错误信息:".$e->getMessage());
                };
            });
        }

        register_shutdown_function(array($this, 'shutdown'), $channel, $connection);
        // AMQP 队列的轮询时长
        $waitSeconds = $options['wait_seconds'] ?? 15;
        try {
            $channel->consume($waitSeconds);
        } catch (AMQPConnectionClosedException $e) {
        }
    }

    public function publish($data)
    {
        $param = config('rabbitmq.AMQP');
        $connection = new AMQPStreamConnection(
            $param['host'],
            $param['port'],
            $param['login'],
            $param['password'],
            $param['vhost']
        );
        $channel = $connection->channel();
        /*
         * 创建交换机(Exchange)
         * name: vckai_exchange// 交换机名称
         * type: direct        // 交换机类型，分别为direct/fanout/topic，参考另外文章的Exchange Type说明。
         * passive: false      // 如果设置true存在则返回OK，否则就报错。设置false存在返回OK，不存在则自动创建
         * durable: false      // 是否持久化，设置false是存放到内存中的，RabbitMQ重启后会丢失
         * auto_delete: false  // 是否自动删除，当最后一个消费者断开连接之后队列是否自动被删除
         */
        $channel->exchange_declare($data['bus_name'], "fanout", false, true, false);

        /*
             $messageBody:消息体
             content_type:消息的类型 可以不指定
             delivery_mode:消息持久化最关键的参数
             AMQPMessage::DELIVERY_MODE_NON_PERSISTENT = 1; 不持久化
             AMQPMessage::DELIVERY_MODE_PERSISTENT = 2; 持久化
         */

        //将要发送数据变为json字符串
        $messageBody = json_encode($data);
        /*
         * 创建AMQP消息类型
         * $messageBody:消息体
         * delivery_mode 消息是否持久化
         *      AMQPMessage::DELIVERY_MODE_NON_PERSISTENT = 1; 不持久化
         *      AMQPMessage::DELIVERY_MODE_PERSISTENT = 2; 持久化
         */
        $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));

        /*
         * 发送消息
         * msg       // AMQP消息内容
         * exchange  // 交换机名称
         * routing key     // 路由键名称
         */
        $channel->basic_publish($message, $data['bus_name'], $data['bus_name']);
        $channel->close();
        $connection->close();
        //echo  "ok";
    }
}