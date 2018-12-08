<?php

use SYS_ADMIN\assets\AppAsset;

$this->title = "商城管理";

AppAsset::addScript($this, '/vendor/data-tables/js/jquery.dataTables.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/data-tables/js/dataTables.bootstrap.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/data-tables/css/dataTables.bootstrap.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert-extend.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/sweetalert/css/sweet-alert.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/select2/css/select2.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/select2/js/select2.full.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/select2/js/select2-form-extend.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/bootstrap-fileinput/css/fileinput.min.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/bootstrap-fileinput/js/fileinput.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/bootstrap-fileinput/js/zh.js?v=' . Yii::$app->params['versionJS']);
?>


<style type="text/css">
    .position {
        padding-top: 7px;
        margin-bottom: 0;
    }
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
                    Standard table
                </div>
                <div class="panel-body">
                    <p>
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal"
                                onclick="updateInfo()">添加信息
                        </button>
                    </p>

                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true"
                         data-keyboard="false" data-backdrop="static">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="data-form" method="post"
                                      action="<?php echo yii\helpers\Url::to("/video/save"); ?>">
                                    <div class="color-line"></div>
                                    <div class="modal-header text-center">
                                        <h4 class="modal-title"><span id="btnText">添加信息</span></h4>
                                    </div>
                                    <div class="modal-body" style="300px;">
                                        <div class="form-group row text-left" style="display: none;">
                                            <div class="col-sm-9"><input style="display: none" type="hidden" name="id"
                                                                         class="form-control params"></div>
                                        </div>
                                        <div class="form-group row text-left">
                                            <label class="col-sm-3 control-label position">商城直播间：</label>
                                            <div class="col-sm-9">
                                                <?= \SYS_ADMIN\components\SearchWidget::instance()->liveRoom('room_id') ?>
                                            </div>
                                        </div>
                                        <div class="form-group row text-left">
                                            <label class="col-sm-3 control-label position">商城标题：</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="title"
                                                       placeholder="商城标题"/>
                                            </div>
                                        </div>
                                        <div class="form-group row text-left">
                                            <label class="col-sm-3 control-label position">商城子标题：</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="sub_title"
                                                       placeholder="商城子标题"/>
                                            </div>
                                        </div>
                                        <div class="form-group row text-left">
                                            <label class="col-sm-3 control-label position">商城特色：</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control" id="introduction" name="introduction" rows="6" placeholder="商城简介" style="min-width: 90%"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row text-left">
                                            <label class="col-sm-3 control-label position">商城商标：</label>
                                            <div class="col-sm-9">
                                                <input type="file" class="form-control" name="img" id="img" multiple="multiple" data-show-preview="true" placeholder="商城商标" ">
                                                <input type="hidden" class="form-control" name="image_src" id="image_src" placeholder="商城商标" ">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭窗口</button>
                                        <button type="button" class="btn btn-primary" onclick="saveInfo()">保存信息
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <table id="data_table" class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>归属直播间</th>
                            <th>商城名称</th>
                            <th>商城子标题</th>
                            <th>商城特色</th>
                            <th>添加时间</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript">
    $(function () {

        var initialPreview = [];
        var initialPreviewConfig = [];
        editImage(initialPreview, initialPreviewConfig);


        $("#data_table").DataTable({
            ajax: '<?php echo \yii\helpers\Url::to('/shopping-mall/index?api=true')?>',
            bAutoWidth: false,
            ordering: true,
            oLanguage: {
                oPaginate: {
                    sPrevious: "上一页",
                    sNext: "下一页"
                }
            },
            columns: [
                {"data": "id"},
                {"data": "room_name"},
                {"data": "title"},
                {"data": "sub_title"},
                {"data": "introduction"},
                {"data": "created_at"},
                {"data": "status_name"},
            ],
            aoColumnDefs: [
                {
                    "targets": 7,
                    "render": function (data, type, row) {
                        var html = '';
                        if (row.status == 1) {
                            html += "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"savePut('" + row.id + "', 2)\"> 下架 </a>";
                        } else if (row.status == 2) {
                            html += "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"savePut('" + row.id + "', 1)\"> 上架 </a>";
                        }

                        html += "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"updateInfo('" + row.id + "')\"> 编辑 </a>";
                        html += "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"deleteInfo('" + row.id + "')\"> 删除 </a>";
                        return html;
                    }
                }
            ],
        });
    });

    function deleteInfo(id) {
        swal({
                title: "你确认删除这条信息吗?",
                type: "warning",
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确认删除！",
                cancelButtonText: "不，我再想想！",
                closeOnConfirm: false,
                closeOnCancel: true,
                showCancelButton: true,
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: '<?php echo \yii\helpers\Url::to('/shopping-mall/delete')?>',
                        dataType: 'json',
                        type: "POST",
                        data: {'id': id},
                        success: function (result) {
                            if (result.status == 200) {
                                affirmSwals('Deleted!', '删除成功！', 'success', confirmFunc);
                            } else {
                                affirmSwals('Deleted!', result.message, 'error', confirmFunc);
                            }
                        }
                    });
                }
            }
        );
    }

    function updateInfo(autoId = '') {
        if (autoId.length > 0) {
            var data;
            $("#btnText").html('修改信息');
            $.ajax({
                url: '<?php echo \yii\helpers\Url::to('/shopping-mall/one')?>',
                dataType: 'json',
                type: "GET",
                async: false,
                data: {'id': autoId},
                success: function (result) {
                    if (result.status == 200) {
                        data = result.data;
                        $("[name='id']").val(data.id);
                        $("[name='room_id']").val(data.room_id).trigger('change');
                        $("[name='title']").val(data.title);
                        $("[name='sub_title']").val(data.sub_title);
                        $("[name='introduction']").val(data.introduction);
                        $("[name='image_src']").val(data.image_src);
                        var initialPreview = [data.image_src];
                        var initialPreviewConfig = [{showRemove: false}];
                        $("#img").fileinput('destroy');
                        console.log(initialPreview);
                        console.log(initialPreviewConfig);
                        editImage(initialPreview, initialPreviewConfig);
                    }
                }
            });
            $('#myModal').modal('show');
        } else {
            $("#btnText").html('添加信息');
        }
    }

    function saveInfo() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '<?php echo yii\helpers\Url::to('/shopping-mall/save')?>',
            data: $("#data-form").serialize(),
            success: function (result) {
                if ('200' == result.status) {
                    affirmSwals('成功', '成功', 'success', confirmFunc);
                } else {
                    affirmSwals('失败', result.message, 'error', placeholder);
                }
            },
        });
    }

    function savePut(id, status)
    {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '<?php echo yii\helpers\Url::to('/shopping-mall/put')?>',
            data: {'id': id, 'status': status},
            success: function (result) {
                if ('200' == result.status) {
                    affirmSwals('成功', '成功', 'success', confirmFunc);
                } else {
                    affirmSwals('失败', result.message, 'error', placeholder);
                }
            },
        });
    }

    function editImage(initialPreview, initialPreviewConfig)
    {
        $("#img").fileinput({
            language: 'zh', //设置语言
            uploadUrl: '/upload/img', //上传的地址
            allowedFileExtensions: ['png', 'jpg'],//接收的文件后缀
            uploadAsync: true, //默认异步上传
            showUpload: false, //是否显示上传按钮
            showRemove: true, //显示移除按钮
            showPreview: true, //是否显示预览
            showCaption: true,//是否显示标题
            browseClass: "btn btn-primary", //按钮样式
            dropZoneEnabled: true,//是否显示拖拽区域
            maxFileCount: 1, //表示允许同时上传的最大文件个数,
            initialPreviewAsData: true,
            initialPreview: initialPreview,
            initialPreviewConfig: initialPreviewConfig
        }).on('filebatchselected', function (event, files) {//选中文件事件
            $(this).fileinput("upload");
        });

        $("#img").on('fileuploaded', function (event, data, previewId, index) {//异步上传成功结果处理
            console.log(data.response);
            if (data.response.status == 200) {
                $("#image_src").val(data.response.data.img_path)
            }
            // var img = JSON.parse(data.response);//接收后台传过来的json数据
            // alert(img.imgUrl);
        });

        $("#img").on('fileerror', function (event, data, msg) {//异步上传失败结果处理
            alert("uploadError");
        });
    }
</script>
