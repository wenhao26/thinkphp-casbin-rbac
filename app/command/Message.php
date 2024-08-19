<?php
declare (strict_types=1);

namespace app\command;

use app\common\MqUtils;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Config;

/**
 * php think message --city consumer
 *
 * Class Message
 * @package app\command
 */
class Message extends Command
{
    protected $exchangeName = 'topic_exchange';
    protected $exchangeType = 'topic';
    protected $queueName    = 'topic_queue';
    protected $consumerTag  = 'topic';


    protected function configure()
    {
        // 指令配置
        $this->setName('message')
            ->addOption('action', null, Option::VALUE_REQUIRED, 'action name')
            ->setDescription('the message command');
    }

    protected function execute(Input $input, Output $output)
    {
        if ($input->hasOption('action')) {
            $this->consumer();
        } else {
            $this->publish();
        }

        // 指令输出
        $output->writeln('message');
    }


    protected function publish()
    {
        $config = Config::get('rabbitmq.connect');

        $mq = new MqUtils($config);

        for (; ;) {
            $currentDate = date('Y-m-d H:i:s');
            echo $currentDate . PHP_EOL;
            $data = [
                'time' => $currentDate
            ];
            $mq->publish($data, $this->exchangeName, $this->exchangeType, $this->queueName, 'topic_key');
        }
    }

    protected function consumer()
    {
        $config = Config::get('rabbitmq.connect');

        $mq = new MqUtils($config);
        $mq->consumer2($this->exchangeName, $this->exchangeType, $this->queueName, 'topic_key', $this->consumerTag, function ($msg) {
            echo $msg->body . PHP_EOL;
            $msg->ack();
        });
    }

}
