<?php
/**
 * User: liwj
 * Date:2018/11/1
 * Time:20:07
 */

namespace SYS_ADMIN\models;


use yii\data\Pagination;
use yii\db\ActiveRecord;

class Video extends ActiveRecord
{

    public function rules()
    {
        return [
            [['video_name', 'video_url', 'sort_num'], 'required' ],
            ['sort_num', 'integer']
        ];
    }


    public function attributeLabels()
    {
        return [
            'video_name' => '视频名称',
            'video_url' => '视频链接',
            'sort_num' => '排序值',
            'status' => '状态',
        ];
    }

    public function getVideoList($data = [])
    {
        $where = ['<>', 'v.status', 0];
        $model = self::find()
            ->alias('v')
            ->leftJoin('sys_live_room as r', 'r.id = v.room_id')
            ->where($where);

        if (isset($data['room_id']) && $data['room_id'] > 0) { // 普通管理员，只能查看自己的视频
            $model->andWhere(['v.room_id' => $data['room_id']]);
        }

        $video_list = $model->select(['r.room_name', 'v.*'])->orderBy("v.id desc")->asArray()->all();
        if(count($video_list) > 0){
            foreach ($video_list as &$video){
                $video['created_at'] = date('Y-m-d H:i');
                $video['status'] = \Yii::$app->params['status'][$video['status']];
            }
        }

        return $video_list;
    }

}