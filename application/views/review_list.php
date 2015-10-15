<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/order_list.css?v=<?php echo STATIC_FILE_VERSION ?>">
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/review.css?v=<?php echo STATIC_FILE_VERSION ?>">
<div class="wrap breadcrumbs">
    <i class="icon-home">&nbsp;</i>
    <a href="<?php echo genURL('/')?>"><?php echo lang('home');?></a>
    <i class="icon-arr-right">&nbsp;</i>
    <a href="<?php echo genURL('order_list');?>"><?php echo lang('my_account');?></a>
    <i class="icon-arr-right">&nbsp;</i>
    <span><?php echo lang('manage_review');?></span>
</div>
<div class="main" id="review">
	<?php include dirname(__FILE__).'/account/nav.php'; ?>
	<!-- content start -->
	<div class="content">
        <h3 class="review-tit"><?php echo lang('manage_review');?>
            <span class="hint"><?php echo lang('write_reviews_earn_rewards');?>
                <em class="describe">
                    <div class="popBox">
                        <div class="con">
                            <p>
	                            <?php echo lang('review_earn_x_rewards_tips');?>
	                            <br>*<?php echo lang('effective_review');?> :<?php echo lang('effective_review_desc');?>
                            </p>
                        </div>   
                    </div>
                </em>
            </span>
        </h3>
        <div class="tab-tit">
            <a href="<?php echo genURL("review_create")?>" class=""><?php echo lang('write_a_review');?></a>
            <a href="" class="view-rev on"><?php echo lang('my_reviews');?><span>(<?php echo $nums;?>)</span></a>
        </div>
        <!-- review start -->
        <?php if(isset($review_list) && !empty($review_list)): ?>
        <div class="review" id="review">
        	<?php 
        	foreach ($review_list as $k=>$v){
        	?>
        	<div class="write-list clear">
                <a href="<?php echo genURL($v['product_info']['product_url']);?>"><img width="140" height="140" src="<?php echo PRODUCT_IMAGEM_URL.$v['product_info']['product_image']?>" class="list-img"></a>
                <div class="list-info">
                    <div class="intro">
                        <div>
                            <h5><a href="<?php echo genURL($v['product_info']['product_url']);?>"><?php echo isset($v['product_info']['product_description_name'])?$v['product_info']['product_description_name']:$v['product_info']['product_name'];?></a></h5>
                        </div>
                        <p><?php echo date("M d,Y",strtotime($v['review_time_create']))?></p>
                    </div>
                    <p class="size">
                    <?php if(isset($skuInfoArr[$v['product_sku']])):?>
                    	<?php foreach($skuInfoArr[$v['product_sku']] as $key=>$val):?>
                        	<?php echo $key;?>:<em><?php echo $val;?></em>
                    	<?php endforeach;?>
                    <?php endif;?>
                    </p>
                    <div class="evaluate">
                        <p>“<?php echo $v['review_content']?>”</p>
                    </div>
                </div>
            </div>
        	<?php
        	}
        	?>
        </div>
        <!-- review end -->
		<!-- 分页 start -->
		<div class="show-pager">
        	<?php include dirname(__FILE__).'/common/pagination.php'; ?>
		</div>
		<!-- 分页 end -->
		
        <?php else:?>
        <div class="null">
            <?php echo lang('no_reviews_tips');?><a class="btn34-org btn-text" href="<?php echo genURL('/')?>"><?php echo lang('continue_shopping');?></a>
        </div>
        <?php endif;?>
	</div>
	<!-- content end -->
</div>
<?php include dirname(__FILE__).'/common/footer.php'; ?>
</body>
</html>
