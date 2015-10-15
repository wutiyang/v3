<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/keywords.css?v=<?php echo STATIC_FILE_VERSION ?>">
<div class="wrap breadcrumbs">
    <i class="icon-home">&nbsp;</i>
    <a href="<?php echo genURL('/')?>"><?php echo lang('home');?></a>
    <i class="icon-arr-right">&nbsp;</i>
    <span><?php echo $keywords;?></span>
</div>
<div class="main">
    <div class="words">
        <h3><?php echo lang('featured_categories');?>:</h3>
        <ul>
        	<?php foreach ($featuredCategories as $category):?>
            <li><a href="<?php echo $category['url'];?>"><?php echo $category['multilingual_name'];?></a></li>
            <?php endforeach;?>
        </ul>
    </div>
    <div class="words-main clearfix">
        <div class="words-left">
            <h3><?php echo $keywords;?></h3>
            <?php foreach ($goodsList as $good):?>
            <dl>
                <dt><a href="<?php echo genURL($good['product_url']);?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'ATZ','data'=>array(array('id'=>$good['product_id'],'price'=>$good['product_discount_price']))));?>);" ><img width="170" height="170" src="<?php echo PRODUCT_IMAGEM_URL.$good['product_image'];?>"></a></dt>
                <dd>
                    <h4><a href="<?php echo genURL($good['product_url']);?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'ATZ','data'=>array(array('id'=>$good['product_id'],'price'=>$good['product_discount_price']))));?>);"><?php echo isset($good['product_description_name'])?$good['product_description_name']:$good['product_name'];?></a></h4>
                    <p class="words-info">
	                    PID:<span><?php echo $good['product_id'];?></span>
	                    <!-- 
	                    <a href="<?php echo genURL($good['product_url']);?>#detail-tab2" rel="nofollow" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'ATZ','data'=>array(array('id'=>$good['product_id'],'price'=>$good['product_discount_price']))));?>);"></a>
	                     -->
	                    <?php if(isset($reviewNums[$good['product_id']]) && $reviewNums[$good['product_id']] != 0){?>
		                    <a href="<?php echo genURL('review/'.$good['product_url']);?>">
		                    (<em><?php echo $reviewNums[$good['product_id']];?></em>)Review(<span>s</span>)
		                    </a>
	                    <?php };?>                 
                    </p>
                    <?php $orig_desc = isset($good['product_description_content'])?strip_tags(str_replace(array('<br >','<br/>','<br />','<br>'),';',$good['product_description_content'])):'';?>
                    <p class="words-msg"><?php echo eb_substr($orig_desc,160);?></p>
                    <p class="price">
                        <span class="price-o"><s><?php echo $good['product_currency'].number_format($good['product_price_market'],2,'.',',');?></s></span>
                        <span class="price-n"><?php echo $good['product_currency'].number_format($good['product_discount_price'],2,'.',',');?></span>
                        <!-- 
                        <a class="icon-view add" href="">Add to Cart</a>
                         -->
                    </p>
                </dd>
            </dl>
            <?php endforeach;?>
            <div class="sarve"><?php echo $keywordsDesc;?></div>
        </div>
        <!-- left end -->
        <div class="words-right">
            <!-- 
            <div class="words-related">
                <h3>Related Key Words</h3>
                <ul class="related">
                    <li><a href="">A iphone 4 how much is an apple iphone 5s accessories</a></li>
                    <li><a href="">A iphone 4 how much is an apple iphone 5s accessories</a></li>
                    <li><a href="">A iphone 4 how much is an apple iphone 5s accessories</a></li>
                    <li><a href="">A iphone 4 how much is an apple iphone 5s accessories</a></li>
                </ul>
            </div> 
            -->
            <div class="words-related">
                <h3><?php echo lang('related_products');?></h3>
                <div class="words-slider" id="recentlyView_mod_list">
                    <div class="slider-list" id="sliderList">
                        <ul>
                        	<?php foreach ($list as $product):?>
                            <li>
                            	<a href="<?php echo genURL($product['product_url']);?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'ATZ','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);">
                                <img width="170" height="170" src="<?php echo PRODUCT_IMAGEM_URL.$product['product_image'];?>">
                            	
                                <p class="related-info"><?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?></p>
                                </a>
                                <p class="price-n"><?php echo $product['product_currency'].number_format($product['product_discount_price'],2,'.',',');?></p>
                            </li>
                            <?php endforeach;?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php foreach ($imageAdsList as $ad) {?>
                <a class="related-img" href="<?php echo $ad['url'];?>" ><img src="<?php echo COMMON_IMAGE_URL.$ad['img']; ?>" alt="<?php echo $ad['alt'];?>"></a>
            <?php } ?>
        </div>
        <!-- right end -->
    </div>
</div>
<!--main end-->
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script src="<?php echo RESOURCE_URL ?>js/keywords.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>