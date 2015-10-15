<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL?>css/category.css?v=<?php echo STATIC_FILE_VERSION ?>">
<div class="wrap breadcrumbs">
    <i class="icon-home">&nbsp;</i><a href="<?php echo genURL('/');?>"><?php echo lang('home');?></a><i class="icon-arr-right"></i>
    <?php 
    	$count = count($crumbs_list);
    	if($count){
			$num = 1;
    		foreach ($crumbs_list as $k=>$v){
    		?>
    			<a href="<?php echo genURL($v['category_url'],true)?>"><?php echo $v['category_description_name']?></a>
    			<?php if($num<$count){?>
    				<i class="icon-arr-right"></i>
    			<?php } ?>
    		<?php
    		$num++;
    		}
    	}
    ?>
</div>
<?php
$param_isempty = true;
if(isset($all_param)) {
    $all_param['sort'] = $sort;
    if(!empty($selected_attr_id))
        $all_param['attr'] = implode(',',$selected_attr_id);
	$param_isempty = false;
} 
if(!empty($all_param)){
	$param_isempty = false;
}
$url = $category_info['category_url'];
if(!empty($selected_attr_name) || isset($all_param['price_range']))
    $url = 'ns'.$category_info['category_url'];
if(!empty($selected_attr_name)){
    $url_path = implode('/',$selected_attr_name).'/';
    $url_path = trim($url_path);
    $url_path = preg_replace('/[^a-zA-Z0-9_\/?=,&-]/','-',$url_path);
    $url_path = preg_replace('/-(?=-)/', '', $url_path);
    $url_path = strtolower($url_path);
    $url .= $url_path;
}
$no_range = $all_param;
if(isset($no_range['search_price_range']))
    unset($no_range['search_price_range']);
$current_url = genURL($url,true,$no_range);
?>
<?php 
if($category_level < 3 && isset($children_category['children'])){
?>
<div class="wrap current-category"><?php echo $category_info['category_description_name']?></div>
<?php
}
?>
<?php
if(isset($slim_banner) && is_array($slim_banner) && !empty($slim_banner)){
?>
<div class="sale-banner category-banner" style="background:<?php echo $slim_banner['color']; ?>">
    <a href="<?php echo $slim_banner['url'];?>" title="<?php echo $slim_banner['alt'];?>"  onclick='onPromoClick(<?php $productObj['data']['id'] = 'A7';$productObj['data']['name'] = 'category_slim_banner';echo json_encode($productObj);?>)'>
        <?php if($slim_banner['img'] <> ''){ ?>
            <img src="<?php echo COMMON_IMAGE_URL.$slim_banner['img'];?>" width="100%" height="60">
        <?php } else { ?>
            <span class="banner-tit"><?php echo $slim_banner['alt'];?></span>
        <?php } ?>
    </a>
    <div class="timer">
        <div class="bglt">
            <div class="bg-center">
                <span class="endIn"><?php echo lang('end_in_days_first');?><em><strong> <?php echo floor($slim_banner['end_time'] / (24*3600));?> </strong> <?php echo lang('end_in_days_last');?></em></span>
                <div class="cont hide_day" id="sale_banner_counter" style="visibility: visible;">
                    <span data-endtime="<?php echo $slim_banner['end_time']-floor($slim_banner['end_time'] / (24*3600))*(24*3600);?>"><span class="day_text"></span></span>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function() {
            ec.load('ec.ui.countdown', {
                onload : function () {
                    ec.ui.countdown('#sale_banner_counter span', {
                        "html" : "<span class='day_text'><em class='day'>{#day}</em>&nbsp;{#dayText}</span> <em>{#hours}</em><i>:</i><em>{#minutes}</em><i>:</i><em>{#seconds}</em>",
                        "zeroDayHide" : true,
                        "callback" : function (json) {
                            //计时结束时要执行的方法,比如置灰
                            //$(this).parent().addClass('timeend');
                        }
                    });
                }
            });
        });
    </script>
