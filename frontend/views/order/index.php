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
    订单中心
    <div class=" menu detail">
        <img class="img-responsive"
             src="<?= $params['frontend_source'] ?>/img/menu1.svg"/>
    </div>
</header>
<div class="classify">
    <div class="classify-1">
        <div class="classify-1-1">
            <span class="cur-1">进行中</span>
        </div>
        <div class="classify-1-2">
            <span>已完成</span>
        </div>
    </div>
</div>
<div class="blank-piece"></div>
<div class="ongoing">
    <div class="order-status">
        <div class="order-status-no-appointment">
            <img img-responsive"
            src="<?= $params['frontend_source'] ?>/img/order -status_1.svg"/>
            订单状态:未预约
        </div>
        <div class="apply-refund">
            <div class="apply-refund-lft">
                <img img-responsive"
                src="<?= $params['frontend_source'] ?>/img/hotel2-3_09.jpg"/>
            </div>
            <div class="apply-refund-right">
                <div class="apply-refund-right-1">瑞士-日内瓦-威尔逊总统酒店</div>
                <p>订单总额: <span>￥1999</span></p>

                <p>房型: <span>大床房</span></p>

                <p>订单号: <span>231467253621</span>
                </p>
            </div>
        </div>
        <div class="order-status-button">
            <div>
                <button class="cancel-button">取消订单</button>
                <a href="">
                    <button class="appointment-button">立即预约</button>
                </a>
            </div>
        </div>
        <div class="blank-piece"></div>
    </div>
    <div class="order-status">
        <div class="order-status-no-appointment">
            <img img-responsive"
            src="<?= $params['frontend_source'] ?>/img/order -status_2.svg"/>
            订单状态:未预约
        </div>
        <div class="apply-refund">
            <div class="apply-refund-lft">
                <img img-responsive"
                src="<?= $params['frontend_source'] ?>/img/hotel2-3_09.jpg"/>
            </div>
            <div class="apply-refund-right">
                <div class="apply-refund-right-1">瑞士-日内瓦-威尔逊总统酒店</div>
                <p>订单总额: <span>￥1999</span></p>

                <p>房型: <span>大床房</span></p>

                <p>订单号: <span>231467253621</span>
                </p>
            </div>
        </div>
        <div class="order-status-button">

        </div>
        <div class="blank-piece"></div>
    </div>
    <div class="order-status">
        <div class="order-status-no-appointment">
            <img img-responsive"
            src="<?= $params['frontend_source'] ?>/img/order -status_3.svg"/>
            订单状态:待入住
        </div>
        <div class="apply-refund">
            <div class="apply-refund-lft">
                <img img-responsive"
                src="<?= $params['frontend_source'] ?>/img/hotel2-3_09.jpg"/>
            </div>
            <div class="apply-refund-right">
                <div class="apply-refund-right-1">瑞士-日内瓦-威尔逊总统酒店</div>
                <p>订单总额: <span>￥1999</span></p>

                <p>房型: <span>大床房</span></p>

                <p>订单号: <span>231467253621</span>
                </p>
            </div>
        </div>
        <div class="order-status-button">
            <div>
                <button class="appointment-button">查看确认号</button>
            </div>
        </div>
        <div class="confirmation-number">
            <div class="confirmation-number-name">
                确认号:
                <img img-responsive"
                src="<?= $params['frontend_source'] ?>/img/upword.svg"/>
            </div>
            <div class="confirmation-number-no">
                29041185324671
            </div>
            <div class="note">备注:编号可以唯一确定入住人的身份</div>
        </div>
        <div class="blank-piece"></div>
    </div>
    <div class="order-status">
        <div class="order-status-no-appointment">
            <img img-responsive"
            src="<?= $params['frontend_source'] ?>/img/order -status_4.svg"/>
            订单状态:退款中
        </div>
        <div class="apply-refund">
            <div class="apply-refund-lft">
                <img img-responsive"
                src="<?= $params['frontend_source'] ?>/img/hotel2-3_09.jpg"/>
            </div>
            <div class="apply-refund-right">
                <div class="apply-refund-right-1">瑞士-日内瓦-威尔逊总统酒店</div>
                <p>订单总额: <span>￥1999</span></p>

                <p>房型: <span>大床房</span></p>

                <p>订单号: <span>231467253621</span>
                </p>
            </div>
        </div>
        <div class="order-status-button">
            <div>
                <button class="appointment-button">查看进度</button>
            </div>
        </div>
        <div class="refund-schedule">
            <div class="refund-schedule-name">
                退款进度:
                <img img-responsive"
                src="<?= $params['frontend_source'] ?>/img/upword.svg"/>
            </div>
            <div class="refund-schedule-no">
                <div class="refund-schedule-no-1">
                    <span>1</span>

                    <p class="refund-schedule-no-tip">退款申请已提交</p>

                    <p class="refund-schedule-no-state">酒店预订失败,系统退款中</p>
                </div>
            </div>
            <div class="refund-schedule-no">
                <div class="refund-schedule-no-1">
                    <span>2</span>

                    <p class="refund-schedule-no-tip">KAKE处理中</p>

                    <p class="refund-schedule-no-state">KAKE客服会尽快审核,审核周期为一到两个工作日</p>
                </div>
            </div>
            <div class="refund-schedule-no">
                <div class="refund-schedule-no-1">
                    <span>3</span>

                    <p class="refund-schedule-no-tip">退款处理</p>

                    <p class="refund-schedule-no-state">支付平台会在一到三个工作日完成退款</p>
                </div>
            </div>
            <div class="refund-schedule-no">
                <div class="refund-schedule-no-1 no-direction">
                    <span>4</span>

                    <p class="refund-schedule-no-tip">退款成功</p>

                    <p class="refund-schedule-no-state">款项已成功退回到您的账户</p>
                </div>
            </div>
        </div>
        <div class="blank-piece"></div>
    </div>
