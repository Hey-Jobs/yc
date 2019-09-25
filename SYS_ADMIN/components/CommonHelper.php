<?php
/**
 * User: liwj
 * Date:2018/11/8
 * Time:16:17
 */

namespace SYS_ADMIN\components;


use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;
use OSS\OssClient;
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
        return "/" . $path;
    }


    /**
     * @return bool
     * 检测是否为管理员
     */
    public static function isAdmin()
    {
        $user_role = \Yii::$app->authManager->getRolesByUser(\Yii::$app->user->id);
        $role = key($user_role);
        if ($role == 'admin') {
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
        return "https://yc.adaxiang.com";
        return $url;
    }

    /**
     * 获取当前url
     */
    public static function getUrl()
    {
        return CommonHelper::getDomain() . $_SERVER['REQUEST_URI'];
    }

    public static function writeLog($data, $filename = 'log.log')
    {
        $dir = "log/" . date('Ymd') . "/";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $file = "log/" . date('Ymd') . "/" . $filename;
        $content = "";
        if (!is_string($data)) {
            $content = json_encode($data);
        } else {
            $content = $data;
        }

        file_put_contents($file, date('Y-m-d H:i:s') . $content . "\r\n", FILE_APPEND);
        return true;
    }


    public static function writeOrderLog($data)
    {
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
        if (!is_numeric($num)) {
            return $num;
        }


        switch ($type) {
            case 1:
                if ($num >= 10000) {
                    $data = round($num / 10000, 2) . '万';
                } else {
                    if ($num >= 1000) {
                        $data = round($num / 1000, 2) . '千';
                    } else {
                        $data = $num;
                    }
                }

                break;

            case 2:
                $minute = floor($num / 60);
                $second = $num % 60;
                $data = sprintf("%02d", $minute) . ':' . sprintf('%02d', $second);
                break;

            case 3:
                $day = floor($num / (3600 * 24));
                $hour = floor(($num % (3600 * 24)) / 3600);
                $minute = ceil((($num % (3600 * 24)) % 3600) / 60);
                if ($day > 0) {
                    return $day . "天" . $hour . '时' . $minute . '分';
                } else {
                    if ($hour) {
                        return $hour . '时' . $minute . '分';
                    } else {
                        return $minute . '分';
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
        return CommonHelper::getDomain() . CommonHelper::getPicPath($pic);
    }

    /**
     * @param $mobile
     * 检测是否是手机号码
     */
    public static function checkMobile($mobile)
    {
        if (preg_match('/^1[3456789]\d{9}$/', $mobile)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $mobile 手机号码
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
            'accessKeyId' => $smsConf['accessKeyId'],
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

    /**
     * 获取阿里请求sign
     * @param $param
     * @param $secret
     */
    public static function getAliSign($params, $secret)
    {

        ksort($params);  // 排序

        $arr = [];
        foreach ($params as $k => $v) {
            $arr[] = CommonHelper::percentEncode($k) . '=' . CommonHelper::percentEncode($v);
        }

        $queryStr = implode('&', $arr);
        $strToSign = 'POST&%2F&' . CommonHelper::percentEncode($queryStr);
        return base64_encode(hash_hmac('sha1', $strToSign, $secret . '&', true));
    }

    /**
     * 签名拼接转码
     * @param  string $str 转码前字符串
     * @return string
     */
    public static function percentEncode($str)
    {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);

        return $res;
    }

    /**
     * 返回时间格式
     * @return string
     */
    public static function getTimestamp()
    {
        $timezone = date_default_timezone_get();
        date_default_timezone_set('GMT');
        $timestamp = date('Y-m-d\TH:i:s\Z');
        date_default_timezone_set($timezone);

        return $timestamp;
    }

    /**
     * curl请求
     * @param  string $url string
     * @param  array|null $postFields 请求参数
     * @return [type]             [description]
     */
    public static function curl($url, $postFields = null, $json = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //https 请求
        if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == "https") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        if ($json) {

            if (is_array($postFields)) {
                $postFields = json_encode($postFields);
            }

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            $header = array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($postFields)
            );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        } else if (is_array($postFields) && 0 < count($postFields)) {
            $postBodyString = "";
            foreach ($postFields as $k => $v) {
                $postBodyString .= "$k=" . urlencode($v) . "&";
            }
            unset($k, $v);
            curl_setopt($ch, CURLOPT_POST, true);
            $header = array("content-type: application/x-www-form-urlencoded; charset=UTF-8");

            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
        }
        $reponse = curl_exec($ch);
        return $reponse;
    }

    /**
     * @param $type publish_done 禁止推流  publish 恢复推流
     * @param $app
     * @param $stream
     * @param $domain
     */
    public static function httpGetLive($type, $app, $stream, $domain)
    {
        $logStr = 'params: domain:' . $domain . ' appname:' . $app . 'stream:' . $stream . 'action:' . $type . "\r\n";
        CommonHelper::writeLog($logStr, 'push.log');

        $liveConfig = \Yii::$app->params['live'];
        $liveType = $type === 'publish_done' ? $liveConfig['forbid'] : $liveConfig['resume'];
        $params = [
            'Action' => $liveType,
            'AppName' => $app,
            'DomainName' => $domain,
            'StreamName' => $stream,
            'LiveStreamType' => 'publisher',
            'Version' => $liveConfig['version'],
            'AccessKeyId' => $liveConfig['accessKeyId'],
            'SignatureMethod' => $liveConfig['signatureMethod'],
            'Timestamp' => CommonHelper::getTimestamp(),
            'SignatureVersion' => $liveConfig['signatureVersion'],
            'SignatureNonce' => uniqid(),
            'ResourceOwnerAccount' => $liveConfig['account'],
            'Format' => $liveConfig['format'],
        ];

        $sign = CommonHelper::getAliSign($params, $liveConfig['accessKeySecret']);
        $params['Signature'] = $sign;
        $res = CommonHelper::curl($liveConfig['url'], $params);
        CommonHelper::writeLog('rest:' . $res, 'push.log');
        return json_decode($res, true);
    }

    /**
     * 获取近24小时天气预报信息
     * @param $address
     */
    public static function getWeatherInfo($address) {
        if (empty($address)) {
            return null;
        }
        //date_default_timezone_set("PRC");
        $host = "https://weather01.market.alicloudapi.com/area-to-weather";  //通过地区名字查询天气
        $method = "GET";
        $appcode = getenv('WEATHER_CONFIG_APP_CODE');
        $headers = array();
        array_push($headers, "Authorization: APPCODE " . $appcode);

        $querys="area=".$address;//获取html页面传递过来的地区名字
        $bodys = "";
        $url = $host . "?" . $querys;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);

        $response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        $data = json_decode($body, true);
        if (empty($data) || empty($data['showapi_res_body']) || empty($data['showapi_res_body']['now'])) {
            return null;
        }

        $currentWeather = $data['showapi_res_body']['now'];
        $weatherInfo = [];
        $weatherInfo['weather'] = $currentWeather['weather'];
        $weatherInfo['humidity'] = $currentWeather['sd'];
        $weatherInfo['temperature'] = $currentWeather['temperature'];
        $weatherInfo['pm2_5'] = $currentWeather['aqiDetail']['pm2_5'];
        $weatherInfo['quality'] = $currentWeather['aqiDetail']['quality'];
        $weatherInfo['weather_pic'] = $currentWeather['weather_pic'];
        $weatherInfo['temperature_time'] = date('Y-m-d').' '.$currentWeather['temperature_time'];
        return $weatherInfo;
    }

    /**
     * 操控设备
     * @param $macAddress  设备mac
     * @param $operate 操作方向
     */
    public static function lensControl($macAddress, $operate){
        $url = "http://www.setrtmp.com/ptz.php?mac={$macAddress}&op={$operate}";
        CommonHelper::curl($url);
    }


    public static function OssUpload($content, $filename, $dir = "images/") {

        $accessKeyId = getenv('ALIYUN_OSS_ACCESSKEYID');
        $accessKeySecret = getenv('ALIYUN_OSS_ACCESSKEYSECRET');
        $bucket = getenv('ALIYUN_OSS_BUCKET');
        $endpoint = getenv('ALIYUN_OSS_ENDPOINT');
        $client = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        $res = $client->putObject($bucket, $dir.$filename, $content);
        if ($res['x-oss-request-id']) {
            return "https://ycycc.oss-cn-shanghai.aliyuncs.com/".$dir.$filename;
        } else {
            CommonHelper::writeLog($res, "ossuploadError");
            return false;
        }
    }


    //操作设备视频
    public static function operateDeviceStream($action, $deviceId, $accessKeyId, $accessKeySecret)
    {

        $params = [
            'Action' => $action,
            'Id' => $deviceId,
            'Version' => getenv('ALIYUN_LIVE_STREAM_VERSION'),
            'AccessKeyId' => $accessKeyId,
            'SignatureMethod' => getenv('ALIYUN_LIVE_STREAM_SINGNATUREMETHOD'),
            'Timestamp' => CommonHelper::getTimestamp(),
            'SignatureVersion' => getenv('ALIYUN_LIVE_STREAM_SINGNATUREVERSION'),
            'SignatureNonce' => uniqid(),
            'Format' => 'JSON'
        ];
        $sign = CommonHelper::getAliSign($params, $accessKeySecret);
        $params['Signature'] = $sign;

        $dataString = "";
        foreach ($params as $k => $v) {
            $dataString .= "$k=" . urlencode($v) . "&";
        }
        $res = CommonHelper::curl('https://vs.cn-shanghai.aliyuncs.com', $params);
        CommonHelper::writeLog('rest:' . $res, 'videoStream.log');
        return json_decode($res, true);
    }
}