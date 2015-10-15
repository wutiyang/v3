<div class="content clearfix">
    	<div class="focus-imgs">
        	<div id="focus" class="ec_slider ec_slider_filter">
				<div class="ec_slider_main">
					<ul class="ec_slider_list">
						<?php
						if(isset($image_ad[1])){
							foreach ($image_ad[1] as $imagek=>$imagev){
						?>
						<li>
                        	<a href="<?php echo $imagev['lan_content']['url']?>" onclick='onPromoClick(<?php $productObj['data']['id'] = 'A'.$imagev['ad_id'];$productObj['data']['name'] = 'home_main_banner';echo json_encode($productObj);?>)'>
                                <img alt="<?php echo $imagev['lan_content']['alt']?>" src="<?php echo COMMON_IMAGE_URL.$imagev['lan_content']['img'] ?>" width="720" height="410">
                            </a>
                        </li>
						<?php 
							}
						} 
						?>
					</ul>
				</div>
           </div>
        </div>
		<div class="banner-module">
			<?php
				if(isset($image_ad[2])){
			?>
			<a href="<?php echo $image_ad[2][0]['lan_content']['url']?>" title="<?php echo $image_ad[2][0]['lan_content']['alt']?>" class="banner1" onclick='onPromoClick(<?php $productObj['data']['id'] = 'A'.$image_ad[2][0]['ad_id'];$productObj['data']['name'] = 'home_right_banner_1';echo json_encode($productObj);?>)'>
			<img src="<?php echo COMMON_IMAGE_URL.$image_ad[2][0]['lan_content']['img'] ?>"  width="" height="">
            </a>
			<?php
			}
			?>
			
			<?php 
				if(isset($image_ad[3])){
			?>
			<a href="<?php echo $image_ad[3][0]['lan_content']['url']?>" title="<?php echo $image_ad[3][0]['lan_content']['alt']?>" class="banner2" onclick='onPromoClick(<?php $productObj['data']['id'] = 'A'.$image_ad[3][0]['ad_id'];$productObj['data']['name'] = 'home_right_banner_2';echo json_encode($productObj);?>)'>
			<img src="<?php echo COMMON_IMAGE_URL.$image_ad[3][0]['lan_content']['img'] ?>" width="" height=""></a>
			<?php
			}
			?>
        	
		</div>
        <!-- 焦点图片 end -->
	</div>
