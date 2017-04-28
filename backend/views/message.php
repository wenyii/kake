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
    </div>
</div>