<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sys_comment".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $from_id
 * @property integer $client_id
 * @property string $nickname
 * @property string $thumb_img
 * @property string $content
 * @property integer $is_top
 * @property integer $is_hot
 * @property integer $like_num
 * @property integer $reply_num
 * @property integer $is_reply
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'from_id', 'client_id', 'is_top', 'is_hot', 'like_num', 'reply_num', 'is_reply'], 'integer'],
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['nickname'], 'string', 'max' => 64],
            [['thumb_img'], 'string', 'max' => 128],
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
            'from_id' => 'From ID',
            'client_id' => 'ClientController ID',
            'nickname' => 'Nickname',
            'thumb_img' => 'Thumb Img',
            'content' => 'Content',
            'is_top' => 'Is Top',
            'is_hot' => 'Is Hot',
            'like_num' => 'Like Num',
            'reply_num' => 'Reply Num',
            'is_reply' => 'Is Reply',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
