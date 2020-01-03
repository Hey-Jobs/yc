<?php

namespace SYS_ADMIN\models;

use Yii;

/**
 * This is the model class for table "sys_campus".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property integer $cover_id
 * @property integer $bg_cover_id
 * @property string $created_at
 * @property string $updatea_at
 */
class Campus extends CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_campus';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'cover_id', 'bg_cover_id'], 'integer'],
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
            'user_id' => 'User ID',
            'title' => 'Title',
            'cover_id' => 'Cover ID',
            'bg_cover_id' => 'Bg Cover ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
