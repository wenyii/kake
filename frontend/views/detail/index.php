<?php
/* @var $this yii\web\View */

$params = \Yii::$app->params;
$params['ng_ctrl'] = 'detail';
?>

<header>
    <a href="javascript:history.go(-1);">
        <img class="return img-responsive"
             src="<?= $params['frontend_source'] ?>/img/triangle_03.png"/>
    </a>
    酒店详情
    <div class="detail">
        <img class="img-responsive"
             src="<?= $params['frontend_source'] ?>/img/detail_06.png"/>
    </div>
</header>
<div class="body">
    <div class="banner">
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
        <div class="hotel-detail-price">￥<span><?= min(array_column($detail['package'], 'price')) ?></span></div>
        <div class="hotel-detail-1">
            <?= $detail['hotel_name'] ?>
        </div>
        <div class="hotel-detail-2">
         <span class="hotel-detail-2-1">
                  <img class="img-responsive"
                       src="<?= $params['frontend_source'] ?>/img/address.png"/>
             <?= $detail['destination'] ?>
                 </span>
        </div>
    </div>
    <div class="blank">

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
        <div class="blank">

        </div>
        <div class="detail-hotel">
            <div class=" detail-hotel-1">
 <span> <img
         src="<?= $params['frontend_source'] ?>/img/icon-7.png"/></span>
                特色介绍
            </div>
            <div class=" detail-hotel-2">
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
        <div class="blank">

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
</div>

<footer>
    <div class="price"><p>￥<span><?= min(array_column($detail['package'], 'price')) ?></span> 起</p></div>

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
