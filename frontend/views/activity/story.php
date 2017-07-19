<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$params = \Yii::$app->params;
\Yii::$app->params['ng_ctrl'] = 'activity';
?>

<body>
	<div class="kake-panel">
		<!--背景图-->
		<div class="bg"></div>
		<div class="content">
			<div class="file">
				<input type="file"/>
			</div>
			<!--图片显示位置-->
			<span>
				<img src="<?= $params['frontend_source'] ?>/img/activity/photo.png"/>
			</span>
			<!--元素-->
			<div class="diary"></div>
			<div class="train"></div>
			<div class="rcloud"></div>
			<div class="lcloud"></div>
			<div class="plane"></div>
			
			<!--故事内容-->
			<textarea>画法几何翻江倒海发电机房画法几何翻江倒海</textarea>
			
			<a href="javascript:void(0)" class="btn">
				<img src="<?= $params['frontend_source'] ?>/img/activity/btn.png"/>
			</a>
			
		</div>
	</div>
</body>