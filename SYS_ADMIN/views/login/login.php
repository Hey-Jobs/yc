<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;

$this->title = 'Login';

if( Yii::$app->getSession()->hasFlash('success') ) {
    echo Alert::widget([
        'options' => [
            'class' => 'alert-success', //这里是提示框的class
        ],
        'body' => Yii::$app->getSession()->getFlash('success'), //消息体
    ]);
}
?>

<!-- Simple splash screen-->
<div class="splash">
    <div class="color-line"></div>
    <div class="splash-title"><h1>Homer - Responsive Admin Theme</h1>
        <p>Special Admin Theme for small and medium webapp with very clean and aesthetic style and feel. </p>
        <div class="spinner">
            <div class="rect1"></div>
            <div class="rect2"></div>
            <div class="rect3"></div>
            <div class="rect4"></div>
            <div class="rect5"></div>
        </div>
    </div>
</div>
<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a
        href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<div class="color-line"></div>

<div class="login-container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center m-b-md">
                <h2>云窗科技</h2>
            </div>
            <div class="hpanel">
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['id' => 'loginForm']); ?>
                    <div class="form-group">
                        <?= $form->field($model, '用户名')->textInput(['autofocus' => true]) ?>
                    </div>
                    <div class="form-group">
                        <?= $form->field($model, '密码')->passwordInput(['autofocus' => true]) ?>
                    </div>
                    <div class="checkbox">
                        <?= $form->field($model, '保存登录')->checkbox() ?>
                    </div>
                    <div class="form-group">
                        <?= Html::submitButton('登录', ['class' => 'btn btn-success btn-block', 'name' => 'login-button']) ?>
                    </div>
                    <a class="btn btn-default btn-block" href="/login/register">清空</a>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
<!--            <strong></strong> - AngularJS Responsive WebApp <br/> 2018 Copyright 云窗直播-->
            <strong></strong><br/> Copyright  2018 YCLIVE YUNCHUANGLIVE.COM
        </div>
    </div>
</div>