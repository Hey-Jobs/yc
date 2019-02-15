<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/3
 * Time: 11:59
 */

use SYS_ADMIN\assets\AppAsset;

$this->title = "商品管理";

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
                    商品管理
                </div>
                <div class="panel-body">
                    <table id="lens_table"  class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>直播间名称</th>
                            <th>商品名称</th>
                            <th>商品封面图</th>
                            <th>库存量</th>
                            <th>点击量</th>
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
            ajax: '<?php echo \yii\helpers\Url::to('/product/index?api=true')?>',
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
                {"data": "stock"},
                {"data": "click_num"},
                {"data": "status"},
            ],
            order: [[ 0, "desc" ]],
            aoColumnDefs: [
                {
                    "targets": 7,
                    "render" : function(data, type, row) {
                        var html = '';
                        html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"updateProduct('"+ row.id +"')\"> 编辑 </a>";
                        html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"deleteProduct('"+ row.id +"')\"> 删除 </a>";
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



    function updateProduct(autoId = '')
    {
        var url = "<?php echo yii\helpers\Url::to('/product/info'); ?>";
        url += "?id="+autoId;
        window.location.href = url;
    }

    function deleteProduct(autoId) {
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
              url: '<?php echo \yii\helpers\Url::to('/product/delete')?>',
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
</script>
