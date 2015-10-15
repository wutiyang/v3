<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo SSL_ENABLED?RESOURCE_URL_SSL:RESOURCE_URL ?>css/success.css?v=<?php echo STATIC_FILE_VERSION ?>">
<!--main start-->
<div class="main" id="success">
    <div class="main-left">
        <h1><?php echo lang('order_received_tips');?></h1>
        <table class="order-info">
            <tr>
                <td><?php echo lang('order_number_tips');?>：</td>
                <td class="order-num"><?php echo $order_code?></td>
            </tr>
            <tr>
                <td><?php echo lang('order_total_amount');?>：</td>
                <td class="order-num"><?php echo $order_currency.' '.$order_price?></td>
            </tr>
        </table>
        <p class="check-status"><?php echo lang('check_order_status_tips');?> “ <a class="view-status" href="<?php echo genURL('order_detail/'.$order_id)?>"><?php echo lang('view_order');?></a> ” </p>
        <div class="line"></div>
        <?php 
        if($payment_type===true){
        ?>
        <div class="order-success">
            <h2><?php echo lang('transfer_money_to_bank_tips');?></h2>
            <table class="bank-table">
                <tr>
                    <td class="bank-name frist"><?php echo lang('account_holder');?>：</td>
                    <td class="frist">EST CHOICE TECHNOLOGY LIMITED</td>
                </tr>
                <tr>
                    <td class="bank-name"><?php echo lang('account_number');?>：</td>
                    <td>0128 8392 2303 74</td>
                </tr>
                <tr>
                    <td class="bank-name"><?php echo lang('swift_code');?>：</td>
                    <td>BKCHHKHHXX</td>
                </tr>
                <tr>
                    <td class="bank-name"><?php echo lang('bank_name');?>：</td>
                    <td>BANK OF CHINA HONGKONG LIMITED</td>
                </tr>
                <tr>
                    <td class="bank-name last"><?php echo lang('bank_c');?></td>
                    <td class="last">012（<span><?php echo lang('bank_c_notice');?></span>） </td>
                </tr>
            </table>    
            <p class="please-note"><?php echo lang('confirm_payment_tips');?></p>
            <p class="btn-space"><a href="<?php echo genURL()?>" class="btnorg35"><?php echo lang('continue_shopping');?></a><a href="<?php echo genURL('order_detail/'.$order_id)?>" class="icon-view icon-view-dis" id="continue"><?php echo lang('view_order');?></a></p>
        </div>
        <?php	
        }else if($pay_status==OD_PAID){
        ?>
        <!-- div class="pay-success"-->
        <div class="">
            <h2><?php echo lang('payment_success_tips');?></h2>
            <p class="success-info"><?php echo lang('deliver_items_soon_tips');?></p>
            <p class="btn-space"><a href="<?php echo genURL('/')?>" class="btnorg35"><?php echo lang('continue_shopping');?></a><a href="<?php echo genURL('order_detail/'.$order_id)?>" class="icon-view icon-view-dis"><?php echo lang('view_order');?></a></p>
            <?php 
            if(isset($paypal_ec_create_user)){
            ?>
            <div class="line"></div>
            <div class="success-mas">
                <p class="look"><?php echo sprintf(lang('account_create_tips'),$paypal_ec_create_user['email']);?></p>
                <p><?php echo lang('account_password_tips');?><?php echo PAYPALEC_LOGIN_PASSWORD;?>.</p>
                <p><?php echo lang('account_edit_tips');?></p>
            </div>
            <div class="success-mas">
                <p><?php echo lang('account_subscrib_tips');?></p>
                <p><?php echo lang('find_more_options');?> <a href="<?php echo genURL('order_list')?>"><?php echo lang('my_account');?></a></p>
            </div>
            <?php } ?>
        </div>
        <!-- 支付成功 -->
        <?php	
        }else if($pay_status==OD_PAYING){
        ?>
        <!-- div class="payment-delay"-->
        <div>
            <h2><?php echo lang('payment_pending_status_tips');?></h2>
            <p class="delay-tit"><?php echo lang('follow_payment_instructions_tips');?></p>
            <p class="delay-info"><?php echo lang('complete_payment_tips');?></p>
            <p class="delay-info"><?php echo lang('failed_payment_tips');?></p>
            <p class="btn-space"><a href="<?php echo genURL('order_detail/'.$order_id)?>" class="icon-view icon-view-dis"><?php echo lang('view_order');?></a></p>
        </div>
        <!-- 支付延时 -->
        <?php	
        }else{
        ?>
        <!-- div class="payment-failed"-->
        <div class="">
            <h2><?php echo lang('payment_not_succ_tips');?></h2>
            <p class="delay-info">
                <?php if($black_customer==1){?><span class="error">Error Code：BLT</span><?php }?>
                <?php echo lang('complete_payment_tips_start');?>"<a href="<?php echo genURL('/repay/'.$order_id)?>"><?php echo lang('complete_your_payment');?></a>" <?php echo lang('complete_payment_tips_end');?></p>
            <p class="delay-note"><?php echo lang('processing_order_soon_tips');?></p>
            <p class="btn-space">
            	<a href="<?php echo genURL('repay/'.$order_id)?>" class="btnorg35"><?php echo lang('complete_your_payment');?></a>
            	<a href="<?php echo genURL('order_detail/'.$order_id)?>" class="icon-view icon-view-dis"><?php echo lang('view_order');?></a>
            </p>
        </div>
        <!-- 支付未成功 -->	
        <?php
        }
        ?>
    </div>
    
    <?php
    if(!empty($order_product_list)){
    	include dirname(__FILE__).'/success/success_right.php';
    } 
    ?>
    <div class="clear"></div>
    <?php if(isset($ad_list[4])){ ?>
        <div class="clearfix" style="margin:10px 0">
            <a href="<?php echo $ad_list[4]['lan_content']['url']?>" title="<?php echo $ad_list[4]['lan_content']['alt']?>" class="ad2" onclick='onPromoClick(<?php $productObj['data']['id'] = 'A'.$ad_list[4]['ad_id'];$productObj['data']['name'] = 'checkout_success';echo json_encode($productObj);?>)'>
            <img src="<?php echo COMMON_IMAGE_URL.$ad_list[4]['lan_content']['img'] ?>" width="1200" height="120"></a>
        </div>
    <?php } ?>
