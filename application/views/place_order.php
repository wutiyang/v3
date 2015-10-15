<?php include dirname(__FILE__).'/common/miniheader.php'; ?>
<input type="hidden" name="eb_address" id="eb_address" value="<?php echo lang('preferred_address');?>">
<input type="hidden" name="eb_edit"  id="eb_edit" value="<?php echo lang('edit');?>">
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/place_order.css?v=<?php echo STATIC_FILE_VERSION ?>">

<div class="main checkout">
<form class="place-order" id="order" action="" method="post">
	<?php include dirname(__FILE__).'/placeorder/place_order_address.php'; ?>
	<!-- 地址 end -->
	<?php include dirname(__FILE__).'/placeorder/place_order_shipping.php'; ?>
	<!-- shipping end -->
	<?php include dirname(__FILE__).'/placeorder/place_order_payment.php'; ?>
	<!-- Payment Method end -->
	<?php include dirname(__FILE__).'/placeorder/place_order_cart_summary.php'; ?>
	<!-- Cart Summary end -->
</form>
</div>
<div id="checkoutMask" class="checkout_mask"><div class="mask"></div><img class="loading_img" src="<?php echo RESOURCE_URL ?>images/common/loading/60_60.gif?v=<?php echo STATIC_FILE_VERSION ?>"></div>
<script type="text/html" id="placeTips">
<div id="Pop" class="poptips">
	<p><?php echo lang('session_expired_tips');?></p>
    <a title="Start Over" class="btnorg35" href="javascript:;" id="startOver"><?php echo lang('start_over');?></a>
</div>
</script>
<input type="hidden" id="place_order_type" value="<?php echo $place_order_type;?>"/>
<?php include dirname(__FILE__).'/common/minifooter.php'; ?>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/common/utils.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/login.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/place_order.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>