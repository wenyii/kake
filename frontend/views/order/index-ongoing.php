<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$params = \Yii::$app->params;
\Yii::$app->params['ng_ctrl'] = 'order';
?>

<header>
    订单中心
    <div class="menu detail" kk-menu="#menu">
        <img class="img-responsive" src="<?= $params['frontend_source'] ?>/img/menu1.svg"/>
    </div>
</header>
<div class="classify">
    <div class="classify-1">
        <a class="classify-1-1" href="<?= Url::to([
            'order/index',
            'type' => 'ongoing'
        ]) ?>">
            <div class="cur-1">
                <span>进行中</span>
            </div>
        </a>
        <a href="<?= Url::to([
            'order/index',
            'type' => 'completed'
        ]) ?>">
            <div">
                <span>已完成</span>
            </div>
        </a>
    </div>
</div>

<div class="blank-piece"></div>

<div class="ongoing" kk-ajax-load="order/ajax-list" data-params="type=ongoing" data-over="<?= $over ?>">
    <?= trim($html) ? $html : '<p class="no-order">暂无相关订单</p>' ?>
</div>
