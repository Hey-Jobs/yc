<?php
/**
 * User: liwj
 * Date:2018/11/1
 * Time:20:30
 */

use SYS_ADMIN\assets\AppAsset;
use yii\widgets\ActiveForm;

$this->title = count($info) > 0 ? "编辑镜头" : "新增镜头";

AppAsset::addScript($this, '/vendor/data-tables/js/jquery.dataTables.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/data-tables/js/dataTables.bootstrap.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/data-tables/css/dataTables.bootstrap.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert-extend.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/sweetalert/css/sweet-alert.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-validation/jquery.validate.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-validation/messages_zh.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/bootstrap-fileinput/css/fileinput.min.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/bootstrap-fileinput/js/fileinput.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/bootstrap-fileinput/js/zh.js?v=' . Yii::$app->params['versionJS']);

?>


<style type="text/css">
    .position {
        padding-top: 7px;
        margin-bottom: 0;
    }
    .tip-require{color: red; margin-left: 5px;}
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
                    <a href="<?php echo \yii\helpers\Url::to('/lens/list') ?>">镜头管理</a> /
                    <?php echo count($info) > 0 ? "编辑镜头" : "新增镜头"; ?>
                </div>

                <ul id="myTab" class="nav nav-tabs">
                    <li class="active">
                        <a href="#home" >
                            基本
                        </a>
                    </li>
                    <?php if(count($info) > 0) :?>
                        <li><a href="<?php echo \yii\helpers\Url::to('/lens/ext?id='.$info['id'])?>">扩展</a></li>
                    <?php endif; ?>
                </ul>
                
                <div class="panel-body">
                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'lens_form',
                        'options' => ['class' => 'form-horizontal col-sm-10', 'enctype' => 'multipart/form-data'],
                        'fieldConfig' => [
                            'template' => '<div class="form-group">
                                                        <label class="col-sm-2 control-label">{label}</label>
                            
                                                        <div class="col-sm-10">
                                                            {input}
                                                        </div>
                                                    </div>
                                                    <div class="hr-line-dashed"></div>'
                        ],
                    ]) ?>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">所属直播间<span class="tip-require">*</span></label>

                        <div class="col-sm-10">
                            <?= $room_html ?>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">镜头名称<span class="tip-require">*</span></label>

                        <div class="col-sm-10"><input type="text" class="form-control" name="lens_name"
                                                      value="<?= $info['lens_name'] ?? "" ?>"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">服务器管理<span class="tip-require">*</span></label>

                        <div class="col-sm-10">
                            <select class="form-control" id="server_item">
                                <option>请选择服务器</option>
                                <?php foreach ($server_list as $item) :?>
                                    <option data-stream="<?= $item['stream_addr']?>" data-oss="<?= $item['oss_addr']?>"><?= $item['title']?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">设备app name<span class="tip-require">*</span></label>

                        <div class="col-sm-10"><input type="text" class="form-control" name="app_name"
                                                      value="<?= $info['app_name'] ?? "live" ?>"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">设备stream name<span class="tip-require">*</span></label>

                        <div class="col-sm-10"><input type="text" class="form-control" name="stream_name"
                                                      value="<?= $info['stream_name'] ?? "" ?>"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <!--<div class="form-group">
                        <label class="col-sm-2 control-label">镜头缩略图</label>

                        <div class="col-sm-10">
                            <input type="file" class="form-control" name="pcover_img" id="pcover_img"
                                                      value="<?/*= $info['cover_img'] ?? "" */?>">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>-->

                    <div class="form-group">
                        <label class="col-sm-2 control-label">直播流地址<span class="tip-require">*</span></label>

                        <div class="col-sm-10"><input type="text" class="form-control" name="online_url"
                                                      value="<?= $info['online_url'] ?? "" ?>"></div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">直播流封面地址<span class="tip-require">*</span></label>

                        <div class="col-sm-10"><input type="text" class="form-control" name="online_cover_url"
                                                      value="<?= $info['online_cover_url'] ?? "" ?>"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">回放地址<span class="tip-require">*</span></label>

                        <div class="col-sm-10"><input type="text" class="form-control" name="playback_url"
                                                      value="<?= $info['playback_url'] ?? "" ?>"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">回放封面图<span class="tip-require">*</span></label>

                        <div class="col-sm-10"><input type="text" class="form-control" name="marvellous_url"
                                                      value="<?= $info['marvellous_url'] ?? "" ?>"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">mac地址</label>

                        <div class="col-sm-10"><input type="text" class="form-control" name="mac_address"
                                                      value="<?= $info['mac_address'] ?? "" ?>"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">备用播放地址</label>

                        <div class="col-sm-10"><input type="text" class="form-control" name="spare_url"
                                                      value="<?= $info['spare_url'] ?? "" ?>"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">备用播放封面</label>

                        <div class="col-sm-10"><input type="text" class="form-control" name="spare_cover_url"
                                                      value="<?= $info['spare_cover_url'] ?? "" ?>"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">背景音乐地址</label>

                        <div class="col-sm-10"><input type="text" class="form-control" name="bgm_url"
                                                      value="<?= $info['bgm_url'] ?? "" ?>"></div>
                    </div>
                    <div class="hr-line-dashed"></div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label">直播音乐地址</label>

                        <div class="col-sm-10"><input type="text" class="form-control" name="live_music"
                                                      value="<?= $info['live_music'] ?? "" ?>"></div>
                    </div>
                    <div class="hr-line-dashed"></div>


                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">排序值</label>

                        <div class="col-sm-2">
                            <input type="number" min="1" max="1000" class="form-control" name="sort_num"
                                   value="<?= $info['sort_num'] ?? 10 ?>">
                            数值越小靠前
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">状态</label>

                        <div class="col-sm-10">
                            <div class="">
                                <?php if (count($info)): ?>
                                    <label> <input type="radio" name="status" id="status1" value="1"
                                                   class="" <?php echo $info['status'] != 2 ? "checked" : "" ?>>
                                        显示</label>
                                    <label> <input type="radio" name="status" id="status2" value="2"
                                                   class="" <?php echo $info['status'] == 2 ? "checked" : "" ?> >隐藏</label>
                                <?php else : ?>
                                    <label> <input type="radio" name="status" id="status1" value="1" class="" checked>
                                        显示</label>
                                    <label> <input type="radio" name="status" id="status2" value="2" class="">隐藏</label>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-8 col-sm-offset-2">
                            <input type="hidden" name="cover_img" id="cover_img"
                                   value="<?= $info['cover_img'] ?? '' ?>"/>
                            <input type="hidden" name="id" value="<?= $info['id'] ?? 0 ?>"/>
                            <button class="btn btn-primary" type="button" id="sub-form">保存</button>
                            <a href="<?php echo yii\helpers\Url::to('/lens/list') ?>" class="btn btn-default">返回列表</a>
                        </div>
                    </div>
                    <?php ActiveForm::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript">
  var stream_addr = "";
  var oss_addr = "";
    $(function () {
        $("#pcover_img").fileinput({
            language: 'zh', //设置语言
            uploadUrl: '', //上传的地址
            allowedFileExtensions: ['png', 'jpg'],//接收的文件后缀
            uploadAsync: true, //默认异步上传
            showUpload: false, //是否显示上传按钮
            showRemove: true, //显示移除按钮
            showPreview: true, //是否显示预览
            showCaption: true,//是否显示标题
            browseClass: "btn btn-primary", //按钮样式
            dropZoneEnabled: false,//是否显示拖拽区域
            //minImageWidth: 50, //图片的最小宽度
            //minImageHeight: 50,//图片的最小高度
            //maxImageWidth: 1000,//图片的最大宽度
            //maxImageHeight: 1000,//图片的最大高度
            //maxFileSize: 0,//单位为kb，如果为0表示不限制文件大小
            //minFileCount: 0,
            maxFileCount: 1, //表示允许同时上传的最大文件个数,
            <?php if(count($pic_info)): ?>
            initialPreviewAsData: true,
            initialPreview: [
                "/<?= $pic_info['pic_path'] ?? '' ?>",
            ],
            initialPreviewConfig: [
                {
                    caption: "<?= $pic_info['pic_name'] ?? '' ?>",
                    size: "<?= $pic_info['pic_size'] ?? '' ?>",
                    width: "120px",
                    url: "{$url}",
                    key: 1,
                    showRemove: false,
                },
            ]
            <?php endif;?>
        }).on("filebatchselected", function (event, files) {
            $(this).fileinput("upload");
        })
            .on("fileuploaded", function (event, data) {
                if (data.response) {
                    alert('处理成功');
                }
            });
        ;

        $("#status1").click(function () {
            $("#status1").attr("checked", "checked");
            $("#status2").removeAttr("checked");
        });

        $("#status2").click(function () {
            $("#status2").attr("checked", "checked");
            $("#status1").removeAttr("checked");
        });

        $("#lens_form").validate({
            rules: {
              stream_name: {
                required: true,
              },
              app_name: {
                required: true,
              },
                lens_name: {
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
                bgm_url: {
                    //required: true,
                    url: true
                },
              spare_url:{
                url: true,
              },
              spare_cover_url:{
                url: true,
              },
                marvellous_url: {
                    required: true,
                    url: true
                },
                number: {
                    required: true,
                    number: true
                },
            },

        });


        $("#sub-form").click(function () {
            if ($("#lens_form").valid()) {
                if ($("#cover_img").val() == '' && $("#pcover_img").val() == '') {
                    affirmSwals('失败', "请上传封面图片", 'error', placeholder);
                    return false;
                }

                var form_data = new FormData($("#lens_form")[0]);
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: '<?php echo yii\helpers\Url::to('/lens/save')?>',
                    data: form_data,
                    async: false,
                    async: false,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (result) {
                        if ('200' == result.status) {
                            affirmSwals('成功', '成功', 'success', confirmFunc);
                        } else {
                            affirmSwals('失败', result.message, 'error', placeholder);
                        }
                    },
                });
            }

        });

        $("#server_item").on("change", function () {
          stream_addr = $(this).find("option:selected").data("stream");
          oss_addr = $(this).find("option:selected").data("oss");
          changeServerVa();
        });

      $("input[name='app_name']").change(function () {
        changeServerVa();
      });

      $("input[name='stream_name']").change(function () {
        changeServerVa();
      });
    });
    
    function changeServerVa() {
      var app_name = $("input[name='app_name']").val();
      var stream_name = $("input[name='stream_name']").val();
      if (stream_name && app_name && stream_addr && oss_addr) {
        $("input[name='online_cover_url']").val(oss_addr+"/"+app_name+"/"+stream_name+".jpg");
        $("input[name='marvellous_url']").val(oss_addr+"/"+app_name+"/"+stream_name+".jpg");
        $("input[name='online_url']").val(stream_addr+"/"+app_name+"/"+stream_name+".m3u8");
        $("input[name='playback_url']").val(stream_addr+"/"+app_name+"/"+stream_name+".m3u8");
      }
    }

</script>
