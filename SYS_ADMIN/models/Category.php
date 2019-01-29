<?php

namespace SYS_ADMIN\models;

use SYS_ADMIN\components\ConStatus;
use Yii;

/**
 * This is the model class for table "sys_category".
 *
 * @property integer $id
 * @property string $title
 * @property integer $status
 * @property integer $sort_num
 * @property string $created_at
 * @property string $updatea_at
 */
class Category extends CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'sort_num'], 'integer'],
            [['created_at', 'updatea_at'], 'safe'],
            [['title'], 'string', 'max' => 64],
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
            'status' => 'Status',
            'sort_num' => 'Sort Num',
            'created_at' => 'Created At',
            'updatea_at' => 'Updatea At',
        ];
    }

    /**
     * 获取所有分类
     */
    public static function getCategoryList()
    {
        $list = self::find()
            ->select(['id', 'title'])
            ->where(['<>', 'status', ConStatus::$STATUS_DELETED])
            ->orderBy('sort_num asc, id desc')
            ->asArray()
            ->all();

        if (count($list)) {
            foreach ($list as $key => $item) {
                if (in_array($item['title'], ['推荐', '全部'])) {
                    unset($list[$key]);
                }
            }
        }
        return $list;
    }
}
