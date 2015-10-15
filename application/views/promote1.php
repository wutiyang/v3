<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/hurried.css?v=<?php echo STATIC_FILE_VERSION ?>">
<div class="main mould-one">
	<div style="<?php echo isset($color_banner)?'background-color:'.$color_banner:'';?>">
		<div class="banner">
	    	<div class="timer">
	        	<span class="endIn" style="<?php echo isset($color_banner)?'background-color:'.$color_banner:'';?>"><?php echo lang('end_in');?></span>
	            <div class="con" id="counter">
					<span data-endtime="<?php echo $data_end;?>"></span>
				</div>
	           
	        </div>
	        <script type="text/javascript">
	            $(function() {
					ec.load('ec.ui.countdown', {
						onload : function () {
							ec.ui.countdown('#counter span', {
								"html" : "<span class='day_text'><em class='day'>{#day}</em>&nbsp;{#dayText}</span> <em>{#hours}</em><i>:</i><em>{#minutes}</em><i>:</i><em>{#seconds}</em>",
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
	    	<img src="<?php echo empty($promote['promotion_banner'])?'':COMMON_IMAGE_URL.$promote['promotion_banner'];?>" width="100%" height="220"/>
	    </div>
	</div>
 	<div class="box"> 
		<div class="main-list">
			<ul class="thumbnail">
				<?php foreach($promoteDetailList as $promoteDetail):?>
				<li>
					<i class="pop<?php echo $promoteDetail['icon_type'];?>"></i>
					<div class="mask"></div>
					<p><?php echo $promoteDetail['promotion_detail_title'];?></p>
				</li>
				<?php endforeach;?>
			</ul>
		</div>
	 </div> 
	<div class="primary-list">
		<?php foreach($promoteDetailList as $promoteDetail):?>
		<div class="goods-list">
			<ul>
				<?php foreach ($promoteDetail['productList'] as $product):?>
				<li>
	                <div class="p-pic">
	                    <a href="<?php echo genURL($product['product_url']);?>" title="<?php echo $product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);">
	                    	<img width="163" height="163" src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo PRODUCT_IMAGEM_URL.$product['product_image']?>" alt="">
	                    	<?php if( $product['product_discount_number'] != 0):?>
	                        <p class="icon-off"><i><?php echo intval($product['product_discount_number']);?></i></p>
	                        <?php endif;?>
	                    </a>
	                </div>
	                <div class="p-name">
	                	<a href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>"><?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?></a>
	                </div>
	                <div class="p-price">
	                    <span <?php echo in_array($currency,array('USD','GBP','EUR','CHF'))?'':'style="padding-right:70px;"' ?> class="p-price-o"><?php echo $product['product_currency'].number_format($product['product_price_market'],2,'.',',');?></span>
	                    <span class="p-price-n"><?php echo $product['product_currency'].number_format($product['product_discount_price'],2,'.',',');?></span>
	                </div>
				</li>
				<?php endforeach;?>
			</ul>
			<div class="all"><a href="<?php echo $promoteDetail['promotion_detail_url'];?>"><?php echo lang('view_all');?></a></div>
		</div>
		<?php endforeach;?>
	</div>
</div>

<script>
	// An example of measuring promotion views. This example assumes that
	// information about the promotions displayed is available when the page loads.
	dataLayer.push({
	  'ecommerce': {
	    'promoView': {
	      'promotions': <?php echo $tongji_promote;?>
	    }
	  }
	});

</script>

<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script src="<?php echo RESOURCE_URL ?>js/hurried.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>
