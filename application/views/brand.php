<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/brand.css">
<!--main start-->
<div class="wraper">
	<!--main start-->
	<div class="main brand" id="brand">
		<div class="wrap breadcrumbs">
		    <i class="icon-home">&nbsp;</i><a href="<?php echo genURL('');?>"><?php echo lang('home')?></a>
		    <i class="icon-arr-right"></i><a href="<?php echo genURL('brand');?>"><?php echo lang('brand_zone');?></a>
		</div>
		<!-- nav -->
		<div class="brand-nav clearfix" id="brandNav">
			<ul class="nav-box">
				<?php foreach ($brandCategoryList as $brandCategory){?>
				<?php if(count($brandCategory['brands']) == 0) continue;?>
				<li class="nav-list">
					<a class="nav-tit" href="<?php echo genURL($brandCategory['brand_category_url']);?>"><?php echo $brandCategory['name'];?></a>
				</li>
				<?php }?>
			</ul>
			<div class="posiabox">
				<?php foreach ($brandCategoryList as $brandCategory){?>
				<?php if(count($brandCategory['brands']) == 0) continue;?>
				<ul class="subnav">
					<?php foreach ($brandCategory['brands'] as $brand){?>
					<li><a href="<?php echo genURL($brand['brand_url']);?>"><?php echo $brand['brand_title'];?></a></li>
					<?php }?>
				</ul>
				<?php }?>
			</div>
			
		</div>
		<!-- nav end -->
		<!-- banner start -->
		<div class="brand-banner focus-imgs">
        	<div id="focus" class="ec_slider ec_slider_filter" style=" height: 200px;">
				<div class="ec_slider_main">
					<ul class="ec_slider_list" style=" height: 200px; left: 0px; top: 0px;">
						<?php $i = 0;?>
						<?php foreach ($imageAdList as $ad) {?>
						<li style=" display: <?php echo $i == 0?'list-item':'none'?>;">
                        	<a href="<?php echo $ad['url'];?>" onclick="">
                                <img alt="<?php echo $ad['alt'];?>" src="<?php echo COMMON_IMAGE_URL.$ad['img']; ?>" width="1200" height="200">
                            </a>
                        </li>
                        <?php $i++;?>
                        <?php }?>
					</ul>
				</div>
	        </div>
        </div>
        <!-- banner end -->
        <?php foreach ($brandCategoryList as $brandCategory){?>
        <?php 
        	$count = count($brandCategory['brands']);
        	if(count($brandCategory['brands']) == 0) continue;
        	
        	$line = intval(($count-10)/7);
        	
        	$i = 0;
        ?>
        <div class="automotive">
        	<div class="tit"><?php echo $brandCategory['name'];?></div>
        	<div class="brand-cont" id="brandCon">
        		<div class="lists">
	    			<ul class="home-list">
	    				<li class="list-lt"><a href="<?php echo genURL($brandCategory['brand_category_url']);?>" >
	    				<img src="<?php echo COMMON_IMAGE_URL.$brandCategory['brand_category_image'] ?>"></a></li>
	    				<?php foreach ($brandCategory['brands'] as $brand){?>
	    				<?php $i++;?>
	    				<?php if( (($count>10 && $count <=12) && ($i>10 && $i <=12)) || (($count > 17) && ($i > 17)) ){?>
						<li scale='true' style="display:none">
						<?php }else{?>
						<li>
						<?php }?>
							<a href="<?php echo $brand['brand_url'];?>">
								<img src="<?php echo COMMON_IMAGE_URL.$brand['brand_icon'];?>">
								<span class="zone-name"><?php echo $brand['brand_title'];?><?php if($brand['brand_pid_count'] != 0) echo '('.$brand['brand_pid_count'].')';?></span>
							</a>
						</li>
						<?php }?>
					</ul>
					<?php if((($count>10 && $count <=12) && ($i>10 && $i <=12)) || (($count > 17) && ($i > 17))){?>
					<a class="more btn-span"><i></i></a>
					<?php }?>
	        	</div>
        	</div>        	
        </div>
        <?php }?>
        
        <!-- Hot Products  -->
        <div class="goods-list" id="mould1">
			<div class="mouldF-tit">
				<span class="mould-num"><?php echo lang('hot_products');?></span>
			</div>
			<ul>
				<?php foreach ($hotProductList as $product):?>
				<li>
	                <div class="p-pic">
	                    <a href="<?php echo genURL($product['product_url']);?>" title="<?php echo $product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Brand Home','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);">
	                    	<img width="163" height="163" src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo PRODUCT_IMAGEM_URL.$product['product_image']?>" alt="">
	                    	<?php if( $product['product_discount_number'] != 0):?>
	                        <p class="icon-off"><i><?php echo intval($product['product_discount_number']);?></i></p>
	                        <?php endif;?>
	                    </a>
	                </div>
	                <div class="p-name">
	                	<a href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Brand Home','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);"><?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?></a>
	                </div>
	                <div class="p-price">
	                    <span class="p-price-o"><?php echo $product['product_currency'].number_format($product['product_price_market'],2,'.',',');?></span>
	                    <span class="p-price-n"><?php echo $product['product_currency'].number_format($product['product_discount_price'],2,'.',',');?></span>
	                </div>
				</li>
				<?php endforeach;?>
			</ul>
		</div>
	</div>
	<!-- main end -->
</div>
<!-- main end -->
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/common/utils.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/category.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/brand.js"></script>
<script>
	// An example of measuring promotion views. This example assumes that
	// information about the promotions displayed is available when the page loads.
	dataLayer.push({
	  'ecommerce': {
	    'promoView': {
	      'promotions': <?php echo $dataLayerProducts;?>
	    }
	  }
	});

</script>
</body>
</html>