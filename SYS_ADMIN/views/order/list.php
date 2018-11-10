<?php

use SYS_ADMIN\assets\AppAsset;

$this->title = "订单管理";

AppAsset::addScript($this, '/vendor/data-tables/js/jquery.dataTables.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/data-tables/js/dataTables.bootstrap.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/data-tables/css/dataTables.bootstrap.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert-extend.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/sweetalert/css/sweet-alert.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/select2/css/select2.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/select2/js/select2.full.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/select2/js/select2-form-extend.js?v=' . Yii::$app->params['versionJS']);
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
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true"
                         data-keyboard="false" data-backdrop="static">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="data-form" method="post">
                                    <div class="color-line"></div>
                                    <div class="modal-header text-center">
                                        <h4 class="modal-title"><span id="btnText">发货操作</span></h4>
                                    </div>
                                    <div class="modal-body" style="300px;">
                                        <div class="form-group row text-left" style="display: none;">
                                            <div class="col-sm-9"><input style="display: none" type="hidden" name="id"
                                                                         class="form-control params"></div>
                                        </div>
                                        <div class="form-group row text-left">
                                            <label class="col-sm-3 control-label position">快递公司：</label>
                                            <div class="col-sm-9">
                                                <?=\SYS_ADMIN\components\SearchWidget::instance()->express('express_id', '', '--快递选择--')?>
                                            </div>
                                        </div>
                                        <div class="form-group row text-left">
                                            <label class="col-sm-3 control-label position">快递单号：</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="express_no"
                                                       placeholder="快递单号"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭窗口</button>
                                        <button type="button" class="btn btn-primary" onclick="saveInfo()">确认发货
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <table id="data_table" class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                        <tr>
                            <th>订单编号</th>
                            <th>归属直播间</th>
                            <th>收货人</th>
                            <th>收获地址</th>
                            <th>收获号码</th>
                            <th>快递公司</th>
                            <th>快递单号</th>
                            <th>下单时间</th>
                            <th>订单状态</th>
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
        $("#data_table").DataTable({
            ajax: '<?php echo \yii\helpers\Url::to('/order/index?api=true')?>',
            bAutoWidth: false,
            ordering: true,
            oLanguage: {
                oPaginate: {
                    sPrevious: "上一页",
                    sNext: "下一页"
                }
            },
            columns: [
                {"data": "order_id"},
                {"data": "room_name"},
                {"data": "user_name"},
                {"data": "user_address"},
                {"data": "user_phone"},
                {"data": "express_name"},
                {"data": "express_no"},
                {"data": "create_time"},
                {"data": "order_status_name"},
            ],
            aoColumnDefs: [
                {
                    "targets": 9,
                    "render": function (data, type, row) {
                        var html = '';
                        html += "<a href=\"javascript:void(0);\" class=\"m-l-sm\" onclick=\"updateInfo('" + row.id + "')\"> 发货 </a>";
                        return html;
                    }
                }
            ],
        });
    });

    function updateInfo(autoId = '') {
        if (autoId.length > 0) {
            var data;
            $.ajax({
                url: '<?php echo \yii\helpers\Url::to('/order/one')?>',
                dataType: 'json',
                type: "GET",
                async: false,
                data: {'id': autoId},
                success: function (result) {
                    if (result.status == 200) {
                        data = result.data;
                        $("[name='id']").val(data.id);
                        $("[name='express_id']").val(data.express_id).trigger('change');
                        $("[name='express_no']").val(data.express_no);
                    }
                }
            });
            $('#myModal').modal('show');
        }
    }

    function saveInfo() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '<?php echo yii\helpers\Url::to('/order/send')?>',
            data: $("#data-form").serialize(),
            success: function (result) {
                if (200 == result.status) {
                    affirmSwals('成功', '成功', 'success', confirmFunc);
                } else {
                    affirmSwals('失败', result.message, 'error', placeholder);
                }
            },
        });
    }
</script>
