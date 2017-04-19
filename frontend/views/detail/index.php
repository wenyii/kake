<?php
/* @var $this yii\web\View */

$params = \Yii::$app->params;
$params['ng_ctrl'] = 'detail';
?>
<div class="body">
    <div class="banner">
        <a href="javascript:history.go(-1);">
        <div class="arrows">
            <img src="<?= $params['frontend_source'] ?>/img/arrows.svg"/>
            </div>
             </a>
         <div class="menu">
            <img src="<?= $params['frontend_source'] ?>/img/menu.svg"/>

            <div class="menu-1">
                <b>
                    <img class="img-responsive"
                         src="<?= $params['frontend_source'] ?>/img/triangle.svg"/>
                </b>
                <ul>
                    <li>
                        <img
                            src="<?= $params['frontend_source'] ?>/img/site.svg"/>
                        首页
                    </li>
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
        <div class="carousel" id="focus-hot" kk-focus=".point" style="overflow:hidden">
            <div class="carousel-scroller">

                <?php if (!empty($detail['slave_preview_url'])): ?>
                    <?php foreach ($detail['slave_preview_url'] as $photo): ?>
                        <img style="width: 25%;float:left"
                             src="<?= $photo ?>"/>
                    <?php endforeach ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
    <div class="hotel-detail">
        <div class="hotel-detail-price">￥<span><?= min(array_column($detail['package'], 'price')) ?></span></div>
        <div class="hotel-detail-1">
            <?= $detail['hotel_name'] ?>
        </div>
        <div class="hotel-detail-2">
         <span class="hotel-detail-2-1">
                  <img class="img-responsive"
                       src="<?= $params['frontend_source'] ?>/img/address.png"/>
             <?= $detail['destination'] ?>
                 </span>
        </div>
    </div>
    <div class="classify">
        <div class="classify-1">
            <div class="classify-1-1">
                <span class="cur-1">详情介绍</span>
            </div>
            <div class="classify-1-2">
                <span>预订须知</span>
            </div>
        </div>
    </div>
    <div class="detail-hotel_1">
        <div class="detail-hotel">
            <div class=" detail-hotel-1">
 <span> <img
         src="<?= $params['frontend_source'] ?>/img/icon-7.png"/></span>
                简介
            </div>
            <div class=" detail-hotel-2">
                <?= $detail['info'] ?>
            </div>
        </div>
        <div class="detail-hotel">
            <div class=" detail-hotel-1">
 <span> <img
         src="<?= $params['frontend_source'] ?>/img/icon-7.png"/></span>
                特色介绍
            </div>
            <div class=" detail-hotel-2">
                <?= $detail['characteristic'] ?>
            <div class=" detail-hotel-2 detail-hotel_2">
                一:金山口列车</br>
                &nbsp &nbsp &nbsp &nbsp瑞士的美是无法凭借想象的，只有走到那里才能感受那份真实的美好；在瑞士慢游的几天，
                真正感受到了独属于瑞士的这份美好与幸福，瑞士是中欧国家之一，
                北邻德国，西邻法国，南邻意大利，东邻奥地利和列支敦士登，丰富的旅游资源，全国的地理都以高原与山地为主，
                被誉为“欧洲屋脊”之称，有着世界公园的美誉；</br>
                <div class=" detail-hotel-2-1">
                    <a href="<?= $params['frontend_url'] ?>/?r=detail/index&id=1">
                        <img class="img-responsive"
                             src="<?= $params['frontend_source'] ?>/img/hotel2-2_06.jpg"/></a>
                </div>
                二:冰川快线</br>
                &nbsp &nbsp &nbsp &nbsp记得之前有看过一条新闻，为了彻底消除贫困，瑞士政府计划会给每个成年人每周补助425英镑（约合人民币4022元），
                无论工作与否，此外儿童每周也将获得0英镑补助。
                <div class=" detail-hotel-2-1">
                    <a href="<?= $params['frontend_url'] ?>/?r=detail/index&id=1">
                        <img class="img-responsive"
                             src="<?= $params['frontend_source'] ?>/img/hotel2-3_09.jpg"/></a>
                </div>
            </div>
        </div>
    </div>
    <div class="notice">
        <div class="detail-hotel">
            <div class=" detail-hotel-1">
 <span> <img
         src="<?= $params['frontend_source'] ?>/img/icon-7.png"/></span>
                券享说明
            </div>
            <div class="notice-money">
                <?= $detail['enjoy'] ?>
            </div>
        </div>
        <div class="detail-hotel">
            <div class=" detail-hotel-1">
 <span> <img
         src="<?= $params['frontend_source'] ?>/img/icon-7.png"/></span>
                用券说明
            </div>
            <div class="notice-money">
                <?= $detail['use'] ?>
            </div>
        </div>
    </div>
</div>

<footer>
    <div class="price"><p>￥<span><?= min(array_column($detail['package'], 'price')) ?></span> 起</p></div>

    <div class="buy">立即购买</div>
    <div class=" service">
        <img class="img-responsive"
             src="<?= $params['frontend_source'] ?>/img/_service_11.png"/>
    </div>
    <div class=" back-top">
        <img class="img-responsive"
             src="<?= $params['frontend_source'] ?>/img/back-top_13.png"/>
    </div>

</footer>
