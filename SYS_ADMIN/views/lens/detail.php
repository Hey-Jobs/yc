<?php
/**
 * User: liwj
 * Date:2018/11/1
 * Time:20:30
 */

use SYS_ADMIN\assets\AppAsset;

$this->title = count($info) > 0 ? "编辑镜头" : "新增镜头";

AppAsset::addScript($this, '/vendor/data-tables/js/jquery.dataTables.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/data-tables/js/dataTables.bootstrap.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/data-tables/css/dataTables.bootstrap.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert-extend.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/sweetalert/css/sweet-alert.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-validation/jquery.validate.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-validation/messages_zh.min.js?v=' . Yii::$app->params['versionJS']);
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
                    <?php echo count($info) > 0 ? "编辑镜头" : "新增镜头";?>
                </div>


                <div class="panel-body">
                    <form id="lens_form" method="post" action="<?php echo \yii\helpers\Url::to("/lens/save")?>" class="form-horizontal col-sm-10">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">镜头名称</label>

                            <div class="col-sm-10"><input type="text" class="form-control" name="lens_name" value="<?= $info['lens_name'] ?? "" ?>"></div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">镜头缩略图</label>

                            <div class="col-sm-10"><input type="text" class="form-control" name="cover_img" value="<?= $info['cover_img'] ?? "" ?>" ></div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">直播流地址</label>

                            <div class="col-sm-10"><input type="text" class="form-control" name="online_url" value="<?= $info['online_url'] ?? "" ?>"></div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">回放地址</label>

                            <div class="col-sm-10"><input type="text" class="form-control" name="playback_url" value="<?= $info['playback_url'] ?? "" ?>"></div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">背景音乐地址</label>

                            <div class="col-sm-10"><input type="text" class="form-control" name="bgm_url" value="<?= $info['bgm_url'] ?? "" ?>"></div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">精彩回放地址</label>

                            <div class="col-sm-10"><input type="text" class="form-control" name="marvellous_url" value="<?= $info['marvellous_url'] ?? "" ?>"></div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">排序值</label>

                            <div class="col-sm-2"><input type="number" class="form-control" name="sort_num" value="<?= $info['sort_num'] ?? 10?>"></div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">状态</label>

                            <div class="col-sm-10">
                                <div class="">
                                    <label> <input type="radio" id="status1" value="1" class="" <?php echo isset($info['status']) && $info['status'] != 2 ? "checked" : '' ?> > 显示</label>
                                    <label> <input type="radio" id="status2" value="2" class="" <?php echo isset($info['status']) && $info['status'] == 2 ? "checked" : '' ?> >隐藏</label>
                                </div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="col-sm-8 col-sm-offset-2">
                                <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                                <input type="hidden" name="id" value="<?= $info['id'] ?? 0 ?>"/>
                                <button class="btn btn-primary" type="button" id="sub-form">保存</button>
                                <a href="<?php echo yii\helpers\Url::to('/lens/list')?>" class="btn btn-default">取消</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript">
    $(function () {
        $("#status1").click(function () {
            $("#status1").attr("checked","checked");
            $("#status2").removeAttr("checked");
        });

        $("#status2").click(function () {
            $("#status2").attr("checked","checked");
            $("#status1").removeAttr("checked");
        });

        $("#lens_form").validate({
            rules:{
                lens_name:{
                  required: true,
                },
                online_url: {
                    required: true,
                    url: true
                },
                playback_url: {
                    required: true,
                    url: true
                },
                bgm_url:{
                    required: true,
                    url: true
                },
                marvellous_url:{
                    required: true,
                    url: true
                },
                number: {
                    required: true,
                    number: true
                },
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
        
        
        $("#sub-form").click(function () {
            $.ajax({
                type:'POST',
                dataType: 'json',
                url : '<?php echo yii\helpers\Url::to('/lens/save')?>',
                data : $("#lens_form").serialize(),
                success: function(result) {
                    if ('200' == result.status) {
                        affirmSwals('成功', '成功', 'success', confirmFunc);
                    } else {
                        affirmSwals('失败', result.message, 'error', placeholder);
                    }
                },
            });
        });

    });

</script>
