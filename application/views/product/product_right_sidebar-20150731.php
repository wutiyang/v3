<div class="side">
			<?php 
			if(isset($catregory_recommend_product) && count($catregory_recommend_product)){
			?>
			<div class="related">
                <h5>Explore Related Products</h5>
                <ul>
                	<?php 
                	foreach ($catregory_recommend_product as $recommend_key=>$recommend_val){
                	?>
					<li><a href="<?php echo genURL($recommend_val['category_url'])?>">
						<?php 
						if($recommend_val['category_image']){
						?>
						<img src="<?php echo RESOURCE_URL ?>images/common/default.png" data-lazysrc="<?php echo PRODUCT_IMAGE_URL.$recommend_val['category_image']?>" width="92" height="83" alt="<?php echo $recommend_val['category_name']?>"></a>
						<?php							
						}else{
						?>
						<img src="<?php echo RESOURCE_URL?>images/common/default.png" data-lazysrc="<?php echo RESOURCE_URL ?>images/common/default.png"  width="92" height="83" alt="<?php echo $recommend_val['category_name']?>"></a>
						<?php	
						}
						?>
					</li>                	
                	<?php
                	}
                	?>
                </ul>
            </div>
			<?php	
			}
			?>
			<?php 
			if(isset($alsolike_data) && !empty($alsolike_data)){
			?>
			<div class="also">
                <h5>Customers Who Bought  This Item Also Bought</h5>
                <?php 
                foreach ($alsolike_data as $like_k=>$like_v){
                ?>
                <div>
                    <a href=""><img src="<?php echo PRODUCT_IMAGE_URL."158x158/".$like_v['product_image']?>" width="158" height="158"></a>
                    <p><a href=""><?php echo $like_v['product_description_name']?></a></p>
                    <p class="price"><?php echo $like_v['product_currency']?> <?php echo $like_v['product_discount_price']?></p>
                </div>
                <?php
                }
                ?>
            </div>
			<?php
			}
			?>
        </div>
