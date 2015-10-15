<!-- footer start -->
	<div class="footer">
		<div class="wrap">
			<div class="footer-service clear">
				<ul>
					<li><img src="<?php echo RESOURCE_URL?>images/common/footer-info1.png?v=<?php echo STATIC_FILE_VERSION ?>"><strong><?php echo lang('free_shipping');?></strong></li>
					<li><img src="<?php echo RESOURCE_URL?>images/common/footer-info2.png?v=<?php echo STATIC_FILE_VERSION ?>"><strong><?php echo lang('20_local_warehouse');?></strong></li>
					<li><img src="<?php echo RESOURCE_URL?>images/common/footer-info3.png?v=<?php echo STATIC_FILE_VERSION ?>"><strong><?php echo lang('30_days_free_returns');?></strong></li>
					<li class="border"><img src="<?php echo RESOURCE_URL?>images/common/footer-info4.png?v=<?php echo STATIC_FILE_VERSION ?>"><strong><?php echo lang('365_days_quality_warranty');?></strong></li>
				</ul>
			</div>
			<div class="bottom-links">
				<a href="javascript:;" rel="nofollow"><img src="<?php echo RESOURCE_URL?>images/common/bottom-ico-1.png?v=<?php echo STATIC_FILE_VERSION ?>" alt="credit card payment"/></a>
				<a href="javascript:;" rel="nofollow"><img src="<?php echo RESOURCE_URL?>images/common/bottom-ico-2.png?v=<?php echo STATIC_FILE_VERSION ?>" alt="paypal payment"/></a>
				<a href="javascript:;" rel="nofollow"><img src="<?php echo RESOURCE_URL?>images/common/bottom-ico-3.png?v=<?php echo STATIC_FILE_VERSION ?>" alt="paypal verified"/></a>
				<a href="javascript:;" rel="nofollow"><img src="<?php echo RESOURCE_URL?>images/common/bottom-ico-4.png?v=<?php echo STATIC_FILE_VERSION ?>" alt=""/></a>
				<a href="javascript:;" rel="nofollow"><img src="<?php echo RESOURCE_URL?>images/common/bottom-ico-5.png?v=<?php echo STATIC_FILE_VERSION ?>" alt="ems &amp; dhl shipment"/></a>
				<a href="javascript:;" rel="nofollow"><img src="<?php echo RESOURCE_URL?>images/common/bottom-ico-8.png?v=<?php echo STATIC_FILE_VERSION ?>" alt=""/></a>
			</div>
			<div class="bottom-copyright">
				<address>2012-2015 Eachbuyer.com. <?php echo lang('copyright');?></address>
			</div>
			<div id="toolBox" class="tool-box">
				<a href="javascript:;" class="to-top list" id="gotop">top</a>
			</div>
		</div>
		
	</div>
<!-- footer end -->


	<!-- loading img -->
	<img src="<?php echo RESOURCE_URL?>images/common/loading/60_60.gif?v=<?php echo STATIC_FILE_VERSION ?>" width="1" height="1" class="hide" />

