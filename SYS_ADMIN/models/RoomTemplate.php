<?php

namespace SYS_ADMIN\models;

use Yii;

/**
 * This is the model class for table "sys_room_template".
 *
 * @property integer $id
 * @property string $title
 * @property integer $status
 * @property integer $sort_num
 * @property string $created_at
 * @property string $updated_at
 */
class RoomTemplate extends CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_room_template';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'sort_num'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'status' => 'Status',
            'sort_num' => 'Sort Num',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
