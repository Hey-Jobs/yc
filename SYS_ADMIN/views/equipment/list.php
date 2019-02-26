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
      /*aLengthMenu:[1,2,3,5,10],*/
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
      ],
      order: [[ 0, "desc" ]],
      aoColumnDefs: [
        {
          "targets": 4,
          "render" : function(data, type, row) {
            var html = '';
            //html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"updateLens('"+ row.id +"')\"> 推流 </a>";
            //html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"updateLens('"+ row.id +"')\"> 断流 </a>";
            html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"countDevice('"+ row.appname +"','"+row.stream+"')\"> 统计 </a>";
            html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"videoDevice('"+ row.appname +"','"+row.stream+"')\"> 视频文件 </a>";
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

  function videoDevice(appname, stream) {
    var url = "/equipment/video?appname="+appname+"&stream="+stream;
    window.location.href = url;
  }

  function countDevice(appname, stream) {
    var url = "/equipment/statistics?appname="+appname+"&stream="+stream;
    window.location.href = url;
  }



</script>