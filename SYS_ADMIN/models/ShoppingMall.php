<?php

namespace SYS_ADMIN\models;

use Yii;

/**
 * This is the model class for table "sys_shopping_mall".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $room_id
 * @property string $title
 * @property string $sub_title
 * @property string $introduction
 * @property string $image_src
 * @property string $created_at
 * @property string $updated_at
 * @property integer $status
 */
class ShoppingMall extends CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_shopping_mall';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'room_id'], 'integer'],
            [['room_id'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 64],
            [['sub_title'], 'string', 'max' => 32],
            [['introduction', 'image_src'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'room_id' => 'Room ID',
            'title' => 'Title',
            'sub_title' => 'Sub Title',
            'introduction' => 'Introduction',
            'image_src' => 'Image Src',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function getAll()
    {

    }
}
