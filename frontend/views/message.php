<?php
/* @var $this yii\web\View */
/* @var $title string */
/* @var $type string */
/* @var $message string */

$params = \Yii::$app->params;
\Yii::$app->params['ng_ctrl'] = 'generic';
?>

<div class="public">
    <div class="title"><?= $title ?></div>
    <div class="prompt-img">
        <img class="img-responsive" src="<?= $params['frontend_source'] ?>/img/message/<?= $type ?>.png"/>
    </div>
    <div class="prompt-message"><?= $message ?></div>
    <?= isset($extra) ? $extra : null ?>
</div>