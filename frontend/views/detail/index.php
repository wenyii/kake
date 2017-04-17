<?php
/* @var $this yii\web\View */

$params = \Yii::$app->params;
$params['ng_ctrl'] = 'detail';
?>

<header>
    <a href="javascript:history.go(-1);">
        <img class="return img-responsive"
             src="<?= $params['frontend_source'] ?>/img/triangle_03.png"/>
    </a>
    酒店详情
    <div class="detail">
        <img class="img-responsive"
             src="<?= $params['frontend_source'] ?>/img/detail_06.png"/>
    </div>
</header>
<div class="body">
    <div class="banner">
        <div class="carousel" id="focus-hot" kk-focus=".point" style="overflow:hidden">
            <div class="carousel-scroller">
                <img style="width: 25%;float:left"
                     src="<?= $params['frontend_source'] ?>/img/hotel2-1_02.jpg"/>
                <img style="width: 25%;float:left"
                     src="<?= $params['frontend_source'] ?>/img/hotel2-1_02.jpg"/>
                <img style="width: 25%;float:left"
                     src="<?= $params['frontend_source'] ?>/img/hotel2-1_02.jpg"/>
                <img style="width: 25%;float:left"
                     src="<?= $params['frontend_source'] ?>/img/hotel2-1_02.jpg"/>
            </div>

        </div>
    </div>
    <div class="hotel-detail">
        <div class="hotel-detail-price">￥<span>19999</span></div>
        <div class="hotel-detail-1">
            威尔逊总统酒店
        </div>
        <div class="hotel-detail-2">
         <span class="hotel-detail-2-1">
                  <img class="img-responsive"
                       src="<?= $params['frontend_source'] ?>/img/address.png"/>
                      瑞士-日内瓦  Geneva Switzerland
                 </span>
        </div>
        <div class="hotel-detail-2">
         <span class="hotel-detail-2-1">
                  <img class="img-responsive"
                       src="<?= $params['frontend_source'] ?>/img/icon2-1_06.png"/>
                      已售 <span>299</span>份 有效期还剩<span>48</span>天
                 </span>
        </div>
    </div>
    <div class="blank">

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
                推荐理由
            </div>
            <div class=" detail-hotel-2">
                &nbsp &nbsp &nbsp &nbsp瑞士的美是无法凭借想象的，只有走到那里才能感受那份真实的美好；在瑞士慢游的几天，
                真正感受到了独属于瑞士的这份美好与幸福，瑞士是中欧国家之一，
                北邻德国，西邻法国，南邻意大利，东邻奥地利和列支敦士登，丰富的旅游资源，全国的地理都以高原与山地为主，
                被誉为“欧洲屋脊”之称，有着世界公园的美誉；</br>
                &nbsp &nbsp &nbsp &nbsp记得之前有看过一条新闻，为了彻底消除贫困，瑞士政府计划会给每个成年人每周补助425英镑（约合人民币4022元），
                无论工作与否，此外儿童每周也将获得0英镑补助。
            </div>
        </div>
        <div class="blank">

        </div>
        <div class="detail-hotel">
            <div class=" detail-hotel-1">
 <span> <img
         src="<?= $params['frontend_source'] ?>/img/icon-7.png"/></span>
                特色介绍
            </div>
            <div class=" detail-hotel-2">
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
                费用包含
            </div>
            <div class="notice-money">
                1.往返船票,朱家尖到普陀山往返船票</br>
                2.上海到朱家尖往返大巴</br>
                3.行程所列酒店住宿费用</br>
                4.精装酒店住宿</br>
                5.当地中文向导服务
            </div>
        </div>
        <div class="blank">

        </div>
        <div class="detail-hotel">
            <div class=" detail-hotel-1">
 <span> <img
         src="<?= $params['frontend_source'] ?>/img/icon-7.png"/></span>
                用券说明
            </div>
            <div class="notice-money">
                1.往返船票,朱家尖到普陀山往返船票</br>
                2.上海到朱家尖往返大巴</br>
                3.行程所列酒店住宿费用</br>
                4.精装酒店住宿</br>
                5.当地中文向导服务
            </div>
        </div>
        <div class="blank">

        </div>
        <div class="detail-hotel">
            <div class=" detail-hotel-1">
 <span> <img
         src="<?= $params['frontend_source'] ?>/img/icon-7.png"/></span>
                退改规则
            </div>
            <div class="notice-money back-change">
                <ul>
                    <li>1.未预约</br>
                        &nbsp &nbsp订单提交后可随时取消
                    </li>
                    <li>2.已预约</br>
                        &nbsp &nbsp酒店一旦预约入住时间不得更改
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<footer>
    <div class="price"><p>￥<span>19999</span> 起</p></div>

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
