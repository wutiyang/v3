<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="resource/default/css/brand.css">
<div class="wraper">
<div class="main brand" id="brand">
<div class="wrap breadcrumbs">
    <i class="icon-home">&nbsp;</i>
    <a href="<?php echo genURL('');?>"><?php echo lang('home');?></a><i class="icon-arr-right"></i>
    <a href="<?php echo genURL('brand');?>"><?php echo lang('brand_zone');?></a><i class="icon-arr-right"></i>
    <a href="<?php echo genURL($category_info['brand_category_url']);?>"><?php echo $category_info['brand_category_title'];?></a>
</div>
<!--main start-->
<div class="brand-cate">
<h1 class="cate-tit"><?php echo $category_info['brand_category_title'];?></h1>
<!-- 品牌区 -->
<div class="cate-zone" id="cateZone">
    <h2 class="zone-tit"><?php echo lang('all_brands');?></h2>
    <div class="all-brand">
        <ul class="zone-list">
            <?php
            if(isset($brand_list) && !empty($brand_list)){
                foreach($brand_list as $brand){ ?>
                    <li>
                        <a href="<?php echo genURL($brand['brand_url']);?>" title="<?php echo $brand['brand_title'];?>">
                            <img src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo COMMON_IMAGE_URL.$brand['brand_icon']?>" width="126" height="126">
                            <span class="zone-name"><?php echo $brand['brand_title'];?></span>
                        </a>
                    </li>
               <?php  }
            }
            ?>
        </ul>
        <a class="more btn-span"></a>
        <a class="less btn-span"></a>
    </div>
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
