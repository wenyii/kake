<?php

namespace console\controllers;

use yii\console\Controller;
use yii\helpers\Console;

/**
 * General controller
 *
 * @author    <jiangxilee@gmail.com>
 * @copyright 2017-05-22 13:38:48
 */
class GeneralController extends Controller
{
    /**
     * Display color
     *
     * @param string $message
     * @param mixed  $colors
     *
     * @return string
     */
    public function color($message, $colors)
    {
        $colors = (array) $colors;
        foreach ($colors as $color) {
            $message = $this->ansiFormat($message, $color);
        }

        return $message;
    }

    /**
     * Display style and printout
     *
     * @access public
     *
     * @param string $message
     * @param array  $params
     * @param mixed  $style
     * @param mixed  $begin
     * @param mixed  $end
     *
     * @return void
     */
    public function console($message, $params = [], $style = null, $begin = null, $end = null)
    {
        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }

        $message = sprintf($message, ...$params);
        $message = ($begin ?: PHP_EOL) . $message . ($end ?: PHP_EOL . PHP_EOL);
        $this->stdout($message, $style);
    }
}