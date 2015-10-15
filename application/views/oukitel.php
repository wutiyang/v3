<?php include dirname(__FILE__).'/common/header.php'; ?>

<!--main start-->
<div class="wraper">
    <!--main start-->
    <div class="phone1">
        <?php $porduct = $product_list[370987]; ?>
        
        <div class="phone clearfix">
            <div class="fl">
                <div class="phonetit">OUKITEL U8</div>
                <ul class="memory clearfix">
                    <li>2G<span>RAM</span></li>
                    <li>16G<span>ROM</span></li>
                    <li>4G<span>LTE</span><em class="top">MTK6735</em></li>
                    <li class="unlock"><?php echo lang('touch_to_unlock') ?></li>
                </ul>
                <p class="price-o"><?php echo $porduct['product_currency1'].$porduct['product_currency2'] ?><?php echo substr($porduct['product_price_market'],0,strpos($porduct['product_price_market'],'.')+1) ?><sup><?php echo substr($porduct['product_price_market'],strpos($porduct['product_price_market'],'.')+1) ?></sup></p>
                <p class="price-n"><?php echo $porduct['product_currency1'] ?><span><?php echo $porduct['product_currency2'] ?><?php echo substr($porduct['product_discount_price'],0,strpos($porduct['product_discount_price'],'.')+1) ?><sup><?php echo substr($porduct['product_discount_price'],strpos($porduct['product_discount_price'],'.')+1) ?></sup></span></p>
                <a style="font-family:Arial" class="shop" href="<?php echo genUrl($porduct['product_url']) ?>"><?php echo lang('shop_now') ?></a>
            </div>
            <div class="fr"><a class="pic" href="<?php echo genUrl($porduct['product_url']) ?>"><img src="http://img6.eachbuyer.com/upload/201509/2015091914555155fd06f785470.jpg" width="415" height="344"></a></div>
        </div>
    </div>
    <div class="phone2">
        <?php $porduct = $product_list[373335]; ?>
        <div class="phone clearfix">
            <div class="fr">
                <div class="phonetit">OUKITEL U2</div>
                <ul class="memory clearfix">
                    <li class="up"><span class="top">Android</span>5.1</li>
                    <li>4G<span>LTE</span></li>
                    <li>1G<span>RAM</span></li>
                    <li>8G<span>ROM</span></li>
                    <li>2.5D<span><?php echo lang('glass') ?></span><em class="top"><?php echo lang('dual') ?></em></li>
                </ul>
                <p class="price-o"><?php echo $porduct['product_currency1'].$porduct['product_currency2'] ?><?php echo substr($porduct['product_price_market'],0,strpos($porduct['product_price_market'],'.')+1) ?><sup><?php echo substr($porduct['product_price_market'],strpos($porduct['product_price_market'],'.')+1) ?></sup></p>
                <p class="price-n"><?php echo $porduct['product_currency1'] ?><span><?php echo $porduct['product_currency2'] ?><?php echo substr($porduct['product_discount_price'],0,strpos($porduct['product_discount_price'],'.')+1) ?><sup><?php echo substr($porduct['product_discount_price'],strpos($porduct['product_discount_price'],'.')+1) ?></sup></span></p>
                <a class="shop" href="<?php echo genUrl($porduct['product_url']) ?>"><?php echo lang('shop_now') ?></a>
            </div>
            <div class="fl"><a class="pic" href="<?php echo genUrl($porduct['product_url']) ?>"><img src="http://img6.eachbuyer.com/upload/201509/2015091914560255fd070299af1.jpg" width="305" height="340"></a></div>
        </div>
    </div>
    <div class="phone3">
        <?php $porduct = $product_list[374450]; ?>
        <div class="phone clearfix">
            <div class="fl">
                <div class="phonetit">OUKITEL U7</div>
                <ul class="memory clearfix">
                    <li>1G<span>RAM</span></li>
                    <li>8G<span>ROM</span></li>
                    <li>5.5<em class="right"><?php echo lang('inch') ?></em><span>QHD</span></li>
                    <li class="unlock">MTK6582  Quad Core</li>
                </ul>
                <p class="price-o"><?php echo $porduct['product_currency1'].$porduct['product_currency2'] ?><?php echo substr($porduct['product_price_market'],0,strpos($porduct['product_price_market'],'.')+1) ?><sup><?php echo substr($porduct['product_price_market'],strpos($porduct['product_price_market'],'.')+1) ?></sup></p>
                <p class="price-n"><?php echo $porduct['product_currency1'] ?><span><?php echo $porduct['product_currency2'] ?><?php echo substr($porduct['product_discount_price'],0,strpos($porduct['product_discount_price'],'.')+1) ?><sup><?php echo substr($porduct['product_discount_price'],strpos($porduct['product_discount_price'],'.')+1) ?></sup></span></p>
                <a class="shop" href="<?php echo genUrl($porduct['product_url']) ?>"><?php echo lang('shop_now') ?></a>
            </div>
            <div class="fr"><a class="pic" href="<?php echo genUrl($porduct['product_url']) ?>"><img src="http://img6.eachbuyer.com/upload/201509/2015091914561055fd070a10379.jpg" width="228" height="364"></a></div>
        </div>
    </div>
    <div class="phone4">
        <?php $porduct = $product_list[369130]; ?>
        <div class="phone clearfix">
            <div class="fr">
                <div class="phonetit">OUKITEL Original One O901</div>
                <ul class="memory clearfix">
                    <li class="unlock">MT6582  Quad Core</li>
                    <li>4G<span>ROM</span></li>
                    <li class="unlock center">Android 4.4</li>
                </ul>
                <p class="price-o"><?php echo $porduct['product_currency1'].$porduct['product_currency2'] ?><?php echo substr($porduct['product_price_market'],0,strpos($porduct['product_price_market'],'.')+1) ?><sup><?php echo substr($porduct['product_price_market'],strpos($porduct['product_price_market'],'.')+1) ?></sup></p>
                <p class="price-n"><?php echo $porduct['product_currency1'] ?><span><?php echo $porduct['product_currency2'] ?><?php echo substr($porduct['product_discount_price'],0,strpos($porduct['product_discount_price'],'.')+1) ?><sup><?php echo substr($porduct['product_discount_price'],strpos($porduct['product_discount_price'],'.')+1) ?></sup></span></p>
                <a class="shop" href="<?php echo genUrl($porduct['product_url']) ?>"><?php echo lang('shop_now') ?></a>
            </div>
            <div class="fl"><a class="pic" href="<?php echo genUrl($porduct['product_url']) ?>"><img src="http://img6.eachbuyer.com/upload/201509/2015091914561655fd0710942e2.jpg" width="188" height="415"></a></div>
        </div>
    </div>
    <div class="phone5">
        <?php $porduct = $product_list[371956]; ?>
        <div class="phone clearfix">
            <div class="fl">
                <div class="phonetit">OUKITEL A28</div>
                <ul class="memory clearfix">
                    <li><?php echo lang('p5a1-1') ?><span><?php echo lang('p5a1-2') ?></span></li>
                    <li <?php echo in_array($language_code,array('fr','br','ru'))?'style="width:155px"':'' ?>><?php echo lang('p5a2-1') ?><span><?php echo lang('p5a2-2') ?></span></li>
                    <li class="special"><?php echo lang('p5a3-1') ?><span><?php echo lang('p5a3-2') ?></span></li>
                </ul>
                <p class="price-o"><?php echo $porduct['product_currency1'].$porduct['product_currency2'] ?><?php echo substr($porduct['product_price_market'],0,strpos($porduct['product_price_market'],'.')+1) ?><sup><?php echo substr($porduct['product_price_market'],strpos($porduct['product_price_market'],'.')+1) ?></sup></p>
                <p class="price-n"><?php echo $porduct['product_currency1'] ?><span><?php echo $porduct['product_currency2'] ?><?php echo substr($porduct['product_discount_price'],0,strpos($porduct['product_discount_price'],'.')+1) ?><sup><?php echo substr($porduct['product_discount_price'],strpos($porduct['product_discount_price'],'.')+1) ?></sup></span></p>
                <a class="shop" href="<?php echo genUrl($porduct['product_url']) ?>"><?php echo lang('shop_now') ?></a>
            </div>
            <div class="fr"><a class="pic" href="<?php echo genUrl($porduct['product_url']) ?>"><img src="http://img6.eachbuyer.com/upload/201509/2015091914562255fd07160aebe.jpg" width="302" height="360"></a></div>
        </div>
    </div>
    <!-- main end -->
</div>
<!-- main end -->
<?php include dirname(__FILE__).'/common/footer.php'; ?>
</body>
</html>