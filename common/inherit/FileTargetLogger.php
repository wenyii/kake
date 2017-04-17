<?php

namespace common\inherit;

use yii\helpers\VarDumper;
use yii\log\FileTarget;
use yii\log\Logger;

class FileTargetLogger extends FileTarget
{
    /**
     * inheritDoc
     */
    public function formatMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;
        $level = Logger::getLevelName($level);
        if (!is_string($text)) {
            // exceptions may not be serializable if in the call stack somewhere is a Closure
            if ($text instanceof \Throwable || $text instanceof \Exception) {
                $text = (string) $text;
            } else {
                $text = VarDumper::export($text);
            }
        }
        $traces = [];
        if (isset($message[4])) {
            foreach ($message[4] as $trace) {
                $traces[] = "in {$trace['file']}:{$trace['line']}";
            }
        }

        $prefix = $this->getMessagePrefix($message);
        $ms = substr(explode('.', $timestamp)[1], 0, 3);

        return date('Y-m-d H:i:s', $timestamp) . '#' . $ms . " {$prefix}[$level][$category] $text" . (empty($traces) ? '' : "\n    " . implode("\n    ", $traces));
    }
}