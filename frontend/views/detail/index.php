<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$params = \Yii::$app -> params;
\Yii::$app -> params['ng_ctrl'] = 'detail';
?>

<div class="body" ng-init="service.goToTop('.back-top')">
    <div class="banner">
        <div class="menu" kk-menu="#menu">
            <img src="<?= $params['frontend_source'] ?>/img/menu.svg"/>
        </div>

        <div class="carousel" id="focus-hot" kk-focus=".focus-number" focus-number-tpl="< {NOW} / {TOTAL} >"
             style="overflow: hidden">
            <div class="carousel-scroller product-focus">
                <?php if (!empty($detail['slave_preview_url'])): ?>
                    <?php foreach ($detail['slave_preview_url'] as $photo): ?>
                        <img src="<?= $photo ?>"/>
                    <?php endforeach ?>
                <?php endif; ?>
            </div>
        </div>
        <span class="focus-number"></span>
    </div>
    <div class="hotel-detail">
        <!--<div class="hotel-detail-price">￥<span><?= $detail['min_price'] ?></span></div>-->
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
            <div class="detail-hotel-1">
                <span> <img src="<?= $params['frontend_source'] ?>/img/classify.svg"/></span>
                费用包含
            </div>
            <div class="detail-hotel-2">
                <?= $detail['cost'] ?>
            </div>
        </div>
        <div class="detail-hotel">
            <div class="detail-hotel-1">
                <span> <img src="<?= $params['frontend_source'] ?>/img/classify.svg"/></span>
                推荐理由
            </div>
            <div class="detail-hotel-2 detail-hotel_2">
                <?= $detail['recommend'] ?>
            </div>
        </div>
    </div>
    <div class="notice card_second">
        <div class="detail-hotel">
            <div class="detail-hotel-1">
                <span> <img src="<?= $params['frontend_source'] ?>/img/classify.svg"/></span>
                使用说明
            </div>
            <div class="notice-money">
                <?= $detail['use'] ?>
            </div>
        </div>
        <div class="detail-hotel">
            <div class="detail-hotel-1">
                <span> <img src="<?= $params['frontend_source'] ?>/img/classify.svg"/></span>
                退改规则
            </div>
            <div class="notice-money">
                <?= $detail['back'] ?>
            </div>
        </div>
    </div>


    <footer>
        <div class="price"><p>￥<span><?= $detail['min_price'] ?></span> 起/<?= $detail['night_times'] ?>晚</p></div>

        <div class="buy"><a href="<?= Url::to([
                'detail/choose-package',
                'id' => $detail['id']
            ]) ?>">立即购买</a></div>
        <div class=" service">
            <a href="tel:<?= Yii::$app -> params['company_tel'] ?>">
                <img class="img-responsive" src="<?= $params['frontend_source'] ?>/img/customer-service.svg"/>
            </a>
        </div>
        <div class="back-top">
            <img class="img-responsive" src="<?= $params['frontend_source'] ?>/img/back-top_13.png"/>
        </div>

    </footer>
