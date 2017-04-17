<?php
/* @var $this yii\web\View */

$params = \Yii::$app->params;
$params['ng_ctrl'] = 'items';
?>
<header>
    <a href="javascript:history.go(-1);">
        <img class="return img-responsive"
             src="<?= $params['frontend_source'] ?>/img/triangle_03.png"/>
    </a>
    列表
    <div class="detail">
        <img class="img-responsive"
             src="<?= $params['frontend_source'] ?>/img/detail_06.png"/>
    </div>
</header>
<div class="body">
    <div class="recommend">
        <div class="recommend3">
            <div class="recommend3-1">
                <a href="<?= $params['frontend_url'] ?>/?r=detail/index&id=1">
                    <img class="img-responsive"
                         src="<?= $params['frontend_source'] ?>/img/hotel_1_2.jpg"/></a>

                <div class="recommend3-1-1">￥<span>699</span></div>
            </div>
            <div class="recommend3-2">
                云南香格里拉国际大酒店
            </div>
            <div class="recommend3-3">
                三江并流 | 太阳最早照耀的地方
            </div>
        </div>
        <div class="recommend3">
            <div class="recommend3-1">
                <a href="<?= $params['frontend_url'] ?>/?r=detail/index&id=1">
                    <img class="img-responsive"
                         src="<?= $params['frontend_source'] ?>/img/hotel_1_4.jpg"/></a>

                <div class="recommend3-1-1">￥<span>999</span></div>
            </div>
            <div class="recommend3-2">
                 威尔逊总统酒店
            </div>
            <div class="recommend3-3">
                三江并流 | 太阳最早照耀的地方
            </div>
        </div>
        <div class="recommend3">
            <div class="recommend3-1">
                <a href="<?= $params['frontend_url'] ?>/?r=detail/index&id=1">
                    <img class="img-responsive"
                         src="<?= $params['frontend_source'] ?>/img/hotel_1_5.jpg"/></a>

                <div class="recommend3-1-1">￥<span>1999</span></div>
            </div>
            <div class="recommend3-2">
                云南香格里拉国际大酒店
            </div>
            <div class="recommend3-3">
                三江并流 | 太阳最早照耀的地方
            </div>
        </div>
        <div class="recommend3">
            <div class="recommend3-1">
                <a href="<?= $params['frontend_url'] ?>/?r=detail/index&id=1">
                    <img class="img-responsive"
                         src="<?= $params['frontend_source'] ?>/img/hotel_1_6.jpg"/></a>

                <div class="recommend3-1-1">￥<span>19999</span></div>
            </div>
            <div class="recommend3-2">
                 威尔逊总统酒店
            </div>
            <div class="recommend3-3">
                三江并流 | 太阳最早照耀的地方
            </div>
        </div>
    </div>
</div>
