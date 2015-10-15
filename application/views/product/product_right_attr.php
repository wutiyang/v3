<div class="pro-wrapper selectModule">
            <!--share-list start-->
            <div class="share-list">
                <a href="javascript:void(0);" onclick="javascript:window.open('https://www.facebook.com/dialog/share?app_id=1390176774575549&display=popup&href=<?php echo urlencode(genUrl($product_base_info['product_url']).'?utm_source=facebook%26utm_medium=pageshare%26utm_campaign=product') ?>&title=<?php echo urlencode($product_base_info['product_description_name']) ?>&picture=<?php echo urlencode(PRODUCT_IMAGEXL_URL.$product_base_info['product_image']) ?>&redirect_uri=<?php echo urlencode(genUrl('close')) ?>','_blank','width=600, height=652')" class="icon-facebook" title="Facebook"></a>
                <a href="javascript:void(0);" onclick="javascript:window.open('https://twitter.com/intent/tweet?text=<?php echo lang('text_twitter_share') ?>+@eachbuyer+<?php echo urlencode(genUrl($product_base_info['product_url']).'?utm_source=twitter%26utm_medium=pageshare%26utm_campaign=product') ?>','_blank','width=600, height=652')" class="icon-twitter" title="Twitter"></a>
                <a href="javascript:void(0);" onclick="javascript:window.open('http://www.pinterest.com/pin/create/button/?url=<?php echo urlencode(genUrl($product_base_info['product_url']).'?utm_source=pinterest%26utm_medium=pageshare%26utm_campaign=product') ?>&media=<?php echo urlencode(PRODUCT_IMAGEXL_URL.$product_base_info['product_image']) ?>&description=<?php echo urlencode($product_base_info['product_description_name']) ?>','_blank','width=765, height=752')" class="icon-plusone" title="Pinterest"></a>
                <a href="mailto:?subject=<?php echo lang('text_email_share').' - '.$product_base_info['product_description_name'] ?>&body=<?php echo $product_base_info['product_description_name'].'%0A'.genUrl($product_base_info['product_url']).'?utm_source=email%26utm_medium=pageshare%26utm_campaign=product' ?>" class="icon-email" title="Email"></a>
            </div>
            <!--share-list end-->
            <h1>
                <?php echo $product_base_info['product_description_name']?>
                <span class="red"><?php echo $product_extend_price['slogan'];?></span>
            </h1>
            <div class="review-rate">
                <b class="starts-small-<?php echo $star_num;?>"></b>
                <?php 
                if(isset($review_nums) &&$review_nums>0){
                ?>
				(<?php echo $review_nums?>)                
                <?php
                }
                ?>
                <a href="#detail-tab2" rel="nofollow"><?php echo lang('write_a_review');?></a>
            </div> 
            <div class="original-price"><?php echo $product_extend_price['product_currency']?> <span class="elidePrice"><?php echo number_format($product_extend_price['product_price_market'],2,'.',',')?></span></div>
            <div class="discount-price">
                <b><?php echo $product_extend_price['product_currency']?> <span class="salePrice"><?php echo number_format($product_extend_price['product_discount_price'],2,'.',',')?></span></b>
                <?php 
                if(isset($product_extend_price['discount_start']) && isset($product_extend_price['discount_end']) && $product_extend_price['discount_end'] && $product_extend_price['discount_start']){
                ?>
                <span class="counter-timer">
                    <span class="counter-left"></span>
                    <span id="counter" class="counter">
                       <i class="icon-timer"></i>
                       <span data-endtime="<?php echo (strtotime($product_extend_price['discount_end'])-time());?>"></span>
                    </span>
                </span>
                <script type="text/javascript">
                $(function() {
					ec.load('ec.ui.countdown', {
							onload : function () {
								ec.ui.countdown('#counter span', {
									"html" : "<em class='day'>{#day}</em>&nbsp;<span class='day_text'><?php echo lang('days');?></span> <em>{#hours}</em><i>:</i><em>{#minutes}</em><i>:</i><em>{#seconds}</em>",
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
            //if(isset($note_data) && $note_data){
            ?>
            <!-- 
            <div class="sale">
                <span>
                    <i></i><?php //echo $note_data['new_note_content']?>
                </span>
            </div>
             -->
            <?php	
            //}
            ?>
            <!-- 满减 -->
            <div class="pro-reduction">
            	<?php if(!empty($notes['common_note'])):?>
                <li><i class="icon-not"></i><?php echo $notes['common_note']['new_note_content'];?></li>
                <?php endif;?>
                <?php if(!empty($notes['rewards_note'])):?>
                <li class="border">
                		<i class="icon-wallet"></i><?php echo $notes['rewards_note']['content'];?>
                		<em class="describe">
                            <div class="popBox">
                                <div class="pop-icon">
                                    <em>◆</em>
                                    <span>◆</span>
                                </div>
                                <div class="con">
                                    <p><?php echo lang('earn_x_rewards_tips');?></p>
                                </div>   
                             </div>
                        </em>
                </li>
                <?php endif;?>
                <?php if(!empty($notes['promotion_note'])):?>
                <li>
                    <i class="icon-sale"></i>
	                <?php foreach ($notes['promotion_note'] as $note):?>
                    <span>
                    	<a class="<?php if(empty($note['url'])) echo 'dis-cur';?>" href="<?php echo empty($note['url'])?'javascript:void(0);':genURL($note['url']);?>">
                    	<?php echo $note['content'];?>
                    	</a>
                    </span>
                	<?php endforeach;?>
                </li>
                <?php endif;?>
            </div>
            <!-- 满减 end -->
            <input type="hidden"  value="<?php echo $product_base_info['product_id']?>" id="pid"/>
            <input type="hidden"  value="<?php echo $product_extend_price['product_discount_price']?>" id="price"/>
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
                                    <span attrid="default"><?php echo lang('please_select');?></span>
                                </a>
                                <i class="icon-select-arrow"></i>
                            </div>
                            <?php 
                            if(isset($attr_value_v['attr_value'])){
                            ?>
							<div class="drop-box">
                                <ul class="drop-content drop-list">
                                	<li class="hide" attrid="default"><a href="javascript:;"><span><?php echo lang('please_select');?></span></a></li>
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
                    <td class="attr-name"><?php echo lang('qty');?>:</td>
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
					<a href="javascript:;" class="icon-view icon-view-dis"><?php echo lang('sold_out');?></a>               	 	
               	 	<?php	
               	 	}else{
               	 	?>
               	 	<span class="btn-box  dab-btn">
               	 	<a href="<?php echo genURL('cart');?>" data-url="<?php echo genURL('cart');?>" rel="nofollow" class="btnorg35" id="addToCart">
                        <i class="icon-cart"></i><?php echo lang('add_to_cart');?>
                    </a>
               	 	<?php
               	 	}
               	 	?>
                    <div class="pop" id="addToCartTip">
                    	<div class="pop-top"><span></span><em></em></div>
                        <div class="pop-con">
                            <?php echo lang('please_select');?>
                            <div class="tip-p"></div>
                        </div>
                    </div>
                </span>
                <form action="" method="get" id="addWishlist">
                    <a class="love <?php if(isset($product_base_info['love']))echo 'wish-red';?>" id="Love">
                        <div class="pop">
                            <div class="pop-top"><span></span><em></em></div>
                            <div class="pop-con"><?php echo lang('add_to_wishlist');?></div>
                        </div>
                    </a>
                </form>
            </div>
            <?php include dirname(__FILE__).'/product_shipping_time.php'; ?>
        </div>
