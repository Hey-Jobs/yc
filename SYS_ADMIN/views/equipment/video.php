<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/25
 * Time: 20:53
 */

use SYS_ADMIN\assets\AppAsset;

$this->title = "文件列表";

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
                    <table id="equipment_video_table"  class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>AppName</th>
                            <th>StreamName</th>
                            <th>url</th>
                            <th>时长</th>
                            <th>开始时间</th>
                            <th>结束时间</th>
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

    $("#equipment_video_table").DataTable({
      ajax: '<?php echo \yii\helpers\Url::to(['/equipment/video',
          'api' => true,
          'appname' => $appname,
          'stream' => $stream])?>',
      bAutoWidth: false,
      ordering: true,
      /*aLengthMenu:[1,2,3,5,10],*/
      oLanguage: {
        sEmptyTable: "暂无数据",
        oPaginate: {
          sPrevious: "上一页",
          sNext: "下一页"
        }
      },
      columns: [
        {"data": "id"},
        {"data": "app"},
        {"data": "stream"},
        {"data": "uri"},
        {"data": "online_time"},
        {"data": "start_time"},
        {"data": "stop_time"},
      ],
      order: [[ 0, "desc" ]],
      aoColumnDefs: [
        {
          "targets": 3,
          "render" : function(data, type, row) {
            var html = '';
            html+= "<a  target='_blank' href='"+row.uri+"'> "+row.uri+" </a>";
            return html;
          }
        },
      ],
    });
  });




</script>