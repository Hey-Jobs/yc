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
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal" onclick="updateVideo()">添加信息</button>
                    </p>

                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="video-form" method="post" action="<?php echo yii\helpers\Url::to("/video/save"); ?>">
                                <div class="color-line"></div>
                                <div class="modal-header text-center">
                                    <h4 class="modal-title"><span id="btnText">添加视频</span></h4>
                                </div>
                                <div class="modal-body" style="300px;">
                                    <form id="video_form" method="post" >
                                        <div class="form-group row text-left" style="display: none;">
                                            <div class="col-sm-9"><input style="display: none" type="text" name="id" class="form-control params" placeholder="autoId"></div>
                                        </div>

                                        <div class="form-group row text-left">
                                            <label class="col-sm-3 control-label position">所属直播间：</label>
                                            <div class="col-sm-9">
                                                <?= $room_html?>
                                            </div>
                                        </div>

                                        <div class="form-group row text-left">
                                            <label class="col-sm-3 control-label position">视频名称：</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="video_name"  placeholder="视频名称"/>
                                            </div>
                                        </div>

                                        <div class="form-group row text-left">
                                            <label class="col-sm-3 control-label position">视频链接：</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="video_url" placeholder="视频链接"/>
                                            </div>
                                        </div>

                                        <div class="form-group row text-left">
                                            <label class="col-sm-3 control-label position">视频封面：</label>
                                            <div class="col-sm-9">
<!--                                                <input type="file" class="form-control" name="img" id="img" data-show-preview="true" placeholder="视频封面" ">-->
                                                <input type="text" class="form-control" name="cover_img" id="cover_img" placeholder="视频封面">
                                            </div>
                                        </div>

                                        <div class="form-group row text-left">
                                            <label class="col-sm-3 control-label position">视频长度：</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control"  name="video_length" value="" placeholder="视频长度"/>
                                            </div>
                                        </div>

                                        <div class="form-group row text-left">
                                            <label class="col-sm-3 control-label position">排序值：</label>
                                            <div class="col-sm-9">
                                                <input type="number" class="form-control"  name="sort_num" value="10" placeholder="排序值"/>
                                                数据值越小越靠前
                                            </div>
                                        </div>

                                        <div class="form-group row text-left">
                                            <label class="col-sm-3 control-label position">状态：</label>
                                            <div class="col-sm-9">
                                                <label class="radio-inline">
                                                    <input type="radio" name="status" id="status1" value="1" checked> 显示
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="status" id="status2"  value="2" > 不显示
                                                </label>
                                            </div>
                                        </div>
                                    </form>

                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭窗口</button>
                                    <button type="button" class="btn btn-primary" onclick="saveVideo()">保存信息</button>
                                </div>
                                </form>
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

//        var initialPreview = [];
//        var initialPreviewConfig = [];
//        editImage(initialPreview, initialPreviewConfig);

        $("#video-form").validate({
            rules:{
                video_name:{
                    required: true,
                },
                video_url: {
                    required: true,
                    url: true
                },
                sort_num: {
                    required: true,
                },
                room_id: {
                    required: true,
                },
                video_length:{
                    required: true,
                }
            },

        });

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
            order: [[ 0, "desc" ]],
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
                        url: '<?php echo \yii\helpers\Url::to('/video/del')?>',
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

    function updateVideo(autoId = '')
    {
        if (autoId.length != 0) {
            // 初始化选中 所属直播间
            var data;
            $("#btnText").html('修改视频信息');
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
                        $("[name='video_length']").val(data.video_length);
                        // $("#w0").select2('val',data.room_id);
                        $("[name='room_id']").val(data.room_id).trigger('change');
                        if(data.status == 1){
                            $("#status1").attr("checked","checked");
                            $("#status2").removeAttr("checked");
                        }

                        if(data.status == 2){
                            $("#status2").attr("checked","checked");
                            $("#status1").removeAttr("checked");
                        }


                        $("[name='cover_img']").val(data.cover_img);
//                        var initialPreview = [data.pic_path];
//                        var initialPreviewConfig = [{showRemove: false}];
//                        $("#img").fileinput('destroy');
//                        editImage(initialPreview, initialPreviewConfig);
                    }
                }
            });

            $('#myModal').modal('show');

        } else {
            $("#btnText").html('添加视频信息');
        }
    }

    function saveVideo()
    {
        if(!$("#video-form").valid()){
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

//        $("#img").on('fileuploaded', function (event, data, previewId, index) {//异步上传成功结果处理
//            console.log(data.response);
//            if (data.response.status == 200) {
//                $("#cover_img").val(data.response.data.images)
//            }
//        });

        $("#img").on('fileerror', function (event, data, msg) {//异步上传失败结果处理
            alert("uploadError");
        });
    }
</script>
