<?php


namespace SYS_ADMIN\controllers\rest\v2;



use app\models\DeviceAuth;
use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\controllers\CommonApiController;
use yii\helpers\HtmlPurifier;

class DeviceController extends CommonApiController
{
    /**
     * mac设备推流
     */
    public function actionPush()
    {
        $auth = \Yii::$app->request->get('key');
        $mac = \Yii::$app->request->get('mac');
        $pushurl = \Yii::$app->request->get('pushurl');

        $auth = HtmlPurifier::process($auth);
        $mac = HtmlPurifier::process($mac);
        $pushurl = HtmlPurifier::process($pushurl);
        $pushurl = urldecode($pushurl);
        if (empty($auth) || empty($mac) || empty($pushurl)) {
            return $this->errorInfo(ConStatus::$ERROR_PARAMS_MSG);
        }

        $authInfo = DeviceAuth::findOne(['auth_code' => $auth, 'status' => 1]);
        if (empty($authInfo)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_DEVICE_AUTH);
        }

        $pushurl = urlencode($pushurl);
        $url = "https://www.setipc.com/golive.php?c={$mac}&play=ON&pushurl={$pushurl}";
        echo CommonHelper::curl($url);
        exit;
    }


    /**
     * mac设备推流
     */
    public function actionReset()
    {
        $auth = \Yii::$app->request->get('key');
        $mac = \Yii::$app->request->get('mac');

        $auth = HtmlPurifier::process($auth);
        $mac = HtmlPurifier::process($mac);

        if (empty($auth) || empty($mac) ) {
            return $this->errorInfo(ConStatus::$ERROR_PARAMS_MSG);
        }

        $authInfo = DeviceAuth::findOne(['auth_code' => $auth, 'status' => 1]);
        if (empty($authInfo)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_DEVICE_AUTH);
        }

        $url = "https://www.setipc.com/golive.php?c={$mac}&play=ON&pushurl=reset";
        echo CommonHelper::curl($url);
		exit;
		
    }

    // 设备控制
    public function actionControl()
    {
        $auth = \Yii::$app->request->get('key');
        $mac = \Yii::$app->request->get('mac');
        $opt = \Yii::$app->request->get('ptz');

        $auth = HtmlPurifier::process($auth);
        $mac = HtmlPurifier::process($mac);
        $opt = HtmlPurifier::process($opt);

        if (empty($auth) || empty($mac) || !array_key_exists($opt, ConStatus::$LENS_OPERATE_TYPE)){
            return $this->errorInfo(ConStatus::$ERROR_PARAMS_MSG);
        }

        $authInfo = DeviceAuth::findOne(['auth_code' => $auth, 'status' => 1]);
        if (empty($authInfo)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_DEVICE_AUTH);
        }

        echo CommonHelper::lensControl($mac, ConStatus::$LENS_OPERATE_TYPE[$opt]);
        exit;
    }

    // 查询设备状态
    public function actionState()
    {
        $auth = \Yii::$app->request->get('key');
        $mac = \Yii::$app->request->get('mac');

        $auth = HtmlPurifier::process($auth);
        $mac = HtmlPurifier::process($mac);
        if (empty($auth) || empty($mac)) {
            return $this->errorInfo(ConStatus::$ERROR_PARAMS_MSG);
        }

        $authInfo = DeviceAuth::findOne(['auth_code' => $auth, 'status' => 1]);
        if (empty($authInfo)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_DEVICE_AUTH);
        }

        $url = "https://www.setipc.com/golive.php?c={$mac}&play=HOW";
        echo CommonHelper::curl($url);
		exit;
    }

    // 查询推流地址
    public function actionAddress()
    {
        $auth = \Yii::$app->request->get('key');
        $mac = \Yii::$app->request->get('mac');

        $auth = HtmlPurifier::process($auth);
        $mac = HtmlPurifier::process($mac);
        if (empty($auth) || empty($mac)) {
            return $this->errorInfo(ConStatus::$ERROR_PARAMS_MSG);
        }

        $authInfo = DeviceAuth::findOne(['auth_code' => $auth, 'status' => 1]);
        if (empty($authInfo)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_DEVICE_AUTH);
        }

        $url = "https://www.setipc.com/golive.php?c={$mac}&play=ON";
        echo CommonHelper::curl($url);
		exit;
    }


    // 查询MAC 地址
    public function  actionMac()
    {
        $auth = \Yii::$app->request->get('key');
        $uid = \Yii::$app->request->get('uid');

        $auth = HtmlPurifier::process($auth);
        $uid = HtmlPurifier::process($uid);
        if (empty($auth) || empty($uid)) {
            return $this->errorInfo(ConStatus::$ERROR_PARAMS_MSG);
        }

        $authInfo = DeviceAuth::findOne(['auth_code' => $auth, 'status' => 1]);
        if (empty($authInfo)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_DEVICE_AUTH);
        }

        $url = "https://www.setipc.com/get_mac.php?uid={$uid}";
        echo CommonHelper::curl($url);
        exit;
    }

    /**
     * 设备重启
     */
    public function  actionRestart()
    {
        $auth = \Yii::$app->request->get('key');
        $uid = \Yii::$app->request->get('uid');
        $play = \Yii::$app->request->get('play');

        $auth = HtmlPurifier::process($auth);
        $uid = HtmlPurifier::process($uid);
        if (empty($auth) || empty($uid) || empty($play)) {
            return $this->errorInfo(ConStatus::$ERROR_PARAMS_MSG);
        }

        $authInfo = DeviceAuth::findOne(['auth_code' => $auth, 'status' => 1]);
        if (empty($authInfo)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_DEVICE_AUTH);
        }

        $url = "https://www.setipc.com/get_mac.php?uid={$uid}&play={$play}";
        echo CommonHelper::curl($url);
        exit;
    }
}