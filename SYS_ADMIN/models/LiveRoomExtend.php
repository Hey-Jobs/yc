<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/4
 * Time: 19:40
 */

namespace SYS_ADMIN\models;


use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use yii\db\ActiveRecord;

class LiveRoomExtend extends ActiveRecord
{
    public function rules()
    {
        return [

        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '自增ID',
            'room_id' => '直播间编号',
            'cover_img' => '封面图片',
            'introduce' => '简介',
            'content' => '内容介绍'
        ];
    }

    public static function getExtRoomInfo($room_id)
    {
        $room_info = self::find()
            ->alias('e')
            ->innerJoin('sys_live_room as r', 'r.id = e.room_id')
            ->leftJoin('sys_pictrue as p', 'p.id = cover_img')
            ->where(['<>', 'status', ConStatus::$STATUS_DELETED])
            ->andWhere(['r.id' => $room_id])
            ->select(['e.*', 'p.pic_name', 'p.pic_path', 'p.pic_size',])
            ->asArray()
            ->one();

        if($room_info['pic_path']){
            $room_info['pic_path'] = CommonHelper::getPicPath($room_info['pic_path']);
        }
        return $room_info;
    }
}