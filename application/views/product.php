<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/product.css?v=<?php echo STATIC_FILE_VERSION ?>">
<!--breadcrumbs start-->
<div class="wrap breadcrumbs">
    <i class="icon-home">&nbsp;</i><a href="<?php echo genURL('/');?>"><?php echo lang('home');?></a>
    <?php
    	foreach ($crumbs_list as $cat_id=>$cat_info){
    ?>
    	<i class="icon-arr-right"></i><a href="<?php echo genURL($cat_info['category_url'],true)?>"><?php echo $cat_info['category_description_name']?></a>
    <?php
    	}
    ?>
</div>
<!--breadcrumbs end-->
<?php
if(isset($slim_banner) && is_array($slim_banner) && !empty($slim_banner)){
    ?>
    <div class="sale-banner category-banner" style="background:<?php echo $slim_banner['color']; ?>">
        <a href="<?php echo $slim_banner['url'];?>" title="<?php echo $slim_banner['alt'];?>"  onclick='onPromoClick(<?php $productObj['data']['id'] = 'A7';$productObj['data']['name'] = 'product_slim_banner';echo json_encode($productObj);?>)'>
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
<div id="checkoutMask" class="checkout_mask"><div class="mask"></div></div>
<!--main start-->
<div class="main product" id="product">
    <div class="product-hd">
        <!--dImgZoom start-->
        <?php include dirname(__FILE__).'/product/product_left_image.php'; ?>
        <!--dImgZoom end-->
        <!--pro-wrapper start-->
        <?php include dirname(__FILE__).'/product/product_right_attr.php'; ?>    
        <!--pro-wrapper end-->
    </div>
    <div class="product-info">
        <div class="pro-info-lt">
            <!--frequently start-->
            <div class="frequently frequently-con clearfix" id="Frequently">
                <!-- 绑定促销商品 start -->
                <?php 
                if(isset($bundle_product_list) && !empty($bundle_product_list)){
                ?>
                <div  class="frequently-name"><span><?php echo lang('frequently_bought_together');?></span></div>       
                <?php include dirname(__FILE__).'/product/product_bundle.php'; ?>
                <?php } ?>
                <!-- 绑定促销商品 end -->
                <?php 
                    //include dirname(__FILE__).'/product/product_also_like.php';
                ?>
            </div>
            <!--frequently end-->
            <div class="pro-con-top">
                
                <ul class="tit-tab" id="tit">
                    <li><a class="" href="#detail-tab1" rel="nofollow"><?php echo lang('product_description');?></a></li>
                    <li><a href="#detail-tab2" rel="nofollow"><?php echo lang('customer_review');?></a></li>
                    <li><a href="#detail-tab3" rel="nofollow"><?php echo lang('customer_q&a');?></a></li>
                    <li><a href="#detail-tab4" rel="nofollow"><?php echo lang('warranty_return_policies');?></a></li>
                </ul>
                <div class="pro-detail-info detail-tab" id="detail-tab1">
                    <?php 
                    if(isset($product_specifications) && count($product_specifications)){
                    ?>
                    <div class="mod1">
                        <h2><?php echo lang('specifications');?></h2>
                        <div class="parameter pt10">
                            <?php 
                            foreach ($product_specifications as $block_k=>$block_v){
							if(isset($block_v['attribute_block_lang_title'])){
                            ?>
                            <div>
                                <?php
                                if(isset($block_v['attr']) && count($block_v['attr'])){
                                    ?>
                                <h4><?php echo $block_v['attribute_block_lang_title']?></h4>
                                <table>
                                    <tbody>
                                        <?php 
                                        foreach ($block_v['attr'] as $block_attr_k=>$block_attr_v){
                                        if(isset($block_attr_v['attr_value']) && isset($block_attr_v['attribute_id'])){
                                        ?>  
                                         <tr>
                                            <td class="td-l"><?php
                                                echo $block_attr_v['attribute_lang_title'];
                                                if(isset($block_attr_v['attribute_unit']) && $block_attr_v['attribute_unit'] !='')
                                                    echo '('.$block_attr_v['attribute_unit'].')';
                                            ?></td>
                                            <td class="td-r">
                                            <?php 
                                            $num = count($block_attr_v['attr_value']);
                                            foreach ($block_attr_v['attr_value'] as $block_attr_value_k=>$block_attr_value_v){
                                                echo $block_attr_value_v['attribute_value_lang_title'];
                                                if($block_attr_value_k+1<$num) echo "，";
                                            }   
                                            ?>
                                            </td>
                                        </tr>                       
                                        <?php
                                        }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <?php
                                }
                                ?>
                            </div>
                            <?php
                            }}
                            ?>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                    
                    <?php if(isset($product_base_info['product_description_content']) && $product_base_info['product_description_content'] != ''){ ?>
                    <div class="mod2">
                        <h2><?php echo lang('text_description');?></h2>
                        <div class="des-list"><?php echo $product_base_info['product_description_content'];?></div>
                    </div>
                    <?php } ?>
                    
                    <?php 
                    if(isset($product_sizechart_list['Centimeters']) && count($product_sizechart_list['Centimeters'])>1){
                    ?>
                    <div class="mod3">
                        <h2>Size Chart</h2>
                        <div class="pt10 size-tab">
                            <ul class="size" id="size">
                                <li><a class="" href="javascript:;">Centimeters</a></li>
                                <li><a href="javascript:;">Inches</a></li>
                            </ul>
                            <div class="tab-box">
                                <table class="list-view">
                                    <?php
                                    $td = 'th';
                                    foreach ($product_sizechart_list['Centimeters'] as $size_key=>$size_val){
                                    ?>
                                    <tr>
                                        <?php
                                        foreach ($size_val as $con_key=>$con_val){
                                            echo '<'.$td.'>'.$con_val.'</'.$td.'>';
                                        }
                                        $td = 'td';
                                        ?>
                                    </tr>
                                    <?php   
                                    }
                                    ?>
                                </table>
                                <table class="list-view hide">
                                <?php
                                $td = 'th';
                                    foreach ($product_sizechart_list['Inches'] as $size_key=>$size_val){
                                    ?>
                                    <tr>
                                        <?php
                                        foreach ($size_val as $con_key=>$con_val){
                                            echo '<'.$td.'>'.$con_val.'</'.$td.'>';
                                        }
                                        $td = 'td';
                                        ?>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                    ?>

                    <?php if (isset($product_vice_image_list) && !empty($product_vice_image_list)){ ?>
                    <div class="mod4">
                        <h2><?php echo lang('title_detail_photos') ?></h2>
                        <ul class="pro-pic pt10">
                            <?php foreach ($product_vice_image_list as $vice_image_k=>$vice_image_v){ ?>
                            <li><img src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo COMMON_IMAGE_URL.$vice_image_v['product_gallery_path'] ?>" width="500" alt=""></li>
                            <?php } ?>
                        </ul>            
                    </div>
                    <?php } ?>

                    <?php if(isset($category_template) && $category_template != ''){ ?>
                    <div class="mod5">
                        <?php echo $category_template ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- sidebar -->
        <?php include dirname(__FILE__).'/product/product_right_sidebar.php'; ?>
        <!-- sidebar -->
    </div>  
    <div id="detail-box">
       	<!-- review start -->
       	<?php include dirname(__FILE__).'/product/product_review.php'; ?>
       	<!-- review end -->
        <!-- Customer-Q&A start -->
        <?php include dirname(__FILE__).'/product/product_qna.php'; ?>
        <!-- Customer-Q&A end -->
        <?php include dirname(__FILE__).'/product/product_policies.php';?>
        <!-- policies end -->
        <div class="index-box"></div>
    </div>
    <!--img start-->
    <?php 
    	//include dirname(__FILE__).'/product/product_otheralso_view.php'; 
    ?>
    <!--img end-->
    <!--view_history start-->
    <?php include dirname(__FILE__).'/product/product_view_history.php'; ?>
    <!--view_history end-->
</div>
<div class="box_zoom_main" id="boxZoomMain">
		<a class="box_zoom_close box_close btn" href="javascript:;">×</a>
		<div class="box_zoom_content">
			<img src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-sku="1"/>
		</div>
		<a class="box_zoom_arr_l zoom-arr" href="javascript:;"></a>
		<a class="box_zoom_arr_r zoom-arr" href="javascript:;"></a>
</div>
<div class="toolBox" id="toolBoxNew">
	<a href="<?php echo genURL('cart')?>" class="cart"><i></i></a>
    <em></em>
	<a href="<?php echo genURL('wishlist')?>" class="wash"><i></i></a>
    <em></em>
	<a href="javascript:;" id="goTopNew" class="gotop"><i></i></a>
</div>
<input type="hidden" name="reg_from" value="2" id="reg_from"/>
<!--main over-->
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<?php 
if(isset($dataLayer_detail) && $dataLayer_detail){
?>
<script>
dataLayer.push({'ecommerce': <?php echo $dataLayer_detail;?>});
</script>
<?php
}
?>
<script type="text/html" id="listToCartBox">
<div class="poptips" id="Pop">
	<p><?php echo lang('complete_selections_tips');?>.</p>
    <a href="javascript:;" class="btnorg35 cancel" title="Ok">Ok</a>
</div>
</script>
<script type="text/html" id="haveComments">
<div class="poptips" id="Pop">
	<p><?php echo lang('already_submitted_review_tips');?>.</p>
    <a href="javascript:;" class="btnorg35 cancel" title="Ok">Ok</a><a href="<?php echo genURL('review_list');?>" class="otherLink"><?php echo lang('check_your_review');?></a>
</div>
</script>
<script type="text/html" id="noBuy">
<div class="poptips" id="Pop">
	<p class="writeOk"><?php echo lang('bought_before_review_tips');?>.</p>
    <a href="javascript:;" class="btnorg35 cancel" title="Ok">Ok</a>
</div>
</script>
<?php if($ga_show){
    ?>
    <script>
        dataLayer.push({
            'ecommerce': {
                'currencyCode': 'USD',
        'impressions': <?php echo $ga_show;?>
        }
        });

    </script>
<?php
}?>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/common/utils.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>/js/login.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/product.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>
