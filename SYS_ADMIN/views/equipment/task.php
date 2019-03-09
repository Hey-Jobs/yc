<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/9
 * Time: 10:34
 */

use SYS_ADMIN\assets\AppAsset;

$this->title = "定时推断流";

AppAsset::addScript($this, '/vendor/data-tables/js/jquery.dataTables.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/data-tables/js/dataTables.bootstrap.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/data-tables/css/dataTables.bootstrap.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert-extend.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/sweetalert/css/sweet-alert.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-validation/jquery.validate.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-validation/messages_zh.min.js?v=' . Yii::$app->params['versionJS']);

AppAsset::addScript($this, '/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.zh-CN.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css?v=' . Yii::$app->params['versionJS']);


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
                    <p>
                        <a href="<?php echo yii\helpers\Url::to('/equipment/index') ?>" class="btn btn-primary">返回列表</a>
                        <button type="button" class="btn btn-success" id="add-task-btn" data-toggle="modal" data-target="#myModal" onclick="updateTask()">添加信息</button>
                    </p>

                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="task-form" method="post" >
                                    <div class="color-line"></div>
                                    <div class="modal-header text-center">
                                        <h4 class="modal-title"><span id="btnText">添加定时任务</span></h4>
                                    </div>
                                    <div class="modal-body" style="300px;">
                                        <form id="task_form" method="post" >
                                            <input type="hidden" name="equip_id" value="<?= $id?>"></input>
                                            <div class="form-group row text-left" style="display: none;">
                                                <div class="col-sm-9">
                                                    <input style="display: none" type="text" name="id" class="form-control params" placeholder="autoId">
                                                </div>
                                            </div>

                                            <div class="form-group row text-left">
                                                <label class="col-sm-3 control-label position">执行时间：</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" name="task_time"  id="task_time" placeholder="请选择执行时间"/>
                                                </div>
                                            </div>


                                            <div class="form-group row text-left">
                                                <label class="col-sm-3 control-label position">执行类型：</label>
                                                <div class="col-sm-9">
                                                    <label class="radio-inline">
                                                        <input type="radio" name="task_type" value="1" checked> 推流
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="task_type"  value="2" > 断流
                                                    </label>
                                                </div>
                                            </div>
                                        </form>

                                    </div>

                                    <div class="modal-footer">
                                        <input type="hidden" id="equip_id" value="<?= $id?>"/>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭窗口</button>
                                        <button type="button" class="btn btn-primary" onclick="saveTask()">保存信息</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <table id="task_table"  class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>执行时间</th>
                            <th>执行类型</th>
                            <th>添加时间</th>
                            <th>更新时间</th>
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
    var equipId = "<?= $id ?>";
    if(!equipId) {
      $("#add-task-btn").hide()
    }
    $("#task_time").datetimepicker({
      format: 'hh:ii',
      autoclose : true,
      startView:1,
      //maxView: 1,
      language: 'zh-CN',
      bootcssVer:1,
      minuteStep: 1,
      todayBtn: false,
    });

    $('#task_time').focus(function(){
      $(this).blur();//不可输入状态
    })

    $("#task-form").validate({
      rules:{

      },

    });

    $("#task_table").DataTable({
      ajax: '<?php echo \yii\helpers\Url::to('/equipment/task?api=true&id='.$id)?>',
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
        {"data": "task_time"},
        {"data": "task_type"},
        {"data": "created_time"},
        {"data": "updated_time"},
      ],
      order: [[ 1, "desc" ]],
      aoColumnDefs: [
        {
          "targets": 5,
          "render" : function(data, type, row) {
            var html = '';
            html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"updateTask('"+ row.id +"')\"> 编辑 </a>";
            html+= "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"deleteTask('"+ row.id +"')\"> 删除 </a>";
            return html;
          }
        }
      ],
    });
  });

  function deleteTask(autoId)
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
            url: '<?php echo \yii\helpers\Url::to('/equipment/task-del')?>',
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

  function updateTask(autoId = '')
  {
    if (autoId.length != 0) {
      var data;
      $("#btnText").html('修改定时任务');
      $.ajax({
        url: '<?php echo \yii\helpers\Url::to('/equipment/task-info')?>',
        dataType: 'json',
        type: "get",
        async : false,
        data: {'id' : autoId},
        success: function (result) {
          if (result.status == 200) {
            data = result.data;

            $("[name='id']").val(data.id);
            $("[name='task_time']").val(data.taskTimeStr);
            $("input[name='task_type'][value='"+data.task_type+"']").attr('checked', 'true');
          }
        }
      });

      $('#myModal').modal('show');

    } else {
      $("#btnText").html('添加定时任务');
    }
  }

  function saveTask()
  {
    if(!$("#task-form").valid()){
      return false;
    }

    $.ajax({
      type:'POST',
      dataType: 'json',
      url : '<?php echo yii\helpers\Url::to('/equipment/task-save')?>',
      data : $("#task-form").serialize(),
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

