<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/4
 * Time: 23:38
 */


use SYS_ADMIN\assets\AppAsset;
use yii\widgets\ActiveForm;

$this->title = count($info) > 0 ? "直播间资料" : "新增直播间";

AppAsset::addScript($this, '/vendor/data-tables/js/jquery.dataTables.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/data-tables/js/dataTables.bootstrap.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/data-tables/css/dataTables.bootstrap.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert-extend.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/sweetalert/css/sweet-alert.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-validation/jquery.validate.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-validation/messages_zh.min.js?v=' . Yii::$app->params['versionJS']);

AppAsset::addCss($this, '/vendor/summernote/dist/summernote.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/summernote/dist/summernote-bs3.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/summernote/dist/summernote.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/summernote/lang/summernote-zh-CN.js?v=' . Yii::$app->params['versionJS']);

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
                    <?php echo count($info) > 0 ? "直播间资料" : "新增直播间";?>
                </div>

                <ul id="myTab" class="nav nav-tabs">
                    <li class="">
                        <a href="javascript:;" onclick=goLink('<?php echo \yii\helpers\Url::to('/live/base-info?id='.$room_id)?>')>
                            基础信息
                        </a>
                    </li>
                    <li class="active"><a href="#" >扩展信息</a></li>
                </ul>

                <div class="panel-body">
                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'lens_form',
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
                        <label class="col-sm-2 control-label">直播间名称</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" readonly="readonly" name="room_name" value="<?= $info['room_name'] ?? "" ?>">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">封面图</label>

                        <div class="col-sm-10"><input type="file" class="form-control" name="pcover_img" id="pcover_img" value="<?= $info['cover_img'] ?? "" ?>"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">直播间简介</label>

                        <div class="col-sm-10">
                            <textarea class="form-control introduce"  name="introduce" id="introduce"><?= $info['introduce'] ?? "" ?></textarea>
                        </div>
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
                        <div class="col-sm-8 col-sm-offset-2">
                            <input type="hidden" name="cover_img" id="cover_img" value="<?= $info['cover_img'] ?? '' ?>"/>
                            <input type="hidden" name="id" value="<?= $info['room_id'] ?? 0 ?>"/>
                            <button class="btn btn-primary" type="button" id="sub-form">保存</button>
                            <?php if(\SYS_ADMIN\models\LiveRoom::getRoomId() == 0) :?>
                                <a href="<?php echo yii\helpers\Url::to('/live/index')?>" class="btn btn-default">返回列表</a>
                            <?php endif;?>
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
            height:400,
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
            callbacks:{
                onImageUpload: function(files, editor, $editable) {
                    UploadFiles(files,insertImg);
                }
            },
        });


        function UploadFiles(files,func){
            var formData = new FormData();
            for(f in files){
                formData.append("file", files[f]);
            }

            $.ajax({
                data: formData,
                type: "POST",
                url: "/uploadMultipleFile",
                cache: false,
                contentType: false,
                processData: false,
                success: function(imageUrl) {
                    func(imageUrl);

                },
                error: function() {
                    console.log("uploadError");
                }
            })
        }

        $("#pcover_img").fileinput({
            language: 'zh', //设置语言
            uploadUrl: '', //上传的地址
            allowedFileExtensions: ['png', 'jpg'],//接收的文件后缀
            uploadAsync: true, //默认异步上传
            showUpload: false, //是否显示上传按钮
            showRemove: true, //显示移除按钮
            showPreview: true, //是否显示预览
            showCaption: false,//是否显示标题
            browseClass: "btn btn-primary", //按钮样式
            //dropZoneEnabled: false,//是否显示拖拽区域
            //minImageWidth: 50, //图片的最小宽度
            //minImageHeight: 50,//图片的最小高度
            //maxImageWidth: 1000,//图片的最大宽度
            //maxImageHeight: 1000,//图片的最大高度
            //maxFileSize: 0,//单位为kb，如果为0表示不限制文件大小
            //minFileCount: 0,
            maxFileCount: 1, //表示允许同时上传的最大文件个数,
            <?php if(isset($info['pic_path'])): ?>
            initialPreviewAsData: true,
            initialPreview: [
                "/<?= $info['pic_path'] ?? '' ?>",
            ],
            initialPreviewConfig: [
                {caption: "<?= $pic_info['pic_name'] ?? '' ?>", size: "<?= $pic_info['pic_size'] ?? '' ?>", width: "120px", url: "{$url}", key: 1, showRemove: false,},
            ]
            <?php endif;?>
        }).on("filebatchselected", function(event, files) {
            $(this).fileinput("upload");
        })
            .on("fileuploaded", function(event, data) {
                if(data.response)
                {
                    alert('处理成功');
                }
            });;

        $("#status1").click(function () {
            $("#status1").attr("checked","checked");
            $("#status2").removeAttr("checked");
        });

        $("#status2").click(function () {
            $("#status2").attr("checked","checked");
            $("#status1").removeAttr("checked");
        });

        $("#lens_form").validate({
            rules:{
                lens_name:{
                    required: true,
                },
                online_url: {
                    required: true,
                    url: true
                },
                playback_url: {
                    required: true,
                    url: true
                },
                bgm_url:{
                    required: true,
                    url: true
                },
                marvellous_url:{
                    required: true,
                    url: true
                },
                number: {
                    required: true,
                    number: true
                },
            },

        });


        $("#sub-form").click(function () {
            if($("#lens_form").valid()){
                if($("#cover_img").val() == '' && $("#pcover_img").val() == ''){
                    affirmSwals('失败', "请上传封面图片", 'error', placeholder);
                    return false;
                }

                var form_data = new FormData($( "#lens_form" )[0]);
                $.ajax({
                    type:'POST',
                    dataType: 'json',
                    url : '<?php echo yii\helpers\Url::to('/lens/save')?>',
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

    function goLink(url) {
        window.location.href = url;
    }


</script>