</div>
<div class="order-complete">
    <div class="order-status">
        <div class="order-status-no-appointment">
            <img img-responsive"
            src="<?= $params['frontend_source'] ?>/img/order -status_5.svg"/>
            订单状态:已入住
        </div>
        <div class="apply-refund">
            <div class="apply-refund-lft">
                <img img-responsive"
                src="<?= $params['frontend_source'] ?>/img/hotel2-3_09.jpg"/>
            </div>
            <div class="apply-refund-right">
                <div class="apply-refund-right-1">瑞士-日内瓦-威尔逊总统酒店</div>
                <p>订单总额: <span>￥1999</span></p>

                <p>房型: <span>大床房</span></p>

                <p>订单号: <span>231467253621</span>
                </p>
            </div>
        </div>
        <div class="order-status-button">
            <div>
                <button class="appointment-button">开具发票</button>
            </div>
        </div>
        <div class="order-status-button invoice-schedule-button">
            <div>
                <button class="invoice-schedule">查看发票进度</button>
            </div>
        </div>
        <div class="invoice-personal">
            <div class="invoice-name">
                选择发票抬头:
                <img img-responsive"
                src="<?= $params['frontend_source'] ?>/img/upword.svg"/>
            </div>
            <div class="invoice-title">
                <span class="active">个人</span>
                <span>公司</span>
            </div>
            <div class="invoice-address">
                <p>快递地址:</p>
                <input type="text" placeholder="请填写快递地址"/>
            </div>
            <div class="invoice-confirm">
                <p>备注:我们会尽快将酒店发票快递给你</p>
                <span>确定</span>
            </div>
        </div>
        <div class="invoice-company">
            <div class="invoice-name">
                选择发票抬头:
                <img img-responsive"
                src="<?= $params['frontend_source'] ?>/img/upword.svg"/>
            </div>
            <div class="invoice-title">
                <span>个人</span>
                <span>公司</span>
            </div>
            <div class="invoice-address">
                <p>公司名称:</p>
                <input type="text" placeholder="请填写公司名称"/>
            </div>
            <div class="invoice-address">
                <p>快递地址:</p>
                <input type="text" placeholder="请填写快递地址"/>
            </div>
            <div class="invoice-confirm">
                <p>备注:我们会尽快将酒店发票快递给你</p>
                <span>确定</span>
            </div>
        </div>
        <div class="refund-schedule">
            <div class="refund-schedule-name">
                发票开具进度:
                <img img-responsive"
                src="<?= $params['frontend_source'] ?>/img/upword.svg"/>
            </div>
            <div class="refund-schedule-no">
                <div class="refund-schedule-no-1">
                    <span>1</span>

                    <p class="refund-schedule-no-tip">发票开具申请已提交</p>

                    <p class="refund-schedule-no-state">酒店预订失败,系统退款中</p>
                </div>
            </div>
            <div class="refund-schedule-no">
                <div class="refund-schedule-no-1">
                    <span>2</span>

                    <p class="refund-schedule-no-tip">KAKE处理中</p>

                    <p class="refund-schedule-no-state">KAKE客服会尽快审核,审核周期为一到两个工作日</p>
                </div>
            </div>
            <div class="refund-schedule-no">
                <div class="refund-schedule-no-1 no-direction">
                    <span>3</span>

                    <p class="refund-schedule-no-tip">快递已寄出</p>

                    <p class="refund-schedule-no-state">支付平台会在一到三个工作日完成退款</p>
                </div>
            </div>
            <div class="determine-button">
                <button>确定</button>
            </div>
        </div>
        <div class="blank-piece"></div>
    </div>
    <div class="order-status">
        <div class="order-status-no-appointment">
            <img img-responsive"
            src="<?= $params['frontend_source'] ?>/img/order -status_5.svg"/>
            订单状态:已退款
        </div>
        <div class="apply-refund">
            <div class="apply-refund-lft">
                <img img-responsive"
                src="<?= $params['frontend_source'] ?>/img/hotel2-3_09.jpg"/>
            </div>
            <div class="apply-refund-right">
                <div class="apply-refund-right-1">瑞士-日内瓦-威尔逊总统酒店</div>
                <p>订单总额: <span>￥1999</span></p>

                <p>房型: <span>大床房</span></p>

                <p>订单号: <span>231467253621</span>
                </p>
            </div>
        </div>
        <div class="order-status-button">

        </div>
        <div class="blank-piece"></div>
    </div>
</div>
