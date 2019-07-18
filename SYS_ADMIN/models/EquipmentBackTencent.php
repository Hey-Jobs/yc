<?php

namespace app\models;

use SYS_ADMIN\models\CommonModel;
use Yii;

/**
 * This is the model class for table "sys_equipment_back_tencent".
 *
 * @property string $id
 * @property string $domain
 * @property string $app
 * @property string $content
 * @property string $stream
 * @property string $uri
 * @property string $duration
 * @property string $start_time
 * @property string $stop_time
 * @property string $created_at
 * @property string $updated_at
 */
class EquipmentBackTencent extends CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_equipment_back_tencent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['start_time', 'stop_time', 'created_at', 'updated_at'], 'safe'],
            [['app'], 'string', 'max' => 20],
            [['stream', 'uri'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'domain' => 'Domain',
            'app' => 'App',
            'content' => 'Content',
            'stream' => 'Stream',
            'uri' => 'Uri',
            'duration' => 'Duration',
            'start_time' => 'Start Time',
            'stop_time' => 'Stop Time',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
