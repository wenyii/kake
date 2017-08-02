<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$params = \Yii::$app->params;
\Yii::$app->params['ng_ctrl'] = 'generic';
?>
<header>
     全部目的地
    <div class="menu detail" kk-menu="#menu">
        <img class="img-responsive" src="<?= $params['frontend_source'] ?>/img/header-icon-right.svg"/>
    </div>
</header>
<!-- All-hot-aim -->
<div class="allaim">
	<ul>
	<?php foreach ($region as $item): ?>
	    <a href="<?= Url::to(['items/index', 'region' => $item['id']]) ?>">
			<li>
				<img src="<?= current($item['preview_url']) ?>"/>
			</li>
		</a>
	<?php endforeach; ?>
	</ul>
</div>