<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/home.css?v=<?php echo STATIC_FILE_VERSION ?>">
<!--main start-->
<div class="main home" id="home">
	<!-- banner start -->
	<?php include dirname(__FILE__).'/index/banner.php'; ?>
    <!-- banner end -->
    <!-- 商品列表 start -->
    <div class="product-list clear">
    	<?php 
    		if(!empty($product_list)){
    			foreach ($product_list as $k=>$v){
	 	?>
	 	<div class="lists clear">
            <h2><?php echo $v['key'];?></h2>
            <div class="list-left">
                <div class="list-tit">
                    <div class="list-tit-top"><a href="<?php echo genURL($v['product_url'])?>" onclick='productEvent(<?php $productObj = array();$productObj['data'][] = array('price' => $v['product_discount_price'],'id' => $v['product_id']);$productObj['list'] = 'Home Page'; echo json_encode($productObj);?>)'><?php echo $v['title']?></a></div>
                    <p class="price-o"><?php echo $v['product_currency'].$v['product_price_market']?></p>
                    <p class="price-n">
                    <?php 
                    if(strpos($v['product_discount_price'],'.')!==false){
                    	$mark = explode(".", $v['product_discount_price']);
						echo $v['product_currency'].$mark[0].".<sup>".$mark[1]."</sup>";
                    }else{
                    	echo $v['product_currency'].$v['product_discount_price'];
                    }	
                    ?>
                    </p>
                </div>
                <div class="list-img"><a href="<?php echo genURL($v['product_url'])?>"  onclick='productEvent(<?php $productObj = array();$productObj['data'][] = array('price' => $v['product_discount_price'],'id' => $v['product_id']);$productObj['list'] = 'Home Page'; echo json_encode($productObj);?>)'><img width="218" height="218" src="<?php echo PRODUCT_IMAGEM_URL.$v['product_image']?>"></a></div>
            </div>
            <!-- left end -->
            <div class="list-right">
                <ul class="clearfix fl current" id="pro_tab_b_0_1">
                <?php 
                	if(isset($v['children']) && !empty($v['children'])){
						$num  = 0;
                		foreach ($v['children'] as $value){
						if($num<4){
                ?>
                <li>
                        <div class="pro-list-block">
                            <div class="p-pic">
                                <a href="<?php echo genURL($value['product_url'])?>" title="<?php echo $value['product_name']?>" onclick='productEvent(<?php $productObj = array();$productObj['data'][] = array('price' => $value['product_discount_price'],'id' => $value['product_id']);$productObj['list'] = 'Home Page'; echo json_encode($productObj);?>)'>
                                    <img src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo PRODUCT_IMAGEM_URL.$value['product_image']?>" width="191" height="191" alt="<?php echo $value['product_name']?>">
                                    <?php
                                    if($value['product_discount_number']){
                                    ?>
                                    <p class="icon-off"><i><?php echo (int) $value['product_discount_number'];?></i></p>
                                    <?php
                                    }
                                    ?>
                                    <span class="span-left">
		                                    <?php
                                            if($value['new']){
                                                ?>
                                                <i class="icon-news"></i>
                                            <?php
                                            }
                                            ?>
                                        <?php
                                        if($value['icon']){
                                            ?>
                                            <i class="icon-hot"></i>
                                        <?php
                                        }
                                        ?>
                                                </span>
                                </a>
                                
                            </div>
                            <?php 
                            	$productObj = array();
                            	$productObj['data'][] = array(
									'price' => $value['product_discount_price'],
									'id' => $value['product_id']);
								$productObj['list'] = 'Home Page'; 
								
                            ?>
                            <div class="p-name"><a href="<?php echo genURL($value['product_url'])?>" onclick='productEvent(<?php echo json_encode($productObj);?>)'><?php echo isset($value['product_description_name'])?$value['product_description_name']:$value['product_name'];?></a></div>
                            <div class="promotion"></div>
                            <div class="p-price">
                                <span class="price-sm-o"><?php echo $value['product_currency']. number_format($value['product_price_market'],2,'.',',')?></span><span class="price-sm-n"><?php echo $value['product_currency']. number_format($value['product_discount_price'],2,'.',',')?></span>
                            </div>
                            <div class="p-fr"><i></i><em><?php echo lang('free_shipping');?></em></div>
                        </div>
                    </li>
                <?php	
                		$num++;	
                			}	
                		}
                	}
                ?>
                </ul>
            </div>
            <!-- right end -->
        </div>
	 	<?php
				}
    		}
    	?>
    </div>
    <!-- 商品列表 end -->
    <!-- 店铺 start -->
    <?php include dirname(__FILE__).'/index/stores.php'; ?>
    <!-- 店铺 end -->
</div>
<!--main end-->
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>/js/home.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>
