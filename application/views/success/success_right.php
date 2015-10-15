<div class="main-right">
        <h1><?php echo lang('share_your_purchase');?></h1>
        <ul>
        <?php 
        foreach ($order_product_list as $product_key=>$product_val){
        ?>
        	<li>
                <a href="<?php echo genURL($product_val['product_url']);?>" ><img width="118" height="118" src="<?php echo PRODUCT_IMAGEM_URL.$product_val['order_product_image']?>"></a>
                <div class="share-info">
                    <p><a  href="<?php echo genURL($product_val['product_url']);?>"><?php echo $product_val['order_product_name']?></a></p>
                    <p class="orig"><?php echo $order_currency?><s><?php echo $product_val['order_product_price_market']?></s></p>
                    <p class="discount"><?php echo $order_currency?><?php echo $product_val['order_product_price']?></p>
                    <div class="share-list" id="shareList">
                        <span>Share</span>
                        <a href="javascript:void(0);" onclick="javascript:window.open('https://www.facebook.com/dialog/share?app_id=1390176774575549&display=popup&href=<?php echo urlencode(genUrl($product_val['product_url']).'?utm_source=facebook%26utm_medium=pageshare%26utm_campaign=success') ?>&title=<?php echo urlencode($product_val['order_product_name']) ?>&picture=<?php echo urlencode(PRODUCT_IMAGEXL_URL.$product_val['order_product_image']) ?>&redirect_uri=<?php echo urlencode(genUrl('close')) ?>','_blank','width=600, height=652')" class="icon-facebook" title="Facebook"></a>
                        <a href="javascript:void(0);" onclick="javascript:window.open('https://twitter.com/intent/tweet?text=<?php echo lang('text_twitter_share') ?>+@eachbuyer+<?php echo urlencode(genUrl($product_val['product_url']).'?utm_source=twitter%26utm_medium=pageshare%26utm_campaign=success') ?>','_blank','width=600, height=652')" class="icon-twitter" title="Twitter"></a>
                        <a href="javascript:void(0);" onclick="javascript:window.open('http://www.pinterest.com/pin/create/button/?url=<?php echo urlencode(genUrl($product_val['product_url']).'?utm_source=pinterest%26utm_medium=pageshare%26utm_campaign=success') ?>&media=<?php echo urlencode(PRODUCT_IMAGEXL_URL.$product_val['order_product_image']) ?>&description=<?php echo urlencode($product_val['order_product_name']) ?>','_blank','width=765, height=752')" class="icon-plusone" title="Pinterest"></a>
                        <a href="mailto:?subject=<?php echo lang('text_email_share').' - '.$product_val['order_product_name'] ?>&body=<?php echo $product_val['order_product_name'].'%0A'.genUrl($product_val['product_url']).'?utm_source=email%26utm_medium=pageshare%26utm_campaign=success' ?>" class="icon-email" title="Email"></a>
                    </div>
                </div>
            </li>
        <?php
        }
        ?>
        </ul>
    </div>
