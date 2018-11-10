<?php
/**
 * Created by PhpStorm.
 * User: Wilbur
 * Date: 2018/11/6
 * Time: 18:01
 */

namespace SYS_ADMIN\components;


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
                $list = self::defaultPair($modelBuild, 'id', 'username');
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
        $list = \SYS_ADMIN\models\LiveRoom::find()
            ->select(['id', 'room_name as text'])
            ->where(['status' => ConStatus::$STATUS_ENABLE])
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