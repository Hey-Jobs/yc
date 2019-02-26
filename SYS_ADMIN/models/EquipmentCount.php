<?php

namespace SYS_ADMIN\models;

use Yii;

/**
 * This is the model class for table "sys_equipment_count".
 *
 * @property integer $id
 * @property string $appname
 * @property string $stream
 * @property integer $online_time
 * @property string $push_time
 * @property string $push_done_time
 * @property string $created_at
 * @property string $updated_at
 */
class EquipmentCount extends \SYS_ADMIN\models\CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_equipment_count';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['online_time'], 'integer'],
            [['push_time', 'push_done_time', 'created_at', 'updated_at'], 'safe'],
            [['appname', 'stream'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'appname' => 'Appname',
            'stream' => 'Stream',
            'online_time' => 'Online Time',
            'push_time' => 'Push Time',
            'push_done_time' => 'Push Done Time',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
