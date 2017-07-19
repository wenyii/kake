<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$params = \Yii::$app->params;
\Yii::$app->params['ng_ctrl'] = 'activity';
?>

<body>
	<div class="kake-panel" kk-ajax-upload="div.file" data-action="activity/upload-photo" data-callback="handleUpload">
		<div class="file">
			<input type="file"/>
		</div>
		<!--图片显示位置-->
		<span>
			<img id="preview" src="<?= $params['frontend_source'] ?>/img/activity/photo.png"/>
		</span>
		<!--元素-->
		<div class="diary"></div>
		<div class="train"></div>
		<div class="rcloud"></div>
		<div class="lcloud"></div>
		<div class="plane"></div>
		<!--故事内容-->
        <textarea placeholder="我有酒，说出你的故事..." id="feedbackcontent"></textarea>

        <a href="javascript:void(0)" class="btn" kk-tap="submitStory()">
            <img src="<?= $params['frontend_source'] ?>/img/activity/btn.png"/>
        </a>
		
	</div>
</body>