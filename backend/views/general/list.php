<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use common\components\Helper;
use backend\components\ViewHelper;

$flash = \Yii::$app->session->hasFlash('list') ? \Yii::$app->session->getFlash('list') : [];

$controller = \Yii::$app->controller->id;
$action = \Yii::$app->controller->action->id;
?>

<div id="<?= $action ?>">
    <?php if (!empty($filter)): ?>
        <form class="form-inline filter">
            <input type="hidden" name="r" value="<?= $controller ?>/<?= $action ?>">
            <?php foreach ($filter as $field => $item): ?>

                <?php
                $empty = function ($key, $default = null, $data = null, $fn = 'empty') use ($item) {
                    $data = $data ?: $item;
                    $fn = $fn . 'Default';

                    return Helper::$fn($data, $key, $default);
                };
                ?>

                <?= $empty('html') ?>
                <div class="form-group">
                    <label><?= $empty('title') ?></label>
                    <?php if ($item['elem'] == 'input' && $empty('between')): ?> <!-- date -->
                        From
                        <?php $from = '_from'; ?>
                        <?php $to = '_to'; ?>
                        <input class="form-control"
                               type="<?= $empty('type', 'text') ?>"
                               name="<?= $field . $from ?>"
                               placeholder="<?= $empty('placeholder' . $from) ?>"
                               value="<?= $empty('value' . $from) ?>" <?= empty($item['readonly' . $from]) ? null : 'readonly=readonly' ?>>
                        To
                        <input class="form-control"
                               type="<?= $empty('type', 'text') ?>"
                               name="<?= $field . $to ?>"
                               placeholder="<?= $empty('placeholder' . $to) ?>"
                               value="<?= $empty('value' . $to) ?>" <?= empty($item['readonly' . $to]) ? null : 'readonly=readonly' ?>>
                    <?php elseif ($item['elem'] == 'input'): ?> <!-- input -->
                        <input class="form-control"
                               type="<?= $empty('type', 'text') ?>"
                               name="<?= $field ?>"
                               placeholder="<?= $empty('placeholder') ?>"
                               value="<?= Html::encode($empty('value')) ?>" <?= empty($item['readonly']) ? null : 'readonly=readonly' ?>>
                    <?php elseif ($item['elem'] == 'select'): ?> <!-- select -->
                        <?php
                        $value = $empty('value');
                        echo Helper::createSelect($value['list'], $field, $value['selected']);
                        ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary">筛选</button>
            <?php if (!empty($ajaxFilter)): ?>
                <script type="text/javascript">
                    $.ajaxFilterList('<?= $action ?>');
                </script>
            <?php endif; ?>
        </form>
        <hr>
    <?php endif; ?>

    <?php $operationsHtml = ViewHelper::createButton($operations, $controller) ?>
    <?php if (!empty($operations) && $operationsPosition == 'top'): ?>
        <?= $operationsHtml ?>
        <hr>
    <?php endif; ?>

    <table class="table table-hover">
        <thead>
        <tr>
            <?php if (!empty($recordFilter)): ?>
                <th>
                    <?php if ($recordFilter == 'checkbox'): ?>
                        <a href="javascript:$.checkboxAllSelect($(this), '<?= $recordFilterName ?>');">全选</a>
                    <?php endif; ?>
                </th>
            <?php endif; ?>
            <th>No</th>
            <?php foreach ($assist as $item): ?>
                <?php
                if (!empty($item['adorn']['tip']) || !empty($item['adorn']['hidden'])) {
                    continue;
                }
                ?>
                <th>
                    <div <?= ViewHelper::getStyleByAdorn($item['adorn']) ?>><?= $item['title'] ?></div>
                </th>
            <?php endforeach; ?>
            <?php if (!empty($operation)): ?>
                <th>
                    <div>操作</div>
                </th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($list as $key => $item): ?>
            <?php
            $empty = function ($key, $default = null, $data = null, $fn = 'empty') use ($item) {
                $data = $data ?: $item;
                $fn = $fn . 'Default';

                return Helper::$fn($data, $key, $default);
            };

            $tip = [];
            $maxLen = 0;
            foreach ($assist as $field => $value) {
                if (!empty($value['adorn']['tip'])) {
                    $content = ViewHelper::adornHtml($field, $value, $item);
                    $tip[$value['title']] = ViewHelper::escapeScript($content);

                    $maxLen = max($maxLen, strlen($value['title']));
                }
            }

            if (!empty($tip)) {
                array_walk($tip, function(&$value, $key) use ($maxLen) {
                    $key = str_pad($key, $maxLen, '　', STR_PAD_LEFT);
                    $value = $key . ' : ' . $value;
                });
                $tip = implode('<br>', $tip);
                $tip = 'data-toggle="tooltip" data-html="true" data-placement="top" title="' . $tip . '"';
            } else {
                $tip = null;
            }
            ?>

            <tr <?= $tip ?>>
                <?php if (!empty($recordFilter)): ?>
                    <td>
                        <input type="<?= $recordFilter ?>"
                               name="<?= $recordFilterName ?>"
                               value="<?= $item[$recordFilterValueName] ?>">
                    </td>
                <?php endif; ?>
                <td>
                    <?php
                    $number = ($page->getPage() * $page->getPageSize()) + $key + 1;
                    $number = str_pad($number, 2, '0', STR_PAD_LEFT);
                    ?>
                    <p class="text-muted list-p"><?= $number ?></p>
                </td>

                <?php foreach ($assist as $field => $value): ?>
                    <?php
                    if (!empty($value['adorn']['tip']) || !empty($value['adorn']['hidden'])) {
                        continue;
                    }
                    ?>
                    <td>
                        <div <?= ViewHelper::getStyleByAdorn($value['adorn']) ?>>
                            <?= ViewHelper::adornHtml($field, $value, $item) ?>
                        </div>
                    </td>
                <?php endforeach; ?>

                <?php if (!empty($operation)): ?>
                    <td>
                        <div class="operation">
                            <?= ViewHelper::createButtonForRecord($operation, $item, $controller, 'xs') ?>
                        </div>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (!empty($operations) && $operationsPosition == 'bottom'): ?>
        <hr>
        <?= $operationsHtml ?>
    <?php endif; ?>

    <div class="page">
        <?= ViewHelper::page($page) ?>
        <?php if (!empty($ajaxPage)): ?>
            <script type="text/javascript">
                $.ajaxPageList('<?= $action ?>');
            </script>
        <?php endif; ?>
    </div>
</div>