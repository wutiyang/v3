<?php include dirname(__FILE__).'/common/miniheader.php'; ?>
<input type="hidden" name="eb_address" id="eb_address" value="prefessaddress">
<input type="hidden" name="eb_edit"  id="eb_edit" value="Edit">
<input type="hidden" id="place_order_type" value="paypal_ec_nologin"/>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/place_order.css?v=<?php echo STATIC_FILE_VERSION ?>">

<div class="main checkout">
<form class="place-order" id="order" action="" method="post">
	<div class="shipping ship-ads" id="address">
		<h2 class="ship-tit"><i></i><?php echo lang('shipping_address');?></h2>
		<div class="address">
			<ul>
				<li index="567984" class="on">
					<div class="ads-user"><i></i><strong><?php echo $address_data['first_name']." ".$address_data['last_name']?></strong></div>
					<div class="ads-info">
					<p><?php echo $address_data['address'];?></p>
					<p><?php echo $address_data['city'].' '.$address_data['region'].' '.$address_data['zipcode']?></p>
					<p><?php echo $address_data['country_name']?></p>
				</div>
					<div class="ads-phone"><?php echo lang('phone');?>:<?php echo $address_data['mobile']?></div>
					<!-- div class="preferred">Preferred Address</div-->
				</li>
			</ul>
		</div>
	</div>
	<!-- 地址 end -->
	<div class="shipping options" id="shipping">
		<h2 class="ship-tit"><i></i><?php echo lang('shipping_options');?></h2>
			<ul class="ship-potion">
				<?php
				if(!empty($shipping_list)){ 
				foreach ($shipping_list as $shipping_key=>$shipping_val){
				?>
				<li>
					<dl>
						<dt class="remember">
							<input class="radio" id="list1" type="radio" name="options" <?php if($shipping_val['id']==$shipping_id) echo 'checked';?>>
							<label for="list1"><?php echo $shipping_val['title']?></label>
						</dt>
						<dd><?php echo $shipping_val['day']?></dd>
						<dd><?php echo $shipping_val['price']?></dd>
					</dl>
					<?php 
					if($shipping_val['track']!=-1 && in_array($shipping_id,array(4,5)) ){
					?>
					<dl class="potion-info">
						<dt class="remember">
							<input class="check" id="checkbox1" type="checkbox" disabled="true" <?php if($shipping_val['id']==$shipping_id && in_array($shipping_id,array(4,5))) echo 'checked';?>>
							<label for="checkbox1" class="track"><?php echo $shipping_val['trackTitle']?></label>
						</dt>
						<dd><?php echo $shipping_val['trackPrice']?></dd>
					</dl>
					<?php
					}
					?>
				</li>
				<?php
				}}
				?>
				<?php 
				if(isset($order['order_flg_insurance']) && isset($order['order_flg_separate_package']) && ($order['order_flg_separate_package']==1 || $order['order_flg_insurance']==1)){ ?>
				<li class="potion-info">
					<?php if(isset($order['order_flg_insurance']) && $order['order_flg_insurance']==1){?>
					<dl>
						<dt class="remember">
							<input class="check" id="insurance" disabled="true" type="checkbox" <?php if($order['order_flg_insurance']==1) echo 'checked';?>>
							<label for="insurance"><?php echo lang('shipping_insurance');?></label>
						</dt>
						<dd>&nbsp;</dd>
						<dd id="shippingInsurance">+<?php echo $repay_currency.' '.$shipping_insurance?></dd>
					</dl>
					<?php } ?>
					<?php if(isset($order['order_flg_separate_package']) && $order['order_flg_separate_package']==1){?>
					<dl>
						<dt class="remember">
							<input class="check" disabled="true" id="itemsFirst" type="checkbox" <?php if($order['order_flg_separate_package']==1) echo 'checked';?>>
							<label for="itemsFirst"><?php echo lang('ship_available_first_tips');?></label>
						</dt>
						<dd>&nbsp;</dd>
					</dl>
					<?php }?>
				</li>
				<?php } ?>
			</ul>
	</div>
	<!-- shipping end -->
	<div class="shipping payment" id="payment">
		<h2 class="ship-tit"><i></i><?php echo lang('payment_method');?></h2>
		<div class="ship-pay">
			<div class="pay-tit">
				<?php echo lang('payment_methods_available_for');?>
				<div class="change">
					<span class="country" id="countryId" countryId="United Kingdom"><?php echo $payment_country_name;?></span> 
					<?php echo lang('in');?>
					<span class="currency" id="currencyId" currencyId="currency"><?php echo $order['order_currency'].' '.$repay_currency?></span>
				</div>
			</div>
			<ul class="method-list">
				<?php
				if(!empty($payment_list)){
				foreach ($payment_list as $pay_key=>$pay_val){
				if(isset($pay_val['checked']) && isset($pay_val['id']) && isset($pay_val['picname'])){
				?>
				<li class="remember rem-list <?php if($pay_val['checked']==1) echo 'on';?>" paymentid="<?php echo $pay_val['id']?>">
					<img class="pay-img" src="<?php echo $pay_val['picname']?>">
					<input class="method" id="<?php echo 'payment_'.$pay_val['id'];?>" type="checkbox" disabled="disabled"><label for="<?php echo 'payment_'.$pay_val['id'];?>"></label>
				</li>
				<?php
				} } }
				?>
			</ul>
		</div>
	</div>
	<!-- Payment Method end -->
	<div class="shipping ship-cart">
		<h2 class="ship-tit"><i></i><?php echo lang('cart_summary');?><!-- a class="back" href="javascript:;">Back to Cart</a--></h2>
		<div class="ship-tab">
				<table class="myorder-table order-summary" id="cartSummary">
                <thead>
						<tr>
							<th><?php echo lang('item');?></th>
							<th><?php echo lang('item_price');?></th>
							<th><?php echo lang('quantity');?></th>
							<th><?php echo lang('price');?></th>
						</tr>
                  </thead>
                        <tbody>
                        <?php
                        if(!empty($goods_list)){ 
                        foreach ($goods_list as $goods_key=>$goods_val){
                        ?>
                        <tr>
							<td>
								<img width="75" height="75" src="<?php echo PRODUCT_IMAGES_URL.$goods_val['order_product_image'];?>" alt="">
								<div><?php echo $goods_val['order_product_name']?></div>
								<?php 
								if(isset($goods_val['attr']) && $goods_val['attr']){
								foreach ($goods_val['attr'] as $attr_key=>$attr_val){
								?>
								<p><?php echo $attr_val['name'];?>: <span><?php echo $attr_val['value']?></span>
								<?php	
								}	
								}
								?>
							</td>
							<td class="t2"><?php echo $repay_currency.' '.$goods_val['order_product_price']?></td>
							<td class="t3"><?php echo $goods_val['order_product_quantity']?></td>
							<td class="t4"><?php echo $repay_currency.' '.round($goods_val['order_product_quantity']*$goods_val['order_product_price'],2)?></td>
						</tr>
                        <?php
                        }
                        }
                        ?>
						<tr>
							<td class="remember" colspan="2">
								<div class="frm coupon" id="rewards">
									<div class="remember-check">
										<input class="rewards <?php if($order['order_price_rewards'] > 0) echo 'checked';?>" id="rewards1" type="checkbox" value="Rewards" disabled="true">
										<label for="rewards1"><?php echo lang('use_my_rewards_balance');?> <!-- span class="rewards-color">(Available Balance:<em id="rewardsPrice">BRL R$ 43.94</em>)</span--></label>
									</div>
									<?php 
									if($order['order_price_rewards'] > 0){
									?>
									<div class="frm-check info">
                                        <div class="coupon-info" id="rewardsMsg"><?php echo lang('use_up_xxx_for_purchase_tips_start');?> <span id="rewardsPrice"><?php echo $repay_currency.' '.$order['order_price_rewards']?></span> <?php echo lang('use_up_xxx_for_purchase_tips_end');?></div>
                                    </div>
									<?php
									}
									?>
								</div>
								
								<div class="frm coupon" id="coupon">
									<div class="remember-check clearfix">
										<input class="rewards <?php if($order['order_price_coupon'] > 0) echo 'checked';?>" id="rewards2" type="checkbox" value="Rewards" disabled="true">
										<label for="rewards2"><?php echo lang('enter_a_coupon_code');?></label>
									</div>
									<?php 
									if($order['order_price_coupon'] > 0){
									?>
									<div class="frm-check info error">
                                        <div class="coupon-info"><?php echo sprintf(lang('coupon_code_valid_tips'),'<span id="couponId">'.$order['order_coupon'].'</span>');?> <?php echo sprintf(lang('your_discount_tips'),'<em id="couponPrice">'.$repay_currency.' '.$order['order_price_coupon'].'</em>');?></div>
                                    </div>
									<?php
									}
									?>
								</div>
							</td>
							<td colspan="2" class="total">
								<p><?php echo lang('subtotal');?>: <span id="subtotal"><?php echo $repay_currency.' '.$order['order_price_subtotal']?></span></p>
								<p><?php echo lang('shipping_charges');?>: <em id="shippingCharges">+<?php echo $repay_currency.' '.$order['order_price_shipping']?></em></p>
								<p><?php echo lang('insurance');?>: <em id="insurancePrice">+<?php echo $repay_currency.' '.$order['order_price_insurance']?></em></p>
								<p><?php echo lang('rewards_balance');?>: <em class="gray" id="rewardsBalance">-<?php echo $repay_currency.' '.$order['order_price_rewards']?></em></p>
								<p><?php echo lang('coupon_savings');?>: <em class="gray" id="couponSavings">-<?php echo $repay_currency.' '.$order['order_price_coupon']?></em></p>
								<p><?php echo lang('discount_savings');?>: <em class="gray" id="discountSavings">-<?php echo $repay_currency.' '.$order['order_price_discount']?></em></p>
								<h3><?php echo lang('order_total');?>: <span id="payPrice"><?php echo $repay_currency.' '.$order['order_price']?></span></h3>
								<input type="button" value="Place Order" class="place icon-view " onclick="" id="place">
								<input type="hidden" id="order_id" value="<?php echo $order['order_id']?>">
								<input type="hidden" id="payment_id" value="<?php echo $order['payment_id']?>">
							</td>
						</tr>
					</tbody>
				</table>
		</div>
	</div>
	<!-- Cart Summary end -->
</form>
</div>
<script type="text/html" id="placeTips">
<div id="Pop" class="poptips" >
	<p><?php echo lang('session_expired_tips');?></p>
    <a title="Start Over" class="btnorg35" href="javascript:;" id="startOver"><?php echo lang('start_over');?></a>
</div>
</script>
<?php include dirname(__FILE__).'/common/minifooter.php'; ?>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/common/utils.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/login.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/place_order_continue.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>