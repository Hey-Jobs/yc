<?php

namespace SYS_ADMIN\models;

use Yii;

/**
 * This is the model class for table "sys_equipment".
 *
 * @property integer $id
 * @property string $app
 * @property string $appname
 * @property string $stream
 * @property string $push_time
 * @property integer $status
 * @property integer $push_type
 * @property string $created_at
 * @property string $updated_at
 */
class Equipment extends \SYS_ADMIN\models\CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_equipment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['push_time', 'created_at', 'updated_at'], 'safe'],
            [['status'], 'integer'],
            [['app', 'appname', 'stream'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'app' => 'App',
            'appname' => 'Appname',
            'stream' => 'Stream',
            'push_time' => 'Push Time',
            'status' => 'Status',
            'push_type' => 'Push Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
