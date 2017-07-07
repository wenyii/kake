<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$params = \Yii::$app -> params;
\Yii::$app -> params['ng_ctrl'] = 'distribution';
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
			<div class="logo"><img src="<?= $params['frontend_source'] ?>/img/distribution/logo1.png"/></div>
			<div class="product-bg"><img src="<?= $params['frontend_source'] ?>/img/distribution/productbg.png"/></div>
			<!--产品列表-->
			<div class="product_list">
				<ul>
					<li> 
						<a href="javascript:;">
							<div class="photoleft">
								
								<img src="<?= $params['frontend_source'] ?>/img/distribution/产品1.png"/>
							</div>
							<div class="descriptionleft">
								<img src="<?= $params['frontend_source'] ?>/img/distribution/proprice.gif"/>
								<div class="btn"><img src="<?= $params['frontend_source'] ?>/img/distribution/lookup-btn.png"/></div>
								<div class="text">
									<h2>杭州钱江新城</h2>
									<span>假日酒店</span>
									<small>￥788/晚</small>
								</div>
							</div>
						</a>
					</li>
					<li> 
						<a href="javascript:;">
							<div class="photoright">
								<img src="<?= $params['frontend_source'] ?>/img/distribution/产品2.png"/>
							</div>
							<div class="descriptionright">
								<img src="<?= $params['frontend_source'] ?>/img/distribution/proprice.gif"/>
								<div class="btn"><img src="<?= $params['frontend_source'] ?>/img/distribution/lookup-btn.png"/></div>
								<div class="text">
									<h2>杭州钱江新城</h2>
									<span>假日酒店</span>
									<small>￥788/晚</small>
								</div>
							</div>
						</a>
					</li>
					<li> 
						<a href="javascript:;">
							<div class="photoleft">
								<img src="<?= $params['frontend_source'] ?>/img/distribution/产品3.png"/>
							</div>
							<div class="descriptionleft">
								<img src="<?= $params['frontend_source'] ?>/img/distribution/proprice.gif"/>
								<div class="btn"><img src="<?= $params['frontend_source'] ?>/img/distribution/lookup-btn.png"/></div>
								<div class="text">
									<h2>杭州钱江新城</h2>
									<span>假日酒店假日酒店</span>
									<small>￥788/晚</small>
								</div>
							</div>
						</a>
					</li>
					<li> 
						<a href="javascript:;">
							<div class="photoright">
								<img src="<?= $params['frontend_source'] ?>/img/distribution/产品1.png"/>
							</div>
							<div class="descriptionright">
								<img src="<?= $params['frontend_source'] ?>/img/distribution/proprice.gif"/>
								<div class="btn"><img src="<?= $params['frontend_source'] ?>/img/distribution/lookup-btn.png"/></div>
								<div class="text">
									<h2>杭州钱江新城</h2>
									<span>假日酒店</span>
									<small>￥788/晚</small>
								</div>
							</div>
						</a>
					</li>
					<li> 
						<a href="javascript:;">
							<div class="photoleft">
								<img src="<?= $params['frontend_source'] ?>/img/distribution/产品2.png"/>
							</div>
							<div class="descriptionleft">
								<img src="<?= $params['frontend_source'] ?>/img/distribution/proprice.gif"/>
								<div class="btn"><img src="<?= $params['frontend_source'] ?>/img/distribution/lookup-btn.png"/></div>
								<div class="text">
									<h2>杭州钱江新城</h2>
									<span>假日酒店</span>
									<small>￥788/晚</small>
								</div>
							</div>
						</a>
					</li>
				</ul>
				<div class="footer">
					<a href="javascript:;"><img src="<?= $params['frontend_source'] ?>/img/distribution/footerlogo.png"/></a>
				</div>
			</div>
			<!--球-->
			<div class="ball" ><img src="<?= $params['frontend_source'] ?>/img/distribution/ball.png"/></div>
			<!--眼镜-->
			<div class="glasses" ><img src="<?= $params['frontend_source'] ?>/img/distribution/glasses.png"/></div>
			<!--鞋子-->
			<div class="shoes" ><img src="<?= $params['frontend_source'] ?>/img/distribution/shoes.png"/></div>
			<!--斜箭头-->
			<div class="slopearrow" ><img src="<?= $params['frontend_source'] ?>/img/distribution/slopearrow.png"/></div>
			<!--照相机-->
			<div class="camera" ><img src="<?= $params['frontend_source'] ?>/img/distribution/camera.png"/></div>
			<!--指南针-->
			<div class="compass" ><img src="<?= $params['frontend_source'] ?>/img/distribution/compass.png"/></div>
			<!--向下箭头-->
			<div class="downarrow" ><img src="<?= $params['frontend_source'] ?>/img/distribution/downarrow.png"/></div>
		</div>
	</div>
</body>

