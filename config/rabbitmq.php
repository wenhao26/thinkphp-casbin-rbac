<?php

return [
    // 连接信息
    'connect' => [
        'host'     => '161.117.4.208',
        'port'     => '5672',
        'user'     => 'hinovel-test',
        'password' => 'dbPfePFM0FWf2ysh'
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