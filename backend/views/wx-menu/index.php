<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
?>

<div class="title col-sm-offset-1"><span class="glyphicon glyphicon-cog"></span> 编辑菜单接口</div>

<form class="form-horizontal" method="post" action="<?= Url::to(['/wx-menu/edit']) ?>">

    <input name="<?= Yii::$app->request->csrfParam ?>" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
    <div class="form-group">
        <label class="col-sm-2 control-label">JSON代码</label>

        <div class="col-sm-8">
            <textarea class="form-control" rows="15" name="menu"><?= $menu ?></textarea>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-6">
            <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> 提交编辑</button>
            <a class="btn btn-default" target="_blank" href="http://www.bejson.com/">JSON工具</a>
        </div>
    </div>
    <br>
</form>