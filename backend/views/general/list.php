<?php
/* @var $this yii\web\View */

use yii\widgets\LinkPager;
use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use common\components\Helper;

$flash = \Yii::$app->session->hasFlash('list') ? \Yii::$app->session->getFlash('list') : [];

$controller = \Yii::$app->controller->id;
$action = \Yii::$app->controller->action->id;
?>

<div id="<?= $action ?>">
    <?php
    $escapeScript = function ($script) {
        $script = str_replace('"', '&quot;', $script);
        $script = str_replace('\'', '&apos;', $script);

        return $script;
    };

    $escapeParams = function ($params) use ($escapeScript) {
        $paramsStr = '';
        foreach ($params as $item) {
            if (is_array($item)) {
                $paramsStr .= $escapeScript(json_encode($item)) . ', ';
            } else {
                $paramsStr .= '&quot;' . $item . '&quot;, ';
            }
        }

        return '(' . rtrim($paramsStr, ', ') . ')';
    };
    ?>
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
                               name="<?= $empty('name', $field) . $from ?>"
                               placeholder="<?= $empty('placeholder' . $from) ?>"
                               value="<?= $empty('value' . $from) ?>" <?= empty($item['readonly' . $from]) ? null : 'readonly=readonly' ?>>
                        To
                        <input class="form-control"
                               type="<?= $empty('type', 'text') ?>"
                               name="<?= $empty('name', $field) . $to ?>"
                               placeholder="<?= $empty('placeholder' . $to) ?>"
                               value="<?= $empty('value' . $to) ?>" <?= empty($item['readonly' . $to]) ? null : 'readonly=readonly' ?>>
                    <?php elseif ($item['elem'] == 'input'): ?> <!-- input -->
                        <input class="form-control"
                               type="<?= $empty('type', 'text') ?>"
                               name="<?= $empty('name', $field) ?>"
                               placeholder="<?= $empty('placeholder') ?>"
                               value="<?= Html::encode($empty('value')) ?>" <?= empty($item['readonly']) ? null : 'readonly=readonly' ?>>
                    <?php elseif ($item['elem'] == 'select'): ?> <!-- select -->
                        <?php
                        $value = $empty('value');
                        echo Helper::createSelect($value['list'], $value['name'], $value['selected']);
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

    <?php
    if (!empty($operations)) {
        $operationsHtml = '';
        foreach ($operations as $value) {
            $script = Helper::emptyDefault($value, 'script', false);
            $level = Helper::emptyDefault($value, 'level', 'primary');
            $params = Helper::emptyDefault($value, 'params', []);

            if ($script) {
                $url = 'javascript:' . $escapeScript($value['value']);
            } else {
                if (strpos($value['value'], 'http') === 0) {
                    $url = $value['value'];
                } else {
                    $url = strpos($value['value'], '/') ? $value['value'] : ($controller . '/' . $value['value']);
                    $url = Url::to(array_merge([$url], $params));
                }
            }

            $icon = empty($value['icon']) ? null : '<span class="glyphicon glyphicon-' . $value['icon'] . '"></span>';
            $operationsHtml .= '<a href="' . $url . '" class="btn btn-' . $level . '">' . $icon . ' ' . $value['text'] . '</a>';
        }
    }
    ?>

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
                if (!empty($item['adorn']['tip'])) {
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

                    $tip .= $value['title'] . ': ' . $escapeScript($content) . '<br>';
                }
            }
            if ($tip) {
                $tip = 'data-toggle="tooltip" data-html="true" data-placement="top" title="' . $tip . '"';
            }
            ?>
            <tr <?= $tip ?>>
                <?php if (!empty($recordFilter)): ?>
                    <td>
                        <input type="<?= $recordFilter ?>" name="<?= $recordFilterName ?>" value="<?= $item['id'] ?>">
                    </td>
                <?php endif; ?>
                <td>
                    <?= ($page->getPage() * $page->getPageSize()) + $key + 1 ?>
                </td>

                <?php foreach ($assist as $field => $value): ?>
                    <?php
                    if (!empty($value['adorn']['tip'])) {
                        continue;
                    }
                    ?>
                    <td>
                        <div <?= $getStyle($value['adorn']) ?>>
                            <?php
                            $adorn = $value['adorn'];
                            $color = $adorn['color'];
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
                                $content = (empty($adorn['tpl']) || $content === $notSetStr) ? $content : sprintf($adorn['tpl'], $content);
                                echo $content;
                                ?>
                            <?php endif; ?>
                            <?= $codeEnd ?>
                        </div>
                    </td>
                <?php endforeach; ?>

                <?php if (!empty($operation)): ?>
                    <td>
                        <div>
                            <?php foreach ($operation as $value): ?>
                                <?php
                                $show = true;
                                if (!empty($value['show_condition']) && is_callable($value['show_condition'])) {
                                    $show = $value['show_condition']($item);
                                }

                                if (!$show) {
                                    continue;
                                }

                                $type = Helper::emptyDefault($value, 'type', 'url');
                                $level = Helper::emptyDefault($value, 'level', 'primary');

                                $defaultParams = $type == 'url' ? ['id'] : null;
                                $params = Helper::emptyDefault($value, 'params', $defaultParams);
                                if (is_callable($params)) {
                                    $params = $params($item);
                                } else {
                                    $params = Helper::pullSome($item, $params);
                                }

                                if ($type == 'url') {
                                    $url = strpos($value['value'], '/') ? $value['value'] : ($controller . '/' . $value['value']);
                                    $url = Url::to(array_merge([$url], $params));
                                } else if ($type == 'script') {
                                    $params = $params ? $escapeParams($params) : '';
                                    $url = 'javascript:' . $escapeScript($value['value']) . $params . ';';
                                }

                                $icon = empty($value['icon']) ? null : '<span class="glyphicon glyphicon-' . $value['icon'] . '"></span>';
                                ?>

                                <a href="<?= $url ?>"
                                   class="btn btn-<?= $level ?> btn-xs"><?= $icon ?> <?= $value['text'] ?></a>
                            <?php endforeach; ?>
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