<?php include dirname(__FILE__).'/common/header.php'; ?>
<input type="hidden" id="eachbuyer_logState" value="0" >
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/product.css?v=<?php echo STATIC_FILE_VERSION ?>">
<!--breadcrumbs start-->
<div class="wrap breadcrumbs">
    <i class="icon-home">&nbsp;</i>
    <a href="<?php echo genURL('/');?>"><?php echo lang('home');?></a>
    <?php
    	foreach ($crumbs_list as $cat_id=>$cat_info){
    ?>
    <i class="icon-arr-right"></i>
    <a href="<?php echo genURL($cat_info['category_url'],true)?>"><?php echo $cat_info['category_description_name']?></a>
    <?php
    	}
    ?>
    <?php $product_info['product_description_name'] = isset($product_info['product_description_name'])?$product_info['product_description_name']:$product_info['product_name'];?>
    <i class="icon-arr-right"></i><a href="<?php echo genURL($product_info['product_url'])?>" title="<?php echo $product_info['product_description_name'];?>"><?php echo $product_info['product_description_name'];?></a>
    <i class="icon-arr-right"></i><?php echo lang('customer_review');?>
</div>
<!--breadcrumbs end-->
<!--main start-->
<div class="main allReviews" id="allReviews">
    <a href="<?php echo genURL($product_info['product_url'])?>" class="goback"><i class="goback-icon"></i><?php echo lang('back_to_product_page');?></a>
    <div class="main-left recent">
        <h1 class="shop-category"><?php echo lang('customer_review');?></h1>
        <div class="recent-grade">
            <i class="star<?php echo $star_level?>"></i><span><b><?php echo $average_score?></b>(<?php echo $review_user_nums?>)</span>
        </div>
        <?php 
        if(isset($product_info)){
        ?>
        <div class="shop-box">
        	<a href="<?php echo genURL($product_info['product_url'])?>" class="img"><img width="140" height="140" src="<?php echo PRODUCT_IMAGEM_URL.$product_info['product_image']?>" /></a>
            <div class="shop-con clearfix">
            	<h2><a href="<?php echo genURL($product_info['product_url'])?>"><?php echo isset($product_info['product_description_name'])?$product_info['product_description_name']:$product_info['product_name'];?></a></h2>
                <s><?php echo $product_info['product_currency']. number_format($product_info['product_price_market'],2,'.',',')?></s>
                <p><span><?php echo $product_info['product_currency']. number_format($product_info['product_discount_price'],2,'.',',')?></span></p>
            </div>
        </div>
        <?php
        }
        ?>
        <?php 
        if(isset($reviewdata) && !empty($reviewdata)){
        ?>
        <div class="reviews">
            <ul dislikeurl="" linkurl="" class="recent-list clear">
            	<?php 
            	foreach ($reviewdata as $review_k=>$review_v){
            	?>
            	<li indexid="<?php echo $review_v['review_id']?>">
                	<div class="pic-lt">
                		<h5><?php echo $review_v['review_title'];?></h5>
                		<p><i class="star<?php echo $review_v['review_score'];?>"></i><span>by <?php echo $review_v['user_name']?></span></p>
                		<p><?php echo date("M d.Y",strtotime($review_v['review_time_lastmodified']));?></p></div><div class="cont-ct">
                		<p><?php echo $review_v['review_content']?></p>
                	</div>
                	<div class="helpful-rt">
                		<p><?php echo lang('was_this_review_helpful_tips');?></p>
                		<p class="praise">
                            <a class="like" href="javascript:;"><i processing="false" productId="<?php echo $review_v['product_id'];?>" class="icon-like <?php if($review_v['like'] == 0) echo 'unlike';?>"></i><em>(<?php echo $review_v['review_count_helpful']?>)</em></a>
                            <a class="dislike" href="javascript:;"><i processing="false" productId="<?php echo $review_v['product_id'];?>" class="icon-dis <?php if($review_v['unlike'] == 0) echo 'unlike';?>"></i><em>(<?php echo $review_v['review_count_nothelpful']?>)</em></a>
                        </p>
                	</div>
                </li>
            	<?php
            	}
            	?>
                
            </ul>
        </div>
        
        <div class="show-pager">
            <?php include dirname(__FILE__).'/common/pagination.php'; ?>
        </div>
        <?php
        }
        ?>
    </div>
    <!-- sidebar -->
        <div class="side">
            <?php if(isset($alsolike_data) && !empty($alsolike_data)):?>
			<div class="also">
                <h5><?php echo lang('also_bought_tips');?></h5>
                <?php foreach ($alsolike_data as $like_k=>$like_v):?>
                <div>
                    <img src="<?php echo PRODUCT_IMAGEM_URL.$like_v['product_image']?>" width="158" height="158">
                    <p><?php echo isset($like_v['product_description_name'])?$like_v['product_description_name']:$like_v['product_name'];?></p>
                    <p class="price"><?php echo $like_v['product_currency']?> <?php echo number_format($like_v['product_discount_price'],2,'.',',')?></p>
                </div>
                <?php endforeach;?>
            </div>
			<?php endif;?>
        </div>
        <!-- sidebar -->
</div>


<!--main over-->
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script src="<?php echo RESOURCE_URL?>js/product.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>
