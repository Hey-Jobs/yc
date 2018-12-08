<?php

namespace SYS_ADMIN\models;

use Yii;

/**
 * This is the model class for table "sys_test".
 *
 * @property integer $id
 * @property string $content
 * @property string $app
 * @property string $uri
 * @property string $duration
 * @property string $stop_time
 * @property string $stream
 * @property string $start_time
 * @property string $domain
 * @property string $created_at
 * @property string $updated_at
 */
class EquipmentBack extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_equipment_back';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => 'Content',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
