<?php

return [
    // 连接信息
    'connect' => [
        'host'     => '127.0.0.1',
        'port'     => '5672',
        'user'     => 'admin',
        'password' => 'admin'
    ],

    // 测试主题
    'topic_queue' => [
        'exchange_name' => 'topic_exchange',
        'exchange_type' => 'topic',
        'queue_name'    => 'topic_queue',
        'route_key'     => '',
        'consumer_tag'  => 'topic'
    ],

];