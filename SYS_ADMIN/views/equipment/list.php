<?php

use SYS_ADMIN\assets\AppAsset;

$this->title = "设备管理";

AppAsset::addScript($this, '/vendor/data-tables/js/jquery.dataTables.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/data-tables/js/dataTables.bootstrap.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/data-tables/css/dataTables.bootstrap.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert-extend.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/sweetalert/css/sweet-alert.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-validation/jquery.validate.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-validation/messages_zh.min.js?v=' . Yii::$app->params['versionJS']);

?>

<style>
    .push_status{display: inline-block; width: 10px; height: 10px; border-radius: 10px;
        margin-right: 5px}
    .push_online{background: green; }
    .push_offline{background: red}
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

                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="device-form" method="post" action="<?php echo yii\helpers\Url::to("/equipment/save"); ?>">
                                    <div class="color-line"></div>
                                    <div class="modal-header text-center">
                                        <h4 class="modal-title"><span id="btnText">添加回调信息</span></h4>
                                    </div>
                                    <div class="modal-body" style="300px;">
                                        <form id="device_form" method="post" >
                                            <div class="form-group row text-left" style="display: none;">
                                                <div class="col-sm-9"><input style="display: none" type="text" name="id" class="form-control params" placeholder="autoId"></div>
                                            </div>


                                            <div class="form-group row text-left">
                                                <label class="col-sm-3 control-label position">直播状态回调：</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" name="live_callback"  placeholder="直播状态回调地址"/>
                                                </div>
                                            </div>

                                            <div class="form-group row text-left">
                                                <label class="col-sm-3 control-label position">回放回调：</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" name="replay_callback" placeholder="回放回调地址"/>
                                                </div>
                                            </div>
                                        </form>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭窗口</button>
                                        <button type="button" class="btn btn-primary" onclick="saveDevice()">保存截图</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <table id="equipment_table"  class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>AppName</th>
                            <th>StreamName</th>
                            <th>推流时间</th>
                            <th>设备状态</th>
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

    $("#equipment_table").DataTable({
      ajax: '<?php echo \yii\helpers\Url::to('/equipment/index?api=true')?>',
      bAutoWidth: false,
      ordering: true,
      aLengthMenu:[30,40,50,100],
      oLanguage: {
        oPaginate: {
          sPrevious: "上一页",
          sNext: "下一页"
        }
      },
      columns: [
        {"data": "id"},
        {"data": "appname"},
        {"data": "stream"},
        {"data": "push_time"},
        {"data": "push_type"},
      ],
      order: [[ 0, "desc" ]],
      aoColumnDefs: [
        {
          "targets": 4,
          "render" : function(data, type, row) {
            var html = '';
            if(data == 1) {
              html = "<span class='push_status push_online'></span>在线";
            } else {
              html = "<span class='push_status push_offline'></span>下线";
            }
            return html;
          },
        },
        {
          "targets": 5,
          "render" : function(data, type, row) {
            var html = '';
            html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"updateDevice('"+ row.appname +"','"+row.stream+"', 'publish')\"> 推流 </a>";
            html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"updateDevice('"+ row.appname +"','"+row.stream+"', 'publish_done')\"> 断流 </a>";
            html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"countDevice('"+ row.appname +"','"+row.stream+"')\"> 统计 </a>";
            html+= "<a href=\"javascript:void(0);\"  class=\"m-l-sm\" onclick=\"videoDevice('"+ row.appname +"','"+row.stream+"')\"> 视频文件 </a>";
            html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"TaskDevice('"+ row.id +"')\"> 定时推断流 </a>";
            html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"EditDevice('"+ row.id +"')\"> API回调 </a>";
            html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"deleteDevice('"+ row.id +"')\"> 删除 </a>";
            return html;
          }
        },
      ],
    });

    $("#device_form").validate({
      rules: {
        live_callback:{
          url: true,
        },
        replay_callback:{
          url: true,
        },
      },

    });


  });



  function deleteDevice(autoId)
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
            url: '<?php echo \yii\helpers\Url::to('/equipment/del')?>',
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


  function updateDevice(appname, stream, type) {
    $.ajax({
      url: '<?php echo \yii\helpers\Url::to('/equipment/push')?>',
      dataType: 'json',
      type: "POST",
      data: {'appname' : appname, 'stream':stream, 'type': type},
      success: function (result) {
        if (result.status == 200) {
          affirmSwals('Sucess!', '操作成功！', 'success', confirmFunc);
        } else {
          affirmSwals('Error!', result.message, 'error', confirmFunc);
        }
      }
    });
  }

  function videoDevice(appname, stream) {
    var url = "/equipment/video?appname="+appname+"&stream="+stream;
    //window.location.href = url;
    window.open(url);
  }

  function countDevice(appname, stream) {
    var url = "/equipment/statistics?appname="+appname+"&stream="+stream;
    //window.location.href = url;
    window.open(url);
  }

  function TaskDevice(id) {
    var url = "/equipment/task?id="+id;
    //window.location.href = url;
    window.open(url);
  }


  function EditDevice(autoId = '')
  {
    if (autoId.length != 0) {
      // 初始化选中 所属直播间
      var data;
      $("#btnText").html('修改回调信息');
      $.ajax({
        url: '<?php echo \yii\helpers\Url::to('/equipment/info')?>',
        dataType: 'json',
        type: "POST",
        async : false,
        data: {'id' : autoId},
        success: function (result) {
          if (result.status == 200) {
            data = result.data;

            $("[name='id']").val(data.id);
            $("[name='replay_callback']").val(data.replay_callback);
            $("[name='live_callback']").val(data.live_callback);
          }
        }
      });

      $('#myModal').modal('show');

    } else {
      $("#btnText").html('添加回调信息');
    }
  }


  function saveDevice()
  {
    if(!$("#device-form").valid()){
      return false;
    }

    $.ajax({
      type:'POST',
      dataType: 'json',
      url : '<?php echo yii\helpers\Url::to('/equipment/save')?>',
      data : $("#device-form").serialize(),
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