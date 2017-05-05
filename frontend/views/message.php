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
    <div class="prompt-message">
        <?= $message ?>
        <?php if ($type == 'error' || $type == '404'): ?>
            <br><br>
            <a href="javascript:history.go(0)">刷新重试</a> 或 <a href="/">回到首页</a>
        <?php endif; ?>
    </div>
    <?= isset($extra) ? $extra : null ?>
</div>