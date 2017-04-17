<?php

namespace backend\assets;

use yii\web\AssetBundle;
use yii;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = false;
    public $baseUrl = null;
    public $css = [];
    public $js = [];
    public $depends = [];
    public $jsOptions = ['position' => yii\web\View::POS_HEAD];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->baseUrl = Yii::$app->params['backend_source'];

        $minDirectory = (YII_ENV == 'dev' ? null : '_min');
        $suffix = (YII_ENV == 'dev' ? time() : VERSION);

        $this->css = [
            "css{$minDirectory}/bootstrap.css?version=" . $suffix,
            "css{$minDirectory}/main.css?version=" . $suffix,
        ];
        $this->js = [
            "js{$minDirectory}/jquery.js?version=" . $suffix,
            "js{$minDirectory}/bootstrap.js?version=" . $suffix,
            "js{$minDirectory}/main.js?version=" . $suffix,
        ];
    }
}
