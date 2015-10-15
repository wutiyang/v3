<div class="search-condition" id="searchCondition">
    	<?php 
    		if(isset($attribute_data) && count($attribute_data)){
    			foreach ($attribute_data as $att_key=>$att_val){
				if(isset($att_val['group']) && count($att_val['group'])){
    	?>
    	<dl class="clearfix <?php if($category_info['category_type_display']==CATEGORY_TYPE_VER_DISPLAY) echo 'vertical';?>">
            <dt><?php echo $att_val['attribute_lang_title']?></dt>
            <dd>
            	<a href="javascript:void(0)" class="more btn-span">More<i></i></a>
                <a href="javascript:void(0)" class="less btn-span">Less<i></i></a>
                <ul class="attr-item">
                	<?php 
                		foreach ($att_val['group'] as $group_key=>$group_val){
						if($group_val['selected'] || $group_val['nums']){
					?>
					<li <?php if(isset($group_val['selected']) && ($group_val['selected']==true)) echo 'class="selected"';?>>
                		<a href="<?php echo $group_val['link'];?>" value="<?php echo $att_key."_".$group_val['attribute_value_group_id']?>"><i class="select-icon"></i><?php echo $group_val['new_group_lang'];?></a>(<?php echo $group_val['nums']?>)
                	</li>
                	<?php } ?>
                	<?php		
						}
                	?>
                	<!-- li class="selected"><a href="javascript:;" value=""><i class="select-icon"></i>Hid Xenmon </a>(23)</li>
               		<li><a href="javascript:;" value=""><i class="select-icon"></i>Hid Xenmon </a>(23)</li-->
                </ul>
            </dd>
        </dl>
    	<?php			
	    			}
	    		}				
	    	}
    	?>
    	<?php 
    	if(isset($attribute_data['price']) && count($attribute_data['price'])){
    	?>
    	<dl class="clearfix">
            <dt>Price</dt>
            <dd>
            	<a href="javascript:void(0)" class="more btn-span">More<i></i></a>
                <a href="javascript:void(0)" class="less btn-span">Less<i></i></a>
                <ul class="attr-item">
                	<?php 
                		foreach ($attribute_data['price'] as $price_id=>$price_info){
                	?>
                	<li <?php if($price_info['selected']) {echo "class='selected'";}?>>
                        <a href="<?php echo $price_info['link'];?>" value="<?php echo ($price_id+1)?>">
                            <i class="select-icon"></i><?php echo $price_info['product_currency'].$price_info['category_narrow_price_start']." - ".$price_info['product_currency'].$price_info['category_narrow_price_end']?> </a>(<?php echo $price_info['nums']?>)</li>
                	<?php
                		}
                	?>
                	<!-- li><a href="javascript:;" value=""><i class="select-icon"></i>Hid Xenmon </a>(23)</li-->
                </ul>
                <div class="intr-last">
                    <form method="post" name="priceForm">
                        <span class="price info"><?php echo $new_currency;?><input value="<?php $search_price_range = isset($basicParam['search_price_range'])?$basicParam['search_price_range']:',';$search_price_range = explode(',',$search_price_range);if(count($search_price_range) == 1)echo 0;else echo $search_price_range[0];?>" class="price-text front" type="text" name="price-front"></span>
                        <strong>to</strong>
                        <span class="price info"><?php echo $new_currency;?><input value="<?php if(count($search_price_range) == 1)echo $search_price_range[0];else echo $search_price_range[1];?>" class="price-text back" type="text" name="price-back"></span>
                        <input class="go" type="submit" value="Go">
                    </form>
                </div>
            </dd>
        </dl>
    	<?php	
    	}
    	?>
    </div>
