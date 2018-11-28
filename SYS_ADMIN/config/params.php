<?php
return [
    'versionJS' => '20171115202100',
    'adminEmail' => 'admin@example.com',
    'status' => [0 => '删除', 1=> '显示', 2=>'不显示',],
    'wx'=>[
        //  公众号信息
        'mp'=>[
            //  账号基本信息
            'app_id'  => 'wx2e4c11f43a7669eb', // 公众号的appid
            'secret'  => '4df9871fe2ab5c6b1e61350d5b05bb76', // 公众号的秘钥
            'token'   => '', // 接口的token
            'encodingAESKey'=>'',
            'safeMode'=>0,

            //  微信支付
            'payment'=>[
                'mch_id'        =>  '1313582001',// 商户ID
                'key'           =>  'edaqNz6bCpDQSLtG2FgK4YWmxhwRUoEs',// 商户KEY
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
    ]
];
