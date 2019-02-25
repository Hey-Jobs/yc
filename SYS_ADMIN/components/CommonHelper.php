<?php
/**
 * User: liwj
 * Date:2018/11/8
 * Time:16:17
 */

namespace SYS_ADMIN\components;


use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;
use SYS_ADMIN\models\LiveRoom;
use SYS_ADMIN\models\SmsLog;

class CommonHelper
{
    /**
     * @param $path
     * @return string
     *  获取图片路径
     */
    public static function getPicPath($path)
    {
        return "/".$path;
    }


    /**
     * @return bool
     * 检测是否为管理员
     */
    public static function isAdmin()
    {
        $user_role = \Yii::$app->authManager->getRolesByUser(\Yii::$app->user->id);
        $role = key($user_role);
        if($role == 'admin'){
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $room_id
     * 检测是否 有权操作该直播间
     */
    public static function checkRoomId($room_id)
    {
        $flag = true;
        $user_room = LiveRoom::getUserRoomId();
        if (!CommonHelper::isAdmin() && !array_key_exists($room_id, $user_room)) {
            $flag = false;
        }

        return $flag;
    }

    /**
     * 获取当前域名
     */
    public static function getDomain()
    {
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $url = $http_type . $_SERVER['HTTP_HOST'];
        return $url;
    }

    /**
     * 获取当前url
     */
    public static function getUrl()
    {
        return CommonHelper::getDomain().$_SERVER['REQUEST_URI'];
    }

    public static function writeLog($data, $filename = 'log.log'){
        $dir = "log/".date('Ymd')."/";
        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }
        $file = "log/".date('Ymd')."/".$filename;
        $content = "";
        if(!is_string($data)){
            $content = json_encode($data);
        } else {
            $content = $data;
        }

        file_put_contents($file, date('Y-m-d H:i:s').$content."\r\n", FILE_APPEND);
        return true;
    }


    public static function writeOrderLog($data){
        return self::writeLog($data, "order.log");
    }

    /**
     * Groups an array by a given key. Any additional keys will be used for grouping
     * the next set of sub-arrays.
     * @by WilburXu
     * @param array $arr The array to have grouping performed on.
     * @param mixed $key The key to group or split by.
     *
     * @return array
     */
    public static function array_group_by($arr, $key)
    {
        if (!is_array($arr)) {
            trigger_error('array_group_by(): The first argument should be an array', E_USER_ERROR);
        }
        if (!is_string($key) && !is_int($key) && !is_float($key)) {
            trigger_error('array_group_by(): The key should be a string or an integer', E_USER_ERROR);
        }
        // Load the new array, splitting by the target key
        $grouped = array();
        foreach ($arr as $value) {
            $grouped[$value[$key]][] = $value;
        }
        // Recursively build a nested grouping if more parameters are supplied
        // Each grouped array value is grouped according to the next sequential key
        if (func_num_args() > 2) {
            $args = func_get_args();
            foreach ($grouped as $key => $value) {
                $parms = array_merge(array($value), array_slice($args, 2, func_num_args()));
                $grouped[$key] = call_user_func_array('array_group_by', $parms);
            }
        }
        return $grouped;
    }

    /**
     * @param integer $num 数值
     * @param integer $type 格式化类型 1 点击数格式化 2整数转成时间格式 3 天 时 分 秒
     * 数字格式化
     */
    public static function numberFormat($num, $type = 1)
    {
        $data = "";

        switch ($type) {
            case 1:
                if ($num >= 10000) {
                    $data = round($num / 10000, 2).'万';
                } else if ($num >= 1000) {
                    $data = round($num / 1000, 2).'千';
                } else {
                    $data = $num;
                }

                break;

            case 2:
                $minute = floor($num / 60);
                $second = $num % 60;
                $data = sprintf("%02d", $minute).':'.sprintf('%02d', $second);
                break;

            case 3:
                $day = floor($num / (3600*24));
                $hour = floor(($num % (3600*24)) / 3600);
                $minute = ceil((($num % (3600*24)) % 3600) / 60);
                if ($day > 0) {
                    return $day."天".$hour.'时'.$minute.'分';
                } else {
                    if ($hour) {
                        return $hour.'时'.$minute.'分';
                    } else {
                        return $minute.'分';
                    }
                }

                break;
        }

        return $data;
    }

    /**
     * 获取直播间默认logo
     */
    public static function getDefaultLogo()
    {
        return CommonHelper::getImgPath('images/default.png');
    }

    /**
     * @param $pic
     * @return string
     *
     */
    public static function getImgPath($pic)
    {
        return CommonHelper::getDomain().CommonHelper::getPicPath($pic);
    }

    /**
     * @param $mobile
     * 检测是否是手机号码
     */
    public static function checkMobile($mobile)
    {
        if (preg_match('/^1[34578]\d{9}$/', $mobile)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $mobile  手机号码
     * @param string $template 短信模板名称
     * @param array $params 短信参数
     * @param string $sign 短信签名名称
     * 发送短信
     *
     */
    public static function sendSms($mobile, $template, $params = [], $sign = 'yclive')
    {
        $smsConf = \Yii::$app->params['sms'];
        $smsTemplate = $smsConf['template'][$template];
        $smsTemplate['param'] = $params;
        $config = [
            'accessKeyId'    => $smsConf['accessKeyId'],
            'accessKeySecret' => $smsConf['accessKeySecret'],
        ];

        $sendClient = new Client($config);
        $sendSms = new SendSms();

        $sendSms->setPhoneNumbers($mobile);
        $sendSms->setSignName($smsConf['signName'][$sign]);
        $sendSms->setTemplateCode($smsTemplate['code']);
        $sendSms->setTemplateParam($smsTemplate['param']);
        $res = $sendClient->execute($sendSms);

        return [
            'bizId' => $res->BizId ?? "",
            'code' => $res->Code,
            'message' => $res->Message,
            'requestId' => $res->RequestId,
        ];
    }


    public static function smsLog($mobile, $message, $bizId, $content = [], $clientId = null)
    {
        $model = new SmsLog();
        $model->mobile = $mobile;
        $model->biz_id = $bizId;
        $model->message = $message;
        $model->content = json_encode($content);
        $model->client_id = $clientId;
        $model->save();
    }

}