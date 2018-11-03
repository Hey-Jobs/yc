<?php
/**
 * User: liwj
 * Date:2018/11/1
 * Time:20:30
 */

use SYS_ADMIN\assets\AppAsset;

$this->title = "镜头管理";

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

                    <table id="lens_table"  class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>直播间</th>
                            <th>镜头名称</th>
                            <th>镜头缩略图</th>
                            <th>点击量</th>
                            <th>排序</th>
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

        $("#lens_table").DataTable({
            ajax: '<?php echo \yii\helpers\Url::to('/lens/list?api=true')?>',
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
                {"data": "lens_name"},
                {"data": "cover_img"},
                {"data": "click_num"},
                {"data": "sort_num"},
                {"data": "status"},
            ],
            aoColumnDefs: [
                {
                    "targets": 7,
                    "render" : function(data, type, row) {
                        var html = '';
                        html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"updateLens('"+ row.id +"')\"> 编辑 </a>";
                        html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"deleteLens('"+ row.id +"')\"> 删除 </a>";
                        return html;
                    }
                }
            ],
        });
    });

    function deleteLens(autoId)
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
                        url: '<?php echo \yii\helpers\Url::to('/lens/del')?>',
                        dataType: 'json',
                        type: "POST",
                        data: {'id' : autoId},
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

    function updateLens(autoId = '')
    {
        if (autoId.length != 0) {
            var data;
            $("#btnText").html('修改镜头信息');
            $.ajax({
                url: '<?php echo \yii\helpers\Url::to('/video/info')?>',
                dataType: 'json',
                type: "POST",
                async : false,
                data: {'id' : autoId},
                success: function (result) {
                    if (result.status == 200) {
                        data = result.data;

                        $("[name='id']").val(data.id);
                        $("[name='video_name']").val(data.video_name);
                        $("[name='video_url']").val(data.video_url);
                        $("[name='sort_num']").val(data.sort_num);

                        if(data.status == 1){
                            $("#status1").attr("checked","checked");
                            $("#status2").removeAttr("checked");
                        }

                        if(data.status == 2){
                            $("#status2").attr("checked","checked");
                            $("#status1").removeAttr("checked");
                        }

                    }
                }
            });

            $('#myModal').modal('show');

        } else {
            $("#btnText").html('添加镜头信息');
        }
    }

    function saveVideo()
    {
        if($("input[name='video_name']").val() == ""){
            affirmSwals('失败', "请填写视频名称", 'error', placeholder);
            return false;
        }

        if($("input[name='video_url']").val().indexOf('http') == -1){
            affirmSwals('失败', "请填写正确的视频链接", 'error', placeholder);
            return false;
        }

        if($("input[name='sort_num']").val() == ""){
            affirmSwals('失败', "请填写排序值", 'error', placeholder);
            return false;
        }

        $.ajax({
            type:'POST',
            dataType: 'json',
            url : '<?php echo yii\helpers\Url::to('/video/save')?>',
            data : $("#video-form").serialize(),
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
