<?php

namespace SYS_ADMIN\models;

use Yii;

/**
 * This is the model class for table "sys_product_detail".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $content
 * @property string $created_at
 * @property string $updated_at
 */
class ProductDetail extends CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_product_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id'], 'required'],
            [['id', 'product_id'], 'integer'],
            [['content', 'banner_img'], 'string'],
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
            'product_id' => 'Product ID',
            'content' => 'Content',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'banner_img' => '商品图片'
        ];
    }
}
