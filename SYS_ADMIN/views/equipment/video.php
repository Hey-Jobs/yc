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

AppAsset::addScript($this, '/vendor/jquery-validation/jquery.validate.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-validation/messages_zh.min.js?v=' . Yii::$app->params['versionJS']);

?>
<link rel="stylesheet" href="https://g.alicdn.com/de/prismplayer/2.8.1/skins/default/aliplayer-min.css" />
<script type="text/javascript" charset="utf-8" src="https://g.alicdn.com/de/prismplayer/2.8.1/aliplayer-min.js"></script>
<style>
    .modal-dialog{width: 850px}
    .modal-header{padding: 10px}
    .play-container{display: inline-block; width: 400px;}
    .play-image-container{display: inline-block; width: 285px;
        position: absolute;
        top: 20px;
        margin-left: 20px;
    }
    .image-container{width: 100%; text-align: center; margin-bottom: 15px}
    .image-container img{width: 280px; height: 150px;}
    .play-image-container .control-label{line-height: 2.5}
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
                    <a href="<?php echo yii\helpers\Url::to('/equipment/index') ?>" class="btn btn-primary">返回列表</a>
                </div>
                <div class="panel-body">

                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="snapshot_form" method="post" >
                                    <div class="color-line"></div>
                                    <div class="modal-header text-center">
                                        <h4 class="modal-title"><span id="btnText">视频截图</span></h4>
                                    </div>
                                    <div class="modal-body" style="300px;">
                                        <div id="player-con" class="play-container"></div>
                                        <div class="play-image-container">

                                                <div class="image-container">
                                                    <img id="snapshot" src=""/>
                                                </div>
                                                <div class="form-group row text-left">
                                                    <label class="col-sm-3 control-label position">标题</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" name="title" placeholder="标题"/>
                                                    </div>
                                                </div>
                                                <div class="form-group row text-left">
                                                    <label class="col-sm-3 control-label position">直播间</label>
                                                    <div class="col-sm-9">
                                                        <?= $room_html?>
                                                    </div>
                                                </div>
                                                <div class="form-group row text-left">
                                                    <label class="col-sm-3 control-label position">排序</label>
                                                    <div class="col-sm-9">
                                                        <input type="number" min="1" max="1000" class="form-control" name="sort_num" value="10">
                                                        数值越小靠前
                                                    </div>
                                                </div>
                                                <div class="form-group row text-left">
                                                    <label class="col-sm-3 control-label position">备注</label>
                                                    <div class="col-sm-9">
                                                        <textarea class="form-control" name="remark" placeholder="备注"></textarea>
                                                    </div>
                                                </div>

                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <input type="hidden" name="cover" id="cover"/>
                                        <button type="button" class="btn btn-default" onclick="closeSnapshot()">关闭窗口</button>
                                        <button type="button" class="btn btn-primary" onclick="saveSnapshot()">上传封面</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

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
    $("#snapshot").hide();
    $("#snapshot_form").validate({
      rules:{
        title:{
          required: true,
        },
        room_id:{
          required: true,
        },
        cover:{
          required: true,
        }
      },

    });

    $("#equipment_video_table").DataTable({
      ajax: '<?php echo \yii\helpers\Url::to(['/equipment/video',
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
            //html+= "<a  target='_blank' href='"+row.uri+"'> "+row.uri+" </a>";
            html += "<a  target='_blank' href='javascript:;' onclick='showVideo(\""+row.uri+"\")'> "+row.uri+" </a>"
            return html;
          }
        },
      ],
    });
  });


    function showVideo(video_url){
      player = new Aliplayer({
        id: "player-con",
        source: video_url,
        width: "500px",
        height: "380px",
        autoplay: true,
        isLive: false,
        "extraInfo": {
          "crossOrigin": "anonymous"
        },
        "skinLayout": [
          { "name": "bigPlayButton", "align": "blabs", "x": 30, "y": 80 },
          { "name": "H5Loading", "align": "cc" },
          { "name": "errorDisplay", "align": "tlabs", "x": 0, "y": 0 },
          { "name": "infoDisplay" },
          { "name": "thumbnail" },
          {
            "name": "controlBar", "align": "blabs", "x": 0, "y": 0,
            "children": [
              { "name": "progress", "align": "blabs", "x": 0, "y": 44 },
              { "name": "playButton", "align": "tl", "x": 15, "y": 12 },
              { "name": "timeDisplay", "align": "tl", "x": 10, "y": 7 },
              { "name": "fullScreenButton", "align": "tr", "x": 10, "y": 12 },
              { "name": "snapshot", "align": "tr", "x": 0, "y": 9 }
            ]
          }
        ]
      });
      /* h5截图按钮, 截图成功回调 */
      player.on('snapshoted', function (data) {
        $.ajax({
          type:'POST',
          url : '<?php echo yii\helpers\Url::to('/upload/img-base64')?>',
          data : {'img': data.paramData.base64},
          dataType: 'JSON',
          success: function(result) {
            if ('200' == result.status) {
              $("#snapshot").attr('src', result.data.img_path);
              $("#cover").val(result.data.images);
              $("#snapshot").show();
            } else {
              affirmSwals('失败', result.message, 'error', placeholder);
            }
          },
        });
      })
      $('#myModal').modal('show');
    }


    function saveSnapshot(){
      if(!$("#snapshot_form").valid()){
        return false;
      }

      if(!$("[name='room_id']").val()){
        affirmSwals('失败', "请选择直播间", 'error', placeholder);
        return false;
      }

      if(!$("#cover").val()){
        affirmSwals('失败', "请先截图", 'error', placeholder);
        return false;
      }


      $.ajax({
        type:'POST',
        dataType: 'json',
        url : '<?php echo yii\helpers\Url::to('/snapshot/upload')?>',
        data : $("#snapshot_form").serialize(),
        success: function(result) {
          if ('200' == result.status) {
            affirmSwals('成功', '成功', 'success', function () {
              $("#cover").val("");
              $("[name='title']").val("");
              $("[name='remark']").val("");
              $("#snapshot").hide();
            });
          } else {
            affirmSwals('失败', result.message, 'error', placeholder);
          }
        },
      });
    }
    
    function closeSnapshot() {
      player.dispose();
      $("#cover").val("");
      $("[name='title']").val("");
      $("[name='remark']").val("");
      $("#snapshot").hide();
      $("#myModal").modal('hide');
    }
</script>