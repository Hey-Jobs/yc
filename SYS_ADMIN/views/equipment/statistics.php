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
                    <a href="<?php echo yii\helpers\Url::to('/equipment/index') ?>" class="btn btn-primary">返回列表</a>
                </div>
                <div class="panel-body">
                    <table id="equipment_count_table"  class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>AppName</th>
                            <th>StreamName</th>
                            <th>在线时长</th>
                            <th>上线时间</th>
                            <th>下线时间</th>
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

    $("#equipment_count_table").DataTable({
      ajax: '<?php echo \yii\helpers\Url::to(['/equipment/statistics',
          'api' => true,
          'appname' => $appname,
          'stream' => $stream])?>',
      bAutoWidth: false,
      ordering: true,
      aLengthMenu:[30,40,50,100],
      oLanguage: {
        sEmptyTable: "暂无数据",
        oPaginate: {
          sPrevious: "上一页",
          sNext: "下一页"
        }
      },
      columns: [
        {"data": "id"},
        {"data": "appname"},
        {"data": "stream"},
        {"data": "online_time"},
        {"data": "push_time"},
        {"data": "push_done_time"},
      ],
      order: [[ 0, "desc" ]],
      aoColumnDefs: [
      ],
      language:{

      }
    });
  });





</script>