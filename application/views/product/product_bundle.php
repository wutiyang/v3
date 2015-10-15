<div class="con">
            <ul class="together">
                <li class="master selectModule" >
                    <div class="pro-list-block">
                        <div class="p-pic">
                            <a title="<?php echo $product_base_info['product_description_name']?>" href="<?php echo genURL($product_base_info['product_url'])?>" onclick='productEvent(<?php $productObj = array();$productObj['data'][] = array('price' => $product_extend_price['product_discount_price'],'id' => $product_base_info['product_id']);$productObj['list'] = 'Product Detail Page'; echo json_encode($productObj);?>)'><img alt="<?php echo $product_base_info['product_description_name']?>" src="<?php echo PRODUCT_IMAGEM_URL.$product_base_info['product_image']?>" width="160" height="160"></a>
                        </div>
                        <div class="p-name current">
                            <i class="icon-ipt"></i><a title="<?php echo $product_base_info['product_description_name']?>" href="<?php echo genURL($product_base_info['product_url'])?>" onclick='productEvent(<?php $productObj = array();$productObj['data'][] = array('price' => $product_extend_price['product_discount_price'],'id' => $product_base_info['product_id']);$productObj['list'] = 'Product Detail Page'; echo json_encode($productObj);?>)'><?php echo $product_base_info['product_description_name']?></a>
                        </div>
                        <div class="p-price">
                            <i class="elidePrice hide"><?php echo number_format($product_extend_price['product_price_market'],2,'.',',')?></i>
                            <?php echo $product_extend_price['product_currency']?><i class="salePrice"><?php echo number_format($product_extend_price['product_discount_price'],2,'.',',')?></i>  
                        </div>
                       <table class="attr" pidDate="eachbuyer<?php echo $product_base_info['product_id']?>" sku="" bool=false pid="<?php echo $product_base_info['product_id']?>">
                        <?php
                        if(isset($attr_and_attrvalue)){ 
		            	foreach ($attr_and_attrvalue as $bundle_attr_value_k=>$bundle_attr_value_v){
		            	?>
		            	<tr value='<?php echo $bundle_attr_value_v['complexattr_id']?>'>
                            <td class="select-div select-box">
                                <div class="select-block" title="<?php echo $bundle_attr_value_v['complexattr_lang_title']?>">
                                    <div class="selected">
                                        <a title="" href="javascript:;" rel="nofollow">
                                            <i class="account-icon"></i>
                                            <span attrid="default" class="default">Select <?php echo $bundle_attr_value_v['complexattr_lang_title']?></span>
                                        </a>
                                        <i class="icon-select-arrow"></i>
                                    </div>
                                    <?php 
		                            if(isset($bundle_attr_value_v['attr_value'])){
		                            ?>
		                            <div class="drop-box">
                                        <ul class="drop-content drop-list">
                                            <li class="hide" attrid="default"><a href="javascript:;">Select <?php echo $bundle_attr_value_v['complexattr_lang_title']?></a></li>
                                            <?php 
		                                	foreach ($bundle_attr_value_v['attr_value'] as $bundle_attrvalue_k=>$bundle_attrvalue_v){
		                                	?>
		                                	<li attrid="<?php echo $bundle_attrvalue_v['complexattr_value_id']?>"><a href="javascript:;"><span><?php echo $bundle_attrvalue_v['complexattr_value_lang_title']?></span></a></li>
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
		            	}
		            	?>
		            	<input type="hidden"  pid-data="<?php echo $product_base_info['product_id']?>" value="" class="SKU"/>
	                    <script type="text/javascript">
			            var eachbuyer<?php echo $product_base_info['product_id']?>= <?php echo $sku_and_attrvalue;?>;
			            </script>
	                    <?php
	                    }else{
	                    ?>
	                    <input type="hidden"  pid-data="<?php echo $product_base_info['product_id']?>" value="<?php if(isset($single_sku_value))echo $single_sku_value;?>" class="SKU"/>
	                    <?php	
	                    }
	                    ?>
                    </table>
                    </div>
                </li>
                
                <li class="add"></li>
                <?php 
                foreach ($bundle_product_list as $bundle_pro_k=>$bundle_pro_v){
                ?>
                <li class="selectModule">
                    <div class="pro-list-block">
                        <div class="p-pic"><?php $product_description_name = isset($bundle_pro_v['product_description_name'])?$bundle_pro_v['product_description_name']:'';?>
                            <a title="<?php echo $product_description_name;?>" href="<?php echo genURL($bundle_pro_v['product_url'])?>" onclick='productEvent(<?php $productObj = array();$productObj['data'][] = array('price' => $product_extend_price['product_discount_price'],'id' => $product_base_info['product_id']);$productObj['list'] = 'Product Detail Page'; echo json_encode($productObj);?>)'><img alt="" src="<?php echo PRODUCT_IMAGEM_URL.$bundle_pro_v['product_image']?>" width="160" height="160"></a>
                        </div>
                        <div class="p-name current">
                            <i class="icon-ipt"></i><a title="<?php echo $product_description_name;?>" href="<?php echo genURL($bundle_pro_v['product_url'])?>" onclick='productEvent(<?php $productObj = array();$productObj['data'][] = array('price' => $product_extend_price['product_discount_price'],'id' => $product_base_info['product_id']);$productObj['list'] = 'Product Detail Page'; echo json_encode($productObj);?>)'><?php echo $product_description_name;?></a>
                        </div>
                        <div class="p-price">
                            <i class="elidePrice hide"><?php echo $bundle_pro_v['product_price_market']?></i>
                            <?php echo $bundle_pro_v['product_currency']?><i class="salePrice"><?php echo $bundle_pro_v['product_discount_price']?></i> 
                        </div>
                    <table class="attr" pidDate="eachbuyer<?php echo $bundle_pro_v['product_id']?>" sku="" bool=false  pid="<?php echo $bundle_pro_v['product_id']?>">
                     <?php 
                     if(isset($bundle_pro_v['attr_data'])){
                     foreach ($bundle_pro_v['attr_data'] as $bundle_value_k=>$bundle_value_v){
                     ?>
                     <tr value="<?php echo $bundle_value_v['complexattr_id']?>">
                    	<td class="select-div">
                        <div class="select-div select-box">
                            <div class="select-block">
                                <div class="selected" title="Select Color">
                                    <a rel="nofollow" href="javascript:;">
                                        <i class="account-icon"></i>
                                        <span attrid="default"  class="default">Select <?php echo $bundle_value_v['complexattr_lang_title']?></span>
                                    </a>
                                    <i class="icon-select-arrow"></i>
                                </div>
                                <div class="drop-box">
                                    <ul class="drop-content drop-list">
                                        <li class="hide" attrid="default"><a href="javascript:;">Select <?php echo $bundle_value_v['complexattr_lang_title']?></a></li>
                                        <?php 
                                        foreach ($bundle_value_v['attr_value'] as $bundle_value_key=>$bundle_value_val){
                                        ?>
										<li attrid="<?php echo $bundle_value_val['complexattr_value_id']?>"><a href="javascript:;"><?php echo $bundle_value_val['complexattr_value_lang_title']?></a></li>                                        
                                        <?php	
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        </td>
                        </tr>
                     <?php
                     }
                     ?>
                     <input type="hidden"  pid-data="<?php echo $bundle_pro_v['product_id']?>" value="" class="SKU"/>
                     <script type="text/javascript">
			            var eachbuyer<?php echo $bundle_pro_v['product_id']?> = <?php echo $bundle_pro_v['sku_data']?>;
			         </script>
                     <?php
                     }else{
                     ?>
                     <input type="hidden"  pid-data="<?php echo $bundle_pro_v['product_id']?>" value="<?php echo $bundle_pro_v['single_sku_value'];?>" class="SKU"/>
                     <?php	
                     }
                     ?>
                        </table>
                    </div>
                </li>
                <?php
                }
                ?>
                <li class="list-tocart" id="countPrice">
                    <strong>Total Price<span class="count"><?php echo $product_extend_price['product_currency']?><i>144.00</i></span></strong>
                    <a href="/cart"  class="btnorg35" id="listToCart"><?php echo lang('add_selected_items_to_cart');?></a>
                    <strong>Save<span class="save"><?php echo $product_extend_price['product_currency']?><i>14.00</i>!</span></strong>
                </li>
            </ul>
        </div>
