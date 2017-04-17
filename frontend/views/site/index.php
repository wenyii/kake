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
                                 src="<?= $focus['cover_deep_path'] ?>/<?= $focus['cover_filename'] ?>"/></a>
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
        <?php if (!empty($focusList)): ?>
            <div class="carousel" id="scroll-near" kk-scroll>
                <div class="carousel-scroller" id="carousel-scroller">
                    <?php foreach ($flashSalesList as $flashSales): ?>
                        <div>
                            <a href="<?= $params['frontend_url'] ?>/?r=detail/index&id=<?= $flashSales['id'] ?>">
                                <img class="img-responsive"
                                     src="<?= $flashSales['cover_deep_path'] ?>/<?= $flashSales['cover_filename'] ?>"/></a>

                            <p><?= $flashSales['title'] ?></p>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
    <?php if (!empty($bannerList)): ?>
        <div class="activity">
            <a href="<?= $params['frontend_url'] ?>/?r=detail/index&id=1">
                <img class="img-responsive"
                     src="<?= $params['frontend_source'] ?>/img/bananer.jpg"/></a>
        </div>
    <?php endif; ?>
    <div class="recommend">
        <p>
            <span class="recommend2">精品推荐</span>
        </p>

        <div class="recommend3">
            <div class="recommend3-1">
                <a href="<?= $params['frontend_url'] ?>/?r=detail/index&id=1">
                    <img class="img-responsive"
                         src="<?= $params['frontend_source'] ?>/img/hotel_1_2.jpg"/></a>

                <div class="recommend3-1-1">￥<span>699</span></div>
            </div>
            <div class="recommend3-2">
                云南香格里拉国际大酒店
            </div>
            <div class="recommend3-3">
                三江并流 | 太阳最早照耀的地方
            </div>
        </div>
        <div class="recommend3">
            <div class="recommend3-1">
                <a href="<?= $params['frontend_url'] ?>/?r=detail/index&id=1">
                    <img class="img-responsive"
                         src="<?= $params['frontend_source'] ?>/img/hotel_1_4.jpg"/></a>

                <div class="recommend3-1-1">￥<span>999</span></div>
            </div>
            <div class="recommend3-2">
                 威尔逊总统酒店
            </div>
            <div class="recommend3-3">
                三江并流 | 太阳最早照耀的地方
            </div>
        </div>
        <div class="recommend3">
            <div class="recommend3-1">
                <a href="<?= $params['frontend_url'] ?>/?r=detail/index&id=1">
                    <img class="img-responsive"
                         src="<?= $params['frontend_source'] ?>/img/hotel_1_5.jpg"/></a>

                <div class="recommend3-1-1">￥<span>1999</span></div>
            </div>
            <div class="recommend3-2">
                云南香格里拉国际大酒店
            </div>
            <div class="recommend3-3">
                三江并流 | 太阳最早照耀的地方
            </div>
        </div>
        <div class="recommend3">
            <div class="recommend3-1">
                <a href="<?= $params['frontend_url'] ?>/?r=detail/index&id=1">
                    <img class="img-responsive"
                         src="<?= $params['frontend_source'] ?>/img/hotel_1_6.jpg"/></a>

                <div class="recommend3-1-1">￥<span>19999</span></div>
            </div>
            <div class="recommend3-2">
                 威尔逊总统酒店
            </div>
            <div class="recommend3-3">
                三江并流 | 太阳最早照耀的地方
            </div>
        </div>
    </div>
</div>