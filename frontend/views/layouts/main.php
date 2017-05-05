<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\helpers\Url;

AppAsset::register($this);

$params = \Yii::$app->params;

$controller = \Yii::$app->controller->id;
$action = \Yii::$app->controller->action->id;

$ngApp = empty($params['ng_app']) ? 'kkApp' : $params['ng_app'];
$ngCtl = empty($params['ng_ctrl']) ? null : (' ng-controller="' . $params['ng_ctrl'] . '"');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html ng-app="<?= $ngApp ?>" lang="<?= \Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="keywords" content="<?= $params['app_keywords'] ?>">
    <meta name="description" content="<?= $params['app_description'] ?>">
    <?= Html::csrfMetaTags() ?>
    <title><?= $params['app_title'] ?></title>
    <?php $this->head() ?>
</head>

<script type="text/javascript">
    var baseUrl = '<?= $params["frontend_url"];?>';
    var requestUrl = '<?= $params["frontend_url"];?>/?r=';
</script>

<body<?= $ngCtl ?>>

<kk-loading loading="factory.loading"></kk-loading>
<kk-message message="factory.message"></kk-message>

<div id="menu">
    <div class="triangle"></div>
    <div>
        <a href="<?= Url::to(['site/index']) ?>">
            <img src="<?= $params['frontend_source'] ?>/img/site.svg"/>
            首页
        </a>
        <hr/>
        <a href="<?= Url::to(['order/index']) ?>">
            <img src="<?= $params['frontend_source'] ?>/img/order-center.svg"/>
            订单中心
        </a>
        <hr/>
        <a href="tel:<?= Yii::$app->params['company_tel'] ?>">
            <img src="<?= $params['frontend_source'] ?>/img/phone.svg"/>
            咨询客服
        </a>
    </div>
</div>

<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>

<?php
$minDirectory = (YII_ENV == 'dev' ? null : '_min');
$suffix = (YII_ENV == 'dev' ? time() : VERSION);

$items = [
    'css',
    'js'
];
foreach ($items as $item) {
    $variable = 'source' . ucfirst($item);
    $register = 'register' . ucfirst($item) . 'File';

    if (is_null($this->context->{$variable}) || 'auto' == $this->context->{$variable}) {
        $source = "/{$item}{$minDirectory}/{$controller}/{$action}.{$item}";
        $this->{$register}($params['frontend_source'] . $source . "?version=" . $suffix);
    } elseif (is_array($this->context->{$variable})) {
        foreach ($this->context->{$variable} as $value) {
            $source = "/{$item}{$minDirectory}/{$value}.{$item}";
            $this->{$register}($params['frontend_source'] . $source . "?version=" . $suffix);
        }
    }
}
?>

</body>
<script>
    var _hmt = _hmt || [];
    (function () {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?0dbdcd4d413051d54182fbda00151c4a";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
</html>
<?php $this->endPage() ?>
