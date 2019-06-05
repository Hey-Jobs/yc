<?php

namespace SYS_ADMIN\models;

use Yii;

/**
 * This is the model class for table "sys_live_server".
 *
 * @property integer $id
 * @property string $title
 * @property string $stream_addr
 * @property string $oss_addr
 * @property string $remark
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class LiveServer extends \SYS_ADMIN\models\CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_live_server';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 128],
            [['stream_addr', 'oss_addr', 'remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'stream_addr' => 'Stream Addr',
            'oss_addr' => 'Oss Addr',
            'remark' => 'Remark',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
