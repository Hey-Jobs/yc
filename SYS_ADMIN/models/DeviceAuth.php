<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sys_device_auth".
 *
 * @property integer $auth_id
 * @property string $auth_code
 * @property string $remark
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class DeviceAuth extends \SYS_ADMIN\models\CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_device_auth';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['auth_code', 'status'], 'required'],
            [['status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['auth_code'], 'string', 'max' => 32],
            [['remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'auth_id' => 'Auth ID',
            'auth_code' => 'Auth Code',
            'remark' => 'Remark',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
