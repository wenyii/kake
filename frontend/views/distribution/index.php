<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$params = \Yii::$app -> params;
\Yii::$app->params['ng_ctrl'] = 'distribution';
?>

<body>
	<div class="distri_panel">

		<!--入场动画-->
		<div class="distri_ani" ng-show="isShowAni">
			<img src="<?= $params['frontend_source'] ?>/img/distribution/ani-bg.png" class="ani-bg"/>
			<img src="<?= $params['frontend_source'] ?>/img/distribution/l-leef.png" class="l-leef"/>
			<img src="<?= $params['frontend_source'] ?>/img/distribution/r-leef.png" class="r-leef"/>
			<img src="<?= $params['frontend_source'] ?>/img/distribution/people.png" class="people"/>
			<img src="<?= $params['frontend_source'] ?>/img/distribution/car.png" class="car"/>
		</div>

		<!--内容页-->
		<div class="distri_content" ng-hide="isShowAni">
			<div class="gif"><img src="<?= $params['frontend_source'] ?>/img/distribution/holiday.gif"/></div>
			<div class="line"><img src="<?= $params['frontend_source'] ?>/img/distribution/line.png"/></div>
			<div class="logo"><img src="<?= current($producer['logo_preview_url']) ?>"/></div>

			<!--产品列表-->
			<div class="product_list">
				<ul>
					<?php
					$topMap = [
						0 => '0px',
						1 => '0px',
						2 => '150px',
						3 => '40px',
						4 => '150px'
					];	
					?>
                    <?php foreach ($product as $i => $item): ?>
                    <?php
                        $picCls = ($i % 2 == 0) ? 'photoleft' : 'photoright';
                        $desCls = ($i % 2 == 0) ? 'descriptionleft' : 'descriptionright';
                    ?>
					<li style="margin-top:<?= $topMap[$i] ?>"> 
						<a href="<?= Url::to(['detail/index', 'id' => $item['id']]) ?>">
							<div class="<?= $picCls ?>">
								<img src="<?= current($item['cover_preview_url']) ?>"/>
							</div>
							<div class="<?= $desCls ?>">
								<img src="<?= $params['frontend_source'] ?>/img/distribution/proprice.gif"/>
								<div class="btn"><img src="<?= $params['frontend_source'] ?>/img/distribution/lookup-btn.png"/></div>
								<div class="text">
									<h2><?= $item['name'] ?></h2>
									<small>￥<?= $item['min_price'] ?></small>
								</div>
							</div>
						</a>
					</li>
                    <?php endforeach; ?>
				</ul>
				<div class="footer">
					<a href="javascript:void();"><img src="<?= $params['frontend_source'] ?>/img/distribution/footerlogo.png"/></a>
				</div>
			</div>
			<!--球-->
<<<<<<< Updated upstream
			<div class="ball" ><img src="<?= $params['frontend_source'] ?>/img/distribution/ball.png"/></div>
			<!--眼镜-->
			<div class="glasses" ><img src="<?= $params['frontend_source'] ?>/img/distribution/glasses.png"/></div>
			<!--鞋子-->
			<div class="shoes" ><img src="<?= $params['frontend_source'] ?>/img/distribution/shoes.png"/></div>
			<!--斜箭头-->
			<div class="slopearrow" ><img src="<?= $params['frontend_source'] ?>/img/distribution/slopearrow.png"/></div>
			<!--照相机-->
			<!--<div class="camera" ><img src="<?= $params['frontend_source'] ?>/img/distribution/camera.png"/></div>-->
			<!--指南针-->
			<div class="compass" ><img src="<?= $params['frontend_source'] ?>/img/distribution/compass.png"/></div>
			<!--向下箭头-->
			<!--<div class="downarrow" ><img src="<?= $params['frontend_source'] ?>/img/distribution/downarrow.png"/></div>-->
=======
			
>>>>>>> Stashed changes
		</div>
	</div>
</body>

