<?php

namespace SYS_ADMIN\models;

use Yii;

/**
 * This is the model class for table "sys_category".
 *
 * @property integer $id
 * @property string $title
 * @property integer $status
 * @property integer $sort_num
 * @property string $created_at
 * @property string $updatea_at
 */
class Category extends CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'sort_num'], 'integer'],
            [['created_at', 'updatea_at'], 'safe'],
            [['title'], 'string', 'max' => 64],
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
            'updatea_at' => 'Updatea At',
        ];
    }
}
