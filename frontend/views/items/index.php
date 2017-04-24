<?php
/* @var $this yii\web\View */

$params = \Yii::$app->params;
\Yii::$app->params['ng_ctrl'] = 'items';
?>
<header>
    <a href="javascript:history.go(-1);">
        <img class="return img-responsive"
             src="<?= $params['frontend_source'] ?>/img/return.svg"/>
    </a>
    列表
    <div class=" menu detail" kk-menu>
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
<div class="body" ng-init="ajaxNextPage()">

    <div class="recommend" data-page="2">

        <?= $html ?>
    </div>
</div>
