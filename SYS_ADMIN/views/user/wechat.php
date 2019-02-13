<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/12
 * Time: 23:15
 */

use SYS_ADMIN\assets\AppAsset;
use yii\widgets\ActiveForm;

$this->title = $title;

AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert-extend.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/sweetalert/css/sweet-alert.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-validation/jquery.validate.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-validation/messages_zh.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/bootstrap-fileinput/css/fileinput.min.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/bootstrap-fileinput/js/fileinput.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/bootstrap-fileinput/js/zh.js?v=' . Yii::$app->params['versionJS']);

AppAsset::addScript($this, '/static/js/qrcode.min.js?v=' . Yii::$app->params['versionJS']);

?>


<style>
    .bind-user-title{height: 50px; line-height: 50px; vertical-align: middle}
    .wechat-img, .wechat-name{display: inline-block; }
    .wechat-img{width: 50px; height: 50px; margin: 0 10px;}
    .wechat-img img{max-width: 100%; max-height: 100%; vertical-align: middle}
    .bind-wechat{width: 200px; text-align: center}
    .bind-title{margin-top: 10px}
</style>
<div class="content animate-panel">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        <a class="closebox"><i class="fa fa-times"></i></a>
                    </div>
                    <?= $title?>
                </div>


                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-sm-2 control-label bind-user-title">已绑定微信</label>
                        <div class="col-sm-10zxS">
                            <div class="wechat-img">
                                <img src="../static/images/my2.png"/>
                            </div>
                            <div class="wechat-name">料位计</div>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-10 bind-wechat">
                            <div id="bind-code"></div>
                            <div class="bind-title">扫码绑定微信</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        new QRCode(document.getElementById('bind-code'), {
          text: "<?= $qrcode ?>",
          width: 200,
          height: 200,
        });
    });

    window.setInterval(function () {
      getBindWechat();
    }, 1000);

    function getBindWechat() {
      $.ajax({
        type : 'get',
        dataType: 'json',
        url : "/user/check-bind",
        success : function(data) {
          if (data.data.status == 1) {
            location.reload();
          }
        },
      });
    }
</script>