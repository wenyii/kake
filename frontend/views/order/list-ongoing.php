<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$params = \Yii::$app->params;
?>

<?php if (!empty($list)): ?>
    <?php foreach ($list as $item): ?>
        <div class="order-status">
            <div class="order-status-no-appointment">
                <img class="img-responsive" src="<?= $params['frontend_source'] ?>/img/order-status_<?= $item['state'] ?>.svg"/>
                订单状态: <?= empty($item['payment_state']) ? '未支付' : $item['state_info'] ?>
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

            <?php if (empty($item['payment_state'])): ?>

                <div class="order-status-button">
                    <div>
                        <button class="cancel-button" kk-tap="cancelOrder('<?= $item['order_number'] ?>')">取消订单</button>
                        <button class="appointment-button" kk-tap="paymentAgain(<?= $item['payment_method'] ?>, '<?= $item['order_number'] ?>')">立即支付</button>
                    </div>
                </div>

            <?php elseif ($item['state'] == 0): ?> <!-- 未预约 -->

                <?php $refund = 'show_refund_form[' . $item['id'] . ']' ?>
                <?php $order = 'show_order_form[' . $item['id'] . ']' ?>

                <div class="order-status-button">
                    <div>
                        <button class="cancel-button" kk-tap="<?= $refund ?> = !<?= $refund ?>; <?= $order ?> = 0">申请退款</button>
                        <button class="appointment-button" kk-tap="<?= $order ?> = !<?= $order ?>; <?= $refund ?> = 0">立即预约</button>
                    </div>
                </div>

                <div class="invoice-personal kk-animate" ng-class="{'kk-b2s': <?= $refund ?>}" ng-show="<?= $refund ?>">

                    <?php $sub = 'refund[' . $item['id'] . ']'; ?>

                    <div class="invoice-address">
                        <p>退款原因:</p>
                        <textarea type="text" ng-model="<?= $sub ?>.remark" placeholder="请填写退款原因"/></textarea>
                    </div>
                    <div class="invoice-confirm">
                        <p></p>
                        <span kk-tap="applyRefund(<?= $item['id'] ?>)">提交</span>
                    </div>
                </div>

                <div class="invoice-personal kk-animate" ng-class="{'kk-b2s': <?= $order ?>}" ng-show="<?= $order ?>">

                    <?php $sub = 'order[' . $item['id'] . ']'; ?>

                    <div class="invoice-address">
                        <p>入住人姓名:</p>
                        <input type="text" ng-model="<?= $sub ?>.name" placeholder="请填写入住人姓名"/>
                    </div>
                    <div class="invoice-address">
                        <p>入住人电话:</p>
                        <input type="text" ng-model="<?= $sub ?>.phone" placeholder="请填写入住人联系方式"/>
                    </div>
                    <div class="invoice-address">
                        <p>入住预约日:</p>
                        <input type="date" ng-model="<?= $sub ?>.date" placeholder="请选择入住日期"/>
                    </div>
                    <div class="invoice-confirm">
                        <p></p>
                        <span kk-tap="applyOrder(<?= $item['id'] ?>)">提交</span>
                    </div>
                </div>

            <?php elseif ($item['state'] == 1): ?> <!-- 预约中 -->

                <?php $info = 'show_order_info[' . $item['id'] . ']' ?>

                <div class="order-status-button">
                    <div>
                        <button class="appointment-button" kk-tap="<?= $info ?> = !<?= $info ?>">预约信息</button>
                    </div>
                </div>

                <div class="refund-schedule kk-animate" ng-class="{'kk-b2s': <?= $info ?>}" ng-show="<?= $info ?>">
                    <div class="refund-schedule-name">
                        <p>入住人姓名：<?= $item['check_in_name'] ?></p>
                        <p>入住人电话：<?= $item['check_in_phone'] ?></p>
                        <p>入住预约日：<?= $item['check_in_time'] ?></p>
                    </div>
                </div>

            <?php elseif ($item['state'] == 2): ?> <!-- 待入住 -->

                <?php $info = 'show_order_info[' . $item['id'] . ']' ?>

                <div class="order-status-button">
                    <div>
                        <button class="cancel-button" kk-tap="completed(<?= $item['id'] ?>)">我已入住</button>
                        <button class="appointment-button" kk-tap="<?= $info ?> = !<?= $info ?>">查看确认号</button>
                    </div>
                </div>

                <div class="refund-schedule kk-animate" ng-class="{'kk-b2s': <?= $info ?>}" ng-show="<?= $info ?>">
                    <div class="refund-schedule-name">
                        <p>入住人姓名：<?= $item['check_in_name'] ?></p>
                        <p>入住人电话：<?= $item['check_in_phone'] ?></p>
                        <p>入住预约日：<?= $item['check_in_time'] ?></p>
                    </div>
                </div>

                <div class="confirmation-number kk-animate" ng-class="{'kk-b2s': <?= $info ?>}" ng-show="<?= $info ?>">
                    <div class="confirmation-number-name">
                        确认号:
                    </div>
                    <div class="confirmation-number-no"><?= $item['conformation_number'] ?: '请联系客服更新' ?></div>
                    <div class="note">备注: 确认号可以唯一确定入住人的身份</div>
                </div>

            <?php elseif ($item['state'] == 3): ?> <!-- 退款申请中 -->

                <?php $info = 'show_refund_info[' . $item['id'] . ']' ?>

                <div class="order-status-button">
                    <div>
                        <button class="appointment-button" kk-tap="<?= $info ?> = !<?= $info ?>">退款信息</button>
                    </div>
                </div>

                <div class="refund-schedule kk-animate" ng-class="{'kk-b2s': <?= $info ?>}" ng-show="<?= $info ?>">
                    <div class="refund-schedule-name refund-schedule-name-refund"><?= $item['remark'] ?></div>
                </div>

            <?php endif; ?>

            <div class="blank-piece"></div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
