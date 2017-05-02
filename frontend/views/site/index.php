<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$params = \Yii::$app->params;
?>

<div class="body">
    <div class="banner">
        <div class="menu" kk-menu="#menu">
            <img src="<?= $params['frontend_source'] ?>/img/menu.svg"/>
        </div>
    </div>

    <?php if (!empty($focusList)): ?>
        <div class="carousel" id="focus-hot" kk-focus=".point" style="overflow:hidden">
            <div class="carousel-scroller">
                <?php foreach ($focusList as $focus): ?>
                    <a href="<?= Url::to([
                        'detail/index',
                        'id' => $focus['id']
                    ]) ?>">
                        <img style="width: 25%;float:left"
                             src="<?= current($focus['cover_preview_url']) ?>"/></a>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php if (!empty($flashSalesList)): ?>
    <div class=" experience">
        <div class=" experience-1">
            <span>
                <img src="<?= $params['frontend_source'] ?>/img/classify.svg"/>
            </span>
            闪购专区
            <a href="<?= Url::to(['items/index']) ?>">
                <div class="experience-1-more">更多
                    <img class="img-responsive"
                         src="<?= $params['frontend_source'] ?>/img/more.svg"/>
                </div>
            </a>
        </div>
        <div class="carousel" id="carousel-scroller" kk-scroll>
            <div class="carousel-scroller scroll">

                <?php foreach ($flashSalesList as $flashSales): ?>
                    <div>
                        <a href="<?= Url::to([
                            'detail/index',
                            'id' => $flashSales['id']
                        ]) ?>">
                            <img class="img-responsive"
                                 src="<?= current($flashSales['cover_preview_url']) ?>"/></a>
                        <p><?= $flashSales['name'] ?></p>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($banner)): ?>
    <div class="activity">
        <a target="<?= $banner['target_info'] ?>" href="<?= $banner['link_url'] ?>">
            <img class="img-responsive"
                 src="<?= current($banner['preview_url']) ?>"></a>
    </div>
<?php endif; ?>
<div class="recommend">
    <?php if (!empty($standardList)): ?>
        <p><span class="recommend2">精品推荐</span></p>
        <?php foreach ($standardList as $standard): ?>
            <div class="recommend3">
                <div class="recommend3-1">
                    <a href="<?= Url::to([
                        'detail/index',
                        'id' => $standard['id']
                    ]) ?>">
                        <img class="img-responsive"
                             src="<?= current($standard['cover_preview_url']) ?>"/></a>

                    <div class="recommend3-1-1">￥<span><?= $standard['price'] ?></span></div>
                </div>
                <div class="recommend3-2">
                    <?= $standard['name'] ?>
                </div>
                <div class="recommend3-3">
                    <?= $standard['title'] ?>
                </div>
            </div>
        <?php endforeach ?>
    <?php endif; ?>
</div>
</div>