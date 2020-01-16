<?php

namespace SYS_ADMIN\controllers;

use app\models\EquipmentCutoutTencent;
use app\models\EquipmentTencent;
use OSS\OssClient;
use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\Equipment;
use SYS_ADMIN\models\EquipmentBack;
use SYS_ADMIN\models\EquipmentCount;
use SYS_ADMIN\models\EquipmentCutout;
use SYS_ADMIN\models\Lens;
use yii\helpers\HtmlPurifier;

class ApiController extends CommonApiController
{

    private $TencentKey = "7100e82a874a06ffcbf3c9ead04cdc89";

    private $pushServerList = [
        'Tx01' => [
            'key' => 'yunchuanglive2019',
            'rtmpUri' => 'tx1rtmp.yunchuanglive.com',
            'playUri' => 'tx1.yunchuanglive.com',
            'validDate' => '2030-11-17 23:59:59'
        ],
        'Tx02' => [
            'key' => '03341632f89c15d2dfda2ff917603d5d',
            'rtmpUri' => 'krtxrtmp1.setrtmp.com',
            'playUri' => 'krtxplay1.setrtmp.com',
            'validDate' => '2030-11-17 23:59:59'
        ],
        'Tx03' => [
            'key' => 'f7dd976db75dd2d8089c6417658cfac6',
            'rtmpUri' => 'jx1rtmp.yunchuanglive.com',
            'playUri' => 'jx1.yunchuanglive.com',
            'validDate' => '2030-11-17 23:59:59'
        ],
        'BSL01' => [
            'key' => 'b64a9b29ebe276e51dddf87b38cba545',
            'rtmpUri' => 'tx1rtmp.tjbsl.com',
            'playUri' => 'tx1.tjbsl.com',
            'validDate' => '2030-11-17 23:59:59'
        ],
    ];

    private $defaultServer = 'Tx01';

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
        $info['app'] = array_key_exists($info['app'], ConStatus::$STORAGE_DOMAIN) ? $info['app'] : 'live';
        $domain = ConStatus::$STORAGE_DOMAIN[$info['app']];
        $equipM = new EquipmentBack();
        $equipM->content = $content;
        $equipM->stream = $info['stream'];
        $equipM->domain = $info['domain'];
        $equipM->app = $info['app'];
        $equipM->stream = $info['stream'];
        $equipM->uri = $domain . $info['uri'];
        $equipM->duration = $info['duration'];
        $equipM->stop_time = date('Y-m-d H:i:s', $info['stop_time']);
        $equipM->start_time = date('Y-m-d H:i:s', $info['start_time']);

        if (!$equipM->save()) {
            return $this->errorInfo(400);
        }

        if ($info['duration'] > 60) {  // 大于60秒才更新
            Lens::updateAll(['playback_url' => $domain . $info['uri']],
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
            ->andWhere(['IS', 'push_done_time', null])
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
            if (md5($this->TencentKey . $data['t']) === $data['sign'] && (time() - $data['t']) < 60) {
                $pic_url = str_replace('http', 'https', $data['pic_full_url']);

                $stream_name = $data['stream_id'];
                $app_name = 'live';

                // 图片存储阿里云对象云存储
                $content = CommonHelper::curl($pic_url);
                $url = CommonHelper::OssUpload($content, $app_name ."/". $stream_name . ".jpg");
                if (!$url) {
                    echo json_encode(['code' => ConStatus::$STATUS_ERROR_OSS_UPLOAD, 'data' => ConStatus::$ERROR_OSS_UPLOAD_MSG]);
                    exit;
                }

                $model = Lens::find()
                    ->where(['app_name' => $app_name, 'stream_name' => $stream_name])
                    ->one();
                if ($model != null) {
                    $model->online_cover_url = $url;
                    $model->save();
                }

                echo json_encode(['code' => 0]);
                exit;
            }
        }

        return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
    }


