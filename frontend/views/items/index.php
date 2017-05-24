<?php
/* @var $this yii\web\View */

$params = \Yii::$app->params;
\Yii::$app->params['ng_ctrl'] = 'generic';
?>

<header>
    列表
    <div class="menu detail" kk-menu="#menu">
        <img class="img-responsive" src="<?= $params['frontend_source'] ?>/img/menu1.svg"/>
    </div>
</header>
<div class="body">
    <div class="recommend" kk-ajax-load="items/ajax-list" data-over="<?= $over ?>">
        <?= $html ?>
    </div>
</div>





