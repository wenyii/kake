<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$params = \Yii::$app->params;
\Yii::$app->params['ng_ctrl'] = 'detail';
?>

<div class="body" ng-init="service.goToTop('.back-top')">
    <div class="banner">
        <a href="javascript:history.go(-1);">
            <div class="arrows">
                <img src="<?= $params['frontend_source'] ?>/img/arrows.svg"/>
            </div>
        </a>
        <div class="menu" kk-menu="#menu">
            <img src="<?= $params['frontend_source'] ?>/img/menu.svg"/>
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
                      已售 <span><?= $detail['max_sales'] ?></span> 份
                 </span>
        </div>
    </div>
    <div class="classify" kk-fixed>
        <div class="classify-1" kk-tab-card="cur-1" tab-element="div">
            <div class="classify-1-1 cur-1" tab-card=".card_first">
                <span>详情介绍</span>
            </div>
            <div class="classify-1-2" tab-card=".card_second">
                <span>预订须知</span>
            </div>
        </div>
    </div>
    <div class="detail-hotel_1 card_first">
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
    <div class="notice card_second">
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
        <div class="price"><p>￥<span><?= $detail['min_price'] ?></span> 起/<?= $detail['night_times'] ?>晚</p></div>

        <div class="buy"><a href="<?= Url::to(['detail/choose-package', 'id' => $detail['id']]) ?>">立即购买</a></div>
        <div class=" service">
            <a href="tel:<?= Yii::$app->params['company_tel'] ?>">
                <img class="img-responsive"
                     src="<?= $params['frontend_source'] ?>/img/_service_11.png"/>
            </a>
        </div>
        <div class="back-top">
            <img class="img-responsive"
                 src="<?= $params['frontend_source'] ?>/img/back-top_13.png"/>
        </div>

    </footer>
