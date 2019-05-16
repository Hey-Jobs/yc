<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/13
 * Time: 9:04
 */

use SYS_ADMIN\assets\AppAsset;
use yii\widgets\ActiveForm;

$this->title = "文章管理";

AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert-extend.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/sweetalert/css/sweet-alert.css?v=' . Yii::$app->params['versionJS']);

AppAsset::addScript($this, '/vendor/jquery-validation/jquery.validate.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-validation/messages_zh.min.js?v=' . Yii::$app->params['versionJS']);


AppAsset::addCss($this, '/vendor/bootstrap-fileinput/css/fileinput.min.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/bootstrap-fileinput/js/fileinput.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/bootstrap-fileinput/js/zh.js?v=' . Yii::$app->params['versionJS']);


AppAsset::addCss($this, '/vendor/summernote/summernote.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/summernote/summernote-bs4.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/summernote/summernote.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/summernote/summernote-zh-CN.min.js?v=' . Yii::$app->params['versionJS']);


?>



<style type="text/css">
    .position {
        padding-top: 7px;
        margin-bottom: 0;
    }

    #allmap{height: 300px;}
    .introduce{height: 100px !important;}
    .summernote{height: 400px !important;}
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
                    编辑文章
                </div>
                <div class="panel-body">
                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'article_form',
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
                        <label class="col-sm-2 control-label">所属直播间</label>

                        <div class="col-sm-10">
                            <?= $room_html ?>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">标题</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control"  name="title" value="<?= $info['title'] ?? "" ?>">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label">封面图</label>

                        <div class="col-sm-10"><input type="file" class="form-control" name="pcover" id="pcover"  value="<?= $info['cover'] ?? "" ?>"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">内容</label>

                        <div class="col-sm-10">
                            <div class="summernote">

                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">点击量</label>

                        <div class="col-sm-2">
                            <input type="number" class="form-control" name="click_num"
                                   value="<?= $info['click_num'] ?? 10 ?>">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">排序值</label>

                        <div class="col-sm-2">
                            <input type="number" min="1" max="1000" class="form-control" name="sort_num"
                                   value="<?= $info['sort_num'] ?? 10 ?>">
                            数值越小靠前
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">状态</label>

                        <div class="col-sm-10">
                            <div class="">
                                <?php if (count($info)): ?>
                                    <label> <input type="radio" name="status" id="status1" value="1"
                                                   class="" <?php echo $info['status'] != 2 ? "checked" : "" ?>>
                                        显示</label>
                                    <label> <input type="radio" name="status" id="status2" value="2"
                                                   class="" <?php echo $info['status'] == 2 ? "checked" : "" ?> >隐藏</label>
                                <?php else : ?>
                                    <label> <input type="radio" name="status" id="status1" value="1" class="" checked>
                                        显示</label>
                                    <label> <input type="radio" name="status" id="status2" value="2" class="">隐藏</label>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <div class="col-sm-8 col-sm-offset-2">
                            <textarea style="display: none" id="initContent"><?= $info['content'] ?? "" ?></textarea>
                            <input type="hidden" name="cover" id="cover" value="<?= $info['cover'] ?? '' ?>"/>
                            <input type="hidden" name="id" value="<?= $info['id'] ?? "" ?>"/>
                            <input type="hidden" name="content" id="content" />
                            <button class="btn btn-primary" type="button" id="sub-form">保存</button>
                            <a href="<?php echo yii\helpers\Url::to('/article/index')?>" class="btn btn-default">返回列表</a>
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

    $('.summernote').summernote({
      height:300,
      lang: 'zh-CN',

      // toolbar工具栏默认
      toolbar: [
        ['style', ['style']],
        ['font', ['bold', 'underline', 'clear']],
        ['fontname', ['fontname']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video']],
        ['view', ['fullscreen', /*'codeview', 'help'*/]]
      ],
      popover: {
        image: [
          ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
          ['float', ['floatLeft', 'floatRight', 'floatNone']],
          ['remove', ['removeMedia']]
        ],
        link: [
          ['link', ['linkDialogShow', 'unlink']]
        ],
        air: [
          ['color', ['color']],
          ['font', ['bold', 'underline', 'clear']],
          ['para', ['ul', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link', 'picture']]
        ]
      },
      callbacks: {
        onImageUpload: function(files) { //the onImageUpload API
          img = sendFile(files[0]);
        }
      }
    });

    $('.summernote').summernote('code', $("#initContent").val());
    function sendFile(file) {
      var formdata = new FormData();
      formdata.append("img", file);
      $.ajax({
        data: formdata,
        type: "POST",
        url: "<?php echo yii\helpers\Url::to('/upload/img')?>",
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
          var res = eval("("+data+")");
          if(res.status == 200){
            $(".summernote").summernote('insertImage', res.data.img_path, 'image name');
          }
        }
      });
    }
    $("#article_form").validate({
      rules: {
        introduce: {
          required: true,
        },
      }
    });
    $("#pcover").fileinput({
      language: 'zh', //设置语言
      uploadUrl: '', //上传的地址
      allowedFileExtensions: ['png', 'jpg'],//接收的文件后缀
      uploadAsync: true, //默认异步上传
      showUpload: false, //是否显示上传按钮
      showRemove: true, //显示移除按钮
      showPreview: true, //是否显示预览
      showCaption: true,//是否显示标题
      browseClass: "btn btn-primary", //按钮样式
      dropZoneEnabled: false,//是否显示拖拽区域
      maxFileCount: 1, //表示允许同时上传的最大文件个数,
        <?php if(isset($pic_info['pic_path'])): ?>
      initialPreviewAsData: true,
      initialPreview: [
        "/<?= $pic_info['pic_path'] ?? '' ?>",
      ],
      initialPreviewConfig: [
        {caption: "<?= $pic_info['pic_name'] ?? '' ?>", size: "<?= $pic_info['pic_size'] ?? '' ?>", width: "120px", url: "{$url}", key: 1, showRemove: false,},
      ]
        <?php endif;?>
    }).on("filebatchselected", function(event, files) {
      $(this).fileinput("upload");
    }).on("fileuploaded", function(event, data) {
      if(data.response)
      {
        alert('处理成功');
      }
    });


    $("#sub-form").click(function () {
      if($("#article_form").valid()){
        if($("#cover").val() == '' && $("#pcover").val() == ''){
            affirmSwals('失败', "请上传封面图片", 'error', placeholder);
            return false;
        }

        $("#content").val($('.summernote').summernote('code'));
        var form_data = new FormData($( "#article_form" )[0]);
        $.ajax({
          type:'POST',
          dataType: 'json',
          url : '<?php echo yii\helpers\Url::to('/article/save')?>',
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

    $(".fileinput-remove").click(function () {
      $("#cover").val("");
    });

    $("#pcover").on('fileuploaded', function (event, data, previewId, index) {//异步上传成功结果处理
      console.log(data.response);
      console.log("=============");
      if (data.response.status == 200) {
        $("#cover").val(data.response.data.images)
      }
    });
  });


</script>
