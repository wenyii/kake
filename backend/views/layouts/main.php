<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use backend\assets\AppAsset;
use common\widgets\Alert;
use yii\helpers\Url;

AppAsset::register($this);

$controller = \Yii::$app->controller->id;
$action = \Yii::$app->controller->action->id;
$params = \Yii::$app->params;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title>KAKE后台管理系统</title>
    <?php $this->head() ?>
</head>

<script type="text/javascript">
    var baseUrl = '<?= $params["backend_url"];?>';
    var requestUrl = '<?= $params["backend_url"];?>/?r=';
</script>

<body>
<?php $this->beginBody() ?>

<!-- Alert -->
<div id="message"></div>
<?php
$item = [
    'success',
    'info',
    'warning',
    'danger'
];
foreach ($item as $type): ?>
    <?php if (\Yii::$app->session->hasFlash($type)): ?>
        <script type="text/javascript">
            $(function () {
                $.alert('<?= Html::encode(str_replace(PHP_EOL, ' ', \Yii::$app->session->getFlash($type))) ?>', '<?= $type ?>');
            });
        </script>
    <?php endif; ?>
<?php endforeach; ?>

<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" target="_blank" href="<?= $params['frontend_url'] . Url::toRoute(['site/index']) ?>"><?= $params['app_name'] ?></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="javascript:void(0);">欢迎 <?= !empty($this->params['user_info']->username) ? Html::encode($this->params['user_info']->username) : $this->params['user_info']->phone ?></a>
                </li>
                <li><a href="<?= Url::to(['login/logout']) ?>">退出登录</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container-fluid">
    <div class="row">
        <div id="menu-div" class="col-sm-3 col-md-2 sidebar <?= $this->params['hidden_menu'] ? 'hidden' : null ?>">
            <ul class="nav nav-sidebar">
                <?php foreach ($this->params['menu'] as $master): ?>
                    <?php $class = (in_array($controller, $master['controllers']) ? 'class="active"' : null) ?>
                    <?php $style = !$class ? 'style="display: none;"' : null; ?>
                    <li <?= $class ?>>
                        <a href="javascript:void(0);"><?= $master['name'] ?></a>
                        <ul class="nav nav-sub-sidebar" <?= $style ?>>
                            <?php foreach ($master['sub'] as $slave) { ?>
                                <?php
                                $_class = null;
                                if ($class && $controller == $slave['controller'] && $action == $slave['action']) {
                                    $_class = 'class="active"';
                                }
                                ?>
                                <li <?= $_class ?>><a
                                        href="<?= Url::to(['/' . $slave['controller'] . '/' . $slave['action']]) ?>"><?= $slave['title'] ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
        if ($this->params['hidden_menu']) {
            $class = 'col-sm-12 col-md-12 main';
        } else {
            $class = 'col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main';
        }
        ?>
        <div id="content-div" class="<?= $class ?>">
            <?= $content ?>
        </div>
    </div>
</div>

<?php $this->endBody() ?>

<?php
$minDirectory = (YII_ENV == 'dev' ? null : '_min');
$suffix = (YII_ENV == 'dev' ? time() : VERSION);

$sourceUrl = $params['backend_source'];

$items = [
    'css',
    'js'
];
foreach ($items as $item) {

    $variable = 'source' . ucfirst($item);
    $register = 'register' . ucfirst($item) . 'File';

    if (is_null($this->context->{$variable}) || 'auto' == $this->context->{$variable}) {
        $source = "/{$item}{$minDirectory}/{$controller}/{$action}.{$item}";
        $this->{$register}($sourceUrl . $source . "?version=" . $suffix, ['position' => \yii\web\View::POS_HEAD]);
    } elseif (is_array($this->context->{$variable})) {
        foreach ($this->context->{$variable} as $value) {
            $source = "/{$item}{$minDirectory}/{$value}.{$item}";
            $this->{$register}($sourceUrl . $source . "?version=" . $suffix, ['position' => \yii\web\View::POS_HEAD]);
        }
    }
}
?>

</body>
</html>
<?php $this->endPage() ?>
