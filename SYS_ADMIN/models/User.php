<?php

namespace SYS_ADMIN\models;

use Yii;

/**
 * This is the model class for table "sys_user".
 *
 * @property integer $id
 * @property string $username
 * @property string $name
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $phone
 * @property string $email
 * @property string $wechat_name
 * @property string $wechat_img
 * @property string $wechat_openid
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class User extends CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash', 'created_at', 'updated_at'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'wechat_img'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 50],
            [['auth_key', 'wechat_openid'], 'string', 'max' => 32],
            [['phone'], 'string', 'max' => 20],
            [['wechat_name'], 'string', 'max' => 128],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'name' => 'Name',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'phone' => 'Phone',
            'email' => 'Email',
            'wechat_name' => 'Wechat Name',
            'wechat_img' => 'Wechat Img',
            'wechat_openid' => 'Wechat Openid',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
