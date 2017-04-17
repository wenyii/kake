<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use yii;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = false;
    public $baseUrl = null;
    public $css = [];
    public $js = [];
    public $depends = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->baseUrl = Yii::$app->params['frontend_source'];

        $minDirectory = (YII_ENV == 'dev' ? null : '_min');
        $suffix = (YII_ENV == 'dev' ? time() : VERSION);

        $this->css = [
            "css{$minDirectory}/bootstrap.css?version=" . $suffix,
            "css{$minDirectory}/main.css?version=" . $suffix,
        ];
        $this->js = [
            "js{$minDirectory}/jquery.js?version=" . $suffix,
            "js{$minDirectory}/angular.js?version=" . $suffix,
            "js{$minDirectory}/alloy-bundle.js?version=" . $suffix,
            "js{$minDirectory}/main.js?version=" . $suffix,
        ];
    }
}