</div>
<?php
}
?>
<div class="main" id="category">
	<?php 
		if($category_level < 3){
	?>
	<!--dImgZoom start-->
	<?php include dirname(__FILE__).'/category/category_children.php'; ?>
    <!--dImgZoom end-->
    <!--dImgZoomBox start-->
    <?php include dirname(__FILE__).'/category/category_children_box.php'; ?>
    <!--dImgZoomBox end-->
	<?php
		}
	?>
	<?php 
	if($category_info['category_type_display']==CATEGORY_TYPE_TRAN_DISPLAY){//横向排列
	?>
	<!--search-condition start-->
    <div class="current-cate"><?php echo $category_info['category_description_name']?></div>
    <!-- 横版 搜索条件 start -->
    <?php include dirname(__FILE__).'/category/category_search_tarn_condition.php'; ?>
    <!-- 横版 搜索条件 end -->
    <!--search-condition end-->
	<?php } ?>
     
    <!--列表 start-->
    <!--竖版请加class:vertical-->
    <div class="clearfix <?php if($category_info['category_type_display']==CATEGORY_TYPE_VER_DISPLAY) echo 'vertical';?>">
        <!-- 竖版 搜索条件 start -->
	    <?php include dirname(__FILE__).'/category/category_search_ven_condition.php'; ?>
	    <!-- 竖版 搜索条件 end -->
	    
        <div class="primary-category">
        	<!--一级类目竖版显示 start-->
            <!-- h2 class="primary-tit">Wii Accessories</h2-->
            <!--一级类目竖版显示 end-->
            
        	<!--一级类目显示-->
            <h2 class="primary-tit"><?php echo lang('all_products');?><span>(<?php echo $Total_num;?>)</span></h2>
            <!--一级类目显示 end-->
            
            <!-- “排序” 操作选项 start -->
            <?php include dirname(__FILE__).'/category/category_search_sort_condition.php'; ?>
            <!-- “排序” 操作选项 end -->
            
            <div class="primary-list">
                <ul class="clearfix">
                	<?php 
                		if(isset($return_product) && count($return_product)){
                			foreach ($return_product as $return_key=>$return_val){
                	?>
	                		<li>
		                        <div class="pro-list-block">
		                            <div class="p-pic">
		                                <a href="<?php echo genURL($return_val['product_url'])?>" title="<?php echo $return_val['product_name']?>" onclick='productEvent(<?php $productObj = array();$productObj['data'][] = array('price' => $return_val['product_discount_price'],'id' => $return_val['product_id']);$productObj['list'] = 'Category Listing Page'; echo json_encode($productObj);?>)'>
		                                    <img src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo PRODUCT_IMAGEM_URL.$return_val['product_image']?>" width="189" height="189" alt="">
		                                    <?php 
		                                    if((int) $return_val['product_discount_number']!=0){ ?>
											<p class="icon-off"><i>
		                                    <?php 
		                                    	echo (int) $return_val['product_discount_number'];
		                                    ?>
		                                    </i></p>
											<?php } ?>
		                                    <span class="span-left">
		                                    <?php 
		                                    	//$active_time = $return_val['product_time_initial_active'];
		                                    	//$is_new = diffBetweenTwoDays($active_time,PRODUCT_NEW_DAYS);
		                                    	if($return_val['new']){
		                                    ?>
												<i class="icon-news"></i>
		                                    <?php
		                                    	}
		                                    ?>
		                                    <?php 
		                                    	if($return_val['icon']){
		                                    ?>
		                                    	<i class="icon-hot"></i>
		                                    <?php		
		                                    	}
		                                    ?>
                                                </span>
		                                    <?php 
												if($return_val['product_type']==PRODUCT_HOT_TYPE_VALUE){
		                                    ?>
		                                    <span class="icon-alt">
		                                        <i class="icon-confirm"></i>
		                                        <div class="confirm-first">
		                                            <em class="confirm-top"></em>
		                                            <p><?php echo lang('multiple_color_options');?></p>
		                                        </div>
		                                    </span>
		                                    <?php		
		                                    	}
		                                    ?>
		                                </a>
		                            </div>
		                            <div class="p-name">
		                                <a href="<?php echo genURL($return_val['product_url'])?>" title="<?php if(isset($return_val['product_description_name']))echo $return_val['product_description_name'];?>">
		                                	<?php 
		                                		//echo mb_substr($return_val['product_name'],0,100,"utf8");
		                                		if(isset($return_val['product_description_name']))echo $return_val['product_description_name'];
		                                	?>
		                                	</a>
                                    </div>
                                    <?php
                                    if(isset($return_val['slogan']) && isset($return_val['slogan']['slogan_time_start']) && isset($return_val['slogan']['slogan_time_end'])){
                                        $slogan_start_time = strtotime($return_val['slogan']['slogan_time_start']);
                                        $slogan_end_time = strtotime($return_val['slogan']['slogan_time_end']);

                                        if($slogan_start_time <= time() && $slogan_end_time >= time()){
                                            $lanid = currentLanguageId();
                                            $slogan_array_lang = json_decode($return_val['slogan']['slogan_content'],true);
                                            $slogan_string = $slogan_array_lang[$lanid];
                                            ?>
                                            <div class="promotion"><?php echo $slogan_string?></div>
                                        <?php
                                        }
                                    }

                                    ?>
		                            <div class="p-price">
		                                <span class="p-price-o">
                                            <?php echo $return_val['product_currency'].number_format($return_val['product_price_market'],2,'.',',');?>
                                        </span>
                                        <span class="p-price-n">
                                            <?php echo $return_val['product_currency'].number_format($return_val['product_discount_price'],2,'.',',');?>
                                        </span>
		                            </div>
		                            <div class="p-fr"><i></i><em><?php echo lang('free_shipping');?></em></div>
		                        </div>
		                    </li>
                	<?php	
                			}
                		}
                	?>
                </ul>
                <!-- 分页 s -->
                <div class="show-pager">
                	<?php include dirname(__FILE__).'/common/pagination.php'; ?>
                </div>
                <!-- 分页 end -->
            </div>
            
        </div>
        
    </div>
    <div class="categories">
    			<?php if(count($category_related_lists)) { ?>
                <div class="categories-lt">
                    <h2><?php echo lang('related_categories');?></h2>
                    <ul>
                    <?php 
                    		foreach ($category_related_lists as $krelated=>$vrelated){
                                if(isset($vrelated['category_url']) && isset($vrelated['category_pid_count']) && isset($vrelated['category_description_name'])) {
                                    ?>
                                    <li class="w212"><a href="<?php echo genURL($vrelated['category_url'], true); ?>">
                                            <?php echo $vrelated['category_description_name']; ?>
<!--                                            <span>(--><?php //echo $vrelated['category_pid_count']; ?><!--)</span>-->
                                        </a></li>
                                <?php
                                }
                    	}
                    ?>
                    </ul>
                </div>
                <?php } ?>
                <?php 
                	if($category_info['category_description_footer']){
                ?>
                <div class="categories-rt">
                	<h2><?php echo $category_info['category_description_name']?></h2>
                    <p><?php echo $category_info['category_description_footer']?></p>
                    <!-- p>If you're looking for a new computer, consider the speed and processing power you need and the best screen size for your use. Single and dual core processors are good for basic functions like email and web browsing, but you'll want a quad-core computer if you do a lot of gaming or video streaming</p-->
                    
                </div>
                <?php		
                	}
                ?>
            </div>
 	<!--列表 over-->
</div>
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/javascript" src="<?php echo RESOURCE_URL?>js/category.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>
