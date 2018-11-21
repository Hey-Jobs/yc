<?php
/**
 * User: liwj
 * Date:2018/11/21
 * Time:18:29
 */

namespace SYS_ADMIN\models;


use yii\db\ActiveRecord;

/**
 * Class ClientAddr
 * @package SYS_ADMIN\models
 * 地址管理
 */
class ClientAddr extends ActiveRecord
{
    public function rules()
    {
        return [
            [['client_name', 'client_sex', 'mobile', 'addr', 'detail'], 'required'],
            [['mobile'], 'match','pattern'=>'/^[1][345678][0-9]{9}$/'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'client_name' => '收件人',
            'client_sex' => '性别',
            'mobile' => '手机号码',
            'addr' => '地址',
            'detail' => '门牌号',
        ];
    }
}