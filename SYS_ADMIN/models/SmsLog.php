<?php

namespace SYS_ADMIN\models;

use Yii;

/**
 * This is the model class for table "sys_sms_log".
 *
 * @property integer $id
 * @property integer $mobile
 * @property string $sign_name
 * @property string $templat_code
 * @property string $content
 * @property integer $client_id
 * @property string $biz_id
 * @property string $message
 * @property string $created_time
 */
class SmsLog extends CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_sms_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'mobile', 'client_id'], 'integer'],
            [['created_time'], 'safe'],
            [['message'], 'string', 'max' => 64],
            [['content'], 'string', 'max' => 255],
            [['biz_id'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mobile' => 'Mobile',
            'sign_name' => 'Sign Name',
            'templat_code' => 'Templat Code',
            'content' => 'Content',
            'client_id' => 'Client ID',
            'biz_id' => 'Biz ID',
            'message' => 'Message',
            'created_time' => 'Created Time',
        ];
    }
}
