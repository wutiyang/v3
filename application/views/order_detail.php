<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL?>css/order_list.css?v=<?php echo STATIC_FILE_VERSION ?>">
<div class="wrap breadcrumbs">
	<i class="icon-home">&nbsp;</i>
    <a href="<?php echo genURL('/')?>"><?php echo lang('home');?></a>
    <i class="icon-arr-right">&nbsp;</i>
    <a href="<?php echo genURL('order_list');?>"><?php echo lang('my_account');?></a>
    <i class="icon-arr-right">&nbsp;</i>
    <span><?php echo lang('my_order');?></span>

</div>
<div class="main" id="shipped">
<?php include dirname(__FILE__).'/account/nav.php'; ?>
<div class="canceled">

<div class="order-status-progress">
	<div class="message clear">

		<div class="order phase <?php echo $order['order_status']==OD_CANCEL?'':'orange-lite' ?>"><?php echo lang('progress_created') ?></div>
		<div class="order pend">
		<?php if($order['order_status']!=OD_CANCEL){ ?>
			<span><?php echo lang('order_status_pending') ?></span>
			<?php if(in_array($order['order_status'],array(OD_CREATE,OD_PAYING))){ ?>
			<div class="describe">
				<div class="popBox">
					<div class="pop-icon"><em>◆</em><span>◆</span></div>
					<div class="con">
						<p><?php echo lang('msg_order_incomplete') ?></p>
					</div>   
				</div> 
			</div>
			<?php } ?>
		<?php } ?>
		</div>

		<?php if($order['order_status']==OD_CANCEL){ ?>
		<div class="order payment">&nbsp;</div>
		<?php }else{ ?>
		<div class="order payment <?php echo in_array($order['order_status'],array(OD_CREATE,OD_PAYING,OD_CANCEL))?'':'orange-lite' ?>"><?php echo lang('progress_paid') ?></div>
		<?php } ?>
		<div class="order process">
		<?php if(!in_array($order['order_status'],array(OD_CREATE,OD_PAYING,OD_CANCEL))){ ?>
			<span><?php echo lang('order_status_processing') ?></span>
			<?php if(in_array($order['order_status'],array(OD_PAID,OD_PAIDCONFIRM,OD_AUDIT,OD_PROCESSING,OD_DELIVER))){ ?>
			<div class="describe">
				<div class="popBox">
					<div class="pop-icon"><em>◆</em><span>◆</span></div>
					<div class="con">
						<p><?php echo lang('msg_order_processing') ?></p>
						<div class="space1"><a class="hover-uorange" href="<?php echo genURL('review_create') ?>"><?php echo lang('write_review') ?></a></div> 
					</div>   
				</div> 
			</div>
			<?php } ?>
		<?php } ?>
		</div>

		<?php if($order['order_status']==OD_CANCEL){ ?>
		<div class="order ship"><?php echo lang('progress_canceled') ?></div>
		<?php }else{ ?>
		<div class="order ship <?php echo in_array($order['order_status'],array(OD_DELIVERED,OD_COMPLETED))?'orange-lite':'' ?>"><?php echo lang('progress_shipped') ?></div>
		<?php } ?>
	</div>	
	<?php
	$status2style = array(
		OD_CREATE => 'pending',
		OD_PAYING => 'pending',
		OD_PAID => 'process',
		OD_PAIDCONFIRM => 'process',
		OD_AUDIT => 'process',
		OD_PROCESSING => 'process',
		OD_DELIVER => 'process',
		OD_DELIVERED => 'shipped',
		OD_COMPLETED => 'shipped',
		OD_CANCEL => 'unpaid',
	);
	?>
	<div class="plan <?php echo id2name($order['order_status'],$status2style) ?>"></div>

	<div class="time">
		<div class="april"><?php echo date('h:i:s',strtotime($order['order_time_create']))?><br><?php echo date('M d,Y',strtotime($order['order_time_create'])) ?></div>
		<?php if(!in_array($order['order_status'],array(OD_CREATE,OD_PAYING,OD_CANCEL)) && $order['order_time_pay'] != '0000-00-00 00:00:00'){ ?>
		<div class="april april-c"><?php echo date('h:i:s',strtotime($order['order_time_pay']))?><br><?php echo date('M d,Y',strtotime($order['order_time_pay'])) ?></div>
		<?php } ?>
		<?php if(in_array($order['order_status'],array(OD_DELIVERED,OD_COMPLETED)) && $order['order_time_shipped'] != '0000-00-00 00:00:00'){ ?>
		<div class="april"><?php echo date('h:i:s',strtotime($order['order_time_shipped']))?><br><?php echo date('M d,Y',strtotime($order['order_time_shipped'])) ?></div>
		<?php } ?>
	</div>
