<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL?>css/category.css?v=<?php echo STATIC_FILE_VERSION ?>">
<div class="search main">
    <div class="search-top">
        <h2>“<span><?php echo $keywords?></span>” <?php echo sprintf(lang('found_items_count_tips'),$goods_count);?></h2>
        <div class="search-res" id="searchRes">
        	<?php if($goods_count){?>
        	<!--状态1 start-->
            <div class="result">
                <h3><?php echo lang('show_result_for');?></h3>
                <ul class="clearfix" id="showRes">
                	<?php 
                		if(isset($search_category_list) && !empty($search_category_list)){
                			foreach ($search_category_list as $cat_id=>$cat_info){
                	?>
                			<li><a href="<?php echo $cat_info['url']?>"><?php echo $cat_info['category_name']?><span>(<?php echo $cat_info['count']?>)</span></a></li>
                	<?php			
                			}
                		}
                	?>
                    <!-- li><a href="javascript:void(0)">Tripods & Supports<span>(2120)</span></a></li>
                    <li><a href="javascript:void(0)">Lighting & Studio<span>(210)</span></a></li-->
                </ul>
                <div class="view-all">
                    <a class="btn-span more" href="javascript:void(0)"><?php echo lang('view_more');?></a>
                    <a class="btn-span less" href="javascript:void(0)"><?php echo lang('view_less');?></a>
                </div>
            </div>  
        	<!--状态1 end--> 
        	<?php }else{?>
        	<!--状态2 start-->
            <div class="result">
                    <h3><?php echo lang('search_tips');?></h3>  
                    <ol class="res-ol clearfix">
                        <li><a href="javascript:void(0)"><?php echo lang('search_tips1');?></a></li>
                        <li><a href="javascript:void(0)"><?php echo lang('search_tips2');?></a></li>
                        <li><a href="javascript:void(0)"><?php echo lang('search_tips3');?></a></li>
                        <li><a href="javascript:void(0)"><?php echo lang('search_tips4');?></a></li>
                    </ol>
                </div>
            <!--状态2 end-->
        	<?php } ?>
        	
        </div>
    </div>
    <?php if(is_array($relate_search) && !empty($relate_search)){ ?>
    <div class="search-related">
        <b><?php echo lang('related_search')?>：</b>
        <?php foreach($relate_search as &$item){
        $item = '<a href="'.genURL('/search/?keywords='.urlencode($item)).'">'.$item.'</a>';
         }
        $relate_search_str = implode('<span>|</span>',$relate_search);echo $relate_search_str;
        ?>
    </div>
    <?php }?>
	<!--列表start-->
    <div class="primary-category">
    	<?php if($goods_count){ ?>
        <div class="primary-top clear">
        	<div class="primary-introduction">
                <ul>
                    <li class="intr-list <?php if($sort=='sale_count') echo 'on'?>">
                    	<?php 
                        	$basicParam['sort'] = 'sale_count';
                        ?>
                    	<a href="<?php echo genURL(($pagination['current_page'] != 1)?"search/".$pagination['current_page'].".html":"search/",false,$basicParam)?>"><?php echo lang('most_popular');?></a>
                    </li>
                    <li class="intr-list <?php if($sort=='add_time') echo 'on'?>">
                    	<?php 
                        	$basicParam['sort'] = 'add_time';
                        ?>
                    	<a href="<?php echo genURL(($pagination['current_page'] != 1)?"search/".$pagination['current_page'].".html":"search/",false,$basicParam)?>"><?php echo lang('new_arrivals');?></a>
                    </li><li class="intr-list <?php if($sort=='price' || $sort=='price_desc') echo 'prices'?>">
                        <?php
                        $basicParam['sort'] = $sort=='price'?'price_desc':'price';
                        $icon_class = '';
                        $cion_str = '';
                        if($sort == 'price')
                            $icon_class = 'icon-up';
                        else if($sort == 'price_desc')
                            $icon_class = 'icon-delow';
                        else
                            $icon_str = '<i class="icon-top"></i><i class="icon-down"></i>';
                        ?>
                        <a href="<?php echo genURL(($pagination['current_page'] != 1)?"search/".$pagination['current_page'].".html":"search/",false,$basicParam)?>"><?php echo lang('price');?><span class="<?php echo $icon_class;?>"><?php echo $cion_str;?></span></a>
                    </li>
                    <li>
                        <div class="intr-last">
                            <?php
                            $param_isempty = true;
                            unset($basicParam['sort']);
                            if(isset($sort) && $sort!="sale_count") {
                                $param_isempty = false;
                            }
                            if(!empty($basicParam)){
                                $param_isempty = false;
                            }
                            if($sort!="sale_count")$basicParam['sort'] = $sort;
                            $current_url = genURL("search/",false,$basicParam);
                            ?>
                            <form  method="post" name="<?php echo $current_url;?>">
                                <span class="price"><?php echo $new_currency;?><input class="price-text front" value="<?php $search_price_range = isset($search_price_range)?$search_price_range:',';$search_price_range = explode(',',$search_price_range);if(count($search_price_range) == 1 && $search_price_range[0] != 0)echo 0;else echo $search_price_range[0];?>" type="text" name="price-front"></span>
                                <strong>to</strong>
                                <span class="price info"><?php echo $new_currency;?><input class="price-text back" value="<?php if(count($search_price_range) == 1)echo $search_price_range[0];else echo $search_price_range[1];?>" type="text" name="price-back"></span>
                                <input id="current_url_tim" name="current_url" value="<?php echo $current_url?>" type="hidden">
                                <input id="param_type" type="hidden" value="<?php if($param_isempty) echo 1;else echo 0;?>" >
                                <button class="go" href="" onclick="javascript:gopriceSearch($('#start_price_sort_tim').val(),$('#end_price_sort_tim').val());"><?php echo lang('go');?></button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
            
            <?php 
            if(isset($pagination) && $pagination['total_page'] && $goods_count){
            ?>
            <div class="page-top">
                <?php 
                if($pagination['current_page']!=1){
				?>
				<a class="p-prev" href="<?php echo sprintf($pagination['href'],($pagination['current_page']-1)); ?>" title="Previous"><i class="icon-arraw-left"></i></a>				
				<?php
                }else{
                ?>
                <a class="p-prev-un" href="javascript:void(0)" title="Previous"><i class="icon-arraw-left"></i></a>
                <?php	
                }
                ?>
                <a class="p-page" href=""><span class="current"><?php echo $pagination['current_page']?></span>/<span><?php echo $pagination['total_page']?></span></a>
                <?php 
                if($pagination['current_page']!=$pagination['total_page']){
                ?>
                <a class="p-next" href="<?php echo sprintf($pagination['href'],($pagination['current_page']+1));?>" title="Next"><i class="icon-arraw-right"></i></a>
                <?php	
                }else{
                ?>
                <a class="p-next-un" href="javascript:void(0)" title="Next"><i class="icon-arraw-right"></i></a>
                <?php	
                }
                ?>
                
            </div>
            <?php	
            }
            ?>
        </div>
        <?php } ?>
        <!-- 显示搜索结果 start -->
        <?php 
        if(isset($goodsList) && !empty($goodsList)){
		?>
		<div class="show-res">
            <div class="primary-list">
                <ul class="clearfix">
                	<?php 
                	foreach ($goodsList as $pid=>$product_info){
                	?>
                	<li>
                        <div class="pro-list-block">
                            <div class="p-pic">
                                <a href="<?php echo genURL($product_info['product_url'])?>" title="<?php echo $product_info['product_description_name']?>" onclick='productEvent(<?php $productObj = array();$productObj['data'][] = array('price' => $product_info['product_discount_price'],'id' => $product_info['product_id']);$productObj['list'] = 'Search Result'; echo json_encode($productObj);?>)'>
                                    <img src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo PRODUCT_IMAGEM_URL.$product_info['product_image']?>" alt="<?php echo $product_info['product_description_name']?>" width="189" height="189">
                                    <?php if(isset($product_info['product_discount_number']) && $product_info['product_discount_number']){
                                    ?>
                                    <p class="icon-off"><i><?php echo (int) $product_info['product_discount_number'];?></i></p>
                                    <?php	
                                    }?>
                                    <span class="span-left">
                                    <?php 
                                    if(isset($product_info['new']) && $product_info['new']){
                                    ?>
                                    <i class="icon-news"></i>
                                    <?php		
                                    }
                                    ?>
                                    <?php 
									if(isset($product_info['icon']) && $product_info['icon']){
                                    ?>
                                    <i class="icon-hot"></i>
                                    <?php	
                                    }
                                    ?>
                                    </span>
                                    <?php 
                                    if($product_info['product_type']==2){
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
                            <div class="p-name"><a href="<?php echo genURL($product_info['product_url'])?>" title="<?php echo $product_info['product_description_name']?>"><?php echo $product_info['product_description_name']?></a></div>
                            <div class="p-price">
                                <span class="p-price-o"><?php echo $product_info['product_currency'].$product_info['product_price_market']?></span><span class="p-price-n"><?php echo $product_info['product_currency'].$product_info['product_discount_price']?></span>
                            </div>
                            <div class="p-fr"><i></i><em><?php echo lang('free_shipping');?></em></div>
                        </div>
                    </li>
                	<?php
                	}
                	?>
                </ul>
            </div>
        </div>
		<?php
        }
        ?>
        <!-- 显示搜索结果 end -->
        <!-- 分页 s -->
        <?php 
        if(isset($pagination) && $pagination['total_page'] && $goods_count){
		?>
		<div class="show-pager">
        <?php	include dirname(__FILE__).'/common/pagination.php';?>	
        </div>
        <?php }
        ?>
        <!-- 分页 end -->
        <?php 
        if(isset($recommend_list) && !empty($recommend_list)){
        ?>
        <div class="primary-list">
        	<h2 class="show-tit"><?php echo lang('we_recommend');?></h2>
            <ul class="clearfix">
            <?php 
            foreach ($recommend_list as $key=>$recommend_info){
            ?>
            	<li>
                    <div class="pro-list-block">
                        <div class="p-pic">
                            <a href="<?php echo genURL($recommend_info['product_url'])?>" title="<?php echo $recommend_info['product_description_name']?>">
                                <img src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo PRODUCT_IMAGEM_URL.$recommend_info['product_image']?>" alt="<?php echo $recommend_info['product_description_name']?>" width="189" height="189">
                                <?php 
                                if(isset($recommend_info['product_discount_number']) && $recommend_info['product_discount_number']){
                                ?>
                                <p class="icon-off"><i><?php echo (int) $recommend_info['product_discount_number']?></i></p>
                                <?php	
                                }?>
                                <span class="span-left">
                                <?php 
                                if(isset($recommend_info['new']) && $recommend_info['new']){
                                ?>
                                <i class="icon-news"></i>
                                <?php
                                }
                                ?>
                                <?php 
                                if(isset($recommend_info['product_type']) &&$recommend_info['product_type']==2){
                                ?>
                                <i class="icon-hot"></i>
                                <?php	
                                }
                                ?>
                                </span>
                                <?php 
                                if(isset($recommend_info['icon']) && $recommend_info['icon']){
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
                        <div class="p-name"><a href="<?php echo genURL($recommend_info['product_url']);?>" title="<?php echo $recommend_info['product_description_name']?>"><?php echo $recommend_info['product_description_name']?></a></div>
                        <div class="p-price">
                            <span class="p-price-o"><?php echo $recommend_info['product_currency'].$recommend_info['product_price_market']?></span><span class="p-price-n"><?php echo $recommend_info['product_currency'].$recommend_info['product_discount_price']?></span>
                        </div>
                        <div class="p-fr"><i></i><em><?php echo lang('free_shipping');?></em></div>
                    </div>
                </li>
            <?php	
            }
            ?>
                
            </ul>
            
        </div>
        <?php	
        }
        ?>
        
    </div>
    <!--列表end-->
</div>
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/javascript" src="<?php echo RESOURCE_URL?>js/category.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL?>js/categorywithsearch.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>
