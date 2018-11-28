<?php
/**
 * User: liwj
 * Date:2018/11/8
 * Time:16:17
 */

namespace SYS_ADMIN\components;


use SYS_ADMIN\models\LiveRoom;

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
        if(!CommonHelper::isAdmin() && !array_key_exists($room_id, $user_room)){
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
        if(!is_file($dir)){
            mkdir($dir, "0777");
        }
        $file = "log/".date('Ymd')."/".$filename;
        $content = "";
        if(!is_string($data)){
            $content = json_encode($data);
        } else {
            $content = $data;
        }
        file_put_contents($file, $content."\r\n", FILE_APPEND);
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

}