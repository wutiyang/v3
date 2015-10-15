<?php include dirname(__FILE__).'/common/header.php'; ?>
<input type="hidden" id="eachbuyer_logState" value="0" >
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/product.css?v=<?php echo STATIC_FILE_VERSION ?>">
<!--breadcrumbs start-->
<div class="wrap breadcrumbs">
    <i class="icon-home">&nbsp;</i><a href="<?php echo genURL('/');?>"><?php echo lang('home');?></a>
    <!--
    <i class="icon-arr-right"></i><a href="">Consumer Electronics</a>
    <i class="icon-arr-right"></i>Wii Accessories
    -->
</div>
<!--breadcrumbs end-->
<!--main start-->
<div class="main questions" id="questions">
    <a href="<?php echo genURL($product_info['product_url'])?>" class="goback"><i class="goback-icon"></i><?php echo lang('back_to_product_page');?></a>
    <div class="main-left">
        <h1 class="shop-category"><?php echo lang('customer_q&a');?></h1>
        <?php 
        if(isset($product_info)){
        ?>
        <div class="shop-box">
        	<a href="<?php echo genURL($product_info['product_url'])?>" class="img"><img width="140" height="140" src="<?php echo PRODUCT_IMAGEM_URL.$product_info['product_image']?>" /></a>
            <div class="shop-con clearfix">
            	<h2><?php echo isset($product_info['product_description_name'])?$product_info['product_description_name']:$product_info['product_name'];?></h2>
                <s><?php echo $product_info['product_currency'].number_format($product_info['product_price_market'],2,'.',',')?></s>
                <p><span><?php echo $product_info['product_currency'].number_format($product_info['product_discount_price'],2,'.',',')?></span></p>
            </div>
        </div>
        <?php
        }
        ?>
        <?php 
        if(isset($qna_list) && !empty($qna_list)){
        ?>
        <div class="question question-w">
            <?php 
            foreach ($qna_list as $qna_k=>$qna_v){
            ?>
            <ul class="faqs">
                <li class="que-list">
                    <div class="ask-que"><i class="q"></i>
                        <p class="ask-tit"><strong><?php echo $qna_v['qna_title']?></strong></p>
                        <p class="pose"><?php echo $qna_v['qna_content']?></p>
                        <p class="by">by <?php echo $qna_v['customer_name']?> on <?php echo date("M d,Y",strtotime($qna_v['qna_time_create']));?></p>
                    </div>
                    <div class="answer"><i class="a"></i>
                        <p class="pose"><?php echo $qna_v['qna_answer']?></p>
                        <p class="by">by <?php echo $qna_v['user_name']?> on <?php echo date("M d,Y",strtotime($qna_v['qna_time_reply']));?></p>
                    </div>
                </li>
            </ul>	
            <?php	
            }
            ?>
        </div>
        <?php	
        }
        ?>
		
        <div class="show-pager">
            <?php include dirname(__FILE__).'/common/pagination.php'; ?>
        </div>        
    </div>
    <!-- sidebar -->
        <div class="side">
            <?php if(isset($alsolike_data) && !empty($alsolike_data)):?>
			<div class="also">
                <h5><?php echo lang('also_bought_tips');?></h5>
                <?php foreach ($alsolike_data as $like_k=>$like_v):?>
                <div>
                    <img src="<?php echo PRODUCT_IMAGEM_URL.$like_v['product_image']?>" width="158" height="158">
                    <p><?php echo isset($like_v['product_description_name'])?$like_v['product_description_name']:$like_v['product_name'];?></p>
                    <p class="price"><?php echo $like_v['product_currency']?> <?php echo number_format($like_v['product_discount_price'],2,'.',',')?></p>
                </div>
                <?php endforeach;?>
            </div>
			<?php endif;?>
        </div>
        <!-- sidebar -->
</div>


<!--main over-->
<?php include dirname(__FILE__).'/common/footer.php'; ?>
</body>
</html>