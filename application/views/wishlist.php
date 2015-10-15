<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/order_list.css?v=<?php echo STATIC_FILE_VERSION ?>">
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/wish-list.css?v=<?php echo STATIC_FILE_VERSION ?>">
<div class="wrap breadcrumbs">
    <i class="icon-home">&nbsp;</i>
    <a href="<?php echo genURL('/')?>"><?php echo lang('home');?></a>
    <i class="icon-arr-right">&nbsp;</i>
    <a href="<?php echo genURL('order_list');?>"><?php echo lang('my_account');?></a>
    <i class="icon-arr-right">&nbsp;</i>
    <span><?php echo lang('my_wishlist');?></span>
</div>
<div class="main" id="wish-list">
	<?php include dirname(__FILE__).'/account/nav.php'; ?>
	<!-- content start -->
	<div class="content">
        <div class="wish-tit clear">
            <h3 class="points-tit"><?php echo lang('my_wishlist');?></h3>
        </div>
        <?php 
        if(isset($wishlist) && !empty($wishlist)){
        ?>
        <div class="list">
            <!--index为id号-->
            <?php 
            foreach ($wishlist as $wish_k=>$wish_v){
			if(isset($wish_v['product_info']) && !empty($wish_v['product_info'])){
			?>
            <div class="list-pro clear" index="<?php echo $wish_v['product_id']?>">
                <a href="<?php echo genURL($wish_v['product_info']['product_url'])?>" class="list-img"><img width="138" height="138" src="<?php echo PRODUCT_IMAGEM_URL.$wish_v['product_info']['product_image'] ?>"></a>
                <div class="list-info">
                    <div class="intro">
                        <h5><a href="<?php echo genURL($wish_v['product_info']['product_url'])?>"><?php echo isset($wish_v['product_info']['product_description_name'])?$wish_v['product_info']['product_description_name']:$wish_v['product_info']['product_name'];?></a></h5>
                        <span><?php echo date("M d,Y",strtotime($wish_v['wishlist_time_create']))?></span>
                    </div>
                    <!--
                    <p class="size">Size:L</p>
                    -->
                    <div class="tocart-btn">
                    	<a href="<?php echo genURL($wish_v['product_info']['product_url'])?>" title="See more details" class="add btn30-org"><?php echo lang('see_more_details');?></a>
                        <input type="button" class="del" value="<?php echo lang('delete');?>" title="Delete">
                    </div>
                </div>
            </div>
            <?php
            }	
            }
            ?>
        </div>
        <!-- 分页 start -->
		<div class="show-pager">
        	<?php include dirname(__FILE__).'/common/pagination.php'; ?>
		</div>
		<!-- 分页 end -->
        <?php	
        }else{
        ?>
        <div class="null">
            <?php echo lang('no_wishlist_tips');?><a class="btn34-org btn-text" href="<?php echo genURL('/')?>"><?php echo lang('continue_shopping');?></a>
        </div>
        <?php } ?>

<script type="text/html" id="addToCartBox">
<div class="ab-confirm" id="Pop">
    <h3><i></i>Added to Cart</h3>
    <div>
        <a href="<?php echo genURL('/cart')?>" class="view" id="view"><?php echo lang('view_cart');?></a>
        <a href="javascript:void(0)" class="cancel"><?php echo lang('close');?></a>
    </div>
</div>
</script>
<script type="text/html" id="delCartBox">
 <div class="del-confirm" id="Pop">
    <h4><?php echo lang('delete_item_tips');?></h4>
    <div>
        <button id="delete" class="btn34-org" title="Delete" type="button">
            <span class="btn-right"><span class="btn-text"><?php echo lang('delete');?></span></span>
        </button>
        <button id="delete" class="btn34-gray cancel" title="Cancel" type="button">
            <span class="btn-right"><span class="btn-text"><?php echo lang('cancel');?></span></span>
        </button>
    </div>
 </div>
</script>

	</div>
	<!-- content end -->
</div>
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/common/utils.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/login.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/wish-list.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>
