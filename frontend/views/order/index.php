<?php
/* @var $this yii\web\View */

$params = \Yii::$app->params;
\Yii::$app->params['ng_ctrl'] = 'order';
?>

<header>
    <a href="javascript:history.go(-1);" class="return">
        <img class="img-responsive"
             src="<?= $params['frontend_source'] ?>/img/return.svg"/>
    </a>
    订单中心
    <div class=" menu detail">
        <img class="img-responsive"
             src="<?= $params['frontend_source'] ?>/img/menu1.svg"/>
    </div>
</header>
<div class="classify">
    <div class="classify-1" kk-tab-card="cur-1" tab-element="div">
        <div class="classify-1-1 cur-1" tab-card=".card_first">
            <span>进行中</span>
        </div>
        <div class="classify-1-2" tab-card=".card_second">
            <span>已完成</span>
        </div>
    </div>
</div>

<div class="blank-piece"></div>

<div class="ongoing card_first"><?= $ongoing ?: '<p class="no-order">暂无相关订单</p>' ?></div>
<div class="order-complete card_second"><?= $completed ?: '<p class="no-order">暂无相关订单</p>' ?></div>
