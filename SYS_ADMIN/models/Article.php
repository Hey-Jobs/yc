<?php

namespace SYS_ADMIN\models;

use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use Yii;

/**
 * This is the model class for table "sys_article".
 *
 * @property integer $id
 * @property integer $room_id
 * @property string $title
 * @property integer $cover
 * @property integer $click_num
 * @property string $content
 * @property integer $sort_num
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class Article extends \SYS_ADMIN\models\CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['room_id', 'title'], 'required'],
            [['room_id', 'cover', 'sort_num', 'status'], 'integer'],
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'room_id' => '直播间',
            'title' => '标题',
            'cover' => '封面',
            'click_num' => '点击数',
            'content' => '内容',
            'sort_num' => '排序值',
            'status' => '状态',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function getList($data = [])
    {
        $user_room = LiveRoom::getUserRoomId();
        $room_id = array_keys($user_room);
        $where = ['<>', 'status', ConStatus::$STATUS_DELETED];
        $model = self::find()
            ->where($where);

        if (!CommonHelper::isAdmin()) { // 普通管理员
            $model->andWhere(['in', 'room_id', $room_id]);
        }

        $list = $model->asArray()->all();
        if (count($list) > 0) {
            $pic_id = array_column($list, 'cover');
            $pic_list = Pictrue::getPictrueList($pic_id);
            foreach ($list as &$item) {
                $item['created_at'] = date('Y-m-d H:i', strtotime($item['created_at']));
                $item['status'] = ConStatus::$STATUS_LIST[$item['status']];
                $item['room_name'] = $user_room[$item['room_id']]['room_name'];
                $item['pic_path'] = isset($pic_list[$item['cover']]) ? $pic_list[$item['cover']]['pic_path'] : '';
            }
        }

        return $list;
    }
}
