<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

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
    <div class="classify-1">
        <a href="<?= Url::to([
            'order/index',
            'type' => 'ongoing'
        ]) ?>">
            <div class="classify-1-1">
                <span>进行中</span>
            </div>
        </a>
        <a href="<?= Url::to([
            'order/index',
            'type' => 'completed'
        ]) ?>">
            <div class="classify-1-2 cur-1">
                <span>已完成</span>
            </div>
        </a>
    </div>
</div>

<div class="blank-piece"></div>

<div class="order-complete" kk-ajax-load="order/ajax-list" extra-params="type=completed" blank-message="所有订单已加载显示~">
    <?= trim($html) ? $html : '<p class="no-order">暂无相关订单</p>' ?>
</div>
