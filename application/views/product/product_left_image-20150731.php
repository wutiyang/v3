<div class="pro-zoom" id="picZoom">
			<?php 
			if(isset($product_extend_images) && isset($product_extend_images[0])){
			?>
			<div class="pro-original relative">
                <a title="" href="javascript:;">
                    <img src="<?php echo PRODUCT_IMAGE_URL."450x450/".$product_extend_images[0]['product_image_path'] ?>" data-sku="<?php echo $product_extend_images[0]['product_sku']?>" id="bigPic" width="450" height="450">
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
                    	<li data-sku="<?php echo $vi['product_sku']?>" class="item">
                            <a href="javascript:;">
                                <img src="<?php echo PRODUCT_IMAGE_URL."450x450/".$vi['product_image_path'] ?>" alt=""></a>
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