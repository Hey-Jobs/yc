<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/3
 * Time: 1:17
 */

namespace SYS_ADMIN\models;


use yii\db\ActiveRecord;

class LiveRoom extends ActiveRecord
{

    public function rules()
    {
        return [

        ];
    }

    public function attributeLabels()
    {
        return [

        ];
    }

    /**
     * 获取所属 直播间 id
     */
    public static function getRoomId()
    {
        $room_id = 0;
        $user_id = \Yii::$app->user->identity->getId();

        $room_info = self::find()
            ->where(['user_id' => $user_id])
            ->asArray()
            ->one();
        if(!empty($room_info)){
            $room_id = $room_info['id'];
        }
        $room_id = 1;
        return $room_id;
    }

    public static function getRoomInfo($room_id)
    {
        $model = self::find()
            ->alias('r')
            ->innerJoin('sys_room_extend as e', 'r.id = e.room_id')
            ->where(['<>', 'status', 0])
            ->andWhere(['r.id' => $room_id]);
    }

}