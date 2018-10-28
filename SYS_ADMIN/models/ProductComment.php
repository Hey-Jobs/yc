<?php

namespace SYS_ADMIN\models;

use Yii;

/**
 * This is the model class for table "sys_product_comment".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $comment
 * @property integer $like_num
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class ProductComment extends CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_product_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'product_id'], 'required'],
            [['id', 'product_id', 'like_num', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['comment'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'comment' => 'Comment',
            'like_num' => 'Like Num',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
