<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/3
 * Time: 0:57
 */

namespace SYS_ADMIN\models;


use yii\db\ActiveRecord;

class Lens extends ActiveRecord
{

    public function rules()
    {
        return [
            [['lens_name', 'cover_img',], 'required'],
            [['sort_num', 'status'], 'integer'],
        ];
    }


    public function attributeLabels()
    {
       return [
           'id' => '编号',
           'room_id' => '所属直播间',
           'lens_name' => '镜头名称',
           'cover_img' => '封面图片',
           'online_url' => '直播流地址',
           'playback_url' => '镜头回放地址',
           'bgm_url' => '背景音乐地址',
           'marvellous_url' => '精彩回放地址',
           'sort_num' => '排序值',
           'status' => '状态',
       ];
    }

    /**
     * 获取镜头列表
     */
    public static function  getLensList()
    {
        $room_id = LiveRoom::getRoomId();
        $model = self::find()
            ->alias('l')
            ->innerJoin('sys_live_room r', 'r.id = l.room_id')
            ->where(['<>', 'l.status', 0]);

        if($room_id  > 0){
            $model->andWhere(['l.room_id' => $room_id]);
        }

        $lens_list = $model->select(['r.room_name', 'l.*'])->orderBy('l.id desc')->asArray()->all();
        if(count($lens_list)){
            foreach ($lens_list as &$len){
                $len['created_at'] = date('Y-m-d H:i');
                $len['status'] = \Yii::$app->params['status'][$len['status']];
            }
        }

        return $lens_list;
    }


}