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
                                            <div class="col-sm-9"><input style="display: none" type="text" name="id"
                                                                         class="form-control params"
                                                                         placeholder="autoId"></div>
                                        </div>
                                        <div class="form-group row text-left">
                                            <label class="col-sm-3 control-label position">商城直播间：</label>
                                            <div class="col-sm-9">
                                                <?=\SYS_ADMIN\components\SearchWidget::instance()->liveRoom('room_id')?>
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
                                            <label class="col-sm-3 control-label position">商城简介：</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="introduction"
                                                       placeholder="商城简介"/>
                                            </div>
                                        </div>
                                        <div class="form-group row text-left">
                                            <label class="col-sm-3 control-label position">商城商标：</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="image_src"
                                                       placeholder="商城商标"/>
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

                    <table id="video_table" class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>归属直播间</th>
                            <th>商城名称</th>
                            <th>商城子标题</th>
                            <th>商城介绍</th>
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
        $("#video_table").DataTable({
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
                        url: '<?php echo \yii\helpers\Url::to('/video/del')?>',
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
        if (autoId.length != 0) {
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
//                        $("[name='id']").val(data.id);
//                        $("[name='video_name']").val(data.video_name);
//                        $("[name='video_url']").val(data.video_url);
//                        $("[name='sort_num']").val(data.sort_num);

//                        if (data.status == 1) {
//                            $("#status1").attr("checked", "checked");
//                            $("#status2").removeAttr("checked");
//                        }
//
//                        if (data.status == 2) {
//                            $("#status2").attr("checked", "checked");
//                            $("#status1").removeAttr("checked");
//                        }

                    }
                }
            });

            $('#myModal').modal('show');

        } else {
            $("#btnText").html('添加信息');
        }
    }

    function saveInfo() {
//        if ($("input[name='video_name']").val() == "") {
//            affirmSwals('失败', "请填写视频名称", 'error', placeholder);
//            return false;
//        }
//
//        if ($("input[name='video_url']").val().indexOf('http') == -1) {
//            affirmSwals('失败', "请填写正确的视频链接", 'error', placeholder);
//            return false;
//        }
//
//        if ($("input[name='sort_num']").val() == "") {
//            affirmSwals('失败', "请填写排序值", 'error', placeholder);
//            return false;
//        }

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
</script>
