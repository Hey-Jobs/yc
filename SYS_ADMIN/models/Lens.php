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
           'id' => '���',
           'room_id' => '����ֱ����',
           'lens_name' => '��ͷ����',
           'cover_img' => '����ͼƬ',
           'online_url' => 'ֱ������ַ',
           'playback_url' => '��ͷ�طŵ�ַ',
           'bgm_url' => '�������ֵ�ַ',
           'marvellous_url' => '���ʻطŵ�ַ',
           'sort_num' => '����ֵ',
           'status' => '״̬',
       ];
    }

    /**
     * ��ȡ��ͷ�б�
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