<?php

namespace SYS_ADMIN\models;

use Yii;

/**
 * This is the model class for table "sys_activity".
 *
 * @property integer $id
 * @property string $title
 * @property string $activity_time
 * @property string $activity_url
 * @property integer $sort_num
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class Activity extends \SYS_ADMIN\models\CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_activity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sort_num', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'activity_url'], 'string', 'max' => 255],
            [['activity_time'], 'string', 'max' => 64],
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
            'activity_time' => 'Activity Time',
            'activity_url' => 'Activity Url',
            'sort_num' => 'Sort Num',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
