<?php

namespace app\models;

use SYS_ADMIN\models\CommonModel;
use Yii;

/**
 * This is the model class for table "sys_equipment_tencent".
 *
 * @property integer $id
 * @property string $app
 * @property string $appname
 * @property string $stream
 * @property string $push_time
 * @property integer $push_type
 * @property integer $status
 * @property string $replay_callback
 * @property string $live_callback
 * @property string $created_at
 * @property string $updated_at
 */
class EquipmentTencent extends CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_equipment_tencent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['push_time', 'created_at', 'updated_at'], 'safe'],
            [['push_type', 'status'], 'integer'],
            [['app', 'appname', 'stream'], 'string', 'max' => 64],
            [['replay_callback', 'live_callback'], 'string', 'max' => 255],
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
            'push_type' => 'Push Type',
            'status' => 'Status',
            'replay_callback' => 'Replay Callback',
            'live_callback' => 'Live Callback',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
