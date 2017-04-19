<?php
/* @var $this yii\web\View */

$params = \Yii::$app->params;
$params['ng_ctrl'] = 'items';
?>
<header>
    <a href="javascript:history.go(-1);">
        <img class="return img-responsive"
             src="<?= $params['frontend_source'] ?>/img/return.svg"/>
    </a>
    列表
    <div class=" menu detail">
        <img class="img-responsive"
             src="<?= $params['frontend_source'] ?>/img/menu1.svg"/>

        <div class="menu-1">
            <b>
                <img class="img-responsive"
                     src="<?= $params['frontend_source'] ?>/img/triangle.svg"/>
            </b>
            <ul>
                <a href="<?= $params['frontend_url'] ?>/">
                    <li>
                        <img
                            src="<?= $params['frontend_source'] ?>/img/site.svg"/>
                        首页
                    </li>
                </a>
                <li>
                    <img
                        src="<?= $params['frontend_source'] ?>/img/order-center.svg"/>
                    订单中心
                </li>
                <li class="menu-order-center">
                    <img
                        src="<?= $params['frontend_source'] ?>/img/phone.svg"/>
                    咨询客服
                </li>
            </ul>
        </div>
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
