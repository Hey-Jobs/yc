<?php

namespace SYS_ADMIN\models;

use Yii;

/**
 * This is the model class for table "sys_equipment_cutout".
 *
 * @property integer $id
 * @property string $action
 * @property string $app
 * @property string $appname
 * @property string $stream
 * @property string $ip
 * @property string $node
 * @property string $content
 * @property string $created_at
 * @property string $updated_at
 */
class EquipmentCutout extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_equipment_cutout';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['action', 'app', 'appname', 'stream', 'node'], 'string', 'max' => 100],
            [['ip'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'action' => 'Action',
            'app' => 'App',
            'appname' => 'Appname',
            'stream' => 'Stream',
            'ip' => 'Ip',
            'node' => 'Node',
            'content' => 'Content',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
