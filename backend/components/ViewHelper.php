<?php

namespace backend\components;

use common\components\Helper;
use yii\base\Object;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/**
 * Helper components
 *
 * @author    Leon <jiangxilee@gmail.com>
 * @copyright 2017-05-19 16:22:19
 */
class ViewHelper extends Object
{
    /**
     * 分页组件
     *
     * @access public
     *
     * @param object $pagination
     *
     * @return string
     */
    public static function page($pagination)
    {
        return LinkPager::widget([
            'pagination' => $pagination,
            'firstPageLabel' => true,
            'lastPageLabel' => true,
            'prevPageLabel' => '上一页',
            'nextPageLabel' => '下一页'
        ]);
    }

    /**
     * 转义脚本代码
     *
     * @access public
     *
     * @param string $script
     *
     * @return string
     */
    public static function escapeScript($script)
    {
        $script = str_replace('"', '&quot;', $script);
        $script = str_replace('\'', '&apos;', $script);

        return $script;
    }

    /**
     * 转义脚本参数
     *
     * @access public
     *
     * @param array $params
     *
     * @return string
     */
    public static function escapeParams($params)
    {
        $paramsStr = '';
        foreach ($params as $item) {
            if (is_array($item)) {
                $paramsStr .= self::escapeScript(json_encode($item)) . ', ';
            } else {
                $paramsStr .= '&quot;' . $item . '&quot;, ';
            }
        }

        return '(' . rtrim($paramsStr, ', ') . ')';
    }

    /**
     * 根据数组规则创建按钮组
     *
     * @access public
     *
     * @param array  $operations
     * @param string $controller
     * @param string $size
     *
     * @return string
     */
    public static function createButton($operations, $controller, $size = null)
    {
        if (empty($operations)) {
            return null;
        }

        $buttons = null;
        foreach ($operations as $value) {
            $script = Helper::emptyDefault($value, 'script', false);
            $level = Helper::emptyDefault($value, 'level', 'primary');
            $params = Helper::emptyDefault($value, 'params', []);

            if ($script) {
                $url = 'javascript:' . ViewHelper::escapeScript($value['value']);
            } else {
                if (strpos($value['value'], 'http') === 0) {
                    $url = $value['value'];
                } else {
                    $url = strpos($value['value'], '/') ? $value['value'] : ($controller . '/' . $value['value']);
                    $url = Url::to(array_merge([$url], $params));
                }
            }

            $icon = empty($value['icon']) ? null : '<span class="glyphicon glyphicon-' . $value['icon'] . '"></span>';
            $_size = $size ? "btn-{$size}" : null;
            $alt = Helper::emptyDefault($value, 'alt');

            $buttons .= "<a href='{$url}' class='btn btn-{$level} {$_size}' title='{$alt}'>{$icon} {$value['text']}</a>" . PHP_EOL;
        }

        return $buttons;
    }

    /**
     * 根据数组规则创建按钮组 (单条记录专用)
     *
     * @access public
     *
     * @param array  $operation
     * @param array  $item
     * @param string $controller
     * @param string $size
     *
     * @return string
     */
    public static function createButtonForRecord($operation, $item, $controller, $size = null)
    {
        if (empty($operation)) {
            return null;
        }

        $buttons = null;
        foreach ($operation as $value) {
            $show = true;
            if (!empty($value['show_condition']) && is_callable($value['show_condition'])) {
                $show = $value['show_condition']($item);
            }

            if (!$show) {
                continue;
            }

            $type = Helper::emptyDefault($value, 'type', 'url');
            $level = Helper::emptyDefault($value, 'level', 'primary');

            $defaultParams = $type == 'url' ? ['id'] : [];
            $params = Helper::emptyDefault($value, 'params', $defaultParams);
            if (is_callable($params)) {
                $params = $params($item);
            } else {
                $params = Helper::pullSome($item, $params);
            }

            if ($type == 'url') {
                $url = strpos($value['value'], '/') ? $value['value'] : ($controller . '/' . $value['value']);
                $url = Url::to(array_merge([$url], $params));
            } else {
                $params = $params ? self::escapeParams($params) : '';
                $url = 'javascript:' . self::escapeScript($value['value']) . $params . ';';
            }

            $icon = empty($value['icon']) ? null : '<span class="glyphicon glyphicon-' . $value['icon'] . '"></span>';

            if (!empty($value['br'])) {
                $buttons .= '<br>';
            }

            $_size = $size ? "btn-{$size}" : null;
            $buttons .= "<a href='{$url}' class='btn btn-{$level} {$_size}'>{$icon} {$value['text']}</a>" . PHP_EOL;
        }

        return $buttons;
    }

