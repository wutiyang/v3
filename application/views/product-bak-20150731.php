<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/product.css">
<!--breadcrumbs start-->
<div class="wrap breadcrumbs">
    <i class="icon-home">&nbsp;</i><a href="<?php echo genURL('');?>">Home</a>
    <?php
    	foreach ($crumbs_list as $cat_id=>$cat_info){
    ?>
    	<i class="icon-arr-right"></i><a href="<?php echo genURL($cat_info['category_url'])?>"><?php echo $cat_info['category_description_name']?></a>
    <?php
    	}
    ?>
</div>
<!--breadcrumbs end-->
<!--main start-->
<div class="main product" id="product">
    <div class="product-hd">
        <!--dImgZoom start-->
        <?php include dirname(__FILE__).'/product/product_left_image.php'; ?>
        <!--dImgZoom end-->
        <!--pro-wrapper start-->
        <?php include dirname(__FILE__).'/product/product_right_attr.php'; ?>    
        <!--pro-wrapper end-->
    </div>  
    <!--frequently start-->
    <div class="frequently frequently-con clearfix" id="Frequently">
        <!-- sidebar -->
        <?php include dirname(__FILE__).'/product/product_right_sidebar.php'; ?>
        <!-- sidebar -->
        <!-- 绑定促销商品 start -->
        <?php 
        if(isset($bundle_product_list) && !empty($bundle_product_list)){
		?>
		<div  class="frequently-name"><span>Frequently Bought Together</span></div>		
		<?php include dirname(__FILE__).'/product/product_bundle.php'; ?>
        </div>
        <?php
        }
        ?>
        <!-- 绑定促销商品 end -->
        
        <?php 
        	//include dirname(__FILE__).'/product/product_also_like.php';
        ?>
       

        <ul class="tit-tab" id="tit">
            <li><a class="" href="#detail-tab1">Product Description</a></li>
            <li><a href="#detail-tab2">Customer Reviews</a></li>
            <li><a href="#detail-tab3">Customer Q&A</a></li>
            <li><a href="#detail-tab4">Warranty & Return Policies</a></li>
        </ul>
