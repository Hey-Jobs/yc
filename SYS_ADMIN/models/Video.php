<?php
/**
 * User: liwj
 * Date:2018/11/1
 * Time:20:07
 */

namespace SYS_ADMIN\models;


use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use yii\data\Pagination;
use yii\db\ActiveRecord;

class Video extends ActiveRecord
{

    public function rules()
    {
        return [
            [['room_id', 'video_name', 'video_url', 'sort_num'], 'required' ],
            ['sort_num', 'integer']
        ];
    }


    public function attributeLabels()
    {
        return [
            'room_id' => '所属直播间',
            'video_name' => '视频名称',
            'video_url' => '视频链接',
            'sort_num' => '排序值',
            'status' => '状态',
        ];
    }

    public static function getVideoList($data = [])
    {
        $user_room = LiveRoom::getUserRoomId();
        $room_id = array_keys($user_room);
        $where = ['<>', 'status', ConStatus::$STATUS_DELETED];
        $model = self::find()
            ->where($where);

        if (!CommonHelper::isAdmin()) { // 普通管理员，只能查看自己的视频
            $model->andWhere(['in', 'room_id', $room_id]);
        }

        $video_list = $model->asArray()->all();
        if(count($video_list) > 0){
            foreach ($video_list as &$video){
                $video['created_at'] = date('Y-m-d H:i', $video['created_at']);
                $video['status'] = ConStatus::$STATUS_LIST[$video['status']];
                $video['room_name'] = $user_room[$video['room_id']]['room_name'];
            }
        }

        return $video_list;
    }

}