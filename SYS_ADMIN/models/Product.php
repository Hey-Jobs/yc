<?php

namespace SYS_ADMIN\models;

use Yii;

/**
 * This is the model class for table "sys_product".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $room_id
 * @property integer $cate_id
 * @property string $title
 * @property string $desc
 * @property string $price
 * @property integer $stock
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class Product extends CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'room_id', 'cate_id', 'stock', 'status'], 'integer'],
            [['room_id', 'title'], 'required'],
            [['price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'desc'], 'string', 'max' => 255],
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
            'cate_id' => 'Cate ID',
            'title' => 'Title',
            'desc' => 'Desc',
            'price' => 'Price',
            'stock' => 'Stock',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
