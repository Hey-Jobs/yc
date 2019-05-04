<?php

namespace SYS_ADMIN\models;

use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use Yii;

/**
 * This is the model class for table "sys_snapshot".
 *
 * @property integer $id
 * @property integer $room_id
 * @property integer $cover
 * @property string $title
 * @property integer $sort_num
 * @property string $remark
 * @property string $created_at
 * @property string $updated_at
 */
class Snapshot extends \SYS_ADMIN\models\CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_snapshot';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'room_id'], 'required'],
            [['id', 'room_id', 'cover', 'sort_num'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '编号',
            'room_id' => '直播间',
            'cover' => '封面图',
            'title' => '标题',
            'sort_num' => '排序值',
            'status' => '状态',
            'remark' => '备注',
            'created_at' => '创建',
            'updated_at' => '更新',
        ];
    }

    public static function getSnapshotList($data = [])
    {
        $user_room = LiveRoom::getUserRoomId();
        $room_id = array_keys($user_room);
        $where = ['<>', 'status', ConStatus::$STATUS_DELETED];
        $model = self::find()
            ->where($where);

        if (!CommonHelper::isAdmin()) { // 普通管理员，只能查看自己的视频
            $model->andWhere(['in', 'room_id', $room_id]);
        }

        $snapshot_list = $model->asArray()->all();
        if (count($snapshot_list) > 0) {
            $picid_list = array_column($snapshot_list, 'cover');
            $pic_list = Pictrue::getPictrueList($picid_list);
            foreach ($snapshot_list as &$snapshot) {
                $snapshot['pic_path'] = isset($pic_list[$snapshot['cover']]) ? $pic_list[$snapshot['cover']]['pic_path'] : '';
                $snapshot['created_at'] = date('Y-m-d H:i', strtotime($snapshot['created_at']));
                $snapshot['status'] = ConStatus::$STATUS_LIST[$snapshot['status']];
                $snapshot['room_name'] = $user_room[$snapshot['room_id']]['room_name'];
            }
        }

        return $snapshot_list;
    }
}
