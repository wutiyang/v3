<?php 
if($category_info['category_type_display']==CATEGORY_TYPE_VER_DISPLAY){//竖向排列
?>
<!-- sidebar end -->
		<?php 
	    if(isset($attribute_data) && count($attribute_data)){
	    ?>
        <div class="aside">
            <h2><?php echo lang('refine_by');?></h2>
            <ul class="aside-list" id="accordion">
                <?php
                $attr = isset($basicParam['attr'])?explode(',',$basicParam['attr']):array();
                foreach ($attribute_data as $att_key=>$att_val){
                	if(isset($att_val['group']) && count($att_val['group'])){
                ?>
                	<li class="list-par">
	                    <a href="javascript:;" class="add"><?php echo $att_val['attribute_lang_title']?><i></i></a>
	                    <ul class="collapse">
	                    	<?php
	                    	foreach ($att_val['group'] as $group_key=>$group_val){
	                    		if($group_val['selected'] || $group_val['nums']){
							?>
							<li <?php if(isset($group_val['selected']) && ($group_val['selected']==true)) echo 'class="selected"';?>>
                                <?php
                                $attr_str = $att_key.'_'.$group_val['attribute_value_group_id']; ?>
								<a href="<?php echo $group_val['link'];?>" value="<?php echo $attr_str;?>"><em></em><span><?php echo $group_val['new_group_lang']?></span></a><span>(<?php echo $group_val['nums']?>)</span>
							</li>	
							<?php
								}
							}
	                    	?>
	                    </ul>
	                </li>
                <?php		
                	}
                }
                ?>
                <?php 
		    	if(isset($attribute_data['price']) && count($attribute_data['price'])){
		    	?>
		    	<li class="list-par">
                    <a class="add" href="javascript:;">Price<i></i></a>
                    <div>
                        <ul class="collapse">
                        	<?php 
                        	foreach ($attribute_data['price'] as $price_id=>$price_info){
                        	?>
                        	<li <?php if($price_info['selected']) {echo "class='selected'";}?>><a href="<?php echo $price_info['link'];?>">
                        		<em></em><?php echo $price_info['product_currency'].$price_info['category_narrow_price_start']." - ".$price_info['product_currency'].$price_info['category_narrow_price_end']?></a><span>(<?php echo $price_info['nums']?>)</span>
                        	</li>
                        	<?php	
                        	}
                        	?>
                        </ul>
                        <div class="intr-last">
                            <form method="post" name="priceForm">
                                <span class="price info"><?php echo $new_currency;?><input value="<?php $search_price_range = isset($basicParam['search_price_range'])?$basicParam['search_price_range']:',';$search_price_range = explode(',',$search_price_range);if(count($search_price_range) == 1)echo 0;else echo $search_price_range[0];?>" class="price-text front w37" type="text" name="price-front"></span>
                                <strong>to</strong>
                                <span class="price info"><?php echo $new_currency;?><input value="<?php if(count($search_price_range) == 1)echo $search_price_range[0];else echo $search_price_range[1];?>" class="price-text back w37" type="text" name="price-back"></span>
                                <input class="go" type="submit" value="Go">
                            </form>
                        </div>
                    </div>
                </li>
		    	<?php	
		    	}
		    	?>
                
                
            </ul>
            
        </div>
<!-- sidebar end -->		
<?php } } ?>
