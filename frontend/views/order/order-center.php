<?php
/* @var $this yii\web\View */

$params = \Yii::$app->params;
\Yii::$app->params['ng_ctrl'] = 'order';
?>
<header>
    <a href="javascript:history.go(-1);">
        <img class="return img-responsive"
             src="<?= $params['frontend_source'] ?>/img/return.svg"/>
    </a>
   订单中心
    <div class=" menu detail">
        <img class="img-responsive"
             src="<?= $params['frontend_source'] ?>/img/menu1.svg"/>

        <div class="menu-1">
            <b>
                <img class="img-responsive"
                     src="<?= $params['frontend_source'] ?>/img/triangle.svg"/>
            </b>
        </div>
    </div>
</header>
<div class="body">
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
</div>
