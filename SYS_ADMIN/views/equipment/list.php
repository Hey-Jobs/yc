<?php

use SYS_ADMIN\assets\AppAsset;

$this->title = "设备管理";

AppAsset::addScript($this, '/vendor/data-tables/js/jquery.dataTables.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/data-tables/js/dataTables.bootstrap.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/data-tables/css/dataTables.bootstrap.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert-extend.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/sweetalert/css/sweet-alert.css?v=' . Yii::$app->params['versionJS']);

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
            html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"videoDevice('"+ row.appname +"','"+row.stream+"')\"> 视频文件 </a>";
            html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"TaskDevice('"+ row.id +"')\"> 定时推断流 </a>";
            html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"deleteDevice('"+ row.id +"')\"> 删除 </a>";
            return html;
          }
        },
      ],
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
    window.location.href = url;
  }

  function countDevice(appname, stream) {
    var url = "/equipment/statistics?appname="+appname+"&stream="+stream;
    window.location.href = url;
  }

  function TaskDevice(id) {
    var url = "/equipment/task?id="+id;
    window.location.href = url;
  }


</script>