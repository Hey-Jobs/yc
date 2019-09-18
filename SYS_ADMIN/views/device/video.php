<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge" >
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no"/>
    <title><?= $title ?></title>
    <script src="https://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://g.alicdn.com/de/prismplayer/2.8.2/skins/default/aliplayer-min.css" />
    <script type="text/javascript" charset="utf-8" src="https://g.alicdn.com/de/prismplayer/2.8.2/aliplayer-min.js"></script>
    <style>
        body{ padding:0; margin: 0; }
        .prism-player{width: 100%; height: calc((9 / 16 )* 100vw);}
    </style>
</head>
<body>
<div class="prism-player" id="player-con"></div>
<script>
    $(function () {
        var boxHeight = $("#player-con").width() * 0.5625 + "px";

        var player = new Aliplayer({
                "id": "player-con",
                "source": "<?= $uri?>",
                "width": "100%",
                "height": boxHeight,
                "autoplay": true,
                "isLive": true,
                "rePlay": false,
                "playsinline": true,
                "preload": true,
                "controlBarVisibility": "hover",
                "useH5Prism": true,
                "extraInfo": {
                    "crossOrigin": "anonymous"
                },
            }, function (player) {
                player._switchLevel = 0;
                console.log("播放器创建了。");
            }
        );

        $(window).on('beforeunload',function(){
            var deviceId = "<?= $deviceId?>"
            $.ajax({
                url: '<?php echo \yii\helpers\Url::to('/device/suspend')?>',
                dataType: 'json',
                type: "GET",
                data: {'id' : deviceId},
                success: function (result) {
                    console.log(result);
                }
            });
        });

        window.addEventListener('popstate', (e) => {
            var deviceId = "<?= $deviceId?>"
            $.ajax({
                url: '<?php echo \yii\helpers\Url::to('/device/suspend')?>',
                dataType: 'json',
                type: "GET",
                data: {'id' : deviceId},
                success: function (result) {
                    console.log(result);
                }
            });
        }, false)
    });

</script>
</body>
