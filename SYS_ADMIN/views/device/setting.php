<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>直播管理系统V9.0-TX01云直播</title>
    <!-- CSS -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
    <link rel="stylesheet" href="/static/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/assets/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/static/assets/css/form-elements.css">
    <link rel="stylesheet" href="/static/assets/css/style.css">
    <link rel="shortcut icon" href="/static/assets/ico/favicon.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/static/assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/static/assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/static/assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="/static/assets/ico/apple-touch-icon-57-precomposed.png">
    <style>
        .step2,.step3, .step4{display: none}
        .form-top-left{position: relative}
        .form-top-left .refresh-button{position: absolute; right: 0; top: 20px;}
    </style>
</head>
<body>
<!-- Top content -->
<div class="top-content">

    <div class="inner-bg">
        <div class="container">
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2 text">
                    <h1><strong>云直播</strong> 直播管理系统</h1>
                    <div class="description">
                        <p>
                            本系统为内部使用，请勿外传！
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3 form-box step1">
                    <div class="form-top">
                        <div class="form-top-left">
                            <h3>服务器：<?php echo $sid; ?>云直播</h3>
                            <p>输入设备UID<a href=""> 注:见设备/包装标签</a></p>
                        </div>
                        <div class="form-top-right">
                            <i class="fa fa-key"></i>
                        </div>
                    </div>
                    <div class="form-bottom">
                        <div class="form-group">
                            <label class="sr-only" for="form-username">Username</label>
                            <input type="text" name="streamname"  value="SSSS-" class="form-username form-control" id="streamname">
                        </div>
                        <button type="button" class="btn" onclick="getStreamInfo()">提 交</button>
                    </div>
                </div>

                <div class="col-sm-6 col-sm-offset-3 form-box step2">
                    <div class="form-top">
                        <div class="form-top-left" style="width:100%">
                            <h3><span class="stream-name">SSSS-</span> &nbsp&nbsp<a href="javascript:;" onclick="initDevice()">切换设备</a></h3>
                            <p>状态： <a href="javascript:;"><span class="stream-status"></span></a>
                                &nbsp&nbsp时间： <span class="stream-time"></span> <br></p>
                            <button class="refresh-button btn" onclick="refreshState()">
                                刷新
                            </button>
                        </div>
                    </div>
                    <div class="form-bottom-2"  align="center">
                        <a href="javascript:;" onclick="showrtmp()">一键自动直播设置</a>
                    </div>
                    <div class="form-bottom"  align="center">
                        <a href="javascript:;" onclick="showdiy()">自定义直播设置</a>
                    </div>
                </div>

                <div class="col-sm-6 col-sm-offset-3 form-box step3">
                    <div class="form-top">
                        <div class="form-top-left" style="width:100%">
                            <h3><span class="stream-name">SSSS-</span> &nbsp&nbsp<a href="javascript:;" onclick="initDevice()">切换设备</a></h3>
                            <p>状态： <a href="javascript:;"><span class="stream-status"></span></a>
                                &nbsp&nbsp时间： <span class="stream-time"></span> <br></p>
                            <button class="refresh-button btn" onclick="refreshState()">
                                刷新
                            </button>
                        </div>
                    </div>
                    <div class="form-bottom-2">已生成自动推流地址
                        <div class="form-group">
                            <label class="sr-only" for="form-username">Username</label>

                            <input type="text" name="rtmp-url" value="" class="form-username form-control" id="rtmp-url">
                            <p>
                                M3U8播放地址<br>
                                <a href="javascript:;"><span class="m3u8-url"></span></a></p>
                        </div>
                        <button type="button" class="btn" onclick="pushRtmpUrl()">设 置</button>
                    </div>
                </div>

                <div class="col-sm-6 col-sm-offset-3 form-box step4">
                    <div class="form-top">
                        <div class="form-top-left" style="width:100%">
                            <h3><span class="stream-name">SSSS-</span> &nbsp&nbsp<a href="javascript:;" onclick="initDevice()">切换设备</a></h3>
                            <p>状态： <a href="javascript:;"><span class="stream-status"></span></a>
                                &nbsp&nbsp时间： <span class="stream-time"></span> <br></p>
                            <button class="refresh-button btn" onclick="refreshState()">
                                刷新
                            </button>
                        </div>
                    </div>
                    <div class="form-bottom-2">请输入推流地址
                        <div class="form-group">
                            <label class="sr-only" for="form-username">Username</label>
                            <input type="text" name="diy-rtmp-url" value="rtmp://" class="form-username form-control" id="form-username">
                        </div>
                        <button type="button" class="btn" onclick="pushDiyUrl()">设 置</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
<div class="copyrights">Collect from <a href="javascript:void(0)"  title="云窗在线">云窗在线</a></div>

<!-- Javascript -->
<script src="/static/assets/js/jquery-1.11.1.min.js"></script>
<script src="/static/assets/bootstrap/js/bootstrap.min.js"></script>
<script src="/static/assets/js/jquery.backstretch.min.js"></script>
<script src="/static/assets/js/scripts.js"></script>

<!--[if lt IE 10]>
<script src="/static/assets/js/placeholder.js"></script>
<![endif]-->

<script>
    var streamname;
    function getStreamInfo() {
        streamname = $("#streamname").val().trim();
        var uid = streamname.replace("SSSS-", "");
        var sid = "<?php echo $sid;?>";
        if (!uid) {
            return false;
        }

        $(".stream-name").html(streamname);
        // 获取流信息
        $.ajax({
            url: '<?php echo \yii\helpers\Url::to('/api/device-info')?>',
            dataType: 'json',
            type: "POST",
            data: {'uid' : streamname, 'sid': sid},
            success: function (result) {
                if (result.status == 200) {
                    $(".step1").hide();
                    $(".step2").show();
                    $(".stream-status").text(result.data.status);
                    $(".stream-time").text(result.data.status_time);
                    $(".m3u8-url").text(result.data.online_url);
                    $("#rtmp-url").val(result.data.rtmp_url);
                }
            }
        });
    }

    function initDevice() {
        $(".step1").show();
        $(".step2").hide();
        $(".step3").hide();
        $(".step4").hide();
    }

    function showInfo() {
        $(".step2").show();
        $(".step3").hide();
        $(".step4").hide();
    }

    function showrtmp() {
        $(".step3").show();
        $(".step2").hide();
    }

    function showdiy() {
        $(".step4").show();
        $(".step2").hide();
    }

    function pushDiyUrl() {
        setUrl($("input[name='diy-rtmp-url']").val());
    }

    function pushRtmpUrl() {
        setUrl($("input[name='rtmp-url']").val());
    }
    function setUrl(pushurl) {
        $.ajax({
            url: '<?php echo \yii\helpers\Url::to('/api/device-push')?>',
            dataType: 'json',
            type: "POST",
            data: {'uid' : streamname, 'pushurl': encodeURIComponent(pushurl)},
            success: function (result) {
                alert("设置成功");
                $(".step3").hide();
                $(".step4").hide();
                $(".step2").show();
            }
        });
    }

    function refreshState() {
        $.ajax({
            url: '<?php echo \yii\helpers\Url::to('/api/device-state')?>',
            dataType: 'json',
            type: "POST",
            data: {'uid' : streamname},
            success: function (result) {
                $(".stream-status").text(result.data.status);
                $(".stream-time").text(result.data.status_time);
            }
        });
    }
</script>
</body>

</html>