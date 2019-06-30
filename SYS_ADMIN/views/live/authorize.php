<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/27
 * Time: 8:38
 */

use SYS_ADMIN\assets\AppAsset;
use yii\widgets\ActiveForm;

$this->title = $title;

AppAsset::addScript($this, '/vendor/data-tables/js/jquery.dataTables.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/data-tables/js/dataTables.bootstrap.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/data-tables/css/dataTables.bootstrap.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert-extend.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/sweetalert/css/sweet-alert.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-validation/jquery.validate.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-validation/messages_zh.min.js?v=' . Yii::$app->params['versionJS']);

AppAsset::addCss($this, '/vendor/bootstrap-fileinput/css/fileinput.min.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/bootstrap-fileinput/js/fileinput.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/bootstrap-fileinput/js/zh.js?v=' . Yii::$app->params['versionJS']);

?>

<style type="text/css">
    .position {
        padding-top: 7px;
        margin-bottom: 0;
    }

    #allmap{height: 300px;}
    .show-img{width: 150px; }
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
                    手机授权访问
                </div>

                <ul id="myTab" class="nav nav-tabs">
                    <li class="">
                        <a href="<?php echo \yii\helpers\Url::to('/live/base-info?id='.$room_id)?>">
                            基础信息
                        </a>
                    </li>
                    <li><a href="<?php echo \yii\helpers\Url::to('/live/ext-info?id='.$room_id)?>">扩展信息</a></li>
                    <li class="">
                        <a href="<?php echo \yii\helpers\Url::to('/live/banner?id='.$room_id)?>">
                            广告栏
                        </a>
                    </li>
                    <li class="active"><a href="#" >手机授权访问</a></li>
                </ul>

                <div class="panel-body">
                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'live_auth_form',
                        'options' => ['class' => 'form-horizontal col-sm-10', 'enctype' => 'multipart/form-data'],
                        'fieldConfig' => [
                        'template' => '<div class="form-group">
                                                    <label class="col-sm-2 control-label">{label}</label>
                        
                                                    <div class="col-sm-10">
                                                        {input}
                                                    </div>
                                                </div>
                                                <div class="hr-line-dashed"></div>'
                        ],
                    ]) ?>



                    <div class="form-group">
                        <label class="col-sm-2 control-label">加密模板</label>

                        <div class="col-sm-10">
                            <div class="auth">
                                <select id="auth_template" class=" form-control" name="auth_template">
                                    <option>选择加密模板</option>
                                    <?php foreach ($auth_template as $tkey => $tval) :?>
                                        <option value="<?= $tkey?>"><?= $tval?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">直播间加密</label>

                        <div class="col-sm-10">
                            <textarea name="secret_key" style="width: 100%; height: 100px"><?= $auth_info['secret_key'] ?? "" ?></textarea>
                            <div>不为空，表示开启房间密钥。多个手机号码用逗号隔开。</div>
                            <div>例如：1501355000,1352222546,1375050...</div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <div class="col-sm-8 col-sm-offset-2">
                            <input type="hidden" name="id" value="<?= $room_id ?>"/>
                            <button class="btn btn-primary" type="button" id="sub-form">保存</button>
                            <a href="<?php echo yii\helpers\Url::to('/live/index')?>" class="btn btn-default">返回列表</a>
                        </div>
                    </div>
                    <?php ActiveForm::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript">

  $(function () {
    $("#auth_template").val("<?= $auth_info['auth_template']?>");
    $("#sub-form").click(function () {
      if($("#live_auth_form").valid()){
        var form_data = new FormData($( "#live_auth_form" )[0]);
        $.ajax({
          type:'POST',
          dataType: 'json',
          url : '<?php echo yii\helpers\Url::to('/live/authorize')?>',
          data : form_data,
          async: false,
          async: false,
          cache: false,
          contentType: false,
          processData: false,
          success: function(result) {
            if ('200' == result.status) {
              affirmSwals('成功', '成功', 'success', confirmFunc);
            } else {
              affirmSwals('失败', result.message, 'error', placeholder);
            }
          },
        });
      }

    });

  });


</script>
