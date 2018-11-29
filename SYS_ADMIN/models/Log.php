<?php

namespace SYS_ADMIN\models;

use Yii;

/**
 * This is the model class for table "sys_log".
 *
 * @property integer $id
 * @property string $type
 * @property string $url
 * @property string $content
 * @property string $created_at
 */
class Log extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['created_at'], 'safe'],
            [['type', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'url' => 'Url',
            'content' => 'Content',
            'created_at' => 'Created At',
        ];
    }

    public static function Add($data)
    {

    }
}
