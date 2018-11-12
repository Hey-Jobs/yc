<?php

use SYS_ADMIN\assets\AppAsset;

$this->title = "评论管理";

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
                    <table id="data_table" class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>所属来源</th>
                            <th>目标名称</th>
                            <th>用户昵称</th>
                            <th>评论内容</th>
                            <th>被赞次数</th>
                            <th>评论状态</th>
                            <th>评论时间</th>
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
        $("#data_table").DataTable({
            ajax: '<?php echo \yii\helpers\Url::to('/comment/index?api=true')?>',
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
                {"data": "source_name"},
                {"data": "from_name"},
                {"data": "nickname"},
                {"data": "content"},
                {"data": "like_num"},
                {"data": "status_name"},
                {"data": "created_at"},
            ],
            aoColumnDefs: [
                {
                    "targets": 8,
                    "render": function (data, type, row) {
                        var html = '';
                        html += "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"DeleteInfo('" + row.id + "')\"> 删除 </a>";
                        return html;
                    }
                }
            ],
        });
    });

    function DeleteInfo(id = '') {
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
                        url: '<?php echo \yii\helpers\Url::to('/comment/delete')?>',
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
</script>
