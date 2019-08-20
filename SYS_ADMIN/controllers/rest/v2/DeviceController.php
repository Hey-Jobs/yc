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
        $url = "http://www.setrtmp.com/golive.php?c={$mac}&pushurl={$pushurl}";
        CommonHelper::curl($url);
        return $this->successInfo("success");
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

        $url = "http://www.setrtmp.com/golive.php?c={$mac}&pushurl=reset";
        CommonHelper::curl($url);
        return $this->successInfo("success");
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

        CommonHelper::lensControl($mac, ConStatus::$LENS_OPERATE_TYPE[$opt]);
        return $this->successInfo("success");
    }
}