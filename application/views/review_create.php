<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL?>css/order_list.css?v=<?php echo STATIC_FILE_VERSION ?>">
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL?>css/review.css?v=<?php echo STATIC_FILE_VERSION ?>">
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
            <a href="javascript:void(0)" class="on"><?php echo lang('write_a_review');?></a>
            <a href="<?php echo genURL("review_list")?>" class="view-rev"><?php echo lang('my_reviews');?>(<span><?php echo $reviewNums;?></span>)</a>
        </div>
        <div class="wrapper">
            <!-- write a review start -->
            <div class="write-rev">
                <?php foreach ($orderProductList as $orderProduct):?>
                <form class="write-list clear" product_id="<?php echo $orderProduct['product_id'];?>" order_id="<?php echo $orderProduct['order_id'];?>" product_sku="<?php echo $orderProduct['product_sku'];?>" action="" method="post">
                	<input type="hidden" name="product_id" value="<?php echo $orderProduct['product_id'];?>"/>
                	<input type="hidden" name="order_id" value="<?php echo $orderProduct['order_id'];?>"/>
                	<input type="hidden" name="product_sku" value="<?php echo $orderProduct['product_sku'];?>"/>
                	
                     <a href="<?php echo genURL($productInfoList[$orderProduct['product_id']]['product_url']);?>">
                     	<img width="140" height="140" src="<?php echo PRODUCT_IMAGEM_URL.$orderProduct['order_product_image'];?>" class="list-img">
                     </a>
                    <div class="list-info">
                        <div class="intro">
                            <div>    
                                <h5 class="intro-info">
                                 <a href="<?php echo genURL($productInfoList[$orderProduct['product_id']]['product_url']);?>">
                                <?php echo $orderProduct['order_product_name'];?>
                                </a>
                                </h5>
                                <h5 class="intro-price"><?php //echo $orderProduct['order_currency'];?> <em><?php echo $orderProduct['format_price'];?></em></h5>
                            </div>    
                            <p class="time"><?php echo date('M d,Y',strtotime($orderProduct['order_product_time_create']));?></p>
                        </div>
                        <p class="size">
                        	<?php if(isset($skuInfoArr[$orderProduct['product_sku']])):?>
	                        	<?php foreach($skuInfoArr[$orderProduct['product_sku']] as $key=>$val):?>
		                        	<?php echo $key;?>:<em><?php echo $val;?></em>
	                        	<?php endforeach;?>
                        	<?php endif;?>
                        </p>
                        <div class="remark" product_id="<?php echo $orderProduct['product_id'];?>" order_id="<?php echo $orderProduct['order_id'];?>" product_sku="<?php echo $orderProduct['product_sku'];?>">
                            <div class="rat tips">
                                <label><?php echo lang('rating');?>:</label>
                                <div class="stars">
                                    <!-- <i class="star5"></i> -->
                                    <span class="starts">
                                        <i class="star1"></i>
                                        <i class="star2"></i>
                                        <i class="star3"></i>
                                        <i class="star4"></i>
                                        <i class="star5"></i>
                                    </span>
                                </div>
                                <em class="error-tit"><?php echo lang('rating_is_required');?></em>
                            </div>
                            <p class="tips">
                                <label><?php echo lang('text_title');?></label>
                                <input type="text" class="title-it" id="titleIt" name="title">
                                <em class="error-tit form-tit"><?php echo lang('title_is_required');?></em>
                            </p>
                            <p class="tips2">
                                <label class="mess"><?php echo lang('review');?>:</label>
                                <textarea cols="30" rows="10" class="review-it" name="content"></textarea> 
                                <em class="error-tit form-tit"><?php echo lang('content_is_required');?></em>
                            </p>
                            <p class="errtext">
                                <label></label>
                                <span class="tomit">
                                    <input type="button" class="sub" id="sub" value="<?php echo lang('submit').' '.lang('review');?>">
                                    <em class=""><i class="num-to">15</i>-<i class="num">1000</i> words</em>
                                </span>
                            </p>
                        </div>
                        <!-- success start -->
                        <div class="success hide">
                            <p><?php echo str_replace('！','!</p><p>',lang('thank_you_for_review_tips'));?></p>
                        </div>
                        <!-- success end -->
                    </div>
                </form>
                <?php endforeach;?>
            </div>
        </div>
        <!-- write a review end -->
        <?php if(!empty($orderProductList)):?>
        <!-- 分页 start -->
        <div class="show-pager">
            <?php include dirname(__FILE__).'/common/pagination.php'; ?>
        </div>
        <?php endif;?>
        <?php if(empty($orderProductList)):?>
        <div class="null">
            <?php echo lang('no_reviews_tips');?><a class="btn34-org btn-text" href="<?php echo genURL('/')?>"><?php echo lang('continue_shopping');?></a>
        </div>
        <?php endif;?>
    </div>
        
       	

	<!-- </div> -->
	<!-- content end -->
</div>
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/javascript" src="<?php echo RESOURCE_URL?>js/review.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>
