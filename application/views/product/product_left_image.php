<div class="pro-zoom" id="picZoom">
			<?php 
			if(isset($product_extend_images) && isset($product_extend_images[0])){
                $product_base_info['product_description_name'] = isset($product_base_info['product_description_name'])?$product_base_info['product_description_name']:'';
			?>
			<div class="pro-original relative">
                <a title="<?php echo $product_base_info['product_description_name'];?>" href="javascript:;">
                    <img src="<?php echo PRODUCT_IMAGEL_URL.$product_extend_images[0]['product_image_path'] ?>" alt="<?php echo $product_base_info['product_description_name'];?>" data-sku="<?php echo $product_extend_images[0]['product_sku']?>" data-image="<?php echo PRODUCT_IMAGEXL_URL.$product_extend_images[0]['product_image_path'] ?>" id="bigPic" width="450" height="450">
                </a>
                <?php 
                if(isset($product_extend_price) && isset($product_extend_price['product_discount_number']) && $product_extend_price['product_discount_number']){
                ?>
				<p class="icon-off"><i><?php echo (int) $product_extend_price['product_discount_number'];?></i></p>                
                <?php	
                }
                ?>
            </div>
            <div class="pro-thumb relative">
                <div class="pro-thumb-list relative">
                    <ul class="imgZoomLi">
                    	<?php 
                    	foreach ($product_extend_images as $ki=>$vi){
                    	?>
                    	<li data-sku="<?php echo $vi['product_sku']?>" data-image="<?php echo PRODUCT_IMAGEXL_URL.$vi['product_image_path'] ?>"  class="item">
                            <a href="javascript:;">
                                <img width="54" height="54" src="<?php echo PRODUCT_IMAGEL_URL.$vi['product_image_path'] ?>" alt=""></a>
                                <em class="arrow"></em>
                        </li>
                    	<?php
                    	}
                    	?>
                    </ul>
                </div>
                <a href="javascript:;" class="btn-arrow arrow_top"></a>
                <a href="javascript:;" class="btn-arrow arrow_bottom"></a>
            </div>			
			<?php
			}
			?>
        </div>
