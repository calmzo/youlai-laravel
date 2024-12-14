<?php

return [
    # 连接信息
    'AMQP'         => [
        'host'     => env('RABBITMQ_HOST'),   //连接rabbitmq,此为安装rabbitmq服务器
        'port'     => env('RABBITMQ_PORT'),
        'user'     => env('RABBITMQ_USER'),
        'password' => env('RABBITMQ_PASSWORD'),
        'vhost'    => '/'
    ],
    # 队列1
    'direct_queue' => [
        'exchange_name' => 'yuluo.test',
        'exchange_type' => 'direct',#直连模式
        'queue_name'    => 'test_mq_class',
        'route_key'     => 'direct_roteking',
        'consumer_tag'  => 'direct'
    ],


    # 队列2
    'test_advertiser_class_queue' => [
        'exchange_name' => 'yuluo.test',
        'exchange_type' => 'direct',#直连模式
        'queue_name'    => 'test_advertiser_class',
        'route_key'     => 'direct_roteking',
        'consumer_tag'  => 'direct'
    ]
];
