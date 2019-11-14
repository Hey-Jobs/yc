<?php


namespace SYS_ADMIN\controllers;


use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use yii\helpers\HtmlPurifier;

class DeviceController extends CommonController
{
    private $appName = "live";

    private $accessKeyId;

    private $accessKeySecret;


    public function init()
    {
        parent::init();
        $this->accessKeyId = getenv('ALIYUN_LIVE_STREAM_ACCESSKEYID');
        $this->accessKeySecret = getenv('ALIYUN_LIVE_STREAM_ACCESSKEYSECRET');
    }

    /**
     * 获取设备视频
     */
    public function actionVideo()
    {
        $title = "云窗直播";
        $deviceId = \Yii::$app->request->get('id');
        $deviceId = HtmlPurifier::process($deviceId);
        if (empty($deviceId)) {
            return $this->errorInfo(ConStatus::$ERROR_PARAMS_MSG);
        }

        // 开始拉流
        CommonHelper::operateDeviceStream(ConStatus::$DEVICE_STREAM_START,
            $deviceId,
            $this->accessKeyId,
            $this->accessKeySecret);

        // 获取流信息
        $data = CommonHelper::operateDeviceStream(ConStatus::$DEVICE_STREAM_INFO,
            $deviceId,
            $this->accessKeyId,
            $this->accessKeySecret);
        $streamName = $data && $data['Name'] ? $data['Name'] : '';
        // 获取播放地址
        $time = time() + getenv("ALIYUN_LIVE_STREAM_AUTH_TIME");
        $auth_key = getenv("ALIYUN_LIVE_STREAM_AUTH_KEY");
        $domain = getenv("ALIYUN_LIVE_STREAM_URL");
        $strviewm3u8 = "/$this->appName/$streamName.m3u8-$time-0-0-$auth_key";
        $videoUrl = "$domain/$this->appName/$streamName.m3u8?auth_key=$time-0-0-".md5($strviewm3u8);
        return $this->renderPartial('video', [
            'uri' => $videoUrl,
            'title' => $title,
            'deviceId' => $deviceId,
        ]);
    }

    /**
     * 停止拉流
     */
    public function actionPull()
    {
        $deviceId = \Yii::$app->request->get('id');
        $deviceId = HtmlPurifier::process($deviceId);
        if (empty($deviceId)) {
            return $this->errorInfo(ConStatus::$ERROR_PARAMS_MSG);
        }

        $data = CommonHelper::operateDeviceStream(ConStatus::$DEVICE_STREAM_START,
            $deviceId,
            $this->accessKeyId,
            $this->accessKeySecret);
        if ($data && !empty($data['RequestId'])) {
            return $this->successInfo();
        } else {
            return $this->errorInfo(ConStatus::$ERROR_PARAMS_MSG);
        }
    }

    /**
     * 停止拉流
     */
    public function actionSuspend()
    {
        $deviceId = \Yii::$app->request->get('id');
        $deviceId = HtmlPurifier::process($deviceId);
        if (empty($deviceId)) {
            return $this->errorInfo(ConStatus::$ERROR_PARAMS_MSG);
        }

        $data = CommonHelper::operateDeviceStream(ConStatus::$DEVICE_STREAM_STOP,
            $deviceId,
            $this->accessKeyId,
            $this->accessKeySecret);
        if ($data && !empty($data['RequestId'])) {
            return $this->successInfo();
        } else {
            return $this->errorInfo(ConStatus::$ERROR_PARAMS_MSG);
        }
    }


    /**
     * 推流地址设置
     */
    public function actionSetting()
    {
        $sid = \Yii::$app->request->get('sid');
        $sid = HtmlPurifier::process($sid);
        return $this->renderPartial("setting", ['sid' => $sid]);
    }
}