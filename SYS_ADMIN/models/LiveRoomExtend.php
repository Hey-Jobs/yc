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

}