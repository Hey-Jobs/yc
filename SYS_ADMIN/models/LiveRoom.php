<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/3
 * Time: 1:17
 */

namespace SYS_ADMIN\models;


use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use yii\db\ActiveRecord;

class LiveRoom extends ActiveRecord
{

    public function rules()
    {
        return [
            [[ 'room_name', ], 'required'],

        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '直播间ID',
            'user_id' => '所属用户',
            'room_name' => '直播间名称',
            'logo_img' => '直播间LOGO',
            'click_num' => '点击数',
            'valid_time' => '到期时间',
            'addr_url' => '链接地址',
            'addr' => '地址',
            'coordinate' => '坐标地址',
            'sort_num' => '排序值',
            'status' => '状态',
            'mini_status' => '小程序显示',
            'online_url' => '直播地址',
            'online_cover' => '直播封面图',
        ];
    }

    /**
     * @return array|ActiveRecord[]
     * 返回 所属用户的直播间
     * 管理员 返回所有直播间
     */
    public static function getUserRoomId()
    {
        $user_id = \Yii::$app->user->id;

        $model = self::find()
            ->where(['<>', 'status', ConStatus::$STATUS_DELETED]);

        if(!CommonHelper::isAdmin()){
            $model->andWhere(['user_id' => $user_id]);
        }

        $room_info = $model->select(['id', 'user_id', 'room_name'])
            ->indexBy('id')
            ->asArray()
            ->all();

        return $room_info;
    }

    public static function getRoomNameById($room_id)
    {
        $room_info = self::find()
            ->select(['room_name'])
            ->where(['id' => $room_id])
            ->asArray()
            ->one();

        return $room_info['room_name'] ?? "";
    }
    

    public static function getRoomInfo($room_id)
    {
        $room_info = self::find()
            ->alias('r')
            ->leftJoin('sys_pictrue as l', 'l.id = r.logo_img')
            ->where(['<>', 'status', 0])
            ->andWhere(['r.id' => $room_id])
            ->select(['r.*',
                'l.pic_name as logo_name', 'l.pic_path as logo_path', 'l.pic_size as logo_size',
            ])
            ->asArray()
            ->one();

        return $room_info;
    }

}