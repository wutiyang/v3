<div class="frequently module-con clearfix" id="moduleConId1">
	            <div class="frequently-name"><span><?php echo lang('items_in_wishlist');?></span></div>
	            <div class="con" size="5">
	                <ul class="con-list">
	                	<?php 
	                	foreach ($wishlist as $wishkey=>$wishval){
                            if(!isset($wishval['product_id'])) continue;
                            $wishval['product_description_name'] = isset($wishval['product_description_name'])?$wishval['product_description_name']:$wishval['product_name'];
                            $wishval['product_url'] = isset($wishval['product_url'])?$wishval['product_url']:'';
                            $wishval['product_image'] = isset($wishval['product_image'])?$wishval['product_image']:'';
                            $wishval['sku_and_attrvalue'] = isset($wishval['sku_and_attrvalue'])?$wishval['sku_and_attrvalue']:json_encode(array());
	                	?>
	                	<li class="selectModule">
	                        <div class="pro-list-block">
	                            <div class="p-pic">
	                                <a title="<?php echo $wishval['product_description_name']?>" href="<?php echo genURL($wishval['product_url'])?>">
	                                	<img width="160" height="160" alt="<?php echo $wishval['product_description_name']?>" src="<?php echo PRODUCT_IMAGEM_URL.$wishval['product_image']?>">
	                                </a>
	                            </div>
	                            <div class="p-name">
	                                <a title="<?php echo $wishval['product_description_name']?>" href="<?php echo genURL($wishval['product_url'])?>">
	                                <?php echo $wishval['product_description_name']?>
	                                </a>
	                            </div>
	                            <?php 
	                            if($wishval['single_sku_type']==1 && !empty($wishval['single_sku_type'])){
	                            ?>
	                            <table class="attr" piddate="eachbuyer<?php echo $wishval['product_id']?>" sku="<?php echo $wishval['single_sku_value']?>" bool="false" pid="<?php echo $wishval['product_id']?>"></table>
	                            <a class="icon-view add" href="javascript:void(0);"><?php echo lang('add_to_cart');?></a>
	                            <?php
	                            }else{
	                            ?>
	                            <table class="attr" piddate="eachbuyer<?php echo $wishval['product_id']?>" sku="" bool="false" pid="<?php echo $wishval['product_id']?>">
			                        <tbody>
			                        	<?php 
			                        	foreach ($wishval['attr_and_attrvalue'] as $attr_key=>$attr_val){
			                        	?>
			                        	<tr value="<?php echo $attr_val['complexattr_id']?>">
				                            <td class="select-div select-box">
				                                <div class="select-block" title="<?php echo $attr_val['complexattr_lang_title']?>">
				                                    <div class="selected">
				                                        <a title="" href="javascript:;" rel="nofollow">
				                                            <i class="account-icon"></i>
				                                            <span attrid="default" class="default">Select <?php echo $attr_val['complexattr_lang_title']?></span>
				                                        </a>
				                                        <i class="icon-select-arrow"></i>
				                                    </div>
				                                    <div class="drop-box">
				                                        <ul class="drop-content drop-list">
				                                            <li class="hide" attrid="default"><a href="javascript:;">Select <?php echo $attr_val['complexattr_lang_title']?></a></li>
				                                            <?php 
				                                            if(isset($attr_val['attr_value'])){
															foreach ($attr_val['attr_value'] as $attrvalue_key=>$attrvalue_val){
				                                            ?>
				                                            <li attrid="<?php echo $attrvalue_val['complexattr_value_id']?>"><a href="javascript:;"><?php echo $attrvalue_val['complexattr_value_lang_title']?></a></li>
				                                            <?php
				                                            }}
				                                            ?>
				                                        </ul>
				                                    </div>
				                                </div>
				                            </td>
				                        </tr>
			                        	<?php
			                        	}
			                        	?>
				                    </tbody>
				                </table>
				                <script type="text/javascript">
	                            var eachbuyer<?php echo $wishval['product_id']?>= <?php echo $wishval['sku_and_attrvalue']?>;
								</script>
				               <p><a class="icon-view add"  href="<?php echo genURL("cart")?>"><?php echo lang('add_to_cart');?></a></p>
	                            <?php
	                            }
	                            ?>
	                        </div>
	                    </li>
	                	<?php
	                	}
	                	?>
	                </ul>
	                <a class="arrow_left btn_arrow arrow_disabled" href="javascript:;"><span class="icon_arraw_left"></span> </a>
	                <a class="arrow_right btn_arrow" href="javascript:;"><span class="icon_arraw_right"></span> </a>
	            </div>
	        </div>
