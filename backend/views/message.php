<?php

/* @var $this yii\web\View */
/* @var $title string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

?>
<div class="site-error">

    <h1><?= Html::encode($title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
        <br><br>
        <a href="javascript:history.go(-1)">返回</a> / <a href="/">回到首页</a> 或 <a href="javascript:history.go(0)">刷新页面</a>
    </div>
</div>