</div>

<div class="order-Information">
	<div class="order-tit">
		<h5 class="tit-lt"><?php echo lang('order_info') ?></h5>
		<h5 class="tit-rt"><?php echo lang('order_action') ?></h5>
	</div>
	<div class="order-info">
		<div class="info-lt">
			<table class="info-tab">
				<tr><td><?php echo lang('order_info_no') ?>:</td><td><?php echo id2name('order_code',$order) ?></td></tr>
				<tr><td><?php echo lang('order_info_date') ?>:</td><td><?php echo date('M d,Y h:i:s',strtotime($order['order_time_create'])) ?></td></tr>
				<tr><td><?php echo lang('order_info_status') ?>:</td><td><?php echo lang('order_status_detail'.$order['order_status']) ?></td></tr>
				<tr><td><?php echo lang('order_info_address') ?>:</td><td><?php echo id2name('order_address_firstname',$order).' '.id2name('order_address_lastname',$order).','.id2name('order_address_street',$order).','.id2name('order_address_city',$order).','.id2name('order_address_state',$order).','.id2name('order_address_postalcode',$order).','.id2name('order_address_country',$order).','.id2name('order_address_phone',$order) ?></td></tr>
				<tr><td><?php echo lang('order_info_shipping') ?>:</td><td><?php echo lang('order_shipping'.$order['shipping_id']) ?></td></tr>
				<?php if(in_array($order['shipping_id'],array(4,5))){ ?>
				<tr class="successful"><td>&nbsp;</td><td><i></i><?php echo lang('order_track') ?></td></tr>
				<?php } ?>
				<?php if($order['order_flg_insurance'] == STATUS_ACTIVE){ ?>
				<tr class="successful"><td>&nbsp;</td><td><i></i><?php echo lang('order_shipping_insurance') ?></td></tr>
				<?php } ?>
				<?php if($order['order_flg_separate_package'] == STATUS_ACTIVE){ ?>
				<tr class="successful"><td>&nbsp;</td><td><i></i><?php echo lang('order_shipping_separate') ?></td></tr>
				<?php } ?>
				<tr><td><?php echo lang('order_info_payment') ?>:</td><td><?php echo $order['payment_name'] ?></td></tr>
				<tr><td><?php echo lang('order_info_total') ?>:</td><td><?php echo currencyAmount($order['order_price'],$order['order_currency']);?></td></tr>
			</table>
		</div>

		<div class="info-rt">
			<ul class="pending">
				<?php if($order['order_status'] == OD_CREATE){ ?>
				<?php if($flg_repay){ ?>
				<li><a class="review" href="<?php echo genURL('repay/'.$order['order_id']) ?>"><?php echo lang('complete_your_payment') ?><i class="to"></i></a></li>
				<?php } ?>
				<li><a class="cancelBtn" href="javascript:void(0);"><?php echo lang('btn_cancel') ?></a></li>
				<?php } ?>
				<?php if(!in_array($order['order_status'],array(OD_CREATE,OD_PAYING,OD_CANCEL))){ ?>
				<li><a class="review" href="<?php echo genURL('review_create') ?>"><?php echo lang('write_review') ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>

<div class="cart-summary">
	<h4><?php echo lang('cart_summary') ?></h4>
	<table class="myorder-table order-summary">
		<tr>
			<th><?php echo lang('order_detail_item') ?></th>
			<th><?php echo lang('order_detail_price') ?></th>
			<th><?php echo lang('order_detail_qty') ?></th>
			<th><?php echo lang('order_detail_subtotal') ?></th>
			<th><?php echo lang('order_detail_shipment') ?></th>
		</tr>
		<?php foreach($order_detail as $order_package_id => $sku_list){ ?>
			<?php if($order_package_id == 0){ ?>
				<?php foreach($sku_list as $sku){ ?>
					<tr>
						<td>
							<img width="73" height="73" alt="<?php echo addslashes($sku['order_product_name'])?>" src="<?php echo PRODUCT_IMAGES_URL.$sku['order_product_image'] ?>">
							<a href="<?php echo genUrl('-p'.$sku['product_id'].'.html') ?>"><?php echo $sku['order_product_name'] ?></a>
							<p>
								<?php foreach($sku['complexattr_list'] as $attr){ ?>
								<span><?php echo $attr['complexattr'] ?>: <?php echo $attr['complexattr_value'] ?></span>
								<?php } ?>
							</p>
						</td>
						<td class="t2"><?php echo currencyAmount($sku['order_product_price'],$order['order_currency']) ?></td>
						<td class="t3"><?php echo $sku['quantity'] ?></td>
						<td class="t4"><?php echo $sku['quantity']==$sku['order_product_quantity']?currencyAmount($sku['order_product_price_subtotal'],$order['order_currency']):currencyAmount($sku['order_product_price']*$sku['quantity'],$order['order_currency']) ?></td>
						<td class="t5"></td>
					</tr>
				<?php } ?>
			<?php }else{ ?>
				<tr>
					<td class="has-sub" colspan="4">
					<table>
					<?php foreach($sku_list as $sku){ ?>
						<tr>
							<td>
								<img width="73" height="73" alt="<?php echo addslashes($sku['order_product_name'])?>" src="<?php echo PRODUCT_IMAGES_URL.$sku['order_product_image'] ?>">
								<a href="<?php echo genUrl('-p'.$sku['product_id'].'.html') ?>"><?php echo $sku['order_product_name'] ?></a>
								<p>
									<?php foreach($sku['complexattr_list'] as $attr){ ?>
									<span><?php echo $attr['complexattr'] ?>: <?php echo $attr['complexattr_value'] ?></span>
									<?php } ?>
								</p>
							</td>
							<td class="t2"><?php echo currencyAmount($sku['order_product_price'],$order['order_currency']) ?></td>
							<td class="t3"><?php echo $sku['quantity'] ?></td>
							<td class="t4"><?php echo $sku['quantity']==$sku['order_product_quantity']?currencyAmount($sku['order_product_price_subtotal'],$order['order_currency']):currencyAmount($sku['order_product_price']*$sku['quantity'],$order['order_currency']) ?></td>
						</tr>
					<?php } ?>
					</table>
					</td>
					<td class="t5">
						<p>
							<?php echo lang('order_detail_shipment_shipped') ?> (<?php echo date('M d,Y h:i:s',strtotime($sku_list[0]['order_package_time_process'])) ?>)
						</p>
					</td>
				</tr>
			<?php } ?>
		<?php } ?>
	</table>
</div>


<div class="price">
	<p><span><?php echo lang('order_subtotal') ?>:</span><em class="orange"><?php echo currencyAmount($order['order_price_subtotal'],$order['order_currency']);?></em></p>

	<?php if($order['order_price_shipping'] > 0){ ?>
		<p><span><?php echo lang('order_shipping') ?>:</span>+ <em><?php echo currencyAmount($order['order_price_shipping'],$order['order_currency']);?></em></p>
	<?php } ?>
	<?php if($order['order_price_insurance'] > 0){ ?>
		<p><span><?php echo lang('order_insurance') ?>:</span>+ <em><?php echo currencyAmount($order['order_price_insurance'],$order['order_currency']);?></em></p>
	<?php } ?>
	<?php if($order['order_price_discount'] > 0){ ?>
		<p><span><?php echo lang('order_discount') ?>:</span>- <em><?php echo currencyAmount($order['order_price_discount'],$order['order_currency']);?></em></p>
	<?php } ?>
	<?php if($order['order_price_coupon'] > 0){ ?>
		<p><span><?php echo lang('order_coupon') ?>:</span>- <em><?php echo currencyAmount($order['order_price_coupon'],$order['order_currency']);?></em></p>
	<?php } ?>
	<?php if($order['order_price_rewards'] > 0){ ?>
		<p><span><?php echo lang('order_rewards') ?>:</span>- <em><?php echo currencyAmount($order['order_price_rewards'],$order['order_currency']);?></em></p>
	<?php } ?>
	<div class="grand-total">
		<a href="<?php echo genUrl('order_list') ?>"><< <?php echo lang('back_to_order_list') ?></a>
		<p><strong><?php echo lang('order_grand_total') ?>:</strong><em class="orange"><?php echo currencyAmount($order['order_price'],$order['order_currency']);?></em></p>
	</div>
</div>


</div>
</div>


<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/html" id="shippedTips">
<div class="per-confirm ship-confirm" id="Pop">
    <h3><?php echo lang('order_cancel_alert') ?></h3>
    <div class="btn-form">
        <a class="btn34-org btn-text" id="shippedOk" href="<?php echo genUrl('order_detail/cancel/'.$order['order_id']) ?>"><?php echo lang('alert_btn_ok') ?></a>
        <a class="btn34-gray btn-text" id="shippedClose" href="javascript:;"><?php echo lang('alert_btn_close') ?></a>
    </div>
</div>
</script>
<script type="text/javascript" src="<?php echo RESOURCE_URL?>js/common/utils.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL?>js/order_list.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>