<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/3
 * Time: 0:57.
 */

namespace SYS_ADMIN\models;

use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use yii\db\ActiveRecord;

class Lens extends ActiveRecord
{
    public function rules()
    {
        return [
            [['lens_name', 'room_id'], 'required'],
            [['sort_num', 'status'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '编号',
            'room_id' => '所属直播间',
            'lens_name' => '镜头名称',
            'cover_img' => '镜头缩略图',
            'online_url' => '直播流地址',
            'playback_url' => '镜头回放地址',
            'bgm_url' => '背景音乐地址',
            'marvellous_url' => '精彩回放地址',
            'sort_num' => '排序值',
            'status' => '状态',
            'stream_status' => '流状态',
        ];
    }

    /**
     * 获取镜头列表.
     */
    public static function getLensList()
    {
        $user_room = LiveRoom::getUserRoomId();
        $room_id = array_keys($user_room);
        $model = self::find()
            ->where(['<>', 'status', ConStatus::$STATUS_DELETED]);

        if (!CommonHelper::isAdmin()) { // 非管理员
            $model->andWhere(['in', 'room_id', $room_id]);
        }

        $lens_list = $model->orderBy('id desc')->asArray()->all();
        if (count($lens_list)) {
            $pic_id = array_column($lens_list, 'cover_img');
            $pic_list = Pictrue::getPictrueList($pic_id);

            foreach ($lens_list as &$len) {
                $len['room_name'] = isset($user_room[$len['room_id']]) ? $user_room[$len['room_id']]['room_name'] : '';
                $len['created_at'] = date('Y-m-d H:i', $len['created_at']);
                $len['status'] = ConStatus::$STATUS_LIST[$len['status']];
                $len['pic_path'] = isset($pic_list[$len['cover_img']]) ? $pic_list[$len['cover_img']]['pic_path'] : '';
            }
        }

        return $lens_list;
    }
}
