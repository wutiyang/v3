<div class="shop clear">
        <div class="shop-view clear">
            <h2><?php if(isset($widget_images[0])){
            	echo $widget_images[0]['lan_title'];
            }?></h2>
            <ul class="shop-list clearfix">
                <li>
                <?php 
                	if(isset($widget_images[1])){
						$one_img = $widget_images[1];
                ?>
                	<a href="<?php echo $one_img['lan_url']?>" onclick='onPromoClick(<?php $productObj['data']['id'] = 'H1';$productObj['data']['name'] = 'Hot & Popular';echo json_encode($productObj);?>)'><img src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo COMMON_IMAGE_URL.$one_img['lan_image']?>" width="190" height="240"></a>
                <?php
                	}
                ?>
                </li>
                
                <li>
                <?php 
                if(isset($widget_images[2])){
						$two_img = $widget_images[2];
                ?>
                	<a href="<?php echo $two_img['lan_url']?>"  onclick='onPromoClick(<?php $productObj['data']['id'] = 'H2';$productObj['data']['name'] = 'Hot & Popular';echo json_encode($productObj);?>)'><img src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo COMMON_IMAGE_URL.$two_img['lan_image']?>" width="190" height="240"></a>
                <?php
                	}
                ?>
                </li>
                
                <li>
                <?php 
                	if(isset($widget_images[3])){
						$three_img = $widget_images[3];
                ?>
                	<a href="<?php echo $three_img['lan_url']?>" onclick='onPromoClick(<?php $productObj['data']['id'] = 'H3';$productObj['data']['name'] = 'Hot & Popular';echo json_encode($productObj);?>)'><img src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo COMMON_IMAGE_URL.$three_img['lan_image']?>" width="190" height="240"></a>
                <?php
                	}
                ?>
                </li>
                
                <li>
                <?php 
                	if(isset($widget_images[4])){
						$four_img = $widget_images[4];
                ?>
                	<a href="<?php echo $four_img['lan_url']?>" onclick='onPromoClick(<?php $productObj['data']['id'] = 'H4';$productObj['data']['name'] = 'Hot & Popular';echo json_encode($productObj);?>)'><img src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo COMMON_IMAGE_URL.$four_img['lan_image']?>" width="190" height="240"></a>
                <?php
                	}
                ?>
                </li>
                
                <li class="exception">
                <?php 
                	if(isset($widget_images[5])){
						$five_img = $widget_images[5];
                ?>
                	<a href="<?php echo $five_img['lan_url']?>" onclick='onPromoClick(<?php $productObj['data']['id'] = 'H5';$productObj['data']['name'] = 'Hot & Popular';echo json_encode($productObj);?>)'><img src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo COMMON_IMAGE_URL.$five_img['lan_image']?>"  width="390" height="240"></a>
                <?php
                	}
                ?>
                </li>
                
                <li class="only-pic">
                <?php 
                	if(isset($widget_images[6])){
						$six_img = $widget_images[6];
                ?>
                	<a href="<?php echo $six_img['lan_url']?>"  onclick='onPromoClick(<?php $productObj['data']['id'] = 'H6';$productObj['data']['name'] = 'Hot & Popular';echo json_encode($productObj);?>)'><img src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo COMMON_IMAGE_URL.$six_img['lan_image']?>"  width="291" height="180"></a>
                <?php
                	}
                ?>
                </li>
                
                <li class="only-pic">
                <?php 
                	if(isset($widget_images[7])){
						$sev_img = $widget_images[7];
                ?>
                	<a href="<?php echo $sev_img['lan_url']?>" onclick='onPromoClick(<?php $productObj['data']['id'] = 'H7';$productObj['data']['name'] = 'Hot & Popular';echo json_encode($productObj);?>)'><img src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo COMMON_IMAGE_URL.$sev_img['lan_image']?>"  width="291" height="180"></a>
                <?php
                	}
                ?>
                </li>
                
                <li class="only-pic2">
                <?php 
                	if(isset($widget_images[8])){
						$eight_img = $widget_images[8];
                ?>
                	<a href="<?php echo $eight_img['lan_url']?>" onclick='onPromoClick(<?php $productObj['data']['id'] = 'H8';$productObj['data']['name'] = 'Hot & Popular';echo json_encode($productObj);?>)'><img src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo COMMON_IMAGE_URL.$eight_img['lan_image']?>"  width="291" height="180"></a>
                <?php
                	}
                ?>
                </li>
                
                <li class="only-pic2">
                <?php 
                	if(isset($widget_images[9])){
						$nine_img = $widget_images[9];
                ?>
                	<a href="<?php echo $nine_img['lan_url']?>" onclick='onPromoClick(<?php $productObj['data']['id'] = 'H9';$productObj['data']['name'] = 'Hot & Popular';echo json_encode($productObj);?>)'><img src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo COMMON_IMAGE_URL.$nine_img['lan_image']?>"  width="291" height="180"></a>
                <?php
                	}
                ?>
                </li>
            </ul>
        </div>
    </div>