<div id="detail-box">
    <div class="pro-detail-info detail-tab" id="detail-tab1">
    	<?php 
    	if(isset($product_specifications) && count($product_specifications)){
    	?>
		<div class="mod1">
            <h2>Specifications</h2>
            <div class="parameter pt10">
            	<?php 
            	foreach ($product_specifications as $block_k=>$block_v){
            	?>
            	<div>
                    <h4><?php echo $block_v['attribute_block_lang_title']?></h4>
                    <?php 
                    if(isset($block_v['attr']) && count($block_v['attr'])){
                    ?>
                    <table>
                        <tbody>
                        	<?php 
                        	foreach ($block_v['attr'] as $block_attr_k=>$block_attr_v){
							if(isset($block_attr_v['attr_value']) && isset($block_attr_v['attribute_id'])){
							?>	
							 <tr>
                                <td class="td-l"><?php echo $block_attr_v['attribute_lang_title']?></td>
                                <td class="td-r">
                                <?php 
                                $num = count($block_attr_v['attr_value']);
                                foreach ($block_attr_v['attr_value'] as $block_attr_value_k=>$block_attr_value_v){
                                	echo $block_attr_value_v['attribute_value_lang_title'];
                                	if($block_attr_value_k+1<$num) echo "，";
                                }	
                                ?>
                                </td>
                            </tr>                    	
                        	<?php
                        	}
                        	}
                        	?>
                        </tbody>
                    </table>
                    <?php
                    }
                    ?>
                </div>
            	<?php
            	}
            	?>
            </div>
        </div>
		<?php
    	}
    	?>
        
        <?php 
        if($product_base_info['product_description_content'] && str_replace("<br />", "", $product_base_info['product_description_content'])){
        ?>
        <div class="mod2">
            <h2>Description</h2>
            <div class="des-list"><?php echo $product_base_info['product_description_content']?></div>
        </div>
        <?php	
        }
        ?>
        <?php 
        if(isset($product_sizechart_list) && count($product_sizechart_list['Centimeters'])>1){
        ?>
        <div class="mod3">
            <h2>Size Chart</h2>
            <div class="pt10 size-tab">
                <ul class="size" id="size">
                    <li><a class="" href="javascript:;">Centimeters</a></li>
                    <li><a href="javascript:;">Inches</a></li>
                </ul>
                <div class="tab-box">
                    <table class="list-view">
                        <?php 
                        foreach ($product_sizechart_list['Centimeters'] as $size_key=>$size_val){
                        ?>
                        <tr>
                        	<?php 
                        	foreach ($size_val as $con_key=>$con_val){
                        	?>
                        	<td><?php echo $con_val?></td>
                        	<?php	
                        	}
                        	?>
                        </tr>
                        <?php	
                        }
                        ?>
                    </table>
                    <table class="list-view">
                    <?php 
                        foreach ($product_sizechart_list['Inches'] as $size_key=>$size_val){
                        ?>
                        <tr>
                        	<?php 
                        	foreach ($size_val as $con_key=>$con_val){
                        	?>
                        	<td><?php echo $con_val?></td>
                        	<?php	
                        	}
                        	?>
                        </tr>
                        <?php	
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
        <?php
        }
        ?>
        
        <div class="mod4">
            <h2>Details Photos</h2>
            <?php 
            if (isset($product_vice_image_list) && !empty($product_vice_image_list)){
            ?>
			<ul class="pro-pic pt10">
				<?php 
				foreach ($product_vice_image_list as $vice_image_k=>$vice_image_v){
				?>
				<li><img src="<?php echo PRODUCT_IMAGE_URL."291x300/".$vice_image_v['product_image_path'] ?>" width="291" height="300" alt=""></li>
				<?php
				}
				?>
            </ul>            
            <?php
            }
            ?>
        </div>
    </div>
   	<!-- review start -->
   	<?php include dirname(__FILE__).'/product/product_review.php'; ?>
   	<!-- review end -->
    <!-- Customer-Q&A start -->
    <?php include dirname(__FILE__).'/product/product_qna.php'; ?>
    <!-- Customer-Q&A end -->
    <?php include dirname(__FILE__).'/product/product_policies.php';?>
    <!-- policies end -->

    <div class="index-box"></div>

    </div>
    <!--frequently end-->
    
    <!--img start-->
    <?php 
    	//include dirname(__FILE__).'/product/product_otheralso_view.php'; 
    ?>
    <!--img end-->
    <!--view_history start-->
    <?php include dirname(__FILE__).'/product/product_view_history.php'; ?>
    <!--view_history end-->
</div>
<div class="box_zoom_main" id="boxZoomMain">
		<a class="box_zoom_close box_close btn" href="javascript:;">×</a>
		<div class="box_zoom_content">
			<img src="<?php echo RESOURCE_URL ?>images/details/shop-1.jpg" data-sku="1"/>
		</div>
		<a class="box_zoom_arr_l zoom-arr" href="javascript:;"></a>
		<a class="box_zoom_arr_r zoom-arr" href="javascript:;"></a>
</div>
<div class="toolBox" id="toolBoxNew">
	<a href="<?php echo genURL('cart')?>" class="cart"><i></i></a>
    <em></em>
	<a href="<?php echo genURL('wishlist')?>" class="wash"><i></i></a>
    <em></em>
	<a href="javascript:;" id="goTopNew" class="gotop"><i></i></a>
</div>
<!--main over-->
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<?php 
if(isset($ga_dataLayer) && $ga_dataLayer){
?>
<script>
dataLayer =<?php echo $ga_dataLayer;?>
</script>
<?php	
}
?>
<?php 
if(isset($dataLayer_detail) && $dataLayer_detail){
?>
<script>
dataLayer.push({
	  'ecommerce': {<?php echo $dataLayer_detail;?>}
	});
</script>
<?php
}
?>
<script type="text/html" id="listToCartBox">
<div class="poptips" id="Pop">
	<p>Please complete your selection(s) before adding to cart.</p>
    <a href="javascript:;" class="btnorg35 cancel" title="Ok">Ok</a>
</div>
</script>
<script type="text/html" id="noBuy">
<div class="poptips" id="Pop">
	<p>You have already submitted a review.</p>
    <a href="javascript:;" class="btnorg35 cancel" title="Ok">Ok</a><a href="javascript:;" class="otherLink">Check your review</a>
</div>
</script>
<script type="text/html" id="haveComments">
<div class="poptips" id="Pop">
	<p>You should have bought this item before you write a review.</p>
    <a href="javascript:;" class="btnorg35 cancel" title="Ok">Ok</a>
</div>
</script>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/common/utils.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>/js/login.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/product.js"></script>
</body>
</html>