    /**
     * 腾讯推断流事件
     */
    public function actionTencentStream()
    {
        $content = file_get_contents('php://input');

        if (empty($content)) {
            return false;
        }

        $info = json_decode($content, true);
        if (isset($info['event_type']) && ($info['event_type'] === 1 || $info['event_type'] === 0)) {
            if (md5($this->TencentKey . $info['t']) === $info['sign'] && (time() - $info['t']) < 60) {
                $appname = 'live';
                $app = $info['app'];
                $stream = $info['stream_id'];
                $action = $info['event_type'] === 1 ? "publish" : "publish_done";

                $equitCutM = new EquipmentCutout();
                $equitCutM->action = $action;
                $equitCutM->app = $app;
                $equitCutM->appname = $appname;
                $equitCutM->stream = $stream;
                $equitCutM->ip = $info['user_ip'];
                $equitCutM->content = $content;
                if (!$equitCutM->save()) {
                    return $this->errorInfo(400);
                }

                // 1、 记录推流时间
                $equipModel = EquipmentTencent::findOne(['appname' => $appname, 'stream' => $stream]);
                if (empty($equipModel)) {
                    $equipModel = new EquipmentTencent();
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
                    ->andWhere(['IS', 'push_done_time', null])
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
                        [
                            'app_name' => $appname,
                            'stream_name' => $stream,
                            'status' => ConStatus::$STATUS_ENABLE
                        ]);
                }

                // 回调
                if ($equipModel->live_callback) {
                    CommonHelper::curl($equipModel->live_callback, $content, true);
                }

                echo json_encode(['code' => 0]);
                exit;

            }
        }

