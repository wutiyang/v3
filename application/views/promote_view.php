<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL?>css/category.css?v=<?php echo STATIC_FILE_VERSION ?>">
<div class="wrap breadcrumbs">
    <i class="icon-home">&nbsp;</i><a href="<?php echo genURL('/')?>"><?php echo lang('home');?></a></i>
    <i class="icon-arr-right"></i><a href="<?php echo $promote['promotion_url'];?>"><?php echo $promote['title'];?></a>
    <i class="icon-arr-right"></i><?php echo $categoryName;?>
</div>
<?php
$param_isempty = true;
if(isset($all_param)) {
    $all_param['sort'] = $sort;
	$param_isempty = false;
} 
if(!empty($all_param)){
	$param_isempty = false;
}
$no_range = $all_param;
if(isset($no_range['search_price_range']))
    unset($no_range['search_price_range']);
$current_url = genURL($curUri,false,$no_range);
?>
<!-- <div class="wrap current-category">Consumer Electronics</div> -->
<div class="main" id="category">
	<!--dImgZoom start-->
    <!--dImgZoom end-->
    <!--dImgZoomBox start-->
    <!--dImgZoomBox end-->
    <!--search-condition start-->
    <!--search-condition end--> 
    <!--列表 start-->
    <!--竖版请加class:vertical-->
    <div class="clearfix">
        <!-- sidebar end -->
        <!-- sidebar end -->
        <div class="primary-category">
        	<!--一级类目竖版显示 start-->
            <!-- <h2 class="primary-tit">Wii Accessories</h2> -->
            <!--一级类目竖版显示 end-->
        	<!--一级类目显示-->
            <h2 class="primary-tit"><?php echo lang('all_products');?><span>(<?php echo $nums;?>)</span></h2>
            <div class="primary-top clear">
                <div class="primary-introduction">
                    <ul>
                        <li class="intr-list <?php if($sort==1) echo 'on'?>">
                        	<?php
                            $all_param['sort'] = 1;
                        	?>
                        	<a href="<?php echo genURL($curUri,false,$all_param)?>" rel="nofollow"><?php echo lang('most_popular');?></a>
                        </li>
                        <li class="intr-list <?php if($sort==2) echo 'on'?>">
                        	<?php
                            $all_param['sort'] = 2;
                        	?>
                        	<a href="<?php echo genURL($curUri,false,$all_param)?>" rel="nofollow"><?php echo lang('new_arrivals');?></a>
                        </li>
                        <!--
					                        上下箭头请在span标签上加class
					                        	箭头向上：icon-up
					                            箭头向下：icon-delow
                        -->
                        <li class="intr-list <?php if($sort==3 || $sort==4) echo 'prices'?>">
                        	<?php
                            $all_param['sort'] = $sort==3?4:3;
                            $icon_class = '';
                            $cion_str = '';
                                if($sort == 3)
                                    $icon_class = 'icon-up';
                                else if($sort == 4)
                                    $icon_class = 'icon-delow';
                                else
                                    $icon_str = '<i class="icon-top"></i><i class="icon-down"></i>';
                        	?>
                        	<a href="<?php echo genURL($curUri,false,$all_param)?>" rel="nofollow"><?php echo lang('price');?><span class="<?php echo $icon_class;?>"><?php echo $cion_str;?></span></a>
                        </li>
                        <li>
                            <input id="current_url_tim" name="current_url" value="<?php echo $current_url;?>" type="hidden">
                            <input id="param_type" type="hidden" value="<?php if(isset($param_isempty) && $param_isempty) echo 1;else echo 0;?>" >
                            <div class="intr-last">
                                <form  method="post" name="priceForm">
                                    <span class="price info"><?php echo $new_currency;?><input class="price-text front" value="<?php $search_price_range = isset($basicParam['search_price_range'])?$basicParam['search_price_range']:',';$search_price_range = explode(',',$search_price_range);if(count($search_price_range) == 1)echo 0;else echo $search_price_range[0];?>" type="text" name="price-front"></span>
                                    <strong>to</strong>
                                    <span class="price info"><?php echo $new_currency;?><input class="price-text back" value="<?php if(count($search_price_range) == 1)echo $search_price_range[0];else echo $search_price_range[1];?>" type="text" name="price-back"></span>
                                    <input class="go" type="submit" value="Go">
                                </form>
                            </div>
                        </li>
                    </ul>
                    
                </div>
                <?php if(isset($pagination)){ ?>
                <div class="page-top">
                    <?php if($pagination['current_page'] == 1){ ?>
						<a class="p-prev-un" href="javascript:void(0)"><i class="icon-arraw-left"></i></a>
					<?php }elseif($pagination['current_page'] == 2){ ?>
						<a class="p-prev" href="<?php echo $pagination['default_href']; ?>"><i class="icon-arraw-left"></i></a>
					<?php }else{ ?>
						<a class="p-prev" href="<?php echo sprintf($pagination['href'],($pagination['current_page']-1)); ?>"><i class="icon-arraw-left"></i></a>
					<?php } ?>
                    <a class="p-page" href=""><span class="current"><?php echo $pagination['current_page']?></span>/<span><?php echo $pagination['total_page']?></span></a>
                    <?php if($pagination['current_page'] == $pagination['total_page']){?>
                    	<a class="p-next" href="javascript:void(0)" title="Next"><i class="icon-arraw-right"></i></a>
                    <?php }else{ ?>
                    	<a class="p-next" href="<?php echo sprintf($pagination['href'],($pagination['current_page']+1))?>" title="Next"><i class="icon-arraw-right"></i></a>
                    <?php }?>
                </div>
                <?php } ?>
            </div>
            <!--一级类目显示 end-->
            <div class="primary-list">
                <ul class="clearfix">
                	<?php foreach ($productList as $product):?>
                    <li>
                        <div class="pro-list-block">
                            <div class="p-pic">
                                <a href="<?php echo genURL($product['product_url']);?>" title="<?php echo $product['product_name'];?>">
                                    <img src="<?php echo RESOURCE_URL?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo PRODUCT_IMAGEM_URL.$product['product_image'];?>" width="189" height="189" alt="">
                                    <?php if( $product['product_discount_number'] != 0):?>
                                    <p class="icon-off"><i><?php echo intval($product['product_discount_number']);?></i></p>
                                    <?php endif;?>
                                    
                                     <span class="span-left">
                                     	<?php if(!empty($product['new'])):?>
                                     	<i class="icon-news"></i>
                                     	<?php endif;?>
                                     	<?php if(!empty($product['icon'])):?>
                                     	<i class="icon-hot"></i>
                                     	<?php endif;?>
                                     </span>
                                     
                                    <span class="icon-alt">
                                        <i class="icon-confirm"></i>
                                        <div class="confirm-first">
                                            <em class="confirm-top"></em>
                                            <p><?php echo lang('multiple_color_options');?></p>
                                        </div>
                                    </span>
                                </a>
                            </div>
                            <div class="p-name">
                                <a href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>"><?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?></a>
                                <!--
                                <p class="promotion"><?php echo lang('promotion_tips');?></p>
                                -->
                            </div>
                            <div class="p-price">
                                <span class="p-price-o"><?php echo $product['product_currency'].number_format($product['product_price_market'],2,'.',',');?></span><span class="p-price-n"><?php echo $product['product_currency'].number_format($product['product_discount_price'],2,'.',',');?></span>
                            </div>
                            <div class="p-fr"><i></i><em><?php echo lang('free_shipping');?></em></div>
                        </div>
                    </li>
                    <?php endforeach;?>
                </ul>
                <!-- 分页 s -->
                <div class="show-pager">
                	<?php include dirname(__FILE__).'/common/pagination.php'; ?>
                </div>
                <!-- 分页 end -->
            </div>
            
        </div>
        
    </div>
 	<!--列表 over-->
</div>
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/javascript" src="<?php echo RESOURCE_URL?>js/common/utils.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL?>js/category.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>
