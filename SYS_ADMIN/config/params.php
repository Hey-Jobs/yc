<?php
return [
    'versionJS' => '20171115202100',
    'adminEmail' => 'admin@example.com',
    'status' => [0 => '删除', 1=> '显示', 2=>'不显示',],
    'wx'=>[
        //  公众号信息
        'mp'=>[
            //  账号基本信息
            'app_id'  => 'wx6813aaf9a23e0aaa', // 公众号的appid
            'secret'  => 'd8a6f5c0b285e1819bd45f6c72494ee8', // 公众号的秘钥
            'token'   => '', // 接口的token
            'encodingAESKey'=>'',
            'safeMode'=>0,

            //  微信支付
            'payment'=>[
                'mch_id'        =>  '1519888171',// 商户ID
                'key'           =>  '9p8JjbfSnQZIDVrdU1iO2tcGRasuymHv',// 商户KEY
                'notify_url'    =>  '',// 支付通知地址
                'cert_path'     => '/cert/apiclient_cert.pem',// 证书
                'key_path'      => '/cert/apiclient_key.pem',// 证书
            ],

            // web授权
            'oauth' => [
                'scopes'   => 'snsapi_userinfo',// 授权范围
                'callback' => '',// 授权回调
            ],
        ],
        'template' => [
            'order_success' => 'soksY_RsROaESZZJ8WNsHrR-PxkdpXQq-0rwEFKbFLU',
            'order_fail' => 'E-SF2wvFFtGnENRuMzJcxrATNSmSXyaQ3lfwVSuAfDo',
            'delivery' => 'SJ01IR3dMwI33qsR3toij4e0O5wtsVz0RTE9LlgLlVE',
        ],
        'notify' => "o5NW-51dtx7F1e-lU2uu9acOOPh4",
    ]
];
