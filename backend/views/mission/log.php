<?php
/* @var $this yii\web\View */

?>

<div class="mission">
    <button
        type="button"
        class="btn btn-primary btn-lg btn-block"
        data-action-tag="flush-lately-backend-log">刷新最近后台日志 - 快
    </button>
    <button
        type="button"
        class="btn btn-warning btn-lg btn-block"
        data-action-tag="flush-all-backend-log">刷新所有后台日志 - 慢
    </button>
    <hr>
    <button
        type="button"
        class="btn btn-primary btn-lg btn-block"
        data-action-tag="flush-lately-frontend-log">刷新最近前台日志 - 快
    </button>
    <button
        type="button"
        class="btn btn-warning btn-lg btn-block"
        data-action-tag="flush-all-frontend-log">刷新所有前台日志 - 慢
    </button>
    <hr>
    <button
        type="button"
        class="btn btn-primary btn-lg btn-block"
        data-action-tag="flush-lately-service-log">刷新最近服务日志 - 快
    </button>
    <button
        type="button"
        class="btn btn-warning btn-lg btn-block"
        data-action-tag="flush-all-service-log">刷新所有服务日志 - 慢
    </button>
</div>