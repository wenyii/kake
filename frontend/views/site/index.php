<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$params = \Yii::$app->params;
\Yii::$app->params['ng_ctrl'] = 'generic';
?>

<div class="body">
    <div class="banner" kk-fixed>
        	<div class="menu-box"  kk-menu="#menu" menu-pos-x="-15" menu-pos-y="-15">
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
        <div class="carousel" id="focus-hot" kk-focus=".focus-point" focus-point-current="on" style="overflow:hidden">
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
</div>
<?php if (!empty($flashSalesList)): ?>
    <div class=" experience">
        <div class=" experience-1">
            <span>
                <img src="<?= $params['frontend_source'] ?>/img/classify.svg"/>
            </span>
            喀客专线
            <a target="_blank" href="<?= Url::to(['items/index']) ?>">
                <div class="experience-1-more">更多
                    <img class="img-responsive" src="<?= $params['frontend_source'] ?>/img/more.svg"/>
                </div>
            </a>
        </div>
        <div class="carousel" id="carousel-scroller" kk-scroll>
            <div class="carousel-scroller scroll">
                <?php foreach ($flashSalesList as $flashSales): ?>
                    <div>
                        <a target="_blank"  href="<?= Url::to([
                            'detail/index',
                            'id' => $flashSales['id']
                        ]) ?>" >
                            <img class="img-responsive" src="<?= current($flashSales['cover_preview_url']) ?>"/>
                        </a>
                        <p><?= $flashSales['title'] ?></p>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($banner)): ?>
    <div class="carousel-scroll" id="carousel-scroller-activity" kk-scroll>
        <div class="carousel-scroller activity">
            <?php foreach ($banner as $item): ?>
                <a href="<?= $item['url'] ?>" target="<?= $item['target_info'] ?>">
                    <img class="img-responsive" src="<?= current($item['preview_url']) ?>"/>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<div class="recommend">
    <?php if (!empty($standardHtml)): ?>
        <p><span class="recommend2">精品推荐</span></p>
        <?= $standardHtml ?>
    <?php endif; ?>
</div>
