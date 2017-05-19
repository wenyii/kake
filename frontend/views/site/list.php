<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

?>

<?php if (!empty($list)): ?>
    <?php foreach ($list as $standard): ?>
        <div class="recommend3">
            <div class="recommend3-1">
                <a href="<?= Url::to([
                    'detail/index',
                    'id' => $standard['id']
                ]) ?>">
                    <img class="img-responsive" src="<?= current($standard['cover_preview_url']) ?>"/>
                </a>
                <div class="recommend3-1-1">￥<span><?= $standard['price'] ?></span></div>
            </div>
            <div class="recommend3-2"><?= $standard['name'] ?></div>
            <div class="recommend3-3"><?= $standard['title'] ?></div>
        </div>
    <?php endforeach ?>
<?php endif; ?>