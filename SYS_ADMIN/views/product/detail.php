<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/4
 * Time: 23:38
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

?>


<style type="text/css">
    .position {
        padding-top: 7px;
        margin-bottom: 0;
    }

    .desc{height: 100px !important;}
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

                <ul id="myTab" class="nav nav-tabs">
                    <li class="active">
                        <a href="#home" >
                            基本
                        </a>
                    </li>
                    <?php if(count($info) > 0) :?>
                        <li><a href="<?php echo \yii\helpers\Url::to('/product/detail?id='.$info['id'])?>">详情</a></li>
                    <?php endif; ?>
                </ul>

                <div class="panel-body">
                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'product_form',
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
                        <label class="col-sm-2 control-label">所属商城</label>

                        <div class="col-sm-10">
                            <?= $mall_html ?>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">商品名称</label>

                        <div class="col-sm-10"><input type="text" class="form-control" name="title" value="<?= $info['title'] ?? "" ?>"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">商品缩略图</label>

                        <div class="col-sm-10"><input type="file" class="form-control" name="pcover_img" id="pcover_img" value="<?= $info['pcover_img'] ?? "" ?>"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">商品描述</label>

                        <div class="col-sm-10">
                            <textarea class="form-control desc"  name="desc" id="desc"><?= $info['desc'] ?? "" ?></textarea>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">价格</label>

                        <div class="col-sm-2">
                            <input type="number" min="0.01"  step="0.01" class="form-control" name="price" value="<?= $info['price'] ?? 10?>">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">库存量</label>

                        <div class="col-sm-2">
                            <input type="number" min="1"   class="form-control" name="stock" value="<?= $info['stock'] ?? 10?>">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">排序值</label>

                        <div class="col-sm-2">
                            <input type="number" min="1"  max="1000" class="form-control" name="sort_num" value="<?= $info['sort_num'] ?? 10?>">
                            数值越小靠前
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">状态</label>

                        <div class="col-sm-10">
                            <div class="">
                                <?php if(count($info)):?>
                                    <label> <input type="radio" name="status" id="status1" value="1" class="" <?php echo $info['status'] != 2 ? "checked" : "" ?>> 显示</label>
                                    <label> <input type="radio" name="status" id="status2" value="2" class="" <?php echo $info['status'] == 2 ? "checked" : "" ?> >隐藏</label>
                                <?php else :?>
                                    <label> <input type="radio" name="status" id="status1" value="1" class="" checked> 显示</label>
                                    <label> <input type="radio" name="status" id="status2" value="2" class="" >隐藏</label>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <div class="col-sm-8 col-sm-offset-2">
                            <input type="hidden" name="cover_img" id="cover_img" value="<?= $info['cover_img'] ?? '' ?>"/>
                            <input type="hidden" name="id" value="<?= $info['id'] ?? 0 ?>"/>
                            <button class="btn btn-primary" type="button" id="sub-form">保存</button>
                            <a href="<?php echo yii\helpers\Url::to('/product/index')?>" class="btn btn-default">返回列表</a>

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
        $("#product_form").validate({
            rules:{
                title:{
                    required: true,
                },
                desc: {
                    required: true,
                },
                price:{
                    required: true,
                },
                stock:{
                  required: true,
                },


            },

        });

        $("#pcover_img").fileinput({
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
            //minImageWidth: 50, //图片的最小宽度
            //minImageHeight: 50,//图片的最小高度
            //maxImageWidth: 1000,//图片的最大宽度
            //maxImageHeight: 1000,//图片的最大高度
            //maxFileSize: 0,//单位为kb，如果为0表示不限制文件大小
            //minFileCount: 0,
            maxFileCount: 1, //表示允许同时上传的最大文件个数,
            <?php if(isset($pic_info['pic_path'])): ?>
            initialPreviewAsData: true,
            initialPreview: [
                "<?= $pic_info['pic_path'] ?? '' ?>",
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




        $("#sub-form").click(function () {
            if($("#product_form").valid()){
                if($("#cover_img").val() == '' && $("#pcover_img").val() == ''){
                    affirmSwals('失败', "请上传封面图片", 'error', placeholder);
                    return false;
                }

                var form_data = new FormData($( "#product_form" )[0]);
                $.ajax({
                    type:'POST',
                    dataType: 'json',
                    url : '<?php echo yii\helpers\Url::to('/product/save')?>',
                    data : form_data,
                    async: false,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(result) {
                        if ('200' == result.status) {
                            affirmSwals('成功', '成功', 'success', function () {
                                window.location.href = "/product/detail?id="+result.data.id
                            });
                        } else {
                            affirmSwals('失败', result.message, 'error', placeholder);
                        }
                    },
                });
            }

        });

    });

</script>
