<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$params = \Yii::$app->params;
\Yii::$app->params['ng_ctrl'] = 'generic';
?>

<div class="body">
    <!-- Banner -->
    <div class="banner" kk-fixed>
        <div class="menu-box" kk-menu="#menu" data-pos-x="-15" data-pos-y="-15">
            <div class="menu">
                <img src="<?= $params['frontend_source'] ?>/img/menu.svg"/>
            </div>
        </div>
        <?php if (!empty($focusList)): ?>
            <ul class="focus-point">
                <?php foreach ($focusList as $focus): ?>
                    <li></li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>
    </div>
    <?php if (!empty($focusList)): ?>
        <div class="carousel index-banner-height" id="focus-hot" kk-focus=".focus-point" data-point-current="on" style="overflow:hidden">
            <div class="carousel-scroller product-focus">
                <?php foreach ($focusList as $focus): ?>
                    <?php
                    $ad = !empty($focus['preview_url']);
                    $url = $ad ? $focus['link_url'] : Url::to([
                        'detail/index',
                        'id' => $focus['id']
                    ]);
                    $target = $ad ? $focus['target_info'] : '_self';
                    $img = $ad ? current($focus['preview_url']) : current($focus['cover_preview_url']);
                    ?>

                    <a href="<?= $url ?>" target="<?= $target ?>">
                        <img src="<?= $img ?>"/>
                    </a>
                <?php endforeach ?>
            </div>
        </div>

    <?php endif; ?>

    <!-- Hot-aim -->
    <div class="kake-box hot-aim">
        <div class="kake-title">
            <h3>
                <img src="<?= $params['frontend_source'] ?>/img/index-icon-aim.svg"/>
                热门目的地
            </h3>
            <a href="<?= Url::to(['items/region']) ?>">更多<img src="<?= $params['frontend_source'] ?>/img/index-icon-more.svg"/></a>
        </div>
        <div class="carousel kake-theme" id="carousel-scroller-aim" kk-scroll>
            <div class="carousel-scroller scroll">
                <?php foreach ($plateList as $i => $item): ?>
                    <?php $cls = ($i % 2 == 0) ? null : 'class="top20"' ?>
                    <div <?= $cls ?>>
                        <a href="<?= Url::to([
                            'items/index',
                            'plate' => $item['id']
                        ]) ?>">
                            <img src="<?= current($item['preview_url']) ?>"/>
                        </a>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>

    <!-- Flash-shopping-zone -->
    <div class="kake-box flash-shopping-zone">
        <div class="kake-title">
            <h3>
                <img src="<?= $params['frontend_source'] ?>/img/index-icon-sales.svg"/>
                闪购专区
            </h3>
            <a href="<?= Url::to(['items/index']) ?>">更多<img src="<?= $params['frontend_source'] ?>/img/index-icon-more.svg"/></a>
        </div>
        <div class="carousel kake-theme" id="carousel-scroller-flash" kk-camel>
            <div class="carousel-scroller scroll">
                <div class="product_image"></div>
                <?php foreach ($flashSalesList as $flashSales): ?>
                    <div class="product_image">
                        <a href="<?= Url::to([
                            'detail/index',
                            'id' => $flashSales['id']
                        ]) ?>">
                            <img class="img-responsive" src="<?= current($flashSales['cover_preview_url']) ?>"/>
                        </a>
                        <p><?= $flashSales['title'] ?></p>
                    </div>
                <?php endforeach ?>
                <div class="product_image"></div>
            </div>
        </div>
    </div>

    <!-- Activity -->
    <div class="kake-box activity">
        <?php if (!empty($bannerList)): ?>
            <div class="carousel-scroll" id="carousel-scroller-activity" kk-scroll>
                <div class="carousel-scroller activity">
                    <?php foreach ($bannerList as $item): ?>
                        <a href="<?= $item['url'] ?>" target="<?= $item['target_info'] ?>">
                            <img class="img-responsive" src="<?= current($item['preview_url']) ?>"/>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recommend-item -->
    <div class="kake-box product" kk-pull-up>
        <div class="title">
            <img src="<?= $params['frontend_source'] ?>/img/index-icon-recommand.png"/>
        </div>
        <div class="list">
            <ul>
                <?php if (!empty($standardHtml)): ?>
                    <?= $standardHtml ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <a href="javascript:void(0)">
    		<img src="<?= $params['frontend_source'] ?>/img/lookmore.jpeg" style="width: 100%;"/>
    </a>
</div>