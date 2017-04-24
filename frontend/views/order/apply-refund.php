<?php
/* @var $this yii\web\View */

$params = \Yii::$app->params;
\Yii::$app->params['ng_ctrl'] = 'order';
?>
<header>
    <a href="javascript:history.go(-1);" class="return">
        <img class=" img-responsive"
             src="<?= $params['frontend_source'] ?>/img/return.svg"/>
    </a>
    申请退款
    <div class=" menu detail">
        <img class="img-responsive"
             src="<?= $params['frontend_source'] ?>/img/menu1.svg"/>

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
    <div class="apply-refund">
       <div class="apply-refund-lft">
        <img  img-responsive"
             src="<?= $params['frontend_source'] ?>/img/hotel2-3_09.jpg"/>
    </div>
         <div class="apply-refund-right">
        <div class="apply-refund-right-1" >瑞士-日内瓦-威尔逊总统酒店</div>
        <p>订单总额: <span>￥1999</span></p>
               <p>房型: <span>大床房</span></p>
               <p>订单号: <span>231467253621</span></p>
    </div>
    </div>
    <div class="blank">

    </div>
    <div class="refund-money">
      退款金额
        <span>￥1999</span>
    </div>
    <div class="blank">

    </div>
     <div class="refund-style">
          <div class="refund-style-1">
        退款方式
    </div>
          <div class="refund-style-2">
       <p>原路退回 <span>一到七个工作日内到账,0手续费</span></p>
              <p class="refund-style-2-1">退款将退回到你的支付账号中</p>
    </div>
    </div>
     <div class="blank">

    </div>
    <div class="refund-why">
        <p>退款原因</p>
        <textarea  placeholder="请输入退款原因" name="" rows="" cols=""></textarea>
    </div>
</div>

<div class="footer">
  确认退款
</div>