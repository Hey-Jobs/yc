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
        $info = file_get_contents("php://input");
        if (empty($info)) {
            return false;
        }

        $data = json_decode($info, true);
        $equitCutM = new EquipmentCutout();
        $equitCutM->action = $data['action'] ?? '';
        $equitCutM->app = $data['app'] ?? '';
        $equitCutM->appname = $data['appname'] ?? '';
        $equitCutM->stream = $data['id'] ?? '';
        $equitCutM->ip = $data['ip'] ?? '';
        $equitCutM->node = $data['node'] ?? '';
        $equitCutM->content = $info;
        if (!$equitCutM->save()) {
            return $this->errorInfo(400);
        }

        // 记录设备状态
        if(isset($data['id']) && !empty($data['id']) && array_key_exists($data['action'], ConStatus::$STREAM_STATUS)){
            Lens::updateAll(['stream_status' => ConStatus::$STREAM_STATUS[$data['action']]], ['stream_name' => $data['id'], 'status' => ConStatus::$STATUS_ENABLE]);
        }
        return $this->successInfo(true);
    }
}
