<?php
namespace SYS_ADMIN\controllers;

use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\EquipmentBack;
use SYS_ADMIN\models\EquipmentCutout;
use SYS_ADMIN\models\Lens;

class ApiController extends CommonApiController
{
    /**
     * 回放回写
     */
    public function actionStreamSave()
    {
        $content = file_get_contents("php://input");

        if (empty($content)) {
            return false;
        }

        $info = json_decode($content, true);
        $equipM = new EquipmentBack();
        $equipM->content = $content;
        $equipM->stream = $info['stream'];
        $equipM->domain = $info['domain'];
        $equipM->app = $info['app'];
        $equipM->stream = $info['stream'];
        $equipM->uri = "https://ycycc.yunchuanglive.com/" . $info['uri'];
        $equipM->duration = $info['duration'];
        $equipM->stop_time = date('Y-m-d H:i:s', $info['stop_time']);
        $equipM->start_time = date('Y-m-d H:i:s', $info['start_time']);

        if (!$equipM->save()) {
            return $this->errorInfo(400);
        }

        Lens::updateAll(['playback_url' => "https://ycycc.yunchuanglive.com/" . $info['uri']], ['stream_name' => $info['stream'], 'status' => ConStatus::$STATUS_ENABLE]);

        return $this->successInfo(true);
    }

    /**
     * 推断流回写
     */
    public function actionCutoutBack()
    {
        $action = \Yii::$app->request->get('action');
        $app = \Yii::$app->request->get('app');
        $appname = \Yii::$app->request->get('appname');
        $stream = \Yii::$app->request->get('id');
        $ip = \Yii::$app->request->get(' ip');
        $node = \Yii::$app->request->get('node');
        $content = json_encode(\Yii::$app->request->get());
        if(empty($appname) || empty($stream)){
            return $this->errorInfo(400);
        }

        $equitCutM = new EquipmentCutout();
        $equitCutM->action = $action;
        $equitCutM->app = $app;
        $equitCutM->appname = $appname;
        $equitCutM->stream = $stream;
        $equitCutM->ip = $ip;
        $equitCutM->node = $node;
        $equitCutM->content = $content;
        if (!$equitCutM->save()) {
            return $this->errorInfo(400);
        }

        // 记录设备状态
        if(isset($stream) && !empty($stream) && array_key_exists($action, ConStatus::$STREAM_STATUS)){
            Lens::updateAll(['stream_status' => ConStatus::$STREAM_STATUS[$action]], ['stream_name' => $stream, 'status' => ConStatus::$STATUS_ENABLE]);
        }
        return $this->successInfo(true);
    }
}
