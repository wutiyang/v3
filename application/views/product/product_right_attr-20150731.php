<div class="pro-wrapper selectModule">
            <!--share-list start-->
            <div class="share-list">
                 <a href="javascript:;" class="icon-facebook" title="Facebook"></a><a href="javascript:;" class="icon-twitter" title="Tweet"></a><a href="javascript:;" class="icon-plusone" title="Google+"></a><a href="javascript:;" class="icon-email" title="Email"></a>
            </div>
            <!--share-list end-->
            <h1><?php echo $product_base_info['product_description_name']?></h1> 
            <div class="review-rate">
                <b class="starts-small-<?php echo $star_num;?>"></b>
                <?php 
                if(isset($review_nums) &&$review_nums>0){
                ?>
				(<?php echo $review_nums?>)                
                <?php
                }
                ?>
                <a href="#detail-tab2">Write a Review</a>
            </div> 
            <div class="original-price"><?php echo currentCurrency()?> <?php echo $product_extend_price['product_currency']?> <span class="elidePrice"><?php echo $product_extend_price['product_price_market']?></span></div>
            <div class="discount-price">
                <?php echo currentCurrency()?> <b><?php echo $product_extend_price['product_currency']?> <span class="salePrice"><?php echo $product_extend_price['product_discount_price']?></span></b>
                <?php 
                if(isset($product_extend_price['discount_start']) && isset($product_extend_price['discount_end']) && $product_extend_price['discount_end'] && $product_extend_price['discount_start']){
                ?>
                <span class="counter-timer">
                    <span class="counter-left"></span>
                    <span id="counter" class="counter">
                       <i class="icon-timer"></i>
                       <span data-endtime="<?php echo ($product_extend_price['discount_end']-$product_extend_price['discount_start']);?>"></span>
                    </span>
                </span>
                <script type="text/javascript">
                $(function() {
					ec.load('ec.ui.countdown', {
							onload : function () {
								ec.ui.countdown('#counter span', {
									"html" : "<em class='day'>{#day}</em>&nbsp;<span class='day_text'>{#dayText}</span> <em>{#hours}</em><i>:</i><em>{#minutes}</em><i>:</i><em>{#seconds}</em>",
									"zeroDayHide" : true,
									"callback" : function (json) {
										//计时结束时要执行的方法,比如置灰
										//$(this).parent().addClass('timeend');
									}
								});
							}
						});
					});
				</script>
                <?php
                }
                ?>
            </div>
            <?php 
            if(isset($note_data) && $note_data){
            ?>
            <div class="sale">
                <span>
                    <i></i><?php echo $note_data['new_note_content']?>
                </span>
            </div>
            <?php	
            }
            ?>
            <input type="hidden"  value="<?php echo $product_base_info['product_id']?>" id="pid"/>
            <table class="attr" pidDate="eachbuyer<?php echo $product_base_info['product_id']?>" sku="" bool=true>
            	<?php
            	if(isset($attr_and_attrvalue)){ 
            	foreach ($attr_and_attrvalue as $attr_value_k=>$attr_value_v){
            	?>
            	<tr value='<?php echo $attr_value_v['complexattr_id']?>'>
                    <td class="attr-name"><?php echo $attr_value_v['complexattr_lang_title']?>:</td>
                    <td class="select-div select-box">
                        <div class="select-block" title="<?php echo $attr_value_v['complexattr_lang_title']?>">
                            <div class="selected">
                                <a title="" href="javascript:;" rel="nofollow">
                                    <i class="account-icon"></i>
                                    <span attrid="default"></span>
                                </a>
                                <i class="icon-select-arrow"></i>
                            </div>
                            <?php 
                            if(isset($attr_value_v['attr_value'])){
                            ?>
							<div class="drop-box">
                                <ul class="drop-content drop-list">
                                	<li class="hide" attrid="default"><a href="javascript:;"><span></span></a></li>
                                	<?php 
                                	foreach ($attr_value_v['attr_value'] as $attrvalue_k=>$attrvalue_v){
                                	?>
									<li attrid="<?php echo $attrvalue_v['complexattr_value_id']?>"><a href="javascript:;"><span><?php echo $attrvalue_v['complexattr_value_lang_title']?></span></a></li>                                	
                                	<?php
                                	}
                                	?>
                                </ul>
                            </div>                            
                            <?php
                            }
                            ?>
                        </div>
                    </td>
                </tr>
            	<?php
            	}?>
            	<input type="hidden"  pid-data="<?php echo $product_base_info['product_id']?>" value="" class="SKU"/>
            	<script type="text/javascript">
	            var json_data = <?php echo $sku_and_attrvalue;?>;
	            var eachbuyer<?php echo $product_base_info['product_id']?>= json_data;
	            </script>
            	<?php
            	}else{
            	?>
            	<input type="hidden"  pid-data="<?php echo $product_base_info['product_id']?>" value="<?php if(isset($single_sku_value))echo $single_sku_value;?>" class="SKU"/>
            	<?php
            	}
            	?>
                <tr>
                    <td class="attr-name">Qty:</td>
                    <td>
                        <input type="text" data-max="65535" data-min="1" value="1" id="goodsNumInput" class="cart-num">
                        <span class="input-num" id="inputNum"></span>
                    </td>
                </tr>
            </table>
            <div class="add-tocartAndwish">
                <input type="hidden" id="prcId" value="<?php echo $product_base_info['product_id'];?>">
               	 	<?php 
               	 	if(!isset($sold_out) || $sold_out==true){
               	 	?>
               	 	<span class="btn-box">
					<a href="javascript:;" class="icon-view icon-view-dis">Sold Out</a>               	 	
               	 	<?php	
               	 	}else{
               	 	?>
               	 	<span class="btn-box  dab-btn">
               	 	<a href="<?php echo genURL('cart/');?>" class="btnorg35" id="addToCart" onclick='addCartEvent(<?php $productObj=array();$productObj['data'][]=array('id'=>$product_base_info['product_id'],'price'=>$product_extend_price['product_discount_price']);echo json_encode($productObj);?>)'>
                        <i class="icon-cart"></i>Add to Cart
                    </a>
               	 	<?php
               	 	}
               	 	?>
                    <div class="pop" id="addToCartTip">
                    	<div class="pop-top"><span></span><em></em></div>
                        <div class="pop-con">
                            Please select
                            <div class="tip-p"></div>
                        </div>
                    </div>
                </span>
                
                <a class="love" id="Love">
                    <div class="pop">
                        <div class="pop-top"><span></span><em></em></div>
                        <div class="pop-con">Add to Wishlist</div>
                    </div>
                </a>
            </div>
            <?php include dirname(__FILE__).'/product_shipping_time.php'; ?>
        </div>
