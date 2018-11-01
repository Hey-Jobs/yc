<?php
/**
 * User: liwj
 * Date:2018/11/1
 * Time:20:30
 */

use SYS_ADMIN\assets\AppAsset;

$this->title = "视频管理";

AppAsset::addScript($this, '/vendor/data-tables/js/jquery.dataTables.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/data-tables/js/dataTables.bootstrap.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/data-tables/css/dataTables.bootstrap.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert-extend.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/sweetalert/css/sweet-alert.css?v=' . Yii::$app->params['versionJS']);

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
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal" onclick="updateDeptLeader()">添加信息</button>
                    </p>

                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="color-line"></div>
                                <div class="modal-header text-center">
                                    <h4 class="modal-title"><span id="btnText">添加信息</span></h4>
                                </div>
                                <div class="modal-body" style="300px;">
                                    <div class="form-group row text-left" style="display: none;">
                                        <label class="col-sm-3 control-label position">autoId：</label>
                                        <div class="col-sm-9"><input style="display: none" type="text" name="autoId" class="form-control params" placeholder="autoId"></div>
                                    </div>
                                    <div class="form-group row text-left">
                                        <label class="col-sm-3 control-label position">审批领导：</label>
                                        <div class="col-sm-9">
                                            <select class="js-example-placeholder-single params" name="leader" style="width: 50%">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row text-left">
                                        <label class="col-sm-3 control-label position">一级部门：</label>
                                        <div class="col-sm-9">
                                            <select class="js-example-placeholder-single params" name="costPrimDept" style="width: 50%">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row text-left">
                                        <label class="col-sm-3 control-label position">二级部门：</label>
                                        <div class="col-sm-9">
                                            <select class="js-example-placeholder-single params" name="costSecDept" style="width: 50%">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭窗口</button>
                                    <button type="button" class="btn btn-primary" onclick="save()">保存信息</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table id="video_table"  class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>直播间</th>
                            <th>视频名称</th>
                            <th>视频链接</th>
                            <th>点击数</th>
                            <th>状态</th>
                            <th>添加时间</th>
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
        /*var sec;
        $("[name='leader']").select2({
            placeholder: "-- 请选择 --",
            dropdownParent: $("#myModal"),
            allowClear: true,
            data : getPersons(),
        });

        $("[name='costPrimDept']").select2({
            placeholder: "-- 请选择 --",
            dropdownParent: $("#myModal"),
            data : getDepts(1),
        });

        $("[name='costPrimDept']").on("select2:select", function () {
            let deptId = $(this).val();
            $("[name='costSecDept']").empty();
            $("[name='costSecDept']").select2({
                placeholder: "-- 请选择 --",
                dropdownParent: $("#myModal"),
                allowClear: true,
                data : getDepts(2, deptId),
            });
        });*/

        $("#video_table").DataTable({
            ajax: '<?php echo \yii\helpers\Url::to('/video/list?api=true')?>',
            bAutoWidth: false,
            ordering: true,
            /*aLengthMenu:[1,2,3,5,10],*/
            oLanguage: {
                oPaginate: {
                    sPrevious: "上一页",
                    sNext: "下一页"
                }
            },
            columns: [
                {"data": "id"},
                {"data": "room_name"},
                {"data": "video_name"},
                {"data": "video_url"},
                {"data": "click_num"},
                {"data": "status"},
                {"data": "created_at"},
            ],
            aoColumnDefs: [
                {
                    "targets": 7,
                    "render" : function(data, type, row) {
                        var html = '';
                        html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"updateVideo('"+ row.id +"')\"> 编辑 </a>";
                        html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"deleteVideo('"+ row.id +"')\"> 删除 </a>";
                        return html;
                    }
                }
            ],
        });
    });

    function deleteVideo(autoId)
    {
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
                        url: '<?php echo \yii\helpers\Url::to('video/del')?>',
                        dataType: 'json',
                        type: "POST",
                        data: {'autoId' : autoId},
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

    function updateVideo(autoId = '')
    {
        if (autoId.length != 0) {
            var data;
            $("#btnText").html('修改信息');
            $("[name='costSecDept']").empty();
            $.ajax({
                url: '<?php echo \yii\helpers\Url::to('video/info')?>',
                dataType: 'json',
                type: "POST",
                async : false,
                data: {'autoId' : autoId},
                success: function (result) {
                    if (result.status == 200) {
                        data = result.data;
                        $("[name='costSecDept']").select2({
                            placeholder: "-- 请选择 --",
                            dropdownParent: $("#myModal"),
                            data : getDepts(2, data.costPrimDept),
                        });

                        $("[name='autoId']").val(data.autoId);
                        $("[name='leader']").val(data.leader).trigger("change");
                        $("[name='costPrimDept']").val(data.costPrimDept).trigger("change");
                        $("[name='costSecDept']").val(data.costSecDept).trigger("change");
                    }
                }
            });

            $('#myModal').modal('show');

        } else {
            $("#btnText").html('添加信息');
        }
    }

    function save()
    {
        var post = {};
        $('.params').each(function (index, element) {
            post[$(element)[0].name] = $(element)[0].value;
        });

        $.ajax({
            type:'POST',
            dataType: 'json',
            url : '<?php yii\helpers\Url::to('video/save')?>',
            data : post,
            success: function(result) {
                if ('200' == result.status) {
                    affirmSwals('成功', '成功', 'success', confirmFunc);
                } else {
                    affirmSwals('失败', result.message, 'error', placeholder);
                }
            },
        });
    }
</script>
