<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$params = \Yii::$app->params;
?>

<?php if (!empty($list)): ?>
    <?php foreach ($list as $item): ?>
        <div class="order-status">
            <div class="order-status-no-appointment">
                <img class="img-responsive" src="<?= $params['frontend_source'] ?>/img/order-status_completed.svg"/>
                订单状态: <?= $item['state_info'] ?>
            </div>
            <div class="apply-refund">
                <div class="apply-refund-lft">
                    <img class="img-responsive" src="<?= current($item['cover_preview_url']) ?>"/>
                </div>
                <div class="apply-refund-right">
                    <div class="apply-refund-right-1"><?= $item['title'] ?></div>
                    <p>订单金额: <span>￥<?= $item['price'] ?></span></p>
                    <p>酒店名称: <span><?= $item['hotel_name'] ?></span></p>
                    <p>套餐名称: <span><?= $item['package_name'] ?></span></p>
                    <p>订单编号: <span><?= $item['order_number'] ?></span></p>
                </div>
            </div>

            <?php if ($item['state'] == 4): ?> <!-- 已操作退款 -->

                <?php $info = 'show_refund_info[' . $item['id'] . ']' ?>

                <div class="order-status-button">
                    <div>
                        <button class="appointment-button" kk-tap="<?= $info ?> = !<?= $info ?>">退款说明</button>
                    </div>
                </div>

                <div class="refund-schedule kk-animate ng-hide" ng-class="{'kk-b2s-show': <?= $info ?>}" ng-show="<?= $info ?>">
                    <div class="refund-schedule-name refund-schedule-name-refund">退还款将按支付方式原路返回，不同的支付方式到账时间在及时~7个工作日不等，若逾期还未到账请咨询客服
                    </div>
                </div>

            <?php elseif ($item['state'] == 5): ?> <!-- 已入住 -->

                <?php $form = 'show_bill_form[' . $item['id'] . ']' ?>
                <?php $schedule = 'show_bill_schedule[' . $item['id'] . ']' ?>

                <?php if (!$item['bill_id']): ?>

                    <div class="order-status-button">
                        <div>
                            <button class="appointment-button" kk-tap="<?= $form ?> = !<?= $form ?>">开具发票</button>
                        </div>
                    </div>

                    <div class="invoice-personal kk-animate ng-hide" ng-class="{'kk-b2s-show': <?= $form ?>}" ng-show="<?= $form ?>">

                        <?php $sub = 'bill[' . $item['id'] . ']'; ?>
                        <?php $company = $sub . '.company' ?>

                        <div class="invoice-name">
                            发票抬头:
                        </div>
                        <div class="invoice-title">
                            <span kk-tap="<?= $company ?> = 0" ng-class="{active: !<?= $company ?>}">个人</span>
                            <span kk-tap="<?= $company ?> = 1" ng-class="{active: <?= $company ?>}">公司</span>
                        </div>
                        <div class="invoice-address" ng-show="<?= $company ?>">
                            <p>公司名称:</p>
                            <input type="text" ng-model="<?= $sub ?>.company_name" placeholder="请填写公司名称"/>
                        </div>
                        <div class="invoice-address">
                            <p>快递地址:</p>
                            <input type="text" ng-model="<?= $sub ?>.address" placeholder="请填写快递地址"/>
                        </div>
                        <div class="invoice-confirm">
                            <p></p>
                            <span kk-tap="applyBill(<?= $item['id'] ?>)">提交</span>
                        </div>
                    </div>

                <?php else: ?>

                    <div class="order-status-button">
                        <div>
                            <button class="invoice-schedule" kk-tap="<?= $schedule ?> = !<?= $schedule ?>">发票进度
                            </button>
                        </div>
                    </div>

                    <div class="refund-schedule kk-animate ng-hide" ng-class="{'kk-b2s-show': <?= $schedule ?>}"
                         ng-show="<?= $schedule ?>">
                        <div class="refund-schedule-name">
                            发票开具进度:
                        </div>
                        <div class="refund-schedule-no">
                            <div class="refund-schedule-no-1">
                                <span class="schedule">1</span>
                                <p class="refund-schedule-no-tip">申请已提交</p>
                                <p class="refund-schedule-no-state">发票抬头：<?= $item['invoice_title'] ?: '个人' ?></p>
                            </div>
                        </div>
                        <div class="refund-schedule-no">
                            <div class="refund-schedule-no-1">
                                <span class="schedule">2</span>
                                <p class="refund-schedule-no-tip">KAKE处理中</p>
                                <p class="refund-schedule-no-state">我们将尽快为您处理，请耐心等待1~2个工作日</p>
                            </div>
                        </div>
                        <div class="refund-schedule-no">
                            <div class="refund-schedule-no-1 no-direction">
                                <span <?= $item['courier_number'] ? 'class="schedule"' : null ?>>3</span>

                                <p class="refund-schedule-no-tip">票据快递已发出</p>
                                <?php if ($item['courier_number']): ?>
                                    <p class="refund-schedule-no-state">快递公司：<?= $item['courier_company'] ?: '未知' ?></p>
                                    <p class="refund-schedule-no-state">快递单号：<?= $item['courier_number'] ?></p>
                                <?php endif; ?>
                                <p class="refund-schedule-no-state">收件地址：<?= $item['address'] ?></p>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>

            <?php endif; ?>

            <div class="blank-piece"></div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
