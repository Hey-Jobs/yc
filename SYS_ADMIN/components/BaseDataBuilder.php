<?php
/**
 * Created by PhpStorm.
 * User: Wilbur
 * Date: 2018/11/6
 * Time: 18:01
 */

namespace SYS_ADMIN\components;


use SYS_ADMIN\models\LiveRoom;

class BaseDataBuilder
{
    private static $_instance = null;
    private static $_className = null;

    private function __construct()
    {
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function instance($modelBuild, $column = true)
    {
        $list = null;

        switch ($modelBuild) {
            case "LiveRoom" :
                $list = self::defaultPair($modelBuild, 'id', 'room_name');
                break;
            case "LiveRoomUser" :
                $list = $list = self::roomUserPair($modelBuild, 'user_id', 'id');
                break;

            case "User":
                $list = self::UserPair();
                break;

            case "UserLiveRoom":
                $list = self::liveRoomPair();
                break;

            case "ShoppingMall":
                $list = self::ShoppingMallPair();
                break;
                
            case "Product":

                $list = self::defaultPair($modelBuild, 'id', 'title');
                break;
            default :
                return false;
        }

        if (true === $column) {
            $list = array_column($list, 'text', 'id');
        }

        return $list;
    }

    private static function roomUserPair()
    {
        $list = \SYS_ADMIN\models\LiveRoom::find()
            ->select(['id', 'user_id as text'])
            ->where(['status' => ConStatus::$STATUS_ENABLE])
            ->asArray()
            ->all();
        return $list;
    }

    private static function liveRoomPair()
    {
        $room_id = CommonHelper::isAdmin() ? [] : array_keys(LiveRoom::getUserRoomId());
        $list = \SYS_ADMIN\models\LiveRoom::find()
            ->select(['id', 'room_name as text'])
            ->where(['status' => ConStatus::$STATUS_ENABLE])
            ->filterWhere(['in',  'id', $room_id ])
            ->asArray()
            ->all();
        return $list;
    }

    private static function UserPair()
    {
        $list = \SYS_ADMIN\models\User::find()
            ->select(['id', 'name as text'])
            ->where(['status' => ConStatus::$USER_ENABLE])
            ->asArray()
            ->all();
        return $list;
    }


    private static function ShoppingMallPair() {
        $room_id = CommonHelper::isAdmin() ? [] : array_keys(LiveRoom::getUserRoomId());
        $list = \SYS_ADMIN\models\ShoppingMall::find()
            ->select(['room_id', 'room_name as text'])
            ->where(['status' => ConStatus::$STATUS_ENABLE])
            ->filterWhere(['in',  'room_id', $room_id ])
            ->asArray()
            ->all();
        return $list;
    }

    private static function defaultPair($className = null, $id = 'id', $name = 'name')
    {
        $modelObj = '\SYS_ADMIN\models\\' . $className;
        $list = $modelObj::find()
            ->select([$id . ' id', $name . ' text'])
            ->where(['status' => ConStatus::$STATUS_ENABLE])
            ->asArray()
            ->all();
        return $list;
    }

    public function __call($name, $arguments)
    {
        return "this" . $name . "is not exist";
    }
}