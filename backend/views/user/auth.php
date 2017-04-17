<?php
/* @var $this yii\web\View */

use yii\widgets\LinkPager;
use yii\helpers\Url;

?>

<div class="title col-sm-offset-1"><span
        class="glyphicon glyphicon-cog"></span> 配置用户权限
</div>

<form class="form-horizontal" method="post" action="<?= Url::to(['/user/edit-auth-form']) ?>">
    <input name="<?= Yii::$app->request->csrfParam ?>" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
    <input name="old_auth" type="hidden" value="<?= implode(',', $record) ?>">
    <input name="user_id" type="hidden" value="<?= $user_id ?>">

    <?php foreach ($list as $title => $items): ?>
        <div class="form-group">
            <label class="col-sm-2 control-label"><?= $title ?></label>

            <div class="col-sm-10">
                <?php foreach ($items as $key => $name): ?>
                    <label class="checkbox-inline">
                        <?php $checked = in_array($key, $record) ? 'checked="checked"' : null ?>
                        <input name="new_auth[]" type="checkbox" <?= $checked ?> value="<?= $key ?>"> <?= $name ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-2">
            <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> 提交编辑</button>
        </div>
    </div>
    <br>

</form>