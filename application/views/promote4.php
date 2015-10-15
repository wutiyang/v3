<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/hurried.css?v=<?php echo STATIC_FILE_VERSION ?>">
<div style="<?php echo isset($color_banner)?'background-color:'.$color_banner:'';?>">
	<div class="banner">
		<!--
    	<div class="timer">
        	<span class="endIn" style="background-color:<?php echo isset($promote['promotion_config']['color_banner'])?$promote['promotion_config']['color_banner']:'';?>">End In</span>
			<div class="con" id="counter">
				<span data-endtime="400"></span>
			</div>
        </div>
		 -->
         <script type="text/javascript">
                $(function() {
					ec.load('ec.ui.countdown', {
							onload : function () {
								ec.ui.countdown('#counter span', {
									"html" : "<em class='day'>{#day}</em>&nbsp;<span class='day_text'>{#dayText}</span> <em>{#hours}</em><i>:</i><em>{#minutes}</em><i>:</i><em>{#seconds}</em>",
									"zeroDayHide" : true,
									"callback" : function (json) {
										//计时结束时要执行的方法,比如置灰
										//$(this).parent().addClass('timeend');
									}
								});
							}
						});
					});
				</script>	
    	<img src="<?php echo empty($promote['promotion_banner'])?'':COMMON_IMAGE_URL.$promote['promotion_banner'];?>" width="100%" height="300"/>
    	<div class="share-list">
	        <a href="" class="icon-facebook" title="Facebook"></a>
	        <a href="" class="icon-twitter" title="Twitter"></a>
	        <a href="" class="icon-plusone" title="Pinterest"></a>
	        <a href="" class="icon-email" title="Email"></a>
	    </div>
    </div>
