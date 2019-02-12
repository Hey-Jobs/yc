<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/12
 * Time: 21:10
 */

namespace SYS_ADMIN\controllers\rest\v1;

use abei2017\wx\Application;

/**
 * Class WechatManageController
 * @package SYS_ADMIN\controllers\rest\v1
 * 微信管理
 */
class WechatManageController extends CommonController
{
    /**
     * 添加菜单栏
     */
    public function actionAddMenu()
    {
        $conf = \Yii::$app->params['wx']['mp'];
        $menu = (new Application(['conf' => $conf]))->driver("mp.menu");
        $buttons = [
            'button' => [
                [
                    'type' => 'click',
                    'name' => '溯源直播',
                    'url' => 'https://yc.adaxiang.com/front/?from=singlemessage#/',
                ],
            ]
        ];
        $result = $menu->create($buttons);
        var_dump($result);
    }

    /**
     * 接收微信消息
     */
    public function actionMessage()
    {
        $conf = \Yii::$app->params['wx']['mp'];
        $server = (new Application(['conf' => $conf]))->driver("mp.server");
        $server->setMessageHandler(function($message) {
            file_put_contents('wx.txt', json_encode($message), FILE_APPEND);
            return "欢迎你";
        });
    }
}