<?php

namespace common\components;

use yii\base\Object;

/**
 * Ubb with html
 *
 * @author    Leon <jiangxilee@gmail.com>
 * @copyright 2017-01-22 17:15:25
 */
class Ubb extends Object
{
    /**
     * @var string The element of filter
     */
    public $bbsElements = 'class|id|on.*';

    /**
     * @var string The tags fo allow
     */
    public $bbsTags = 'img|hr|br|font|div|span|center|strong|blockquote|code|table|thead|tbody|sub|sup|tr|td|th|em|h[1-6]|a|p|pre|b|u|s|i';

    /**
     * __constructor
     *
     * @access public
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $item = [
            'bbs_elements',
            'bbs_tags'
        ];
        foreach ($item as $attr) {
            if (isset($config[$attr])) {
                $element = Helper::underToCamel($attr);
                $this->$element = $config[$attr];
            }
        }

        parent::__construct();
    }

    /**
     * HTML to UBB
     *
     * @access public
     *
     * @param string  $str      Html code
     * @param boolean $retainBr Keep \n
     *
     * @return string
     */
    public function htmlToUbb($str, $retainBr = false)
    {
        $str = Helper::perfectHtml($str);

        // pretreatment
        if (true === $retainBr) {
            $str = str_replace([
                "\r",
                "\n",
                "\r\n",
                "\t",
                PHP_EOL
            ], '', $str);
        }
        $str = preg_replace('/ (' . $this->bbsElements . ')=".*"/iU', '', $str);
        $str = str_replace('&nbsp;', '[ ]', $str);

        // handle tags
        $str = preg_replace('/\<\/(' . $this->bbsTags . ')\>/iU', '[/$1]', $str);
        $str = preg_replace('/\<(' . $this->bbsTags . ')(?:\/)?\>/iU', '[$1]', $str);
        $str = preg_replace('/\<(' . $this->bbsTags . ') (.*)(?:\/)?\>/iU', '[$1 $2]', $str);

        // filter others
        $str = Helper::perfectHtml($str);

        return $str;
    }

    /**
     * UBB to HTML
     *
     * @access public
     *
     * @param string $str Ubb code
     *
     * @return string
     */
    public function ubbToHtml($str)
    {
        // pretreatment
        $str = str_replace('[ ]', '&nbsp;', $str);

        // handle tags
        $str = preg_replace('/\[\/(' . $this->bbsTags . ')\]/iU', '</$1>', $str);
        $str = preg_replace('/\[(' . $this->bbsTags . ')\]/iU', '<$1>', $str);
        $str = preg_replace('/\[(' . $this->bbsTags . ') (.*)\]/iU', '<$1 $2>', $str);

        return Helper::perfectHtml($str);
    }
}