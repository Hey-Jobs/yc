<?php
$params = array_merge(
    require(YII_FRAMEWORK_PATH . '/common/config/params.php'),
    require(YII_FRAMEWORK_PATH . '/common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'SYS_ADMIN\controllers',
    'bootstrap' => ['log'],
    'defaultRoute' => 'home/index',
    'modules' => [
        "admin" => [
            "class" => 'mdm\admin\Module',
//            'layout' => 'left-menu',//yii2-admin的导航菜单
        ],
        'gii' => ['class' => 'yii\gii\Module']
    ],
    'components' => [
        'request' => [
//            'csrfParam' => '_csrf-backend',
            'cookieValidationKey' => 'qxtDCdvQh-RCSC_urAKhrarouDleMVZl',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'identityCookie' => ['name' => '_identity-SYS_ADMIN', 'httpOnly' => true],
            'loginUrl' => ['login/login'],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-SYS_ADMIN',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
//            'enablePrettyUrl' => true,
//            'enableStrictParsing' => true,
//            'showScriptName' => false,
//            'rules' => [
//                ['class' => 'yii\rest\UrlRule', 'controller' => 'api'],
//            ],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => getenv('YII_DB_SYS_DSN'),
            'username' => getenv('YII_DB_SYS_USER'),
            'password' => getenv('YII_DB_SYS_PASS'),
            'charset' => 'utf8mb4',
            'tablePrefix' => getenv('YII_DB_SYS_PRE')
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager', // 使用数据库管理配置文件
        ],
        'assetManager' => [
            'linkAssets' => true,
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'database' => 0,
        ],
    ],
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            'rest/v1/*',
            'login/login',
            'login/logout',
            'login/captcha',
            'demo/*',
            'text/*',
        ]
    ],

    'params' => $params,
    'language' =>'zh-CN',
];
