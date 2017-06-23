<?php
/* @var $this yii\web\View */

use yii\widgets\LinkPager;
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
        <?php
        $getStyle = function ($item) {
            $styleArray = [];
            $attributes = [
                'width',
                'max-width',
                'min-width'
            ];
            foreach ($attributes as $attribute) {
                if (!empty($item[$attribute])) {
                    $styleArray[] = $attribute . ':' . $item[$attribute];
                }
            }

            return 'style="' . implode(';', $styleArray) . '"';
        };
        ?>
        <thead>
        <tr>
            <?php if (!empty($recordFilter)): ?>
                <th>
                    <?php if ($recordFilter == 'checkbox'): ?>
                        <a href="javascript:$.checkboxAllSelect($(this), '<?= $recordFilterName ?>');">全选</a>
                    <?php endif; ?>
                </th>
            <?php endif; ?>
            <th>
                #
            </th>
            <?php foreach ($assist as $item): ?>
                <?php
                if (!empty($item['adorn']['tip']) || !empty($item['adorn']['hidden'])) {
                    continue;
                }
                ?>
                <th>
                    <div <?= $getStyle($item['adorn']) ?>><?= $item['title'] ?></div>
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

            $handleVal = function ($val) use ($item) {
                if (is_callable($val)) {
                    $val = call_user_func($val, $item);
                }

                return $val;
            }
            ?>
            <?php
            $tip = null;
            foreach ($assist as $field => $value) {
                if (!empty($value['adorn']['tip'])) {
                    $adorn = $value['adorn'];

                    $notSetFn = isset($adorn['empty']) ? 'empty' : 'isset';
                    $notSetStr = $adorn['not_set_info'];

                    $content = $empty($field, $adorn['not_set_info'], null, $notSetFn);
                    $content = (empty($adorn['tpl']) || $content === $notSetStr) ? $content : sprintf($adorn['tpl'], $content);

                    $tip .= $value['title'] . ': ' . ViewHelper::escapeScript($content) . '<br>';
                }
            }
            if ($tip) {
                $tip = 'data-toggle="tooltip" data-html="true" data-placement="top" title="' . $tip . '"';
            }
            ?>
            <tr <?= $tip ?>>
                <?php if (!empty($recordFilter)): ?>
                    <td>
                        <input type="<?= $recordFilter ?>" name="<?= $recordFilterName ?>" value="<?= $item[$recordFilterValueName] ?>">
                    </td>
                <?php endif; ?>
                <td>
                    <?php
                    $number = ($page->getPage() * $page->getPageSize()) + $key + 1;
                    $number = str_pad($number, 3, '0', STR_PAD_LEFT);
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
                        <div <?= $getStyle($value['adorn']) ?>>
                            <?php
                            $adorn = $value['adorn'];
                            $color = $adorn['color'];
                            $color = $handleVal($color);
                            if (is_array($color)) {
                                $colorKey = $empty($field);
                                $color = isset($color[$colorKey]) ? $color[$colorKey] : $color[$colorKey ? 1 : 0];
                            }
                            ?>
                            <?php $notSetFn = isset($adorn['empty']) ? 'empty' : 'isset' ?>
                            <?php $notSetStr = $adorn['not_set_info'] ?>
                            <?php
                            switch ($notSetFn) {
                                case 'empty' :
                                    $code = !empty($item[$field]);
                                    break;
                                case 'isset' :
                                    $code = isset($item[$field]);
                                    break;
                            }
                            ?>
                            <?php $codeBegin = (isset($adorn['code']) && $code) ? '<code class="' . $color . '">' : null ?>
                            <?php $codeEnd = $codeBegin ? '</code>' : null ?>

                            <?= $codeBegin ?>
                            <?php if (isset($adorn['img'])): ?>
                                <?php
                                $img = $empty($field, []);
                                $img = is_string($img) ? $img : current($img);
                                ?>
                                <?php if (empty($img)): ?>
                                    <?= $notSetStr ?>
                                <?php else: ?>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <a href="javascript:void(0)" class="thumbnail">
                                                <img src="<?= $img ?>">
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php elseif (isset($adorn['info'])): ?>
                                <?php $content = $empty($field, null, null, $notSetFn) ?>
                                <?= is_null($content) ? $notSetStr : $empty($field . '_info', $notSetStr, null, $notSetFn) ?>
                            <?php elseif (isset($adorn['link'])): ?>
                                <?= $empty($field) ? '<a href="' . $empty($field) . '" target="_blank">' . $adorn['url_info'] . '</a>' : $notSetStr ?>
                            <?php elseif (isset($adorn['html'])): ?>
                                <?= $empty($field, $notSetStr, null, $notSetFn) ?>
                            <?php else: ?>
                                <?php
                                $content = $empty($field, null, null, $notSetFn);
                                $content = is_null($content) ? $notSetStr : Html::encode($content);
                                $content = (empty($adorn['tpl']) || $content === $notSetStr) ? $content : sprintf($handleVal($adorn['tpl']), $content);
                                echo $content;
                                ?>
                            <?php endif; ?>
                            <?= $codeEnd ?>
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
        <?php
        echo LinkPager::widget([
            'pagination' => $page,
            'firstPageLabel' => true,
            'lastPageLabel' => true
        ]);
        ?>
        <?php if (!empty($ajaxPage)): ?>
            <script type="text/javascript">
                $.ajaxPageList('<?= $action ?>');
            </script>
        <?php endif; ?>
    </div>
</div>