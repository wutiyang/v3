<div class="shipping ship-cart">
		<h2 class="ship-tit"><span><i></i><?php echo lang('cart_summary');?></span><a class="back" href="<?php echo genURL('cart');?>"><?php echo lang('back_to_cart');?></a></h2>
		<?php 
		if(isset($cart_data) && !empty($cart_data)){
		?>
		<div class="ship-tab">
			<!-- <form class="summary" action="" method="post"> -->
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
                        foreach ($cart_data as $cart_key=>$cart_val){
                        ?>
                        <tr>
							<td class="t1">
								<img width="75" height="75" src="<?php echo PRODUCT_IMAGES_URL.$cart_val['product_info']['product_image'];?>" alt="">
								<div><?php echo $cart_val['product_info']['product_description_name']?></div>
								<?php 
								if(isset($cart_val['attr']) && $cart_val['attr']){
								foreach ($cart_val['attr'] as $attr_key=>$attr_val){
								?>
								<p><?php echo $attr_val['name'];?>: <span><?php echo $attr_val['value']?></span>
								<?php	
								}	
								}
								?>
							</td>
							<td class="t2"><?php echo $cart_val['product_info']['product_currency']?> <?php echo number_format($cart_val['product_info']['product_discount_price'],2,'.',',')?></td>
							<td class="t3"><?php echo $cart_val['product_quantity']?></td>
							<td class="t4">
								<div class="t4-price"><?php echo $cart_val['product_info']['product_currency']?> <?php echo number_format(round($cart_val['view_new_product_sum'],2),2,'.',',');?></div>
								<?php if($cart_val['view_product_off'] > 0){?>
								<p class="save-price"><?php echo lang('save')." ".$cart_val['product_info']['product_currency']?><?php echo number_format(round($cart_val['view_product_off'],2),2,'.',',');?></p>
								<?php }?>
							</td>
						</tr>
                        <?php
                        }
                        ?>
						<tr>
							<td class="remember" colspan="2">
								<div class="frm coupon" id="rewards">
									<div class="remember-check">
										<input class="rewards" id="rewards1" type="checkbox" value="Rewards">
										<label for="rewards1"><?php echo lang('use_my_rewards_balance');?> <span class="rewards-color">(<?php echo lang('available_balance');?>:<em id="rewardsPrice"><?php echo $total_rewards?></em>)</span></label>
										
									</div>
									<div class="frm-check info hide">
										<div class="coupon-checkbox">
											<input class="code" type="hidden" id="rewardsKey" value="">
											<input class="code" type="text" id="rewardsKeyShow" value="<?php echo $default_able_use_rewards;?>">
											<span id="monetary"><?php echo $new_currency;?></span>
											<input type="button" value="<?php echo lang('apply');?>" class="apply icon-view" id="rewardsApply">
										</div>
                                        <div class="coupon-info hide" id="rewardsMsg">You use <span id="rewardsPrice">$ 21</span> for this purchase.</div>
                                        <p class="removeLinks"><a class="remove" href="javascript:;"><?php echo lang('remove');?></a></p>
                                    </div>
								</div>
								
								<div class="frm coupon" id="coupon">
									<div class="remember-check clearfix">
										<input class="rewards" id="rewards2" type="checkbox" value="Rewards">
										<label for="rewards2"><?php echo lang('enter_coupon_code');?></label>
									</div>
									<div class="frm-check info hide">
										<div class="coupon-checkbox">
											<input class="code" type="text" id="couponKey" value="">
											<input type="button" value="<?php echo lang('apply');?>" class="apply icon-view" id="couponApply">
										</div>
                                        <div class="coupon-info hide"><?php echo sprintf(lang('coupon_code_valid_tips'),'<span id="couponId">RO3S2C</span>');?> <span id="couponPriceErrorTitle"> <?php echo lang('your_discount_is');?> <em id="couponPrice">USD $ 10.00</em></span></div>
                                        <p class="removeLinks"><a class="remove" href="javascript:;"><?php echo lang('remove');?></a></p>
                                    </div>
								</div>
							</td>
							<td colspan="2" class="total">
								<p><?php echo lang('subtotal');?>: <span id="subtotal"><?php echo $new_currency?>  <?php echo number_format($subtotal,2,'.',',')?></span></p>
								<p><?php echo lang('shipping_charges');?>: +<em id="shippingCharges"><?php echo $new_currency?>  <?php echo number_format($shippingCharges,2,'.',',')?></em></p>
								<p><?php echo lang('insurance');?>: +<em id="insurancePrice"><?php echo $new_currency?>  <?php echo number_format($insurancePrice,2,'.',',')?></em></p>
								<p><?php echo lang('rewards_balance');?>: -<em class="gray" id="rewardsBalance"><?php echo $new_currency?>  <?php echo number_format($rewardsBalance,2,'.',',')?></em></p>
								<p><?php echo lang('coupon_savings');?>: -<em class="gray" id="couponSavings"><?php echo $new_currency?>  <?php echo number_format($couponSavings,2,'.',',')?></em></p>
								<p><?php echo lang('discount_savings');?>: -<em class="gray" id="discountSavings"><?php echo $new_currency?>  <?php echo number_format($discountSavings,2,'.',',')?></em></p>
								<h3><?php echo lang('order_total');?>: <span id="payPrice"><?php echo $new_currency?><?php echo number_format($payPrice,2,'.',',')?></span></h3>
								<input type="button" value="<?php echo lang('place_order');?>" class="place icon-view <?php if($bool==false) echo 'icon-view-dis';?>" onclick='onCheckout(<?php $orderObj = array('step'=>3);foreach($cart_data as $vcart){$orderObj['option'][] = array('id'=>$vcart['product_id'],'price'=>$vcart['product_info']['product_discount_price'],'quantity'=>$vcart['product_quantity']);}echo json_encode($orderObj);?>)' id="place">
								<input type="button" value="<?php echo lang('processing');?>" class="place icon-view icon-view-dis" id="Processing">
							</td>
						</tr>
					</tbody>
				</table>
			<!-- </form> -->
		</div>
		<?php
		}
		?>
		
	</div>
<script>
    window.ready=function(){onCheckout(<?php $orderObj['step']='Checkout';$orderObj['step'] = 2;echo json_encode($orderObj);?>);}
</script>
