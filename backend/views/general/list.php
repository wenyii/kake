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

    <!-- 当前 url -->
    <input type="hidden" name="current-url" value="<?= Yii::$app->request->getHostInfo() . Yii::$app->request->url ?>">

    <!-- 筛选器 -->
    <?php if (!empty($filter)): ?>
        <form class="form-inline filter">
            <input type="hidden" name="r" value="<?= $controller ?>/<?= $action ?>">
            <?php if ($sortQuery = Yii::$app->request->get('sorter')): ?>
                <input type="hidden" name="sorter" value="<?= $sortQuery ?>">
            <?php endif; ?>
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

            <!-- ajax 筛选 -->
            <?php if (!empty($ajaxFilter)): ?>
                <script type="text/javascript">
                    $.ajaxFilterList('<?= $action ?>');
                </script>
            <?php endif; ?>
        </form>
        <hr>
    <?php endif; ?>

    <!-- 全局按钮 - 顶部 -->
    <?php $operationsHtml = ViewHelper::createButton($operations, $controller) ?>
    <?php if (!empty($operations) && $operationsPosition == 'top'): ?>
        <?= $operationsHtml ?>
        <hr>
    <?php endif; ?>

    <!-- 列表 -->
    <table class="table table-hover">

        <!-- 列表 - 标题 -->
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
            <?php foreach ($assist as $key => $item): ?>
                <?php
                if (!empty($item['adorn']['tip']) || !empty($item['adorn']['hidden'])) {
                    continue;
                }
                ?>
                <th>
                    <div <?= ViewHelper::getStyleByAdorn($item['adorn']) ?>>
                        <?= $item['title'] ?>
                        <?php
                        if (!empty($sorter[$key])):
                            $sortClsMap = [
                                'natural' => 'glyphicon-sort',
                                'desc' => 'glyphicon-sort-by-alphabet-alt',
                                'asc' => 'glyphicon-sort-by-alphabet'
                            ];
                            $sort = empty($sorter[$key]['value']) ? 'natural' : strtolower($sorter[$key]['value']);
                            $sortIndex = array_flip(array_keys($sortClsMap))[$sort];
                            ?>
                            <span sort-index="<?= $sortIndex ?>"
                                  sort-name="<?= $sorter[$key]['name'] ?>"
                                  class="glyphicon <?= $sortClsMap[$sort] ?> sort-btn"
                                  title='
                                      点击按钮依次排序<br>
                                      <span class="glyphicon glyphicon-sort"></span> 无排序<br>
                                      <span class="glyphicon glyphicon-sort-by-alphabet-alt"></span> 降序排列<br>
                                      <span class="glyphicon glyphicon-sort-by-alphabet"></span> 升序排序'
                                  data-toggle="tooltip"
                                  data-html="true"
                                  data-placement="top"></span>
                        <?php endif; ?>
                    </div>
                </th>
            <?php endforeach; ?>

            <!-- ajax 排序 -->
            <?php if (!empty($ajaxPage)): ?>
                <script type="text/javascript">
                    $.ajaxSorterList('<?= $action ?>');
                </script>
            <?php endif; ?>

            <?php if (!empty($operation)): ?>
                <th>
                    <div>操作</div>
                </th>
            <?php endif; ?>
        </tr>
        </thead>

        <!-- 列表 - 内容 -->
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
                array_walk($tip, function (&$value, $key) use ($maxLen) {
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

    <!-- 全局按钮 - 底部 -->
    <?php if (!empty($operations) && $operationsPosition == 'bottom'): ?>
        <hr>
        <?= $operationsHtml ?>
    <?php endif; ?>

    <!-- 分页 -->
    <div class="page">
        <?= ViewHelper::page($page) ?>

        <!-- ajax 分页 -->
        <?php if (!empty($ajaxPage)): ?>
            <script type="text/javascript">
                $.ajaxPageList('<?= $action ?>');
            </script>
        <?php endif; ?>
    </div>
</div>