<div class="side">
			<?php 
			if(isset($catregory_recommend_product) && count($catregory_recommend_product)){
			?>
			<div class="related">
                <h5><?php echo lang('explore_related_products');?></h5>
                <ul>
                	<?php 
                	foreach ($catregory_recommend_product as $recommend_key=>$recommend_val){
                	?>
					<li><a href="<?php echo genURL($recommend_val['category_url'],true)?>">
						<?php 
						if($recommend_val['category_image']){
						?>
						<img src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo COMMON_IMAGE_URL.$recommend_val['category_image']?>" width="92" height="83" alt="<?php echo $recommend_val['category_name']?>"></a>
						<?php							
						}else{
						?>
						<img src="<?php echo RESOURCE_URL?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>"  width="92" height="83" alt="<?php echo $recommend_val['category_name']?>"></a>
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
                <h5><?php echo lang('also_bought_tips');?></h5>
                <?php 
                foreach ($alsolike_data as $like_k=>$like_v){
                    $like_v['product_description_name'] = isset($like_v['product_description_name'])?$like_v['product_description_name']:"";
                ?>
                <div>
                    <a href="<?php echo genUrl($like_v['product_url'])?>" onclick='productEvent(<?php $productObj = array();$productObj['data'][] = array('price' => $like_v['product_discount_price'],'id' => $like_v['product_id']);$productObj['list'] = 'Product Detail Page'; echo json_encode($productObj);?>)'><img src="<?php echo PRODUCT_IMAGEM_URL.$like_v['product_image']?>" width="158" height="158"></a>
                    <p class="also-info"><a href="<?php echo genUrl($like_v['product_url'])?>" onclick='productEvent(<?php $productObj = array();$productObj['data'][] = array('price' => $like_v['product_discount_price'],'id' => $like_v['product_id']);$productObj['list'] = 'Product Detail Page'; echo json_encode($productObj);?>)'><?php echo $like_v['product_description_name']?></a></p>
                    <p class="price"><?php echo $like_v['product_currency']?> <?php echo number_format($like_v['product_discount_price'],2,'.',',')?></p>
                </div>
                <?php
                }
                ?>
            </div>
			<?php
			}
			?>
        </div>
