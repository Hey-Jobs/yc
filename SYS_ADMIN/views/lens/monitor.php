<?php

use SYS_ADMIN\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title =  "镜头监控" ;

AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/sweetalert/js/sweet-alert-extend.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addCss($this, '/vendor/sweetalert/css/sweet-alert.css?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-validation/jquery.validate.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-validation/messages_zh.min.js?v=' . Yii::$app->params['versionJS']);
AppAsset::addScript($this, '/vendor/jquery-page/jqPaginator.js?v=' . Yii::$app->params['versionJS']);


$animateIcon = ' <i class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></i>';

?>

<link rel="stylesheet" href="https://g.alicdn.com/de/prismplayer/2.8.1/skins/default/aliplayer-min.css" />
<script type="text/javascript" charset="utf-8" src="https://g.alicdn.com/de/prismplayer/2.8.1/aliplayer-min.js"></script>
<style >
    .hpanel .panel-body{padding: 20px; padding-right: 0;}
    .monitor-main-box{width: 100%; position: relative}
    .monitor-main-box .inline-block{display: inline-block !important;}
    .lens-container, .lens-container .col-sm-5,.lens-container .col-sm-1{padding: 0 !important;}
    .lens-container select{height: 400px; }
    .monitor-name{color: #3498DB; font-size: 14px; margin-right: 20px;}
    .monitor-page{position: absolute; right: 0; top: -10px; padding: 0; margin: 0}
    .monitor-intro select{margin-left: 10px; margin-right: 10px;}
    .video-item{
        padding: 0 !important;
        margin: 5px 0px;
    }
    .monitor-fullscreen{cursor: pointer; color: red;}
    .prism-player{position: relative !important;}
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
                    镜头在线预览
                </div>

                <div class="panel-body">
                    <div class="row monitor-main-box">
                        <div class="lens-container inline-block col-sm-4" id="lens-container">
                            <div class="col-sm-5">
                                <div>所属在线镜头</div>
                                <input class="form-control search" data-target="available"
                                       placeholder="搜索镜头">
                                <select multiple size="20" class="form-control list" data-target="available">
                                </select>
                            </div>
                            <div class="col-sm-1">
                                <br><br>
                                <?=Html::a('&gt;&gt;' . $animateIcon, '', [
                                    'class' => 'btn btn-success btn-assign',
                                    'data-target' => 'available',
                                    'title' => '',
                                ]);?><br><br>
                                <?=Html::a('&lt;&lt;' . $animateIcon, '', [
                                    'class' => 'btn btn-danger btn-assign',
                                    'data-target' => 'assigned',
                                    'title' => '',
                                ]);?>
                            </div>
                            <div class="col-sm-5">
                                <div>预览轮巡镜头</div>
                                <input class="form-control search" data-target="assigned"
                                       placeholder="搜索镜头">
                                <select multiple size="20" class="form-control list" data-target="assigned">
                                </select>
                            </div>
                        </div>
                        <div class="online-container inline-block col-sm-8">
                            <div class="monitor-tab">
                                <div class="inline-block monitor-name">轮播画面</div>
                                <div class="inline-block monitor-fullscreen">
                                    <button  class="btn btn-primary" id="fullscreen" onClick="handleFullScreen()">全屏</button>
                                </div>
                                <div class="inline-block monitor-intro">
                                    自动轮巡
                                    <select class="changeTime" id="changeTime">
                                        <option value="60">60秒</option>
                                        <option value="120">120秒</option>
                                        <option value="180">180秒</option>
                                    </select>
                                    选中镜头超过9个，系统会自动轮巡
                                </div>
                                <div class="monitor-page pagination">
                                </div>
                            </div>
                            <div class="monitor-box row" id="monitor-box">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var pageNum = 9;
    var currentPage = 1;
    var pageTotal = 0;
    var changeTime = 60;
    var lens_list = [];
    var assigment = {};
    var videoPlayerConfig = {
        id: "player-con",
        source: "",
        width: '100%',
        height: "146px",
        isLive: true,
        autoplay: true,
        useH5Prism: true,
        playsinline: true,
        preload: true,
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
                    /*{ "name": "progress", "align": "blabs", "x": 0, "y": 44 },
                    { "name": "playButton", "align": "tl", "x": 15, "y": 12 },
                    { "name": "timeDisplay", "align": "tl", "x": 10, "y": 7 },*/
                    { "name": "fullScreenButton", "align": "tr", "x": 10, "y": 12 },
                ]
            }
        ]
    };
    var playGroup = {
        "player1": "",
        "player2": "",
        "player3": "",
        "player4": "",
        "player5": "",
        "player6": "",
        "player7": "",
        "player8": "",
        "player9": "",
    };

    $(function () {
        var _opts = {};
        $.ajax({
            url: '<?php echo \yii\helpers\Url::to('/lens/monitor?api=true')?>',
            dataType: 'json',
            type: "POST",
            success: function (result) {
                if (result.status == 200) {
                    lens_list = result.data;
                    search('available');
                } else {
                    affirmSwals('Deleted!', result.message, 'error', confirmFunc);
                }
            }
        });

        initVideo();
        $('i.glyphicon-refresh-animate').hide();
        function updateItems() {
            search('available');
            search('assigned');
            initPage();
            initVideo(currentPage);
        }

        $('.btn-assign').click(function () {
            var $this = $(this);
            var target = $this.data('target');
            var items = $('select.list[data-target="' + target + '"]').val();
            if (items && items.length) {
                $.each(items, function (index, item) {
                    if (target == "available") {
                        assigment[item] = lens_list[item];
                        delete lens_list[item];
                    } else {
                        lens_list[item] = assigment[item];
                        delete assigment[item];
                    }

                })
                updateItems();
            }
            return false;
        });

        $('.search[data-target]').keyup(function () {
            search($(this).data('target'));
        });

        function search(target) {
            var $list = $('select.list[data-target="' + target + '"]');
            $list.html('');
            var q = $('.search[data-target="' + target + '"]').val();

            var groups = {};
            var data = target == "available" ? lens_list : assigment;
            $.each(data, function (index, item) {
                // 检测分组是否存在
                if (!groups.hasOwnProperty(item.room_id) && item.room_name != null) {
                    groups[item.room_id] = [$('<optgroup label="'+item.room_name+'">'), false]
                }

                if (item.lens_name !=null && item.lens_name.indexOf(q) >= 0) {
                    $('<option>').text(item.lens_name).val(item.id).appendTo(groups[item.room_id][0]);
                    groups[item.room_id][1] = true;
                }
            });
            $.each(groups, function () {
                if (this[1]) {
                    $list.append(this[0]);
                }
            });
        }

        function initPage() {
            pageTotal = Math.ceil(Object.getOwnPropertyNames(assigment).length / pageNum);

            $('.monitor-page').jqPaginator({
                totalPages: pageTotal,
                visiblePages: 5,
                currentPage: 1,
                first: "",
                last: "",
                prev: "<li class=\"prev\"><a href=\"javascript:;\">上一页</a></li>",
                next: "<li class=\"next\"><a href=\"javascript:;\">下一页</a></li>",
                page: '<li class="page"><a href="javascript:;">{{page}}</a></li>',
                onPageChange: function (num, type) {
                    currentPage = num;
                    console.log(num);
                    initVideo(num);
                }
            });
        }

        function initVideo(pageIndex) {
            var sourceVideo = "http://player.alicdn.com/video/aliyunmedia.mp4";
            videoPlayerConfig.source = sourceVideo;

            //videoPlayerConfig.height = (100% * 9 ) / 16;
            // 1、清空之前节点
            for(var i = 1; i <= 9; i++) {
                if (playGroup["player"+i]) {
                    delete playGroup["player"+i]
                }
            }
            $("#monitor-box").empty();
            // 2、动态渲染节点
            if (pageTotal > 0) {
                var itemIndex = 0;
                var itemHtml = ""
                var assigmentKey = Object.keys(assigment);
                for (var i = 0; i < pageNum; i++) {
                    itemHtml = "";
                    assigmentIndex = (pageIndex - 1)* pageNum + i;
                    itemIndex = assigmentKey[assigmentIndex];
                    if (itemIndex != null && assigment[itemIndex] != null && assigment[itemIndex].lens_name) {
                        itemHtml += "<div class=\"col-sm-4 video-item\">\n" +
                            "                                    <div class=\"video-name\">"+assigment[itemIndex].lens_name+"</div>\n" +
                            "                                    <div id=\"player-con"+(i + 1)+"\" class=\"play-container video-item-player\"></div>\n" +
                            "                                </div>"
                        $("#monitor-box").append(itemHtml);
                        var itemWidth = $(".video-item").width() - 10;
                        // 3、渲染视频
                        videoPlayerConfig.width = itemWidth + "px";
                        videoPlayerConfig.height = itemWidth * 0.5625 + "px";
                        videoPlayerConfig.id = "player-con"+(i + 1);
                        videoPlayerConfig.source = assigment[itemIndex].online_url;
                        var player =  "player"+(i + 1);
                        playGroup[player] = new Aliplayer(videoPlayerConfig, function(player) {
                            player.setVolume(0);
                        });
                        // 镜头全屏 增加声音
                        playGroup[player].on("requestFullScreen", function() {
                            playGroup[player].setVolume(0.8);
                            console.log(playGroup[player].getVolume());
                        });
                        // 退出镜头全屏 设置静音
                        playGroup[player].on("cancelFullScreen", function() {
                            playGroup[player].setVolume(0);
                        });
                    }
                }
            }
        }



        intervalHandel = setInterval(changePage, changeTime * 1000);
        $("#changeTime").change(function () {
            changeTime = $(this).val()
            clearInterval(intervalHandel);
            intervalHandel = setInterval(changePage, changeTime * 1000);
        });
        function changePage() {
            if (pageTotal > 1) {
                if (pageTotal == currentPage) {
                    currentPage = 0;
                }

                currentPage++;
                $('.monitor-page').jqPaginator('option', {
                    currentPage: currentPage
                });
                initVideo(currentPage);
            }
        }
    });


    var fullscreen = false;
    function handleFullScreen(){
        let element = document.documentElement;
        // 判断是否已经是全屏
        // 如果是全屏，退出
        if (fullscreen) {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitCancelFullScreen) {
                document.webkitCancelFullScreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
            console.log('已还原！');
            $("#lens-container").attr("style","display:block !important");
            $(".online-container").addClass("col-sm-8");
            $(".online-container").removeClass("col-sm-12");
            var itemWidth = $(".video-item").width() - 10;
            var videoWidth = itemWidth + "px";
            var videoHeight = itemWidth * 0.5625 + "px";
            $(".video-item-player").attr("style","width: "+videoWidth+"!important; height: "+videoHeight+"!important;");
            $("#fullscreen").html("全屏");
        } else {    // 否则，进入全屏
            if (element.requestFullscreen) {
                element.requestFullscreen();
            } else if (element.webkitRequestFullScreen) {
                element.webkitRequestFullScreen();
            } else if (element.mozRequestFullScreen) {
                element.mozRequestFullScreen();
            } else if (element.msRequestFullscreen) {
                // IE11
                element.msRequestFullscreen();
            }
            $("#lens-container").attr("style","display:none !important");
            $(".online-container").removeClass("col-sm-8");
            $(".online-container").addClass("col-sm-12");
            var itemHeight = (document.documentElement.clientHeight - 60) /4;
            var videoWidth = itemHeight * 1.77 + "px";
            var videoHeight = itemHeight + "px";
            $(".video-item-player").attr("style","width: "+videoWidth+"!important; height: "+videoHeight+"!important;");
            $("#fullscreen").html("退出全屏");
        }
        // 改变当前全屏状态
        fullscreen = !fullscreen;

    }
</script>