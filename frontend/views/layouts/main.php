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
    <meta name="keywords" content="KAKE酒店预订">
    <meta name="description" content="KAKE酒店预订">
    <?= Html::csrfMetaTags() ?>
    <title>KAKE酒店预订</title>
    <?php $this->head() ?>
</head>

<script type="text/javascript">
    var baseUrl = '<?= $params["frontend_url"];?>';
    var requestUrl = '<?= $params["frontend_url"];?>/?r=';
</script>

<body<?= $ngCtl ?>>

<kk-loading></kk-loading>
<kk-message></kk-message>

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
</html>
<?php $this->endPage() ?>
