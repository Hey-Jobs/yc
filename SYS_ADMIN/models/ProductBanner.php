<?php

namespace SYS_ADMIN\models;

use Yii;

/**
 * This is the model class for table "sys_product_banner".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $img_src
 * @property string $created_at
 * @property string $updated_at
 */
class ProductBanner extends CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_product_banner';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'img_src'], 'required'],
            [['id', 'product_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['img_src'], 'string', 'max' => 255],
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
            'img_src' => 'Img Src',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
