<?php 
if(isset($view_history_list) && !empty($view_history_list )){
?>
<div class="module-con big-w" id="moduleCon3">
        <div  class="frequently-name"><span>Your Recently Viewed Items</span></div>
        <div class="con" size="6">
            <ul >
            	<?php 
            	foreach ($view_history_list as $view_history_k=>$view_history_v){
            	?>
            	<li>
                    <div class="pro-list-block">
                        <div class="p-pic">
                            <a title="<?php echo $view_history_v['product_description_name']?>" href="<?php echo genURL($view_history_v['product_url'])?>"><img alt="" src="<?php echo PRODUCT_IMAGE_URL."160x160/".$view_history_v['product_image']?>" width="160" height="160"></a>
                        </div>
                        <div class="p-name">
                            <a title="<?php echo $view_history_v['product_description_name']?>" href="<?php echo genURL($view_history_v['product_url'])?>"><?php echo $view_history_v['product_description_name']?></a>
                        </div>
                        <div class="p-price">
                           <s><?php echo $view_history_v['product_currency']?><?php echo $view_history_v['product_price_market']?></s> <?php echo $view_history_v['product_currency']?><?php echo $view_history_v['product_discount_price']?> 
                        </div>
                    </div>
                </li>
            	<?php	
            	}
            	?>
            </ul>
            <a class="arrow_left btn_arrow arrow_disabled" href="javascript:;"><span class="icon_arraw_left"></span> </a>
            <a class="arrow_right btn_arrow" href="javascript:;"><span class="icon_arraw_right"></span> </a>
        </div>
    </div>
<?php	
}
?>