<?php
/* @var $this yii\web\View */

$params = \Yii::$app->params;
\Yii::$app->params['ng_ctrl'] = 'generic';
?>

<header>
    列表
    <div class="menu detail" kk-menu="#menu">
        <img class="img-responsive" src="<?= $params['frontend_source'] ?>/img/list.svg"/>
    </div>
</header>
<div class="body">
    <div class="recommend" kk-ajax-load="items/ajax-list" data-over="<?= $over ?>">
        <?= trim($html) ? $html : '<div class="no-data">暂无相关酒店</div>' ?>
    </div>
</div>





