<?php
return [

    'aliyun' => [
        'accessKeyID'     => env('ALIOSS_ACCESSKEYID', ''),
        'accessKeySecret' => env('ALIOSS_ACCESSKEYSECRET', ''),
        'bucket'          => env('ALIOSS_BUCKET', ''),
        'endpoint'        => env('ALIOSS_ENDPOINT', ''),
        'domain'          => env('ALIOSS_DOMAIN', ''),
    ]
];
