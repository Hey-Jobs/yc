<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/4
 * Time: 23:38
 */


use SYS_ADMIN\assets\AppAsset;
use yii\helpers\Url;
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

?>


<style type="text/css">
    .position {
        padding-top: 7px;
        margin-bottom: 0;
    }

    #allmap{height: 300px;}
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
                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'campus_form',
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
                            <label class="col-sm-2 control-label">校园首页</label>

                            <div class="col-sm-10">
                                <a href="<?= $info['preview']?>" target="_blank"><?= $info['preview']?></a>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">校园名称</label>

                            <div class="col-sm-10"><input type="text" class="form-control" name="title" value="<?= $info['title'] ?? "" ?>"></div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">校园LOGO</label>

                            <div class="col-sm-10"><input type="file" class="form-control" name="logo_img" id="logo_img" value="<?= $info['logo_img'] ?? "" ?>"></div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">校园背景图</label>

                            <div class="col-sm-10"><input type="file" class="form-control" name="bg_img" id="bg_img" value="<?= $info['bg_img'] ?? "" ?>"></div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-sm-8 col-sm-offset-2">
                                <input type="hidden" name="cover_id" id="cover_id" value="<?= $info['cover_id'] ?? '' ?>"/>
                                <input type="hidden" name="bg_cover_id" id="bg_cover_id" value="<?= $info['bg_cover_id'] ?? '' ?>"/>
                                <button class="btn btn-primary" type="button" id="sub-form">保存</button>
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
        $("#logo_img").fileinput({
            language: 'zh', //设置语言
            uploadUrl: '', //上传的地址
            allowedFileExtensions: ['png', 'jpg'],//接收的文件后缀
            uploadAsync: false, //默认异步上传
            showUpload: false, //是否显示上传按钮
            showRemove: true, //显示移除按钮
            showPreview: true, //是否显示预览
            showCaption: true,//是否显示标题
            browseClass: "btn btn-primary", //按钮样式
            dropZoneEnabled: false,//是否显示拖拽区域
            maxFileCount: 1, //表示允许同时上传的最大文件个数,
            <?php if(isset($logo_info['pic_path'])): ?>
            initialPreviewAsData: true,
            initialPreview: [
                "<?= $logo_info['pic_path'] ?? '' ?>",
            ],
            initialPreviewConfig: [
                {caption: "<?= $logo_info['pic_name'] ?? '' ?>", size: "<?= $logo_info['pic_size'] ?? '' ?>", width: "120px", url: "{$url}", key: 1, showRemove: false,},
            ]
            <?php endif;?>
        }).on("filebatchselected", function(event, files) {
            $(this).fileinput("upload");
        }).on("fileuploaded", function(event, data) {
            if(data.response)
            {
                alert('处理成功');
            }
        });;

        $("#bg_img").fileinput({
            language: 'zh', //设置语言
            uploadUrl: '', //上传的地址
            allowedFileExtensions: ['png', 'jpg'],//接收的文件后缀
            uploadAsync: false, //默认异步上传
            showUpload: false, //是否显示上传按钮
            showRemove: true, //显示移除按钮
            showPreview: true, //是否显示预览
            showCaption: true,//是否显示标题
            browseClass: "btn btn-primary", //按钮样式
            dropZoneEnabled: false,//是否显示拖拽区域
            maxFileCount: 1, //表示允许同时上传的最大文件个数,
            <?php if(isset($bg_img_info['pic_path'])): ?>
            initialPreviewAsData: true,
            initialPreview: [
                "<?= $bg_img_info['pic_path'] ?? '' ?>",
            ],
            initialPreviewConfig: [
                {caption: "<?= $bg_img_info['pic_name'] ?? '' ?>", size: "<?= $bg_img_info['pic_size'] ?? '' ?>", width: "120px", url: "{$url}", key: 1, showRemove: false,},
            ]
            <?php endif;?>
        }).on("filebatchselected", function(event, files) {
            $(this).fileinput("upload");
        }).on("fileuploaded", function(event, data) {
            if(data.response)
            {
                alert('处理成功');
            }
        });;

        $("#sub-form").click(function () {
            if($("#cover_id").val() == '' && $("#logo_img").val() == ''){
                affirmSwals('失败', "请上传校园logo", 'error', placeholder);
                return false;
            }

            if($("#bg_cover_id").val() == '' && $("#bg_img").val() == ''){
                affirmSwals('失败', "请上传校园背景图", 'error', placeholder);
                return false;
            }

            var form_data = new FormData($( "#campus_form" )[0]);
            $.ajax({
                type:'POST',
                dataType: 'json',
                url : '<?php echo yii\helpers\Url::to('/campus/save')?>',
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

        });

    });

</script>
