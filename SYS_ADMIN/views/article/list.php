<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/13
 * Time: 8:57
 */


use SYS_ADMIN\assets\AppAsset;

$this->title = "文章管理";

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
    .show-img{width: 100px; height:  100px;}
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
                    文章管理
                </div>
                <div class="panel-body">
                        <div style="margin-bottom: 10px;">
                            <a href="<?php echo \yii\helpers\Url::to('/article/info') ?>" class="btn btn-primary">新增文章</a>
                        </div>
                    <table id="article_table"  class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>所属直播间</th>
                            <th>标题</th>
                            <th>封面图</th>
                            <th>点击量</th>
                            <th>排序值</th>
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

    $("#article_table").DataTable({
      ajax: '<?php echo \yii\helpers\Url::to('/article/index?api=true')?>',
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
        {"data": "title"},
        {"data": "pic_path"},
        {"data": "click_num"},
        {"data": "sort_num"},
        {"data": "status"},
      ],
      order: [[ 0, "desc" ]],
      aoColumnDefs: [
        {
          "targets": 7,
          "render" : function(data, type, row) {
            var html = '';
            html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"updateArticle('"+ row.id +"')\"> 编辑 </a>";
            html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"deleteArticle('"+ row.id +"')\"> 删除 </a>";
            return html;
          }
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
    return path ? "<img src=\""+path+"\" class=\"show-img\">" : "";
  }

  function deleteArticle(autoId)
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
            url: '<?php echo \yii\helpers\Url::to('/article/del')?>',
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

  function updateArticle(autoId = '')
  {
    var url = "<?php echo yii\helpers\Url::to('/article/info'); ?>";
    url += "?id="+autoId;
    window.location.href = url;
  }


</script>
