<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/brand.css">
<div class="wraper">
	<div class="main brand" id="brand">
		<div class="wrap breadcrumbs">
		    <i class="icon-home">&nbsp;</i>
		    <a href="<?php echo genURL('/');?>"><?php echo lang('home');?></a>
		    <i class="icon-arr-right"></i><?php echo lang('deals');?>
		</div>
		<!--main start-->
		<div class="deals">
			<div class="hot-deals">
				<h2 class="deals-tit"><?php echo lang('hot_deals');?></h2>
				<div class="hot-list">
					<div class="deal-list">
	            		<ul class="clearfix">
		                    <?php foreach ($hotDeals as $hotDeal){
								$product = $hotDeal['product'];
		                    	if(!isset($product['product_description_name'])) $product['product_description_name'] = $product['product_name'];
		                    ?>
		                    <li>
		                        <div class="pro-list-block">
		                            <div class="p-pic">
		                                <a href="<?php echo genURL($hotDeal['deal_url']);?>" title="<?php echo $hotDeal['title'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Deals','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);">
		                                	<img src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo COMMON_IMAGE_URL.$hotDeal['deal_image'];?>" width="290" height="182">
		                                    <?php if( $product['product_discount_number'] != 0){?>
		                                    <p class="icon-off"><i><?php echo intval($product['product_discount_number']);?></i></p>
		                                    <?php }?>
		                                    <span class="span-left">
		                                     	<?php if(!empty($product['new'])){?>
		                                     	<i class="icon-news"></i>
		                                     	<?php }?>
		                                     	<?php if(!empty($product['icon'])){?>
		                                     	<i class="icon-hot"></i>
		                                     	<?php }?>
		                                     </span>
		                                </a>
		                            </div>
		                            <div class="p-name">
		                            	<a href="<?php echo genURL($hotDeal['deal_url']);?>" title="<?php echo $hotDeal['title'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Deals','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);"><?php echo $hotDeal['title'];?></a>
		                            </div>
		                            <div class="p-price">
		                                <span class="p-price-o"><?php echo $product['product_currency'].number_format($product['product_price_market'],2,'.',',');?></span>
		                            </div>
		                            <div class="p-time">
		                            	<span class="p-price-n"><?php echo $product['product_currency'].number_format($product['product_discount_price'],2,'.',',');?></span>
		                            	<?php if(isset($product['discount_start']) && isset($product['discount_end'])){?>
		                            	<div class="con" id="counter">
											<i class="icon-time"></i><span class="con-time" data-endtime="<?php echo (strtotime($product['discount_end'])-time())>0?(strtotime($product['discount_end'])-time()):0;?>"></span>
										</div>
										<?php }?>
		                            </div>
		                        </div>
		                    </li>
		                    <?php }?>
		                    
	                    </ul>
	            	</div>
				</div>
			</div>
			<div class="deal-nav">
				<ul>
					<li class="<?php if($dealId == 0) echo 'all-events';?>"><a href="<?php echo genURL('deals');?>"><?php echo lang('all_events');?></a></li>
					<?php foreach ($deals as $deal){?>
					<li class="<?php if($dealId == $deal['deal_id']) echo 'all-events';?>"><a href="<?php echo genURL($deal['deal_url']);?>"><?php echo $deal['title'];?></a></li>
					<?php }?>
					
				</ul>
			</div>
			<div class="primary-category">
			
				<!-- 基础查询 start -->
                <?php include dirname(__FILE__).'/brand/base_search.php'; ?>
                <!-- 基础查询 end -->
                
	            <div class="primary-list">
	            	<div class="deal-list">
	            		<ul class="clearfix">
		                    
		                    <?php foreach ($dealsProducts as $product){
		                    	if(!isset($product['product_description_name'])) $product['product_description_name'] = $product['product_name'];
		                    ?>
		                    <li>
		                        <div class="pro-list-block">
		                            <div class="p-pic">
		                                <a href="<?php echo genURL($product['product_url']);?>" title="<?php echo $product['product_description_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Deals','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);">
		                                	<img src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo PRODUCT_IMAGEM_URL.$product['product_image'];?>" width="230" height="230">
		                                	<?php if( $product['product_discount_number'] != 0){?>
		                                    <p class="icon-off"><i><?php echo intval($product['product_discount_number']);?></i></p>
		                                    <?php }?>
		                                    <span class="span-left">
		                                     	<?php if(!empty($product['new'])){?>
		                                     	<i class="icon-news"></i>
		                                     	<?php }?>
		                                     	<?php if(!empty($product['icon'])){?>
		                                     	<i class="icon-hot"></i>
		                                     	<?php }?>
		                                     </span>
		                                </a>
		                            </div>
		                            <div class="p-name">
			                            <a href="<?php echo genURL($product['product_url']);?>" title="<?php echo $product['product_description_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Deals','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);"><?php echo $product['product_description_name'];?></a>
		                            </div>
									<div class="promotion"></div>		                           
		                            <div class="p-price">
		                                <span class="p-price-o"><?php echo $product['product_currency'].number_format($product['product_price_market'],2,'.',',');?></span>
		                                <span class="p-price-n"><?php echo $product['product_currency'].number_format($product['product_discount_price'],2,'.',',');?></span>
		                            </div>
		                            <div class="p-time">
		                            	<?php if(isset($product['discount_start']) && isset($product['discount_end']) && strtotime($product['discount_end']) > time()){?>
		                            	<div class="con" id="counter">
											<i class="icon-time"></i><span class="con-time" data-endtime="<?php echo (strtotime($product['discount_end'])-time());?>"></span>
										</div>
										<?php }?>
		                            </div>
		                        </div>
		                    </li>
		                    <?php }?>
		                    
	                    </ul>
	                    <!-- 
	            		<div class="all"><a href=""><?php //echo lang('no_more_products_display');?></a></div>
	                     -->
	                     <div class="show-pager">
		                	<?php include dirname(__FILE__).'/common/pagination.php'; ?>
		                </div>
	            	</div>
	                
	            </div>
	        </div>
		</div>
		<!-- main end -->
	</div>
</div>
<script type="text/javascript" src="resource/default/js/category.js"></script>
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/javascript">
    $(function() {
		ec.load('ec.ui.countdown', {
			onload : function () {
				ec.ui.countdown('#counter span', {
					"html" : "<span class='day_text'><em class='days'>{#day}</em>&nbsp;{#dayText}</span> <em>{#hours}</em><i>:</i><em>{#minutes}</em><i>:</i><em>{#seconds}</em>",
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
</body>
</html>