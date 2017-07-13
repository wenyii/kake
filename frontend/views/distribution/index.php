<?php
/* @var $this yii\web\View */

use yii\helpers\Url;

$params = \Yii::$app -> params;
\Yii::$app->params['ng_ctrl'] = 'distribution';
\Yii::$app->params['title'] = $producer['name'];
\Yii::$app->params['description'] = $producer['name'];
\Yii::$app->params['cover'] = current($producer['logo_preview_url']);
?>

<body>
	<div class="distri_panel">

		<!--入场动画-->
		<div class="distri_ani">
			<div class="content"></div>
			<img src="<?= $params['frontend_source'] ?>/img/distribution/halo.png" class="halo"/>
			<img src="<?= $params['frontend_source'] ?>/img/distribution/people.png" class="people"/>
			<div class="box-false">
	            <div class="box">
	                <img class="car" src="<?= $params['frontend_source'] ?>/img/distribution/car.png">
	                <img class="gas" src="<?= $params['frontend_source'] ?>/img/distribution/gas.png">
	            </div>
	        </div>
		</div>
		
		<!--内容页-->
		<div class="distri_content hidden" style="display: none;">
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
									<h2><?= str_replace(' | ', '', $item['name']) ?></h2>
									<small>￥<?= $item['min_price'] ?></small>
								</div>
							</div>
						</a>
					</li>
                    <?php endforeach; ?>
				</ul>
				<div class="footer">
					<a href="/"><img src="<?= $params['frontend_source'] ?>/img/distribution/footerlogo.png"/></a>
				</div>
			</div>
		</div>
	</div>
</body>

