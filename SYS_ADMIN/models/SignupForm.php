<?php

namespace SYS_ADMIN\models;

use yii\base\Model;
use common\models\User;
use yii\captcha\Captcha;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $name;
    public $password;
    public $passwordConfirm;
    public $phone;
    public $verifyCode;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required', 'message' => '用户账号.'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 6, 'max' => 255],

            ['phone', 'trim'],
            ['phone', 'required', 'message' => '手机号必填.'],
            ['phone', 'string', 'max' => 20],
            ['phone', 'string', 'min' => 11],

            ['name', 'trim'],
            ['name', 'required', 'message' => '用户名称.'],
            ['name', 'string', 'max' => 32],
            ['name', 'string', 'min' => 2],

            ['password', 'required', 'message' => '密码必填.'],
            ['password', 'string', 'min' => 6, 'message' => '不能少于6位.'],
            ['passwordConfirm', 'compare', 'compareAttribute' => 'password', 'message' => '两次密码不一致.'],

            ['verifyCode', 'captcha', 'captchaAction'=>'/login/captcha', 'message'=> '请输入正确的验证码.'],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->name = $this->name;
        $user->phone = $this->phone;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }

    /**
     * Sys_user AttributeLabels
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => '用户账号',
            'password' => '用户密码',
            'name' => '用户名称',
            'email' => '邮箱',
            'passwordConfirm' => '确认密码',
            'phone' => '手机号',
            'verifyCode' => '验证码',
        ];
    }
}
