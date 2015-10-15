<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/hurried_mould.css?v=<?php echo STATIC_FILE_VERSION ?>">

<!-- color_bg -->
<div class="main mould-four" style="background:<?php echo isset($color_bg)?$color_bg:'#f7f7f7;';?>">
	<div class="banner" style=" background-color:#00F;">
    	<div class="timer">
    		<span class="endIn"><?php echo lang('end_in');?></span>
            <div class="con" id="counter">
				<span data-endtime="<?php echo $data_end;?>"></span>
			</div>
        </div>
         <script type="text/javascript">
            $(function() {
				ec.load('ec.ui.countdown', {
						onload : function () {
							ec.ui.countdown('#counter span', {
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
    	<img src="<?php echo empty($promote['promotion_banner'])?'':COMMON_IMAGE_URL.$promote['promotion_banner'];?>" width="100%" height="220"/>
    </div> 
	<div class="four-nav">
		<li><a class="nav-tit" href="javascript:;"><?php echo lang('menu');?></a></li>
		<?php $i=1;?>
		<?php foreach($promoteDetailList as $promoteDetail){?>
		<li><a href="#mould<?php echo $i;?>"><?php echo $promoteDetail['promotion_detail_title'];?></a></li>
		<?php if($i >= 10) break;?>
		<?php $i++;?>
		<?php }?>
		<li id="gotop"></li>
	</div>
	<div class="primary-list">
		<?php $i=1;?>
		<?php foreach($promoteDetailList as $promoteDetail){?>
		<div class="goods-list" id="mould<?php echo $i;?>">
			<div class="mouldF-tit">
				<a class="mould-num" href="javascript:;"><?php echo lang('floor');?> <?php echo $i;?><span class="icon-left"></span></a>
				<span class="tit-info"><?php echo $promoteDetail['promotion_detail_title'];?></span> 
				<a class="tit-all" href="<?php echo $promoteDetail['promotion_detail_url'];?>"><?php echo lang('view_all');?></a>
			</div>
			
			<!-- color_floor -->
			<ul style="background:<?php echo isset($color_floor)?$color_floor:'#fff;';?>">
				<?php foreach ($promoteDetail['productList'] as $product):?>
				<li>
	                <div class="p-pic">
	                    <a href="<?php echo genURL($product['product_url']);?>" title="<?php echo isset($product['product_description_name'])?$product['product_description_name']:$product['product_name'];?>" onclick="javascript:productEvent(<?php echo json_encode(array('list'=>'Promotion','data'=>array(array('id'=>$product['product_id'],'price'=>$product['product_discount_price']))));?>);">
	                    	<img width="186" height="186" src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo PRODUCT_IMAGEM_URL.$product['product_image']?>" alt="">
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
		</div>
		<?php $i++;?>
		<?php }?>
	</div>
</div>
<script>
	dataLayer.push({
	  'ecommerce': {
	    'promoView': {
	      'promotions': <?php echo $tongji_promote;?>
	    }
	  }
	});
	$('.p-price .p-price-o').each(function(){
        var priceW=$(this).innerWidth();
        if(priceW>=70){
        	$(this).parent().css({'line-height':'17px'});
            $(this).css({'float':'none'});
            $(this).next().css({'float':'none','display':'block'});
        }
    })
</script>
<?php include dirname(__FILE__).'/common/footer.php'; ?>
</body>
</html>