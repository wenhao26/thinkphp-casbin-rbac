<?php


namespace app\common;

use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class MqUtils
 * @package app\common
 */
class MqUtils
{
    protected $connection;

    protected $channel;


    /**
     * MqUtils constructor.
     * @param array $config
     * @throws Exception
     */
    public function __construct(array $config = [])
    {
        $this->connection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password']);
        $this->channel    = $this->connection->channel(); // 建立通道
    }

    // 生产者
    public function publish(array $data, string $exchangeName, string $exchangeType, string $queueName, string $routingKey)
    {
        // 初始化交换机
        $this->channel->exchange_declare($exchangeName, $exchangeType, false, true, false);

        // 初始化队列
        $this->channel->queue_declare($queueName, false, true, false, false);

        // 绑定队列与交换机
        $this->channel->queue_bind($queueName, $exchangeName, $routingKey);

        // 生成消息
        $body = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $msg  = new AMQPMessage(
            $body,
            [
                'content-type'  => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
            ]
        );

        // 推送消息
        $this->channel->basic_publish($msg, $exchangeName, $routingKey);
        // $this->channel->close();
        // $this->connection->close();

        return true;
    }

    // 消费者
    public function consumer(string $exchangeName, string $exchangeType, string $queueName, string $routingKey, string $consumerTag)
    {
        // 流量控制
        $this->channel->basic_qos(null, 1, null);

        // 初始化交换机
        $this->channel->exchange_declare($exchangeName, $exchangeType, false, true, false);

        // 初始化队列
        $this->channel->queue_declare($queueName, false, true, false, false);

        // 绑定队列与交换机
        $this->channel->queue_bind($queueName, $exchangeName, $routingKey);

        // 消费消息
        $this->channel->basic_consume($queueName, $consumerTag, false, false, false, false, [$this, 'msgProc']);

        // 退出
        register_shutdown_function([$this, 'shutdown'], $this->channel, $this->connection);

        // 监听
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    public function msgProc($msg)
    {
        echo $msg->body . "\n";
        $msg->ack();
    }

    public function consumer2(string $exchangeName, string $exchangeType, string $queueName, string $routingKey, string $consumerTag, callable $cb)
    {
        // 流量控制
        $this->channel->basic_qos(0, 1000, false);

        // 初始化交换机
        $this->channel->exchange_declare($exchangeName, $exchangeType, false, true, false);

        // 初始化队列
        $this->channel->queue_declare($queueName, false, true, false, false);

        // 绑定队列与交换机
        $this->channel->queue_bind($queueName, $exchangeName, $routingKey);

        // 消费消息
        $this->channel->basic_consume($queueName, $consumerTag, false, false, false, false, $cb);

        // 退出
        register_shutdown_function([$this, 'shutdown'], $this->channel, $this->connection);

        // 监听
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }


    protected function shutdown()
    {
        $this->channel->close();
        $this->connection->close();
    }

}