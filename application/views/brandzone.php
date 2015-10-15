<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="resource/default/css/brand.css">
<div class="wraper">
    <div class="main brand" id="brand">
        <div class="wrap breadcrumbs">
            <i class="icon-home">&nbsp;</i>
            <a href="<?php echo genURL('');?>"><?php echo lang('home');?></a><i class="icon-arr-right"></i>
            <a href="<?php echo genURL('brand');?>"><?php echo lang('brand_zone');?></a><i class="icon-arr-right"></i>
            <a href="<?php echo genURL($brand_category_info['brand_category_url']);?>"><?php echo $brand_category_info['brand_category_title'];?></a><i class="icon-arr-right"></i>
            <a href="<?php echo genURL($brand_info['brand_url']);?>"><?php echo $brand_info['brand_title'];?></a>
        </div>
        <!--main start-->
        <div class="brand-cate">
            <!-- 品牌页 -->
            <div class="zone-page">
                <div class="zone-left">
                    <div class="zone-top">
                        <img class="logo" src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo COMMON_IMAGE_URL.$brand_info['brand_icon'];?>">
                        <span class="zone-title"><?php echo $brand_info['brand_title'];?></span>
                    </div>
                    <div class="zone-info">
                        <p><?php echo $brand_info['brand_description'];?></p>
                    </div>
                </div>
                <img class="zone-right" src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo COMMON_IMAGE_URL.$brand_info['brand_banner'];?>">
            </div>
            <!-- end -->
            <div class="primary-category">
                <!-- 基础查询 start -->
                <?php include dirname(__FILE__).'/brand/base_search.php'; ?>
                <!-- 基础查询 end -->
                <div class="primary-list">
                    <div class="goods-list">
                        <!-- 商品列表 start -->
                        <?php include dirname(__FILE__).'/brand/product_list.php'; ?>
                        <!-- 商品列表 end -->
                        <!-- 分页 s -->
                        <div class="show-pager">
                            <?php include dirname(__FILE__).'/common/pagination.php'; ?>
                        </div>
                        <!-- 分页 end -->
                    </div>
                </div>
            </div>
        </div>
        <!-- main end -->
    </div>
</div>
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/javascript" src="resource/default/js/common/utils.js"></script>
<script type="text/javascript" src="resource/default/js/category.js"></script>
<script type="text/javascript" src="resource/default/js/brand.js"></script>
</body>
</html>
