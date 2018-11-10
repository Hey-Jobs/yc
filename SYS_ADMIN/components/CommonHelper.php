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
        if(!CommonHelper::isAdmin() && !array_keys($room_id, $user_room)){
            $flag = false;
        }

        return $flag;
    }


}