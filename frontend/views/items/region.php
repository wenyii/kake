<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$params = \Yii::$app->params;
\Yii::$app->params['ng_ctrl'] = 'generic';
?>

<?php foreach ($region as $item): ?>
    <?= $item['name'] ?>
    <img src="<?= current($item['preview_url']) ?>">
    <a href="<?= Url::to(['items/index', 'region' => $item['id']]) ?>">跳转链接</a>
    <hr>
<?php endforeach; ?>

