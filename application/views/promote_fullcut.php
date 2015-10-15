<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/hurried_mould.css?v=<?php echo STATIC_FILE_VERSION ?>">
<div class="main content">
	<div class="fullcut-tit"><?php echo $fullcut_note;?></div> 
	<div class="primary-list">
		<div class="goods-list">
			<ul>
				<?php foreach ($productList as $product):?>
				<li>
	                <div class="p-pic">
	                    <a href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);">
	                    	<img width="163" height="163" src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo PRODUCT_IMAGEM_URL.$product['product_image']?>" alt="">
	                        <?php if( $product['product_discount_number'] != 0):?>
	                        <p class="icon-off"><i><?php echo intval($product['product_discount_number']);?></i></p>
	                        <?php endif;?>
	                    </a>
	                </div>
	                <div class="p-name">
	                	<a href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>"><?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?></a>
	                </div>
	                <div class="p-price">
	                    <span class="p-price-o"><?php echo $product['product_currency'].number_format($product['product_price_market'],2,'.',',');?></span>
	                    <span class="p-price-n"><?php echo $product['product_currency'].number_format($product['product_discount_price'],2,'.',',');?></span>
	                </div>
				</li>
				<?php endforeach;?>
			</ul>
			<div class="show-pager">
				<?php include dirname(__FILE__).'/common/pagination.php'; ?>
		    </div>
		</div>
	</div>
	
</div>
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/javascript">
	$('.p-price .p-price-o').each(function(){
        var priceW=$(this).innerWidth();
        if(priceW>=70){
            $(this).css({'float':'none'});
            $(this).next().css({'float':'none','display':'block'});
        }
    })
</script>
</body>
</html>