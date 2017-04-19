<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$params = \Yii::$app->params;
\Yii::$app->params['ng_ctrl'] = 'items';
?>

<?php if (!empty($list)): ?>
    <?php foreach ($list as $item): ?>
        <div class="recommend3">
            <div class="recommend3-1">
                <a href="<?= Url::to(['detail/index', 'id' => $item['id']]) ?>">
                    <img class="img-responsive"
                         src="<?= current($item['cover_preview_url']) ?>"/></a>

                <div class="recommend3-1-1">￥<span><?= $item['price'] ?></span></div>
            </div>
            <div class="recommend3-2">
                <?= $item['name'] ?>
            </div>
            <div class="recommend3-3">
                <?= $item['title'] ?>
            </div>
        </div>
    <?php endforeach ?>
<?php endif; ?>