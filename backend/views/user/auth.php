<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

?>

<div class="title col-sm-offset-1">
    <span class="glyphicon glyphicon-cog"></span> 配置用户权限
</div>

<form class="form-horizontal" method="post" action="<?= Url::to(['/user/edit-auth-form']) ?>">
    <input name="<?= Yii::$app->request->csrfParam ?>" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">

    <input name="old_auth" type="hidden" value="<?= implode(',', $record) ?>">
    <input name="user_id" type="hidden" value="<?= $user_id ?>">

    <div class="form-group">
        <label class="col-sm-2 control-label"></label>

        <div class="col-sm-10 form-inline">
            <button type="button" class="btn btn-default" onclick="selectAll();">全选中</button>
            <button type="button" class="btn btn-default" onclick="cancelSelectAll();">全取消</button>

            <select class="form-control" name="clone-admin">
                <?php foreach ($admin as $uid => $name): ?>
                    <option value="<?= $uid ?>"><?= $name ?></option>
                <?php endforeach; ?>
            </select>
            <button type="button" class="btn btn-primary" onclick="cloneAdmin();">克隆权限</button>
            <script>
                var selectAll = function () {
                    $('input[name="new_auth[]"]').attr('checked', true);
                };
                var cancelSelectAll = function () {
                    $('input[name="new_auth[]"]').attr('checked', false);
                };
                var cloneAdmin = function () {
                    var uid = $('select[name="clone-admin"]').val();
                    $.sendGetAsync(requestUrl + 'user/ajax-get-user-auth&id=' + uid, function (data) {
                        cancelSelectAll();
                        $.each(data.data, function (k, v) {
                            $('input[value="' + k + '"]').attr('checked', !!parseInt(v));
                        });
                    });
                };
            </script>
        </div>
    </div>

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