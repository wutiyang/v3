<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/found.css?v=<?php echo STATIC_FILE_VERSION ?>">
<!--main start-->
<div class="main found" id="found">

    <!-- banner start -->
    <div class="banner">
        <a href=""><img src="<?php echo RESOURCE_URL ?>images/found/banner.png?v=<?php echo STATIC_FILE_VERSION ?>"></a>
    <span class="banner-con">
    	<p><?php echo lang('request_not_found_tips');?></p>
        <a href="<?php echo genURL('/')?>"><?php echo lang('go_home_page_tips');?></a>
    </span>

    </div>
    <!-- banner end -->

    <!--img start-->
    <div class="module-con big-w" id="moduleCon3">
        <div  class="frequently-name"><span><?php echo lang('recently_viewed_items');?></span></div>
        <div class="con" size="6">
            <ul >
                <?php
                if(isset($product_list)) {
                    foreach ($product_list as $product) {
                        $product_name = isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];
                        $product_content = isset($product['product_description_content'])?$product['product_description_content']:'';
                        $currency = $product['product_currency'];

                        ?>
                        <li>
                            <div class="pro-list-block">
                                <div class="p-pic">
                                    <a title="<?php echo $product_name;?>" href="<?php echo genURL($product['product_url']);?>">
                                        <img width="350" height="350" alt="<?php echo $product_name;?>" src="<?php echo PRODUCT_IMAGEM_URL.$product['product_image'];?>"></a>
                                </div>
                                <div class="p-name">
                                    <a title="<?php echo $product_content;?>" href="<?php echo genURL($product['product_url']);?>">
                                        <?php echo $product_name;?>
                                    </a>
                                </div>
                                <div class="p-price">
                                    <s><?php echo $currency.$product['product_price_market'];?></s> <?php echo $currency.$product['product_basediscount_price'];?>
                                </div>
                            </div>
                        </li>
                    <?php
                    }
                }
                ?>
            </ul>
            <a class="arrow_left btn_arrow arrow_disabled" href="javascript:;"><span class="icon_arraw_left"></span> </a>
            <a class="arrow_right btn_arrow" href="javascript:;"><span class="icon_arraw_right"></span> </a>
        </div>
    </div>
    <!--img end-->

</div>
<!--main over-->
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/found.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>
