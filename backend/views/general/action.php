<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\helpers\Html;
use common\components\Helper;

$flash = \Yii::$app->session->hasFlash('list') ? \Yii::$app->session->getFlash('list') : [];

$controller = \Yii::$app->controller->id;
$action = \Yii::$app->controller->action->id;
$modal = empty($view['modal']) ? false : true;
?>

<?php if (!$modal): ?>
    <div class="title col-sm-offset-1"><span
            class="glyphicon glyphicon-<?= $view['title_icon'] ?>"></span> <?= $view['title_info'] ?><?= $modelInfo ?>
    </div>
<?php endif; ?>

<?php

$escapeScript = function ($script) {
    $script = str_replace('"', '&quot;', $script);
    $script = str_replace('\'', '&apos;', $script);
    return $script;
};

if ($modal) {
    $script = empty($view['action']) ? 'false' : $escapeScript($view['action']);
    $action = 'onsubmit="return ' . $script . '"';
} else {
    $action = 'method="post" action="' . Url::to(['/' . $controller . '/' . $view['action']]) . '"';
}
?>
<form class="form-horizontal" <?= $action ?>>
    <?php if (!$modal): ?>
        <input name="<?= Yii::$app->request->csrfParam ?>" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
    <?php endif; ?>

    <?php if (!empty($view['action']) && $view['action'] == 'edit-form'): ?>
        <input name="id" type="hidden" value="<?= $id ?>">
    <?php endif; ?>

    <?php $pre_same_row = false ?>
    <?php foreach ($list as $field => $item): ?>

        <?php
        $empty = function ($key, $default = null, $data = null, $fn = 'empty') use ($item) {
            $data = $data ?: $item;
            $fn = $fn . 'Default';

            return Helper::$fn($data, $key, $default);
        };

        // 附加的组件
        $html_add = null;
        if (!empty($item['add'])) {
            $html_add = '<div class="col-sm-' . $empty('label', 2, $item['add']) . '">' . $empty('html', null, $item['add']) . '</div>';
        }

        // 同一行
        $same_row = $empty('same_row');

        // 是否隐藏
        $html_begin_div = $pre_same_row ? null : '<div class="form-group ' . ($empty('hidden', false) ? 'hidden' : null) . '">';
        $html_end_div = $same_row ? null : '</div>';

        $pre_same_row = $same_row;

        // 栅格数和标题
        $html_label = ($item['title'] === false) ? null : '<label class="col-sm-2 control-label">' . $empty('title') . '</label>';

        // element
        $element = $empty('elem', 'input');

        // attribute value
        $av_name = $empty('name', $field);
        $av_type = $empty('type', 'text');
        $av_value = !empty($flash[$av_name]) ? $flash[$av_name] : $empty('value', null, null, 'isset');

        // attribute string
        $as_readonly = empty($item['readonly']) ? null : 'readonly=readonly';
        $as_placeholder = 'placeholder="' . $empty('placeholder') . '"';

        if (!is_array($av_value)) {
            $av_value = Html::encode($av_value);
            $as_value = 'value="' . strval($av_value) . '"';
        }

        $as_name = 'name="' . $av_name . '"';
        $as_type = 'type="' . $av_type . '"';

        $as_tip = null;
        if ($tip = $empty('tip')) {
            $as_tip = 'data-toggle="tooltip" data-html="true" data-placement="' . $empty('placement', 'right') . '" title="' . $tip . '"';
        }
        ?>

        <?= $html_begin_div ?>
        <?= $html_label ?>
        <?php if ($element == 'input'): ?> <!-- input -->
        <div class="col-sm-<?= $empty('label', 3) ?>" <?= $as_tip ?>>
            <?php $as_name = (($av_type == 'file' ? 'id' : 'name') . '=' . $av_name) ?>
            <input class="form-control"
                <?= $as_name ?>
                <?= $as_readonly ?>
                <?= $as_placeholder ?>
                <?= $as_type ?>
                <?= $as_value ?>>
        </div>
    <?php elseif ($element == 'img'): ?> <!-- img -->
        <div class="col-sm-<?= $empty('label', 10) ?>" <?= $as_tip ?>>
            <div class="row" <?= $as_name ?>>
                <?php
                $attachment = (array) $empty($field, $empty('value'), $flash);
                if (empty($attachment)) {
                    $attachment = [];
                }
                ?>

                <?php $uploader = Helper::issetDefault($list, $empty('upload_key')) ?>
                <?php if (!empty($attachment)): ?>
                    <script type="text/javascript">
                        $(function () {
                            <?php foreach ($attachment as $id => $url): ?>
                            $.createThumb({
                                data: <?= json_encode(compact('id', 'url'), JSON_UNESCAPED_UNICODE) ?>,
                                attachmentName: '<?= Helper::emptyDefault($uploader, 'field_name') ?>',
                                previewName: '<?= Helper::emptyDefault($uploader, 'preview_name') ?>',
                                previewLabel: '<?= $empty('img_label', 4) ?>',
                                multiple: '<?= Helper::emptyDefault($uploader, 'multiple', 0) ?>'
                            });
                            <?php endforeach; ?>
                        });
                    </script>
                <?php endif; ?>
            </div>
        </div>
    <?php elseif ($element == 'select'): ?> <!-- select -->
        <div class="col-sm-<?= $empty('label', 2) ?>" <?= $as_tip ?>>
            <?php
            $value = $empty('value');
            $selected = Helper::issetDefault($flash, $field, $value['selected']);
            echo Helper::createSelect($value['list'], $value['name'], $selected, 'key', $empty('readonly', false));
            ?>
        </div>
    <?php elseif ($element == 'textarea'): ?> <!-- textarea -->
        <div class="col-sm-<?= $empty('label', 6) ?>" <?= $as_tip ?>>
            <?php $as_row = 'rows="' . $empty('row', 3) . '"' ?>
            <textarea class="form-control"
                <?= $as_name ?>
                <?= $as_row ?>
                <?= $as_placeholder ?>><?= $av_value ?></textarea>
        </div>
    <?php elseif ($item['elem'] == 'ckeditor'): ?> <!-- ckeditor -->
        <div class="col-sm-<?= $empty('label', 10) ?>" <?= $as_tip ?>>
            <textarea
                <?= $as_name ?>
                <?= $as_placeholder ?>><?= $av_value ?></textarea>
        </div>
        <script type="text/javascript">
            var <?= $av_name ?> =
            CKEDITOR.replace('<?= $av_name ?>', {
                width: <?= $empty('width', 700) ?>,
                height: <?= $empty('height', 200) ?>,
                files: []
            });
        </script>
    <?php elseif ($item['elem'] == 'tag'): ?>  <!-- tag -->
        <div class="col-sm-<?= $empty('label', 6) ?>" <?= $as_tip ?> <?= $as_name ?>
             format="<?= $empty('format') ?>"></div>
        <?php if (!empty($av_value)): ?>
            <script type="text/javascript">
                $(function () {
                    <?php foreach ($av_value as $pk): ?>
                    $.createTag({
                        data: <?= json_encode($pk, JSON_UNESCAPED_UNICODE) ?>,
                        containerName: '<?= $av_name ?>',
                        fieldName: '<?= $empty('field_name') ?>',
                        fieldNameNew: 'new_<?= $empty('field_name') ?>'
                    });
                    <?php endforeach; ?>
                });
            </script>
        <?php endif; ?>
    <?php elseif ($item['elem'] == 'button'): ?>  <!-- button -->
    <?php
    $script = $escapeScript($empty('script'));
    $script = empty($script) ? '' : 'onclick="' . $script . '"';
    ?>
        <div class="col-sm-<?= $empty('label', 6) ?>" <?= $as_tip ?> <?= $as_name ?> format="<?= $empty('format') ?>">
            <button type="button"
                    class="btn btn-<?= $empty('level', 'primary') ?>" <?= $script ?>><?= $av_value ?></button>
        </div>
    <?php endif; ?>
    <?= $html_add ?>
    <?= $html_end_div ?>

    <?php if ($element == 'input' && $av_type == 'file'): ?>
    <?php $previewRule = Helper::emptyDefault($list, $empty('preview_name'), []) ?>
        <script type="text/javascript">
            $(function () {
                $.uploadAttachment({
                    uploadInput: $('#<?= $av_name ?>'),
                    action: '<?= Url::to(['general/ajax-upload']) ?>',
                    data: {
                        'tag': '<?= $empty('tag') ?>',
                        'controller': '<?= Yii::$app->controller->id ?>',
                        'action': '<?= Yii::$app->controller->action->id ?>',
                        '<?= Yii::$app->request->csrfParam ?>': '<?= Yii::$app->request->csrfToken ?>'
                    },
                    attachmentName: '<?= $empty('field_name') ?>',
                    previewName: '<?= $empty('preview_name', 0) ?>',
                    multiple: '<?= $empty('multiple', 0) ?>',
                    previewLabel: '<?= Helper::emptyDefault($previewRule, 'img_label', 4) ?>'
                });
            });
        </script>
        <div class="form-group">
            <label class="col-sm-2 control-label"></label>

            <div class="col-sm-<?= $empty('label_tips', 4) ?>" <?= $as_tip ?>>
                <table class="table table-bordered table-striped">
                    <tbody>
                    <?php if ($empty('multiple', 0)): ?>
                        <tr>
                            <td><kbd>允许多张</kbd></td>
                            <td><code class="success">是</code></td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($item['rules'] as $k => $v): ?>
                        <tr>
                            <td><kbd><?= $item['rules_info'][$k] ?></kbd></td>
                            <td><code class="info"><?= is_array($v) ? implode(',', $v) : $v ?></code></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
    <?php endforeach; ?>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-2">
            <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span>
                <?= $view['button_info'] ?><?= $modal ? null : $modelInfo ?></button>
        </div>
    </div>
    <br>
</form>