</div>

<!--main over-->
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<?php 
if($pay_status==OD_PAID){
$order_price_without_shipping = round(($order_info['order_baseprice']*100-$order_info['order_baseprice_shipping']*100-$order_info['order_baseprice_insurance']*100)/100,2);
$order_discount_n_integral = $order_info['order_price_coupon'] + $order_info['order_price_rewards'] + $order_info['order_price_discount'];
?>
<!-- WEBGAINS -->
	<?php if($affiliate_source == 'sas'){ ?>
		<img style="display:none;" alt='s2' src="https://shareasale.com/sale.cfm?tracking=<?php echo $order_info['order_code'] ?>&amount=<?php echo $order_price_without_shipping ?>&transtype=sale&merchantID=37049&storeID=1&skulist=<?php echo $webgains_sku_str ?>&pricelist=<?php echo $webgains_price_str ?>&quantitylist=<?php echo $webgains_quantity_str ?>" width="1" height="1" />
	<?php }elseif($affiliate_source == 'cf'){ ?>
		<script type="text/javascript">
		var cf_merchant = "99b595f8-9267-4678-a5fa-543736e5f437";
		var cf_order = "<?php echo $order_code ?>";
		var cf_amount = <?php echo $order_price_without_shipping ?>;
		var cf_items = new Array();
		<?php foreach($order_product_list as $record){ ?>
			cf_items.push({ sku: "<?php echo $record['product_sku'] ?>", price: <?php echo $record['order_product_baseprice'] ?>, quantity: <?php echo $record['order_product_quantity'] ?> });
		<?php } ?>
		</script>
		<script src="https://track.commissionfactory.com.au/Track.js" type="text/javascript"></script>
	<?php }elseif($affiliate_source == 'cj'){ ?>
		<iframe height="1" width="1" frameborder="0" scrolling="no" src="https://www.emjcd.com/tags/c?containerTagId=6325&<?php echo $webgains_cj_goods_str ?>&CID=1529131&OID=<?php echo $order_info['order_code'] ?>&TYPE=366167&CURRENCY=USD&DISCOUNT=<?php echo $order_discount_n_integral ?>" name="cj_conversion" ></iframe>
	<?php }elseif(in_array($affiliate_source,array('wgde','wguk','wgfr','wges','wgit'))){ ?>
		<script language="javascript" type="text/javascript">
		var wgOrderReference = "<?php echo $order_code ?>";
		var wgOrderValue = "<?php echo $webgains_wgOrderValue ?>";
		var wgEventID = <?php echo $webgains_eventid ?>;
		var wgComment = "";
		var wgLang = "en_EN";
		var wgsLang = "javascript-client";
		var wgVersion = "1.2";
		var wgProgramID = <?php echo $webgains_programid ?>;
		var wgSubDomain = "track";
		var wgCheckSum = "";
		var wgItems = "<?php echo $webgains_items ?>";
		var wgVoucherCode = "<?php echo $order_info['order_coupon'] ?>";
		var wgCustomerID = "<?php echo $order_info['customer_id'] ?>";
		var wgCurrency = "<?php echo $webgains_wgcurrency?>";

		if(location.protocol.toLowerCase() == "https:") wgProtocol="https";
		else wgProtocol = "http";

		wgUri = wgProtocol + "://" + wgSubDomain + ".webgains.com/transaction.html" + "?wgver=" + wgVersion + "&wgprotocol=" + wgProtocol + "&wgsubdomain=" + wgSubDomain + "&wgslang=" + wgsLang + "&wglang=" + wgLang + "&wgprogramid=" + wgProgramID + "&wgeventid=" + wgEventID + "&wgvalue=" + wgOrderValue + "&wgchecksum=" + wgCheckSum + "&wgorderreference="  + wgOrderReference + "&wgcomment=" + escape(wgComment) + "&wglocation=" + escape(document.referrer) + "&wgitems=" + escape(wgItems) + "&wgcustomerid=" + escape(wgCustomerID) + "&wgvouchercode=" + escape(wgVoucherCode) + "&wgCurrency=" + escape(wgCurrency);
		document.write('<sc'+'ript language="JavaScript"  type="text/javascript" src="'+wgUri+'"></sc'+'ript>');
		</script>
		<noscript>
		<img src="http://track.webgains.com/transaction.html?wgver=1.2&wgprogramid=<?php echo $webgains_programid ?>&wgrs=1&wgvalue=<?php echo $webgains_wgOrderValue ?>&wgeventid=<?php echo $webgains_eventid ?>&wgorderreference=<?php echo $order_code ?>&wgitems=<?php echo $webgains_items ?>&wgvouchercode=<?php echo $order_info['order_coupon'] ?>&wgcustomerid=<?php echo $order_info['customer_id'] ?>&wgCurrency=<?php echo $webgains_wgcurrency?>" alt="" />
		</noscript>
	<?php }elseif($affiliate_source == 'alipromo'){ ?>
		<?php
		$alipromo_cf = sprintf("%.2f",$order_price_without_shipping*0.07*1.25);
		$alipromo_pl = array();
		foreach($order_product_list as $record){
			$alipromo_pl[] = 'p'.$record['product_id'];
		}
		$alipromo_pl = array_unique($alipromo_pl);
		$alipromo_pl = implode(',',$alipromo_pl);
		$alipromo_sign = md5($order_code.$order_info['order_baseprice'].$alipromo_cf.$alipromo_pl.$affiliate_campaign.'FEf43fSadf77-rtrbGre_43erfSDF43dSs33d#$wsV');
		?>
		<img src="http://alipromo.com/tracking/eachbuyer/?oid=<?php echo $order_code ?>&oa=<?php echo $order_info['order_baseprice'] ?>&cf=<?php echo $alipromo_cf ?>&pl=<?php echo $alipromo_pl ?>&utm_campaign=<?php echo $affiliate_campaign ?>&sign=<?php echo $alipromo_sign ?>" width="1" height="1" border="0" />
	<?php } ?>

	<!-- questler.de -->
	<!-- <img style="display:none;" src="http://www.questler.de/tracking/tracking.php?do=track&id=1354018315&rueck=<?php //echo $order['order_sn'] ?>"  height="1" width="1" /> -->

	<!-- partner.andasa -->
	<div style="display:none;">
	<script type="text/javascript" src="https://partner.andasa.de/TeaserScript.ashx?shopId=6003&revenue=<?php echo $order_info['order_baseprice'] ?>&orderId=<?php echo $order_info['order_code'] ?>"></script>
	</div>
<?php
}
?>
<script>
    onCheckout(<?php $orderObj['step'] = 2;$orderObj['option'] = '';foreach($order_product_list as $v){$orderObj['data'][] = array('id'=>$v['product_id'],'price'=>$v['order_product_baseprice'],'quantity'=>$v['order_product_quantity']);}echo json_encode($orderObj);?>);
</script>
</body>
</html>