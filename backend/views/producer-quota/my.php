<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

?>

<div class="title col-sm-offset-1">
    <span class="glyphicon glyphicon-usd"></span> 申请提现
</div>

<form class="form-horizontal" method="post" action="<?= Url::to(['/producer-quota/withdraw']) ?>">
    <input name="<?= Yii::$app->request->csrfParam ?>" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
    <div class="form-group">
        <label class="col-sm-2 control-label">佣金余额</label>
        <div class="col-sm-6">
            <div class="page-header">
                <h1><?= $quota ?>
                    <small>提现金不能大于此余额</small>
                </h1>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">提现金额</label>
        <div class="col-sm-3 input-group-lg">
            <input class="form-control" type="text" name="quota">
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-2">
            <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> 申请提现</button>
        </div>
    </div>
    <br>
</form>