<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL?>/css/cart.css?v=<?php echo STATIC_FILE_VERSION ?>">
<div class="main">
	<h1 class="title"><?php echo lang('shopping_cart');?></h1>

	<!-- 为空 end -->
			<?php 
			if($cart_merge==1 && $islogin){
			?>
			<div class="merge" id="merge">
				<i></i><?php echo lang('verify_items_tips');?>
			</div>
			<!-- 合并 end -->			
			<?php	
			}
			?>
	<div class="cart">
		<!-- left start -->
		<div class="cart-left">
			<?php 
			if(!isset($cart_group) || empty($cart_group)){
			?>
			<div class="null" id="nullDiv">
				<?php echo lang('your_shopping_cart_is_empty');?>
				<a class="icon-view" id="" href="<?php echo genURL('/');?>"><?php echo lang('continue_shopping');?></a>
			</div>
			<?php
			}else{
			?>
			<!-- cart-summary start -->
			<div class="cart-summary">
				<table class="cart-table" id="cartTable">
					<thead>
						<tr>
							<td><?php echo lang('item');?></td>
							<td><?php echo lang('item_price');?></td>
							<td><?php echo lang('quantity');?></td>
							<td><?php echo lang('price');?></td>
						</tr>
					</thead>
                    <tbody>
                    <?php
                    foreach($cart_group as $cart_item) {
                        if(!isset($cart_item['product_list']) || empty($cart_item['product_list'])) continue;
                        $full_class = false;
                        if(isset($cart_item['discount_type'])){
                            $full_class = true;
                            ?>
                            <!-- 满减提示 -->
                    <tr class="reduction">
						<td colspan="4">
							<div class="front"><strong><?php  echo $cart_item['view_title'];?></strong><span></span></div>
							<div class="back">
                                <?php if($cart_item['view_short_title']){ ?>
                                    <?php if($cart_item['is_off']){ ?>
                                        <a class="dis-act" href="javascript:;"><?php echo $cart_item['view_short_title'];?></a>
                                    <?php }elseif($cart_item['discount_url'] == 'javascript:;'){ ?>
                                        <a class="dis-eff" href="javascript:;"><?php echo $cart_item['view_short_title'];?></a>
                                    <?php }else{ ?>
                                        <a href="<?php echo $cart_item['discount_url'];?>"><?php echo $cart_item['view_short_title'];?></a>
                                    <?php } ?>
                                <?php } ?>
							</div>
						</td>
					</tr>
					<!-- 满减 end -->
                    <?php
                        }
                    $cart_data = $cart_item['product_list'];
                        foreach ($cart_data as $key => $val) {
                            if (isset($val['product_info'])) {
                                ?>
                                <tr cid="<?php echo $val['cart_id'] ?>" pid="<?php echo $val['product_id'] ?>"
                                    class="parent <?php if($full_class) echo 'bkgray';?>" sku="<?php echo $val['product_sku'] ?>">
                                    <td class="t1">
                                        <div class="cart-img"><a
                                                href="<?php echo genURL($val['product_info']['product_url']); ?>"><img
                                                    width="73" height="73"
                                                    src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>"
                                                    data-lazysrc="<?php echo PRODUCT_IMAGES_URL . $val['product_info']['product_image'] ?>"
                                                    alt="<?php echo $val['product_info']['product_description_name'] ?>"></a>
                                        </div>
                                        <div class="cart-info">
                                            <a href="<?php echo genURL($val['product_info']['product_url']); ?>"
                                               onclick='productEvent(<?php $productObj = array();
                                               $productObj['data'][] = array('price' => $val['product_info']['product_discount_price'], 'id' => $val['product_id']);
                                               $productObj['list'] = 'Shopping Cart';
                                               echo json_encode($productObj); ?>)'><?php echo $val['product_info']['product_description_name'] ?></a>

                                            <p class="cart-msg">
                                                <?php
                                                if (isset($val['attr']))
                                                    foreach ($val['attr'] as $attr => $attrv)
                                                         echo '<span>'.$attrv['attr_name'].':'.$attrv['attr_value_name'].'</span>';
                                                ?>
                                            </p>
                                            <?php if (isset($val['product_warehouse_class']) && !in_array($val['product_warehouse_class'], array( '','hide'))) { ?>
                                                <span class="shops-to <?php if(empty($val['product_warehouse_class'])) echo $val['product_warehouse_class']; ?>">
                                                <i class="<?php echo $val['product_warehouse_class'] ?>"></i>
                                                    <?php if($val['product_warehouse_class'] != 'distri-icon'){ ?>
                                                    <span class="warehouse"><?php echo $val['product_warehouse'] ?></span>
                                                    <?php } else { ?>
                                                    <span class="distribution"><?php echo lang('only_ships_european');?> <a href="<?php echo genURL('shipping_special_note.html');?>"><?php echo lang('see_details');?></a></span>
                                                    <?php } ?>
                                                </span>
                                            <?php } ?>
                                        </div>
                                        <div
                                            class="sale-out <?php if (isset($val['sell_out']) && $val['sell_out']) echo ' on'; ?>">
                                            <i class="tips"></i>
                                            <span
                                                class="span-red"><?php echo lang('sorry_the_product_sold_out'); ?></span>
                                        </div>
                                    </td>
                                    <td class="t2">
                                        <p class="price"><?php echo $val['product_info']['product_currency'] ?><?php echo number_format($val['product_info']['product_discount_price'], 2, '.', ','); ?></p>

                                        <p class="discount"><?php echo $val['product_info']['product_currency'] ?><?php echo number_format($val['product_info']['product_price_market'], 2, '.', ','); ?></p>

                                        <p class="save">
                                            <em><?php echo lang('you_save'); ?> </em><span><?php echo $val['product_info']['product_currency'] ?><?php echo number_format($val['product_info']['product_price_market'] - $val['product_info']['product_discount_price'], 2, '.', ','); ?></span>
                                        </p>
                                    </td>
                                    <td class="t3">
                                        <div class="select-box">
                                            <div title="" class="select-block">
                                                <div class="selected">
                                                    <a rel="nofollow" href="javascript:;" title="">
                                                        <i class="account-icon"></i>
                                                        <span class="default"
                                                              attrid="<?php echo $val['product_quantity'] ?>"><?php echo $val['product_quantity'] ?></span>
                                                    </a>
                                                    <i class="icon-select-arrow"></i>
                                                </div>
                                                <div class="drop-box">
                                                    <ul class="drop-content drop-list">
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="links"><a class="move"
                                                            href="<?php echo genURL("cart"); ?>"><?php echo lang('move_to_wishlist'); ?></a>
                                        </p>

                                        <p class="links"><a class="del" href="<?php echo genURL("cart"); ?>"
                                                            onclick='removeCartEvent(<?php $productObj = array();
                                                            $productObj['data'][] = array('id' => $val['product_id'], 'price' => $val['product_info']['product_discount_price'], 'quantity' => $val['product_quantity']);
                                                            echo json_encode($productObj); ?>)'><?php echo lang('delete'); ?></a>
                                        </p>
                                    </td>
                                    <td class="t4">
                                        <?php echo $val['product_info']['product_currency'] ?>
                                        <?php echo isset($val['view_new_product_sum'])?number_format($val['view_new_product_sum'],2,'.',','):number_format($val['view_product_sum'],2,'.',','); ?>
                                        <!-- save -->
                                         <?php if(isset($val['view_product_off']) && $val['view_product_off'] > 0){ ?><p class="save-price"><?php echo lang('save_money');?> <?php echo $val['product_info']['product_currency'].number_format($val['view_product_off'],2,'.',',');?></p> <?php } ?>
                                    </td>
                                </tr>
                            <?php
                            }
                        }
                    }
                    ?>
					<tr>
						<td class="continue">
							<a class="icon-view icon-view-dis" id="continue" href="<?php echo genURL("");?>"><?php echo lang('continue_shopping');?></a>
						</td>
						<td colspan="3" class="t4-bm">
							<div class="t4-price">
								<p class="bold"><?php echo lang('subtotal');?>(<span id="count"><?php echo $count?></span> items): <span class="price" id="subtotal"><?php echo $new_currency.number_format($total_price,2,'.',',');?></span></p>
								<p><?php echo lang('total_savings');?>: <em id="savings"><?php echo $new_currency.number_format($save_price,2,'.',',');?></em></p>
								<div class="t4-price-reward">
									<?php echo sprintf(lang('you_will_get_a_xxx_reward'),'<em  id="reward">'.$new_currency.number_format($reword,2,'.',',').'</em>');?>
									<i class="describe">
										<div class="popBox">
					                        <div class="pop-icon">
					                        	<em>◆</em>
					                        	<span>◆</span>
					                        </div>
					                        <div class="con">
					                            <p><?php echo lang('you_will_get_a_xxx_reward_desc');?>.</p> 
					                        </div>   
					                     </div>
				                    
					                 </i>
								</div>
								
							</div>
						</td>
					</tr>
                    </tbody>
				</table>
                <script type="text/html" id="deleteBox">
				<div class="deletePop">
                	<div class="mask"></div>
                	<div class="content"><?php echo lang('deleting_this_item_from_your_cart');?></div>
                </div>
				</script>
                <script type="text/html" id="wishlistBox">
				<div class="deletePop">
                	<div class="mask"></div>
                	<div class="content"><?php echo lang('moving_this_item_to_your_wishlist');?></div>
                </div>
				</script>
			</div>
			<!-- cart-summary end -->
			<?php	
			}
			?>
            <div class="mode-div">
			<?php
			//wishlist 
			if(isset($login_type) && $login_type==1 && !empty($wishlist)){
				include dirname(__FILE__).'/cart/cart_wishlist.php';
			}
			?>
	        
	        <div class="austin">
	        	<div class="austin1">
	        		<h2><?php echo lang('shop_with_confidence');?></h2>
	        		<p><?php echo lang('shop_with_confidence_description');?></p>
	        	</div>
	        	<div class="austin2">
	        		<h2><?php echo lang('when_will_get_items');?></h2>
	        		<p><?php echo lang('when_will_get_items_description1');?></p>
                    <p class="shipping_time_tips">
                        <em><?php echo lang('shipping_time_freeshipping_text');?> :</em> <?php echo lang('shipping_time_10_20_days');?> <br>
                        <em><?php echo lang('shipping_time_Standard_text');?> :</em> <?php echo lang('shipping_time_6_10_days');?> <br>
                        <em><?php echo lang('shipping_time_expedited_text');?> :</em> <?php echo lang('shipping_time_3_7_days');?> <br>
                        <span class="orange"><?php echo lang('when_will_get_items_description2');?></span>
                    </p>
	        	</div>
	        	<div class="austin3">
	        		<h2><?php echo lang('what_payment_methods_can_use');?></h2>
	        		<p><?php echo lang('what_payment_methods_can_use_description');?></p>
	        	</div>
	        </div>
	        <!--  austin end -->
            </div>
		</div>
		<!-- left end -->
		<!-- right start -->
        <div class="cart-right-box">
        	<input type="hidden" id="eachbuyer_logState" value="<?php echo $login_type?>">
			<div class="cart-right" id="cartRight">
                <p class="total-count"><?php echo lang('subtotal');?>(<span id="count2"><?php echo $count;?></span> items)</p>
                <p class="total-price"><strong id="subtotal2"><?php echo $new_currency?><span><?php echo number_format($total_price,2,'.',',');?></span></strong></p>
                <?php 
                if($islogin){
                ?>
                <div class="checkout"><a class="icon-view" id="checkOut" href="<?php echo genURL("cart/?url=place_order/");?>" onclick='onCheckout(<?php $orderObj=array('option'=>'Proceed to Checkout','step'=>1);foreach($cart_group as $cart_data){foreach($cart_data['product_list'] as $vcart){$orderObj['data'][] = array('id'=>$vcart['product_id'],'price'=>number_format($vcart['product_info']['product_discount_price'],2,'.',','),'quantity'=>$vcart['product_quantity']);}}echo json_encode($orderObj);?>)'><?php echo lang('proceed_to_checkout');?></a></div>
                <?php }else{?>
                <div class="checkout"><a class="icon-view" id="checkOut" href="<?php echo genURL("cart/?url=place_order/");?>"><?php echo lang('proceed_to_checkout');?></a></div>
                <?php }?>
                <div class="or">
                    <em class="or-border"></em>
                    <span class="or-bg"><?php echo lang('or_upper');?></span>
                </div>
                <a class="pay" href="<?php echo genURL('cart/?url=paypal_ec_payment/redirectToPaypal');?>" onclick='onCheckout(<?php $orderObj['option']='Paypal_EC';echo json_encode($orderObj);?>)'><img src="<?php echo RESOURCE_URL?>/images/cart/paypalEc.png?v=<?php echo STATIC_FILE_VERSION ?>"></a>
            </div>
        </div>
		
		<!-- right end -->
	</div>
</div>
<input type="hidden" name="reg_from" value="3" id="reg_from"/>
<?php include dirname(__FILE__).'/common/minifooter.php'; ?>
<script type="text/javascript" src="<?php echo RESOURCE_URL?>/js/common/utils.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL?>/js/common/json2.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL?>/js/login.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL?>/js/cart.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>