        return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
    }


    public function actionDevice()
    {
        $content = file_get_contents('php://input');
        file_put_contents("apiDevice.log", $content, FILE_APPEND);
    }

    /**
     * 阿里云对象云存储
     */
    public function actionOss()
    {

        $url = "https://ycycc.oss-cn-shanghai.aliyuncs.com/yc-ycc-images/live/yctbkh-sjjs002.jpg";
        $content = CommonHelper::curl($url);
        $res = CommonHelper::OssUpload($content, "1.jpg");
    }


    /**
     * 获取设备推流地址
     */
    public function actionDeviceInfo()
    {
        $uid = \Yii::$app->request->post('uid');
        $sid = \Yii::$app->request->post('sid', $this->defaultServer);
        $uid = HtmlPurifier::process($uid);
        $sid = HtmlPurifier::process($sid);

        if (empty($uid)) {
            return $this->errorInfo(ConStatus::$ERROR_PARAMS_MSG);
        }

        $data = $this->getDeviceState($uid);
        if (empty($data) || empty($data['status'])) {
            return $this->errorInfo($data);
        }

        // 获取直播地址
        $serverConfig = $this->pushServerList[$sid];
        $txTime = strtoupper(base_convert(strtotime($serverConfig['validDate']), 10, 16));
        $txSecret = md5($serverConfig['key'] . $uid . $txTime);
        $ext_str = "?" . http_build_query(array(
                "txSecret" => $txSecret,
                "txTime" => $txTime
            ));
        $rtmp_url = "rtmp://{$serverConfig['rtmpUri']}/live/" . $uid . (!empty($ext_str) ? $ext_str : "");
        // 生成推流地址
        $online_url = "https://{$serverConfig['playUri']}/live/$uid.m3u8";

        $data = [
            'status' => $data['status'],
            'status_time' => $data['status_time'],
            'uid' => $uid,
            'rtmp_url' => $rtmp_url,
            'online_url' => $online_url
        ];
        return $this->successInfo($data);
    }


    /**
     * 获取设备状态
     */
    public function actionDeviceState()
    {
        $uid = \Yii::$app->request->post('uid');
        $uid = HtmlPurifier::process($uid);

        if (empty($uid)) {
            return $this->errorInfo(ConStatus::$ERROR_PARAMS_MSG);
        }

        $data = $this->getDeviceState($uid);
        if (empty($data) || empty($data['status'])) {
            return $this->errorInfo($data);
        }

        return $this->successInfo($data);
    }

    public function actionDevicePush()
    {
        $uid = \Yii::$app->request->post('uid');
        $pushurl = \Yii::$app->request->post('pushurl');
        $uid = HtmlPurifier::process($uid);
        $pushurl = HtmlPurifier::process($pushurl);
        $pushurl = urldecode($pushurl);
        if (empty($uid) || empty($pushurl)) {
            return $this->errorInfo(ConStatus::$ERROR_PARAMS_MSG);
        }

        // 根据uid 获取 mac 地址
        $macUrl = ConStatus::$DEVICE_SETTING_GET_MAC;
        $macUrl = str_replace('{uid}', $uid, $macUrl);
        $macInfo = CommonHelper::curl($macUrl);
        $macInfo = json_decode($macInfo, true);

        if (empty($macInfo) || empty($macInfo['mac'])) {
            return $this->errorInfo(ConStatus::$ERROR_DEVICE_UID_MSG);
        }

        $pushurl = urlencode($pushurl);
        $url = ConStatus::$DEVICE_SETTING_PUSH_URL;
        $url = str_replace('{mac}', $macInfo['mac'], $url);
        $url = str_replace('{pushurl}', $pushurl, $url);
        echo CommonHelper::curl($url);
        exit;
    }

    private function getDeviceState($uid)
    {
        // 根据uid 获取 mac 地址
        $macUrl = ConStatus::$DEVICE_SETTING_GET_MAC;
        $macUrl = str_replace('{uid}', $uid, $macUrl);
        $macInfo = CommonHelper::curl($macUrl);
        $macInfo = json_decode($macInfo, true);

        if (empty($macInfo) || empty($macInfo['mac'])) {
            return ConStatus::$ERROR_DEVICE_UID_MSG;
        }

        $status_time = null;
        $status_time2 = null;
        // 获取设备状态
        $stateUrl1 = ConStatus::$DEVICE_SETTING_STATE1;
        $stateUrl1 = str_replace('{mac}', $macInfo['mac'], $stateUrl1);
        $device_state_info = CommonHelper::curl($stateUrl1);
        $device_state_info = json_decode($device_state_info, true);

        $stateUrl2 = ConStatus::$DEVICE_SETTING_STATE2;
        $stateUrl2 = str_replace('{mac}', $macInfo['mac'], $stateUrl2);
        $device_state_info2 = CommonHelper::curl($stateUrl2);
        $device_state_info2 = json_decode($device_state_info2, true);

        if (isset($device_state_info['status_time']) && !empty($device_state_info['status_time'])) {
            $status_time = explode('/', $device_state_info['status_time']);
            $status_time[0] += 2000;
            $status_time = implode('/', $status_time);
        }


        if (isset($device_state_info2['status_time']) && !empty($device_state_info2['status_time'])) {
            $status_time2 = explode('/', $device_state_info2['status_time']);
            $status_time2[0] += 2000;
            $status_time2 = implode('/', $status_time2);
        }
        
        if((empty($status_time) || strtotime($status_time) <strtotime($status_time2)
            || empty($device_state_info)) && (!empty($status_time2) && !empty($device_state_info2))) {
            $status_time = $status_time2;
            $device_state_info = $device_state_info2;
        }

        if ($device_state_info['status'] === 'disconnect') {
            return ConStatus::$ERROR_DEVICE_UID_MSG;
        }


        $status = "";
        if (strpos($device_state_info['status'], "Publishing") !== false) {
            $status = "正在直播";
            if (strtotime($status_time) < time() - 600) {
                $status = "直播超时";
            }
        } else if (strpos($device_state_info['status'], "Will Restart") !== false) {
            $status = "重新启动";
        } else if (strpos($device_state_info['status'], "Initialize IPCAM") !== false) {
            $status = "初始化";
        } else if (strpos($device_state_info['status'], "Idle") !== false) {
            $status = "空闲";
        } else if (strpos($device_state_info['status'], "Connecting") !== false) {
            $status = "连接中";
        } else if (strpos($device_state_info['status'], "Waiting IPCAM Response") !== false) {
            $status = "等待响应";
        }

        return ['status' => $status, 'status_time' => $status_time];
    }

    public function actionTest()
    {
        $password = \Yii::$app->request->get("password");
        $password = 'ncdby2019';
        echo \Yii::$app->getSecurity()->generatePasswordHash($password);
        exit;
    }
}
