<?php
/* @var $this yii\web\View */

$params = \Yii::$app->params;
$params['ng_ctrl'] = 'site';
?>
<div class="body">
    <div class="banner">
        <div class="menu">
            <img src="<?= $params['frontend_source'] ?>/img/menu.svg"/>
        </div>

        <?php if (!empty($focusList)): ?>
            <div class="carousel" id="focus-hot" kk-focus=".point" style="overflow:hidden">
                <div class="carousel-scroller">
                    <?php foreach ($focusList as $focus): ?>
                        <a href="<?= $params['frontend_url'] ?>/?r=detail/index&id=<?= $focus['id'] ?>">
                            <img style="width: 25%;float:left"
                                 src="<?= current($focus['cover_preview_url']) ?>"/></a>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class=" experience">
        <div class=" experience-1">
 <span> <img
         src="<?= $params['frontend_source'] ?>/img/classify.svg"/></span>
            闪购专区
            <a href="<?= $params['frontend_url'] ?>/?r=items/index">
                <div class="experience-1-more">更多
                    <img class="img-responsive"
                         src="<?= $params['frontend_source'] ?>/img/more.svg"/>
                </div>
            </a>
        </div>

        <div class="carousel" id="scroll-near" kk-scroll>
            <div class="carousel-scroller" id="carousel-scroller">
                <?php if (!empty($flashSalesList)): ?>
                    <?php foreach ($flashSalesList as $flashSales): ?>
                        <div>
                            <a href="<?= $params['frontend_url'] ?>/?r=detail/index&id=<?= $flashSales['id'] ?>">
                                <img class="img-responsive"
                                     src="<?= current($flashSales['cover_preview_url']) ?>"/></a>

                            <p><?= $flashSales['name'] ?></p>
                        </div>
                    <?php endforeach ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
    <?php if (!empty($banner)): ?>
        <div class="activity">
            <a target="<?= $banner['target_info'] ?>" href="<?= $banner['link_url'] ?>">
                <img class="img-responsive"
                     src="<?= current($banner['preview_url']) ?>"></a>
        </div>
    <?php endif; ?>
    <div class="recommend">
        <p>
            <span class="recommend2">精品推荐</span>
        </p>

        <?php if (!empty($standardList)): ?>
            <?php foreach ($standardList as $standard): ?>
                <div class="recommend3">
                    <div class="recommend3-1">
                        <a href="<?= $params['frontend_url'] ?>/?r=detail/index&id=<?= $standard['id'] ?>">
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