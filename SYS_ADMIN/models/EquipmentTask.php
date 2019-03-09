<?php

namespace SYS_ADMIN\models;

use Yii;

/**
 * This is the model class for table "sys_equipment_task".
 *
 * @property integer $id
 * @property integer $equip_id
 * @property string $task_time
 * @property integer $task_type
 * @property integer $status
 * @property string $created_time
 * @property string $updated_time
 */
class EquipmentTask extends \SYS_ADMIN\models\CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_equipment_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_id', 'task_type', 'status'], 'integer'],
            [['task_time', 'created_time', 'updated_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'equip_id' => 'Equip ID',
            'task_time' => 'Task Time',
            'task_type' => 'Task Type',
            'status' => 'Status',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
