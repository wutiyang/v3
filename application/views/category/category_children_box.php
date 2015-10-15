<?php 
if(isset($children_category['children']) && isset($children_category['children_children']) && count($children_category['children_children'])){
?>
<div id="dImgZoomBox">
		<?php 
			foreach ($children_category['children_children'] as $cht_key=>$cht_value){
			if(isset($cht_value['son_category']) && isset($cht_value['son_product'])){
		?>
		
		<div class="sub-cate">
            <div class="list">
                <?php
                if(count($cht_value['son_category']) && isset($cht_value['son_category'])){
                    ?>
                    <div class="list-con">
                        <ul>
                            <?php
                            foreach ($cht_value['son_category'] as $ch_cate_key=>$ch_cate_val){
                                if($ch_cate_val['category_pid_count'] == 0)continue;
                                ?>
                                <li><a href="<?php echo genURL($ch_cate_val['category_url'],true)?>" title="<?php echo $ch_cate_val['category_description_name']?>"><?php echo $ch_cate_val['category_description_name']?></a><span>(<?php echo $ch_cate_val['category_pid_count']?>)</span></li>
                            <?php
                            }
                            ?>
                        </ul>
                        <a class="btn-span more" title="View More"><?php echo lang('view_more');?></a>
                        <a class="btn-span less" title="View Less"><?php echo lang('view_less');?></a>
                    </div>
                <?php
                }
                ?>
            </div>
			<?php
			if(count($cht_value['son_category'])){
				$product_num = 4;
			}else{
				$product_num = 5;
			} 
			if(count($cht_value['son_product']) && isset($cht_value['son_product'])){
			?>
			<ul class="pic-list">
				<?php
					$p_num = 0; 
					foreach ($cht_value['son_product'] as $son_pro_key=>$son_pro_val){
					if($p_num<$product_num && isset($son_pro_val['product_url'])){
                        $product_name = isset($son_pro_val['product_description_name'])?$son_pro_val['product_description_name']:$son_pro_val['product_name'];
				?>
					<li>
                        <a class="p-pic" href="<?php echo genURL($son_pro_val['product_url'])?>" title="<?php echo $product_name;?>">
                            <img src="<?php echo RESOURCE_URL?>images/common/default.png" data-lazysrc="<?php echo PRODUCT_IMAGEM_URL.$son_pro_val['product_image']?>" width="208" height="208" alt="<?php echo $product_name;?>">
                            <!-- hot -->
                            <div class="label"><?php
                                if((int) $son_pro_val['product_discount_number']!=0){ ?>
                                    <p class="icon-off"><i>
                                            <?php
                                            echo (int) $son_pro_val['product_discount_number'];
                                            ?>
                                        </i></p>
                                <?php } ?>
                                <span class="span-left">
		                                    <?php
                                            if($son_pro_val['new']){
                                                ?>
                                                <i class="icon-news"></i>
                                            <?php
                                            }
                                            ?>
                                    <?php
                                    if($son_pro_val['icon']){
                                        ?>
                                        <i class="icon-hot"></i>
                                    <?php
                                    }
                                    ?>
                                                </span>
                            </div>
                            <div class="shadow"></div>
                            <div class="title">
                                <p class="p-name"><?php echo $product_name;?></p>

                                <?php
                                if(isset($son_pro_val['slogan']) && isset($son_pro_val['slogan']['slogan_time_start']) && isset($son_pro_val['slogan']['slogan_time_end'])){
                                    $slogan_start_time = strtotime($son_pro_val['slogan']['slogan_time_start']);
                                    $slogan_end_time = strtotime($son_pro_val['slogan']['slogan_time_end']);

                                    if($slogan_start_time <= time() && $slogan_end_time >= time()){
                                        $lanid = currentLanguageId();
                                        $slogan_array_lang = json_decode($son_pro_val['slogan']['slogan_content'],true);
                                        $slogan_string = $slogan_array_lang[$lanid];
                                        ?>
                                        <p class="promotion"><?php echo $slogan_string?></p>
                                    <?php
                                    }
                                }

                                ?>
                                <div class="p-price">
                                    <em class="price-sm-o"><?php echo $son_pro_val['product_currency'].number_format($son_pro_val['product_price_market'],2,'.',',');?></em>
                                    <em class="price-sm-n"><?php echo $son_pro_val['product_currency'].number_format($son_pro_val['product_discount_price'],2,'.',',');?></em>
                                </div>
                            </div>
                        </a>
                    </li>
				<?php
						$p_num++;
						}
					}
				?>
               </ul>
			<?php	
			}
			?>

        </div>
		<?php
				}
			}
		?>
    </div>
<?php	
}
?>