<script src="<?php echo RESOURCE_URL?>js/lang/us.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script src="<?php echo RESOURCE_URL?>js/common/ec.base.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<?php
if(isset($dataLayerProducts) && $dataLayerProducts){
    ?>
    <script>
        dataLayer.push({
            'ecommerce': {
                'currencyCode': 'USD',
                'impressions': <?php echo $dataLayerProducts;?>
            }
        });
    </script>
<?php
}
?>
<script type="text/html" id="loginPop">
        <div class="login-reg" id="Pop">
            <div class="shop-login">
                <a href="javascript:;" title="Close" class="close" id="close">关闭</a>
                <h1><?php echo lang('please_sign_in');?></h1>
                <div class="shop-tab">
                    <a href="javascript:;"><?php echo lang('returning_customer');?></a>
                    <a href="javascript:;"><?php echo lang('new_customer');?></a>
                </div>
               	<div class="info"id="logFormError"></div>
                <div class="shop-menu">
                    <form class="shop-form" action="<?php echo genURL('order_list')?>" method="post" id="loginForm">
                        <div class="shop-tit"><?php echo lang('email_address');?>/<?php echo lang('nickname');?></div>
                        <div class="info">
                            <div class="shop-inp clear">
                                <i class="icon-text"></i>
                                <input id="userName" type="text" name="userName">
                            </div>
                            <p class="require" id="text_info"><?php echo lang('infomation_required_tips');?></p>
                        </div>
                        <div class="shop-tit"><?php echo lang('password');?></div>
                        <div class="info">
                            <div class="shop-inp clear">
                                <i class="icon-psd"></i>
                                <input id="currentPassword" type="password" name="currentPassword">
                            </div>
                            <p class="require" id="psd_info"><?php echo lang('infomation_required_tips');?></p>
                        </div>
                        <div class="remember">
                            <p>
                                <input class="check" id="remember" type="checkbox" value="remember" checked><label for="remember"><?php echo lang('remember_me');?></label>
                                <a class="forgot-psw" href="<?php echo genURL('forget_password');?>"><?php echo lang('forgot_password');?></a>
                            </p>
                        </div>
                        <div class="sign"><input type="button" value="<?php echo lang('sign_in');?>" class="sign icon-view" id="SIGN"></div>
                        <div class="or"><em class="or-border"></em><span class="or-bg"><?php echo lang('or_upper');?></span></div>
                        <div class="facebook">
                            <a class="fb icon-view" id="facebook" href="javascript:void(0);" onclick="fbLogin()"><i class="icon-fb"></i><?php echo lang('sign_in_with_facebook');?></a>
                        </div>
                    </form>
                    <form class="shop-form" action="a" method="post" id="resgisterForm" style="display:none">
                        <div class="shop-tit"><?php echo lang('nickname');?></div>
                        <div class="info">
                            <div class="shop-inp clear">
                                <i class="icon-text"></i>
                                <input id="nickName" type="text" name="nickName">
                            </div>
                        </div>
                        <div class="shop-tit"><?php echo lang('email_address');?></div>
                        <div class="info">
                            <div class="shop-inp clear">
                                <i class="icon-email"></i>
                                <input id="email" type="text" name="email">
                            </div>
                        </div>
                        <div class="shop-tit"><?php echo lang('password');?></div>
                        <div class="info">
                            <div class="shop-inp clear">
                                <i class="icon-psd"></i>
                                <input id="password" type="password" name="password">
                            </div>
                        </div>
                        <div class="shop-tit"><?php echo lang('confirm_password');?></div>
                        <div class="info">
                            <div class="shop-inp clear">
                                <i class="icon-psd"></i>
                                <input id="confirmPassword" type="password" name="confirmPassword">
                            </div>
                        </div>
                        <div class="remember">
                            <p class="info"><input class="check" id="agree" checked="checked" type="checkbox"><label for="agree"><?php echo lang('register_agree_tips');?> <span><?php echo lang('terms_and_conditions');?></span></label></p>
                            <p><input class="check" id="newsletter" checked="checked" type="checkbox"><label for="newsletter"><?php echo lang('register_subscribe_tips');?></label></p>
                        </div>
                        <input type="button" value="<?php echo lang('register');?>" class="sign icon-view" id="REGISTER">
                    </form>
                </div>
            </div>
        </div>
</script>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3&appId=1390176774575549";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<script type="text/javascript">
function statusChangeCallback(response) {
    if(response.status === 'connected') {
        FB.api('/me', function(response) {
            if(!response.id || !response.email){
                window.location.reload();
                return;
            }
            $.ajax({
                url: "/login/facebook",
                data: {
                    facebook_id:response.id,
                    facebook_name:response.name,
                    facebook_email:response.email,
                    refer:window.location.href,
                    source:3
                },
                type: 'POST',
                async:false,  
                dataType: 'json',
                success: function(json){
                    if (json.status!="200") {
                        window.location.reload();
                    }
                    window.location.href=json.data.url;
                }   
            });
        });
    }else{
        window.location.reload();
    }
}
function fbLogin(){
    FB.login(statusChangeCallback,{scope:"email"});
}
</script>