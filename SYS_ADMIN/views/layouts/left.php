<?php
use SYS_ADMIN\assets\AppAsset;

$menu = \mdm\admin\components\MenuHelper::getAssignedMenu(Yii::$app->user->id);
$menu = (isset($menu) && is_array($menu)) ? $menu : [];
?>


<!-- Navigation -->
<aside id="menu">
    <div id="navigation">
        <div class="profile-picture">
            <a href="index.php">
                <img src="/images/profile.jpg" class="img-circle m-b" alt="logo">
            </a>

            <div class="stats-label text-color">
                <span class="font-extra-bold font-uppercase">管理员</span>

                <div id="sparkline1" class="small-chart m-t-sm"></div>
                <div>
                    <h4 class="font-extra-bold m-b-xs">
                        $260 104,200
                    </h4>
                    <small class="text-muted">Your income from the last months in sales product.</small>
                </div>
            </div>
        </div>

        <?=
        \romankarkachev\widgets\Menu::widget(
            [
                "encodeLabels" => false,
                "options" => ["class" => "nav", 'id' => 'side-menu'],
                "items" => $menu,
            ]
        );?>
    </div>
</aside>