</div>
<div class="main special">
	<div class="primary-list">
		<?php foreach($promoteDetailList as $promoteDetail):?>
		<?php if($promoteDetail['type'] == 1):?>
		<h2 class="special-tit one"><?php echo $promoteDetail['promotion_detail_title'];?> </h2>
		<div class="specials">
			<ul>
				<?php foreach ($promoteDetail['productList'] as $product):?>
				<li>
                    <a class="p-pic" href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);">
                    	<img width="275" height="275" src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo PRODUCT_IMAGEM_URL.$product['product_image']?>" alt="">
                    </a>
	                <div class="p-detail">
	                	<a class="p-name" href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);" ><?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?> </a>
	                	<div class="p-price">
		                    <p class="p-price-o"><?php echo $product['product_currency'].number_format($product['product_price_market'],2,'.',',');?></p>
		                    <p class="p-price-n"><?php echo $product['product_currency'].number_format($product['product_discount_price'],2,'.',',');?></p>
		                </div>
		                <a class="p-fr" href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);" >
	                	<?php echo lang('buy_now');?>
	                	</a>
	                </div>
				</li>
				<?php endforeach;?>
			</ul>
		</div>
		<?php elseif($promoteDetail['type'] == 2):?>
		<h2 class="special-tit"><?php echo $promoteDetail['promotion_detail_title'];?></h2>
		<div class="goods-list special-list" id="goods-list">
			<ul>
				<?php foreach ($promoteDetail['productList'] as $product):?>	
				<li>
	                <div class="p-pic">
	                    <a href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);">
	                    	<img width="263" height="263" src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo PRODUCT_IMAGEM_URL.$product['product_image']?>" alt="">
	                    </a>
	                </div>
	                <div class="p-name">
	                	<a href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);"><?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?></a>
	                </div>
	                <div class="p-price">
	                    <span class="p-price-o"><?php echo $product['product_currency'].number_format($product['product_price_market'],2,'.',',');?></span>
	                    <span class="p-price-n"><?php echo $product['product_currency'].number_format($product['product_discount_price'],2,'.',',');?></span>
	                </div>
	                <a class="p-fr" href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);" >
                	<?php echo lang('buy_now');?>
                	</a>
				</li>
				<?php endforeach;?>
			</ul>
		</div>
		<?php elseif($promoteDetail['type'] == 3):?>
		<h2 class="special-tit"><?php echo $promoteDetail['promotion_detail_title'];?></h2>
		<div class="goods-list special-list" id="goods-list">
			<ul>
				<?php $j = 0;?>
				<?php foreach ($promoteDetail['productList'] as $product):?>	
				<?php $j++;?>

				<?php if($j <= 2) { ?>
				<li id="specialsLt" >
                    <a class="p-pic" href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);">
                    	<img width="313" height="313" src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo PRODUCT_IMAGEM_URL.$product['product_image']?>" alt="">
                    </a>
	                <div class="p-detail">
	                	<a class="p-name" href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);" ><?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?> </a>
	                	<div class="p-price">
		                    <p class="p-price-o"><?php echo $product['product_currency'].number_format($product['product_price_market'],2,'.',',');?></p>
		                    <p class="p-price-n"><?php echo $product['product_currency'].number_format($product['product_discount_price'],2,'.',',');?></p>
		                </div>
		                <a class="p-fr" href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);" >
	                	<?php echo lang('buy_now');?>
	                	</a>
	                </div>
				</li>
				<?php } else { ?>
					<li >
	                    <div class="p-pic">
		                    <a href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);">
		                    	<img width="263" height="263" src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo PRODUCT_IMAGEM_URL.$product['product_image']?>" alt="">
		                    </a>
		                </div>
		                <div class="p-name">
		                	<a href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);"><?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?></a>
		                </div>
		                <div class="p-price">
		                    <span class="p-price-o"><?php echo $product['product_currency'].number_format($product['product_price_market'],2,'.',',');?></span>
		                    <span class="p-price-n"><?php echo $product['product_currency'].number_format($product['product_discount_price'],2,'.',',');?></span>
		                </div>
		                <a class="p-fr" href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);" >
	                	<?php echo lang('buy_now');?>
	                	</a>
					</li>
				<?php } ?>
				<?php endforeach;?>
			</ul>
		</div>
		<?php elseif($promoteDetail['type'] == 4):?>
		<h2 class="special-tit"><?php echo $promoteDetail['promotion_detail_title'];?></h2>
		<div class="goods-list special-list" id="goods-list">
			<ul>
				<?php $m = 0;?>
				<?php foreach ($promoteDetail['productList'] as $product):?>
				<?php $m++;?>
				<?php if($m <= 1) {?>
					<li id="specials">
	                    <a class="p-pic" href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);">
	                    	<img width="313" height="313" src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo PRODUCT_IMAGEM_URL.$product['product_image']?>" alt="">
	                    </a>
		                <div class="p-detail">
		                	<a class="p-name" href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);" ><?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?> </a>
		                	<div class="p-price">
			                    <p class="p-price-o"><?php echo $product['product_currency'].number_format($product['product_price_market'],2,'.',',');?></p>
			                    <p class="p-price-n"><?php echo $product['product_currency'].number_format($product['product_discount_price'],2,'.',',');?></p>
			                </div>
			                <a class="p-fr" href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);" >
		                	<?php echo lang('buy_now');?>
		                	</a>
		                </div>
					</li>
				
				<?php }else{ ?>
					<li>
						<div class="p-pic">
		                    <a href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);">
		                    	<img width="263" height="263" src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo PRODUCT_IMAGEM_URL.$product['product_image']?>" alt="">
		                    </a>
		                </div>
		                <div class="p-name">
		                	<a href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);"><?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?></a>
		                </div>
		                <div class="p-price">
		                    <span class="p-price-o"><?php echo $product['product_currency'].number_format($product['product_price_market'],2,'.',',');?></span>
		                    <span class="p-price-n"><?php echo $product['product_currency'].number_format($product['product_discount_price'],2,'.',',');?></span>
		                </div>
		                <a class="p-fr" href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);" >
	                	<?php echo lang('buy_now');?>
	                	</a>
					</li>
				<?php }?>
				<?php endforeach;?>
			</ul>
		</div>
		<?php elseif($promoteDetail['type'] == 5):?>
		<h2 class="special-tit"><?php echo $promoteDetail['promotion_detail_title'];?></h2>
		<div class="special5">
			
			<div class="goods-list special-list" id="goods-list">
				<ul>
					<li  class="special5-lt">
						<!-- 3个 -->
						<?php $m = 1;?>
						<?php foreach ($promoteDetail['productList'] as $product):?>
						<?php if($m > 3) break; else $m++;?>
						<div class="special5-list">
							<a class="p-pic" href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);">
		                    	<img width="246" height="246" src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo PRODUCT_IMAGEM_URL.$product['product_image']?>" alt="">
		                    </a>
			                <div class="p-detail">
			                	<a class="p-name" href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);" ><?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?> </a>
			                	<div class="p-price">
				                    <p class="p-price-o"><?php echo $product['product_currency'].number_format($product['product_price_market'],2,'.',',');?></p>
				                    <p class="p-price-n"><?php echo $product['product_currency'].number_format($product['product_discount_price'],2,'.',',');?></p>
				                </div>
				                <a class="p-fr" href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);" >
			                	<?php echo lang('buy_now');?>
			                	</a>
			                </div>
						</div>
						<?php endforeach;?>
					</li>

					<!-- 4个 -->
					<?php $n = 0;?>
					<?php foreach ($promoteDetail['productList'] as $product):?>
					<?php $n++;?>
					<?php if($n <= 3) continue;?>
					<li>
		                <div class="p-pic">
		                    <a href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);">
		                    	<img width="263" height="263" src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo PRODUCT_IMAGEM_URL.$product['product_image']?>" alt="">
		                    </a>
		                </div>
		                <div class="p-name">
		                	<a href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);"><?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?></a>
		                </div>
		                <div class="p-price">
		                    <span class="p-price-o"><?php echo $product['product_currency'].number_format($product['product_price_market'],2,'.',',');?></span>
		                    <span class="p-price-n"><?php echo $product['product_currency'].number_format($product['product_discount_price'],2,'.',',');?></span>
		                </div>
		                <a class="p-fr" href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);" >
	                	<?php echo lang('buy_now');?>
	                	</a>
					</li>
					
					<?php endforeach;?>
				</ul>
			</div>
		</div>
		<?php endif;?>
		<?php endforeach;?>
	</div>
</div>

<script>
	dataLayer.push({
	  'ecommerce': {
	    'promoView': {
	      'promotions': <?php echo $tongji_promote;?>
	    }
	  }
	});
</script>
<?php include dirname(__FILE__).'/common/footer.php'; ?>
</body>
</html>