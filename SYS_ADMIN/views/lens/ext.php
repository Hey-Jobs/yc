<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/28
 * Time: 1:14
 */
use SYS_ADMIN\assets\AppAsset;
use yii\widgets\ActiveForm;

$this->title =  "编辑镜头" ;

AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert-extend.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/sweetalert/css/sweet-alert.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-validation/jquery.validate.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-validation/messages_zh.min.js?v=' . Yii::$app->params['versionJS']);

?>
<style>
    .storage-btn{margin-top: 50px}
    .storageDay{display: inline-block; width: 40px;  }
    .storage-num{display: inline-block}
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
                    镜头编辑
                </div>

                <ul id="myTab" class="nav nav-tabs">
                    <li>
                        <a href="<?php echo \yii\helpers\Url::to(['/lens/info', 'id' => $info['id']])?>" >
                            基本
                        </a>
                    </li>
                    <li class="active">
                        <a href="#home" >
                            扩展
                        </a>
                    </li>
                </ul>

                <div class="panel-body">
                    <form id="lens_form" method="post">
                    <div class="form-group ">
                        <label class="col-sm-2 control-label">视频存储天数</label>

                        <div class="col-sm-10">
                            <input type="radio" id="storage1" value="0" class="storage-type" >
                                不启用
                            <input type="radio" id="storage2" value="2"  class="storage-type" >启用
                            <div class="storage-num">&nbsp;&nbsp;<span>循环存储  <input type="text" id="storageDay" class="storageDay" value="<?= $info['storage'] > 0 ? $info['storage'] : '' ?>"/>  天数</span><div>

                        </div>
                    </div>



                    <div class="form-group">
                        <div class="col-sm-8 col-sm-offset-2 storage-btn">
                            <input type="hidden" name="id" value="<?= $info['id'] ?? 0 ?>"/>
                            <input type="hidden" name="storage" value="<?= $info['storage'] ?? 0 ?>"/>
                            <button class="btn btn-primary" type="button" id="sub-form">保存</button>
                            <a href="<?php echo yii\helpers\Url::to('/lens/list') ?>" class="btn btn-default">返回列表</a>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="application/javascript">
  $(function () {

    //初始化
    var storageType = '<?php echo $info['storage'] > 0 ? $info['storage'] : 0?>';
    if(parseInt(storageType) > 0) {
        $("#storage2").attr("checked",'checked');
        $("#storage1").removeAttr("checked");
        $(".storage-num").show()
    } else {
      $("#storage1").attr("checked",'checked');
      $("#storage2").removeAttr("checked");
      $(".storage-num").hide()
    }

    
    $(".storage-type").click(function () {
      if(parseInt($(this).val()) > 0) {
        $("#storage2").attr("checked",'checked');
        $("#storage1").removeAttr("checked");
        $(".storage-num").show()

      } else {
        $("#storage1").attr("checked",'checked');
        $("#storage2").removeAttr("checked");
        $(".storage-num").hide()
      }
    });


    $("#sub-form").click(function () {
      if ($("#lens_form").valid()) {
        if(parseInt($("input[type='radio']:checked").val()) > 0) {
            if($("#storageDay").val()){
              $("input[name='storage']").val($("#storageDay").val());
            }else {
              affirmSwals('失败', '请填写存储天数', 'error', placeholder);
              return false;
            }

        } else {
          $("input[name='storage']").val(0);
        }


        var form_data = new FormData($("#lens_form")[0]);
        $.ajax({
          type: 'POST',
          dataType: 'json',
          url: '<?php echo yii\helpers\Url::to('/lens/ext-save')?>',
          data: form_data,
          async: false,
          async: false,
          cache: false,
          contentType: false,
          processData: false,
          success: function (result) {
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