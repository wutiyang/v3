<?php 
if(isset($view_history_list) && !empty($view_history_list )){
?>
<div class="module-con big-w" id="moduleCon3">
        <div  class="frequently-name"><span><?php echo lang('your_recently_viewed_items');?></span></div>
        <div class="con" size="6">
            <ul class="con-list">
            	<?php 
            	foreach ($view_history_list as $view_history_k=>$view_history_v){
                    if(!isset($view_history_v['product_description_name']))continue;
            	?>
            	<li>
                    <div class="pro-list-block">
                        <div class="p-pic">
                            <a title="<?php echo $view_history_v['product_description_name']?>" href="<?php echo genURL($view_history_v['product_url'])?>" onclick='productEvent(<?php $productObj = array();$productObj['data'][] = array('price' => $view_history_v['product_discount_price'],'id' => $view_history_v['product_id']);$productObj['list'] = 'Product Detail Page'; echo json_encode($productObj);?>)'><img alt="" src="<?php echo PRODUCT_IMAGEM_URL.$view_history_v['product_image']?>" width="160" height="160"></a>
                        </div>
                        <div class="p-name">
                            <a title="<?php echo $view_history_v['product_description_name']?>" href="<?php echo genURL($view_history_v['product_url'])?>" onclick='productEvent(<?php $productObj = array();$productObj['data'][] = array('price' => $view_history_v['product_discount_price'],'id' => $view_history_v['product_id']);$productObj['list'] = 'Product Detail Page'; echo json_encode($productObj);?>)'><?php echo $view_history_v['product_description_name']?></a>
                        </div>
                        <div class="p-price">
                           <s><?php echo $view_history_v['product_currency']?><?php echo number_format($view_history_v['product_price_market'],2,'.',',')?></s> <?php echo $view_history_v['product_currency']?><?php echo number_format($view_history_v['product_discount_price'],2,'.',',')?> 
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
