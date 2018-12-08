<?php
namespace SYS_ADMIN\controllers;

use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\EquipmentBack;
use SYS_ADMIN\models\Lens;

class ApiController extends CommonApiController
{
    public function actionStreamSave()
    {
        $content = file_get_contents("php://input");

        if (empty($content)) {
            return false;
        }

        $info = json_decode($content, true);
        $testM = new EquipmentBack();
        $testM->content = $content;
        $testM->stream = $info['stream'];
        $testM->domain = $info['domain'];
        $testM->app = $info['app'];
        $testM->stream = $info['stream'];
        $testM->uri = $info['uri'];
        $testM->duration = $info['duration'];
        $testM->stop_time = date('Y-m-d H:i:s', $info['stop_time']);
        $testM->start_time = date('Y-m-d H:i:s', $info['start_time']);

        if (!$testM->save()) {
            return $this->errorInfo(400);
        }

        $lenM = Lens::findOne(['stream_name' => $info['stream'], 'status' => ConStatus::$STATUS_ENABLE]);
        if ($lenM) {
            $lenM->playback_url = "https://" . $info['domain'] . '/' . $info['uri'];
            $lenM->save();
        }

        return $this->successInfo(true);
    }
}
