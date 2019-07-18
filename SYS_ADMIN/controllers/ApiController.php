<?php

namespace SYS_ADMIN\controllers;

use app\models\EquipmentBackTencent;
use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\Equipment;
use SYS_ADMIN\models\EquipmentBack;
use SYS_ADMIN\models\EquipmentCount;
use SYS_ADMIN\models\EquipmentCutout;
use SYS_ADMIN\models\Lens;
use yii\web\Request;

class ApiController extends CommonApiController
{

    private $TencentKey = "7100e82a874a06ffcbf3c9ead04cdc89";
    /**
     * 回放回写.
     */
    public function actionStreamSave()
    {
        $content = file_get_contents('php://input');

        if (empty($content)) {
            return false;
        }

        $info = json_decode($content, true);
        $info['app'] = array_key_exists($info['app'], ConStatus::$STORAGE_DOMAIN) ? $info['app']: 'live';
        $domain = ConStatus::$STORAGE_DOMAIN[$info['app']];
        $equipM = new EquipmentBack();
        $equipM->content = $content;
        $equipM->stream = $info['stream'];
        $equipM->domain = $info['domain'];
        $equipM->app = $info['app'];
        $equipM->stream = $info['stream'];
        $equipM->uri = $domain.$info['uri'];
        $equipM->duration = $info['duration'];
        $equipM->stop_time = date('Y-m-d H:i:s', $info['stop_time']);
        $equipM->start_time = date('Y-m-d H:i:s', $info['start_time']);

        if (!$equipM->save()) {
            return $this->errorInfo(400);
        }

        if ($info['duration'] > 60) {  // 大于60秒才更新
            Lens::updateAll(['playback_url' => $domain.$info['uri']],
                ['stream_name' => $info['stream'], 'status' => ConStatus::$STATUS_ENABLE]);
        }

        // post 回调
        $equipModel = Equipment::findOne(['appname' => $info['app'], 'stream' => $info['stream']]);
        if ($equipModel->replay_callback) {
            CommonHelper::curl($equipModel->replay_callback, $content, true);
        }
        return $this->successInfo(true);
    }

    /**
     * 推断流回写
     * 1、记录设备列表
     * 2、统计设备在线时长
     * 3、更新设备状态
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
        if (empty($appname) || empty($stream)) {
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

        // 1、 记录推流时间
        $equipModel = Equipment::findOne(['appname' => $appname, 'stream' => $stream]);
        if (empty($equipModel)) {
            $equipModel = new Equipment();
            $equipModel->app = $app;
            $equipModel->appname = $appname;
            $equipModel->stream = $stream;
        }

        $equipModel->push_time = date('Y-m-d H:i:s');
        $equipModel->push_type = ConStatus::$STREAM_STATUS[$action];
        $equipModel->save();

        // 2、 在线时间统计
        $equipCountModel = EquipmentCount::find()
            ->where(['appname' => $appname, 'stream' => $stream])
            ->andWhere(['IS','push_done_time',null])
            ->orderBy('id desc')
            ->one();

        if (empty($equipCountModel)) {
            $equipCountModel = new EquipmentCount();
            $equipCountModel->appname = $appname;
            $equipCountModel->stream = $stream;
            $equipCountModel->push_time = date('Y-m-d H:i:s');
        } else {
            $equipCountModel->push_done_time = date('Y-m-d H:i:s');
            $equipCountModel->online_time = time() - strtotime($equipCountModel->push_time);
        }

        $equipCountModel->save();

        // 记录设备状态
        if (isset($stream) && !empty($stream) && array_key_exists($action, ConStatus::$STREAM_STATUS)) {
            Lens::updateAll(['stream_status' => ConStatus::$STREAM_STATUS[$action]],
                ['stream_name' => $stream, 'status' => ConStatus::$STATUS_ENABLE]);
        }

        // 回调
        if ($equipModel->live_callback) {
            CommonHelper::curl($equipModel->live_callback, $content, true);
        }
        return $this->successInfo(true);
    }


    /**
     * 腾讯截图回调
     */
    public function actionTencentScreenshot()
    {
        $content = file_get_contents('php://input');
        file_put_contents("aa.log", $content, FILE_APPEND);
        if (empty($content)) {
            return false;
        }

        $data = json_decode($content, true);

        if (isset($data['event_type']) && $data['event_type'] == 200) {
            if (md5($this->TencentKey.$data['t']) === $data['sign'] && (time() - $data['t'] ) < 60) {
                $pic_url = $data['pic_full_url'];
                $stream_name = $data['stream_id'];
                $app_name = 'live';

                $model = Lens::find()
                    ->where(['app_name' => $app_name, 'stream_name' => $stream_name])
                    ->one();
                if ($model != null) {
                    $model->online_cover_url = $pic_url;
                    $model->save();
                }

                echo json_encode(['code' => 0]);
                exit;
            }
        }

        return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
    }


    /**
     * 腾讯回放回写
     */
    public function actionTencentStream()
    {
        $content = file_get_contents('php://input');

        if (empty($content)) {
            return false;
        }

        $info = json_decode($content, true);
        if (isset($info['event_type']) && $info['event_type'] == 100) {
            if (md5($this->TencentKey.$info['t']) === $info['sign'] && (time() - $info['t'] ) < 60) {
                $equipM = new EquipmentBackTencent();
                $equipM->content = $content;
                $info['app'] = 'live';

                $equipM->stream = $info['stream_id'];
                $equipM->app = $info['app'];
                $equipM->uri = $info['video_url'];
                $equipM->duration = $info['duration'];
                $equipM->stop_time = date('Y-m-d H:i:s', $info['end_time']);
                $equipM->start_time = date('Y-m-d H:i:s', $info['start_time']);

                $res = $equipM->save();
                if ($info['duration'] > 60) {  // 大于60秒才更新
                    Lens::updateAll(['playback_url' => $info['video_url']],
                        [
                            'app_name' => $info['app'],
                            'stream_name' => $info['stream_id'],
                            'status' => ConStatus::$STATUS_ENABLE
                        ]);
                }
                echo json_encode(['code' => 0]);
                exit;

            }
        }

        return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
    }
}