    /**
     * 创建表格
     *
     * @access public
     *
     * @param array $table
     * @param array $head
     * @param array $tpl
     * @param array $width
     *
     * @return string
     */
    public static function createTable($table, $head = [], $tpl = [], $width = [])
    {
        if (empty($table)) {
            return null;
        }

        $col = count(current($table));
        $manualCol = count($width);

        $autoWidth = null;
        if (!$manualCol) {
            $autoWidth = 100 / $col;
        } else if ($col > $manualCol) {
            $autoWidth = (100 - array_sum($width)) / ($col - $manualCol);
        }

        $widthStyle = function ($key) use ($width, $autoWidth) {
            if (isset($width[$key])) {
                $autoWidth = $width[$key];
            }

            $style = empty($autoWidth) ? null : " style='width: ${autoWidth}%;'";

            return $style;
        };

        $headHtml = null;
        if (!empty($head)) {
            $headHtml = '<thead><tr>';
            foreach ($head as $key => $title) {
                $style = $widthStyle($key);
                $headHtml .= "<th{$style}>{$title}</th>";
            }
            $headHtml .= '</tr></thead>';
        }

        $bodyHtml = '<tbody>';
        foreach ($table as $row) {
            $bodyHtml .= '<tr>';
            foreach ($row as $i => $tr) {
                $tr = isset($tpl[$i]) ? sprintf($tpl[$i], $tr) : $tr;
                $style = $widthStyle($i);
                $bodyHtml .= "<td{$style}>{$tr}</td>";
            }
            $bodyHtml .= '</tr>';
        }
        $bodyHtml .= '</tbody>';
        $tableHtml = "<table class='table table-bordered table-striped'>{$headHtml}{$bodyHtml}</table>";

        return $tableHtml;
    }

    /**
     * 生成列表页的数据修饰
     *
     * @access public
     *
     * @param string $field
     * @param array  $value
     * @param array  $item
     *
     * @return string
     */
    public static function adornHtml($field, $value, $item)
    {
        $empty = function ($key, $default = null, $data = null, $fn = 'empty') use ($item) {
            $data = $data ?: $item;
            $fn = $fn . 'Default';

            return Helper::$fn($data, $key, $default);
        };

        $handleAdorn = function ($val) use ($item) {
            if (is_callable($val)) {
                $val = call_user_func($val, $item);
            }

            return $val;
        };

        $adorn = $value['adorn'];
        $notSetFn = isset($adorn['empty']) ? 'empty' : 'isset';
        $notSetStr = $adorn['not_set_info'];

        if (isset($adorn['img'])) { // img
            $content = $empty($field, []);
            $content = is_string($content) ? $content : current($content);

            if (empty($content)) {
                $content = $notSetStr;
            } else {

                !is_array($adorn['img']) && $adorn['img'] = [];
                !isset($adorn['img']['tip']) && $adorn['img']['tip'] = true;
                !isset($adorn['img']['pos']) && $adorn['img']['pos'] = 'top';
                !isset($adorn['img']['max-width']) && $adorn['img']['max-width'] = '300px';

                $tipStr = null;
                if ($adorn['img']['tip']) {
                    $tipStr = '
                        data-toggle="tooltip"
                        data-html="true"
                        data-placement="' . $adorn['img']['pos'] . '"
                        title="<img style=\'max-width: ' . $adorn['img']['max-width'] . ';\' src=' . $content . '>">';
                }

                $content = '
                    <div class="row" ' . $tipStr . '>
                        <div class="col-sm-12">
                            <a href="javascript:void(0)" class="thumbnail">
                                <img src="' . $content . '">
                            </a>
                        </div>
                    </div>';
            }
        } elseif (isset($adorn['info'])) { // enumeration
            $content = $empty($field, null, null, $notSetFn);
            $default = empty($adorn['field_info'][$item[$field]]) ? $notSetStr : $adorn['field_info'][$item[$field]];
            $fieldInfo = $empty($field . '_info', $default, null, $notSetFn);
            $content = is_null($content) ? $notSetStr : $fieldInfo;
        }

elseif
(isset($adorn['link'])){ // link
            if ($empty($field)) {
                $content = '<a href="' . $empty($field) . '" target="_blank">' . $adorn['url_info'] . '</a>';
            } else {
                $content = $notSetStr;
            }
        } elseif (isset($adorn['html'])){ // html
                 $content = $empty($field, $notSetStr, null, $notSetFn);
        } else { // others
    $content = $empty($field, null, null, $notSetFn);
    $content = is_null($content) ? $notSetStr : Html::encode($content);
}

        if ($content == $notSetStr) {
            return $content;
        }

        if (isset($adorn['price'])) {
            $content = number_format(floatval($content), $adorn['price']);
        }

        if (isset($adorn['tpl'])) {
            $tpl = $handleAdorn($adorn['tpl']);
            $content = sprintf($tpl, $content);
        }

        if (isset($adorn['code'])) {
            $color = $handleAdorn($adorn['color']);
            if (is_array($color)) {
                $_val = $empty($field);
                $color = isset($color[$_val]) ? $color[$_val] : $color[$_val ? 1 : 0];
            }

            switch ($notSetFn) {
                case 'empty' :
                    $code = !empty($item[$field]);
                    break;
                case 'isset' :
                    $code = isset($item[$field]);
                    break;
                default :
                    $code = null;
                    break;
            }

            $content = $code ? ('<code class="' . $color . '">' . $content . '</code>') : $content;
        }

        return $content;
    }

    /**
     * 根据 adorn 数据获取样式
     *
     * @access public
     *
     * @param array $adorn
     *
     * @return string
     */
    public static function getStyleByAdorn($adorn)
{
    $styleArray = [];
    $attributes = [
        'width',
        'max-width',
        'min-width'
    ];
    foreach ($attributes as $attribute) {
        if (!empty($adorn[$attribute])) {
            $styleArray[] = $attribute . ':' . $adorn[$attribute];
        }
    }

    return 'style="' . implode(';', $styleArray) . '"';
}
}