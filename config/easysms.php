<?php

/*
 * This file is part of the leonis/easysms-notification-channel.
 * (c) yangliulnn <yangliulnn@163.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

return [
    // HTTP 请求的超时时间（秒）
    'timeout' => 5.0,

    // 默认发送配置
    'default' => [
        // 网关调用策略，默认：顺序调用
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

        // 默认可用的发送网关
        'gateways' => [
            'aliyun',
        ],
    ],

    // 可用的网关配置
    'gateways' => [
        // 失败日志
        'errorlog' => [
            'channel' => 'smslog',
        ],

        'submail' => [
            'app_id' => env('SMS_SUBMAIL_APP_ID'),
            'app_key' => env('SMS_SUBMAIL_APP_KEY'),
            'project' => env('SMS_SUBMAIL_PROJECT'), // 默认 project，可在发送时 data 中指定
        ],

        // 云片
        'yunpian' => [
            'api_key' => 'efabf**********************20fd3',
        ],

        //阿里云
        'aliyun' => [
            'access_key_id' => env('SMS_ALIYUN_ACCESS_KEY_ID'),
            'access_key_secret' => env('SMS_ALIYUN_ACCESS_KEY_SECRET'),
            'sign_name' => env('SMS_ALIYUN_SIGN_NAME'),
        ],

        // ...
    ],

    'custom_gateways' => [
        'errorlog' => \Leonis\Notifications\EasySms\Gateways\ErrorLogGateway::class,
        'winic' => \Leonis\Notifications\EasySms\Gateways\WinicGateway::class,
    ],
];
