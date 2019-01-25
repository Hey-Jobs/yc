<?php

namespace SYS_ADMIN\models;

use Yii;

/**
 * This is the model class for table "sys_banner".
 *
 * @property integer $id
 * @property integer $banner_type
 * @property string $title
 * @property integer $cover_img
 * @property string $remarks
 * @property integer $status
 * @property integer $sort_num
 * @property integer $room_id
 * @property string $created_at
 * @property string $updatea_at
 */
class Banner extends CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_banner';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['banner_type', 'cover_img', 'status', 'sort_num', 'room_id'], 'integer'],
            [['created_at', 'updatea_at'], 'safe'],
            [['title'], 'string', 'max' => 128],
            [['remarks'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'banner_type' => 'Banner Type',
            'title' => 'Title',
            'cover_img' => 'Cover Img',
            'remarks' => 'Remarks',
            'status' => 'Status',
            'sort_num' => 'Sort Num',
            'room_id' => 'Room ID',
            'created_at' => 'Created At',
            'updatea_at' => 'Updatea At',
        ];
    }
}
