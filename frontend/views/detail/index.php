<?php
/* @var $this yii\web\View */

$params = \Yii::$app->params;
$params['ng_ctrl'] = 'detail';
?>
<div class="body">
    <div class="banner">
        <a href="javascript:history.go(-1);">
        <div class="arrows">
            <img src="<?= $params['frontend_source'] ?>/img/arrows.svg"/>
            </div>
             </a>
         <div class="menu">
            <img src="<?= $params['frontend_source'] ?>/img/menu.svg"/>

            <div class="menu-1">
                <b>
                    <img class="img-responsive"
                         src="<?= $params['frontend_source'] ?>/img/triangle.svg"/>
                </b>
                <ul>
                    <li>
                        <img
                            src="<?= $params['frontend_source'] ?>/img/site.svg"/>
                        首页
                    </li>
                    <li>
                        <img
                            src="<?= $params['frontend_source'] ?>/img/order-center.svg"/>
                        订单中心
                    </li>
                    <li class="menu-order-center">
                        <img
                            src="<?= $params['frontend_source'] ?>/img/phone.svg"/>
                        咨询客服
                    </li>
                </ul>
            </div>
        </div>
        <div class="carousel" id="focus-hot" kk-focus=".point" style="overflow:hidden">
            <div class="carousel-scroller">

                <?php if (!empty($detail['slave_preview_url'])): ?>
                    <?php foreach ($detail['slave_preview_url'] as $photo): ?>
                        <img style="width: 25%;float:left"
                             src="<?= $photo ?>"/>
                    <?php endforeach ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
    <div class="hotel-detail">
        <div class="hotel-detail-price">￥<span><?= $detail['min_price'] ?></span></div>
        <div class="hotel-detail-1">
            <?= $detail['hotel_name'] ?>
        </div>
        <div class="hotel-detail-2">
         <span class="hotel-detail-2-1">
                      库存剩余<span>688</span>份
                 </span>
        </div>
    </div>
    <div class="classify">
        <div class="classify-1">
            <div class="classify-1-1">
                <span class="cur-1">详情介绍</span>
            </div>
            <div class="classify-1-2">
                <span>预订须知</span>
            </div>
        </div>
    </div>
    <div class="detail-hotel_1">
        <div class="detail-hotel">
            <div class=" detail-hotel-1">
 <span> <img
         src="<?= $params['frontend_source'] ?>/img/icon-7.png"/></span>
                简介
            </div>
            <div class=" detail-hotel-2">
                <?= $detail['info'] ?>
            </div>
        </div>
        <div class="detail-hotel">
            <div class=" detail-hotel-1">
 <span> <img
         src="<?= $params['frontend_source'] ?>/img/icon-7.png"/></span>
                特色介绍
            </div>
            <div class=" detail-hotel-2 detail-hotel_2">
                <?= $detail['characteristic'] ?>
        </div>
    </div>
    </div>
    <div class="notice">
        <div class="detail-hotel">
            <div class=" detail-hotel-1">
 <span> <img
         src="<?= $params['frontend_source'] ?>/img/icon-7.png"/></span>
                券享说明
            </div>
            <div class="notice-money">
                <?= $detail['enjoy'] ?>
            </div>
        </div>
        <div class="detail-hotel">
            <div class=" detail-hotel-1">
 <span> <img
         src="<?= $params['frontend_source'] ?>/img/icon-7.png"/></span>
                用券说明
            </div>
            <div class="notice-money">
                <?= $detail['use'] ?>
            </div>
        </div>
    </div>


<footer>
    <div class="price"><p>￥<span><?= $detail['min_price'] ?></span> 起</p></div>

    <div class="buy">立即购买</div>
    <div class=" service">
        <img class="img-responsive"
             src="<?= $params['frontend_source'] ?>/img/_service_11.png"/>
    </div>
    <div class=" back-top">
        <img class="img-responsive"
             src="<?= $params['frontend_source'] ?>/img/back-top_13.png"/>
    </div>

</footer>
