<?php
/* @var $this yii\web\View */

$params = \Yii::$app->params;
$params['ng_ctrl'] = 'items';
?>
<header>
    <a href="javascript:history.go(-1);">
        <img class="return img-responsive"
             src="<?= $params['frontend_source'] ?>/img/triangle_03.png"/>
    </a>
    列表
    <div class="detail">
        <img class="img-responsive"
             src="<?= $params['frontend_source'] ?>/img/detail_06.png"/>
    </div>
</header>
<div class="body">
    <div class="recommend">
        <?php if (!empty($items)): ?>
            <?php foreach ($items as $item): ?>
                <div class="recommend3">
                    <div class="recommend3-1">
                        <a href="<?= $params['frontend_url'] ?>/?r=detail/index&id=<?= $item['id'] ?>">
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
    </div>
</div>
