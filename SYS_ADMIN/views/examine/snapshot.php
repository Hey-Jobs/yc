<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/1
 * Time: 23:08
 */

use SYS_ADMIN\assets\AppAsset;
use yii\helpers\Url;

$this->title = "截图管理";

AppAsset::addScript($this, '/vendor/data-tables/js/jquery.dataTables.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/data-tables/js/dataTables.bootstrap.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/data-tables/css/dataTables.bootstrap.css?v=' . Yii::$app->params['versionJS']);
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
    .video_url{width: 300px; word-break:break-all;}
    .show-img{width: 200px;}
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
                        <button type="button" class="btn btn-success"  onclick="checkInfo(1)">通过</button>
                        <button type="button" class="btn btn-danger" onclick="checkInfo(2)">驳回</button>
                    </p>

                    <table id="snapshot_table"  class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                        <tr>
                            <th><input type='checkbox' id='checkall' value=''/></th>
                            <th>直播间</th>
                            <th>截图名称</th>
                            <th>截图</th>
                            <th>排序值</th>
                            <th>状态</th>
                            <th width="85">添加时间</th>
                            <th width="100">更新时间</th>
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

        $("#snapshot-form").validate({
            rules:{
                title: {
                    required: true,
                },
                sort_num: {
                    required: true,
                }
            },

        });

        $("#checkall").click(function () {
            if (this.checked) {
                $(this).attr('checked', 'checked');
                $("input[name='checklist']").each(function () {
                    this.checked = true;
                });
            } else {
                $(this).attr('checked', 'checked');
                $("input[name='checklist']").each(function () {
                    this.checked = false;
                });
            }
        });

        $("#snapshot_table").DataTable({
            ajax: '<?php echo \yii\helpers\Url::to('/examine/snapshot?api=true')?>',
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
                {"data": "pic_path"},
                {"data": "sort_num"},
                {"data": "status"},
                {"data": "created_at"},
                {"data": "updated_at"},
            ],
            order: [[ 0, "desc" ]],
            aoColumnDefs: [
                {
                    "render":function(data,type,row){
                        return '<input type="checkbox" name="checklist" οnclick="checkCheck()" value="' + row.id + '" />'
                    },
                    "targets":0,
                },
                {
                    "render":function(data,type,row){
                        return showPic(data);
                    },
                    "targets":3,
                }
            ],
        });
    });

    function showPic(path){
        return path ? "<a href=\""+path+"\" target='_blank'><img src=\""+path+"\" class=\"show-img\"></a>" : "";
    }

    function checkInfo(checkResult) {
        var checklist = [];
        $.each($('input[name=\'checklist\']:checkbox:checked'),function(){
            checklist.push($(this).val());
        });

        if (checklist.length <= 0) {
            affirmSwals('失败', "请勾选选项", 'error', placeholder);
        }

        var formdata = {};
        formdata.checktype = checkResult;
        formdata.checklist = checklist.join(",");
        $.ajax({
            type:'POST',
            dataType: 'json',
            url : '<?php echo yii\helpers\Url::to('/examine/check-snapshot')?>',
            data : formdata,
            success: function(result) {
                if ('200' == result.status) {
                    affirmSwals('成功', '成功', 'success', confirmFunc);
                } else {
                    affirmSwals('失败', result.message, 'error', placeholder);
                }
            },
        });

    }

    function checkCheck() {
        if ($(this).is(":checked") == false) {
            $('#checkall').prop("checked", false);
        }
    }

    function saveSnapshot()
    {
        if(!$("#snapshot-form").valid()){
            return false;
        }

        $.ajax({
            type:'POST',
            dataType: 'json',
            url : '<?php echo yii\helpers\Url::to('/snapshot/save')?>',
            data : $("#snapshot-form").serialize(),
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
