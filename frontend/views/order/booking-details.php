<?php
/* @var $this yii\web\View */

$params = \Yii::$app->params;
\Yii::$app->params['ng_ctrl'] = 'order';
?>
<header>
    <a href="javascript:history.go(-1);" class="return">
        <img class="img-responsive"
             src="<?= $params['frontend_source'] ?>/img/return.svg"/>
    </a>
    列表
    <div class="menu detail" kk-menu="#menu">
        <img class="img-responsive"
             src="<?= $params['frontend_source'] ?>/img/menu1.svg"/>
    </div>
</header>
<div class="body">
    <div class="apply-refund">
        <div class="apply-refund-lft">
            <img img-responsive"
            src="<?= $params['frontend_source'] ?>/img/hotel2-3_09.jpg"/>
        </div>
        <div class="apply-refund-right">
            <div class="apply-refund-right-1">瑞士-日内瓦-威尔逊总统酒店</div>
            <p>订单总额: <span>￥1999</span></p>

            <p>房型: <span>大床房</span></p>

            <p class="order-number">订单号: <span>231467253621</span>
                <b> <img src="<?= $params['frontend_source'] ?>/img/tel.svg"/></b>
            </p>
        </div>
    </div>
    <div class="blank">

    </div>
    <div class="booking-information">
        <div class="booking-information-date">
            请选择入住日期
            <span>请选择预约入住日期</span>
        </div>
        <div class="booking-information-name">
            <p>入住人姓名</p>
            <input type="text" placeholder="请输入入住人姓名"/>
        </div>
        <div class="booking-information-name">
            <p>入住人电话</p>
            <input type="text" placeholder="请输入入住人电话号码"/>
        </div>
    </div>
</div>
<div class="footer">
    确认预约
</div>