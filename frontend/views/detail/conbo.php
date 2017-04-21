<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
$params = \Yii::$app->params;
$params['ng_ctrl'] = 'combo';
?>
<header>
    <a href="javascript:history.go(-1);">
        <img class="return img-responsive"
             src="<?= $params['frontend_source'] ?>/img/return.svg"/>
    </a>
   选择套餐
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
    <div class="blank">

    </div>
    <div class="combo">
        <div class=" detail-hotel-1">
 <span> <img
         src="<?= $params['frontend_source'] ?>/img/icon-7.png"/></span>
            选择套餐
        </div>
        <ul>
            <li class="combo_1">
                <div class="combo-1">
                    <b></b>威尔逊总统酒店双床房/大床房 <span>￥1999</span>
                </div>
                <div class="combo-2">
                    <i>
                        <img
                            src="<?= $params['frontend_source'] ?>/img/triangle_top.png"/>
                    </i>

                    <div class="combo-3">
                        周五入住威尔逊总统酒店一晚,入住有效期,2017年2月8日到四月20日,不适用日期2017年3月29日至31日.
                    </div>
                    <div class="combo-4">
                        <div class="combo-4-1">购买数量</div>
                        <div class="combo-4-2" ng-controller="myCtrl">
                            <span class="reduction" ng-click="reduce()">-</span>
                            <span class="num">{{ count }}</span>
                            <span class="add" ng-click="add()">+</span>
                        </div>
                    </div>
                </div>
            </li>
            <li class="combo_2">
                <div class="combo-1">
                    <b></b>威尔逊总统酒店双床房/大床房 <span>￥1999</span>
                </div>
                <div class="combo-2">
                    <i>
                        <img
                            src="<?= $params['frontend_source'] ?>/img/triangle_top.png"/>
                    </i>

                    <div class="combo-3">
                        周五入住威尔逊总统酒店一晚,入住有效期,2017年2月8日到四月20日,不适用日期2017年3月29日至31日.
                    </div>
                    <div class="combo-4">
                        <div class="combo-4-1">购买数量</div>
                        <div class="combo-4-2" ng-controller="myCtrl">
                            <span class="reduction" ng-click="reduce()">-</span>
                            <span class="num">{{ count }}</span>
                            <span class="add" ng-click="add()">+</span>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="blank">

    </div>
    <div class="linkman">
        <div class=" detail-hotel-1">
 <span> <img
         src="<?= $params['frontend_source'] ?>/img/icon-7.png"/></span>
            联系人信息
        </div>
        <input type="" name="name" id="" value="" placeholder="请输入姓名"/>
        <input type="" name="tel" id="" value="" placeholder="请输入手机号码"/>

        <div class="auth-code">
            <input type="" name="auth-code " id="auth-code" value="" placeholder="请输入验证码"/>

            <div class="auth-code-1">获取验证码</div>
        </div>
    </div>
    <div class="blank">

    </div>
    <div class="payment">
        <div class=" detail-hotel-1">
 <span> <img
         src="<?= $params['frontend_source'] ?>/img/icon-7.png"/></span>
            选择支付方式
        </div>
        <ul>
            <li cass="payment-wechat">
                <img class="img-responsive"
                     src="<?= $params['frontend_source'] ?>/img/wechat.png"/>
                <label class="pay">
                    <input type="radio" name="pay" checked="checked"/>
                </label>
            </li>
            <li cass="payment-allpay">
                <img class="img-responsive"
                     src="<?= $params['frontend_source'] ?>/img/allpay.png"/>
                <label class="pay">
                    <input type="radio" name="pay"/>
                </label>
            </li>
        </ul>
    </div>
</div>

<footer>
    <div class="price"><p>￥<span>19999</span> 起</p></div>
    <div class="buy">立即付款</div>
</footer>