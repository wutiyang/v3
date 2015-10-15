<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/login.css?v=<?php echo STATIC_FILE_VERSION ?>">

<div class="loginPage clear" id="login">
	<h1 class="login-tit"><?php echo lang('sign_in_or_register');?></h1>
	<!-- login left -->
	<div class="shop-login login-left">
		<h3><?php echo lang('sign_in');?></h3>
		<?php if(!empty($msg_login)):?>
		<div class="success"><i></i><?php echo $msg_login;?></div>
		<?php endif;?>
        <div class="info" id="logFormError"></div>
		<form class="shop-form" action="<?php echo genURL('order_list')?>" method="post" id="loginForm">
			<div class="shop-tit"><?php echo lang('email_address');?>/<?php echo lang('nickname');?></div>
			<div class="info">
				<div class="shop-inp clear">
					<i class="icon-text"></i>
					<input id="userName" type="text" name="user_name">
				</div>
				<p class="require" id="text_info"><?php echo lang('infomation_required_tips');?></p>
			</div>
			<div class="shop-tit"><?php echo lang('password');?></div>
			<div class="info">
				<div class="shop-inp clear">
					<i class="icon-psd"></i>
					<input id="currentPassword" type="password" name="password">
				</div>
				<p class="require" id="psd_info"><?php echo lang('infomation_required_tips');?></p>
			</div>
			<div class="remember">
				<p>
					<input class="check" id="remember" type="checkbox" value="" checked><label for="remember"><?php echo lang('remember_me');?></label>
					<a class="forgot-psw" href="<?php echo genURL("forget_password");?>"><?php echo lang('forgot_password');?></a>
				</p>
			</div>
			<input type="hidden" name="refer" id="refer" value="<?php echo $refer;?>"/>
			<div class="sign"><input type="button" value="<?php echo lang('sign_in');?>" class="sign icon-view" id="sign"></div>
			<div class="or"><em class="or-border"></em><span class="or-bg"><?php echo lang('or_upper');?></span></div>
			<div class="facebook fb-login-button">
				<a class="fb icon-view" id="facebook" href="javascript:void(0);" onclick="fbLogin()"><i class="icon-fb"></i><?php echo lang('sign_in_with_facebook');?></a>
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
									source:1
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
			</div>
		</form>
	</div>
	<!-- login left end -->
	<!-- login right -->
	<div class="shop-login login-right">
		<h3><?php echo lang('register');?></h3>
		<form class="shop-form" action="<?php echo genURL('login/register')?>" method="post" id="resgisterForm">
			<div class="shop-tit"><?php echo lang('nickname');?></div>
			<div class="info <?php if(!empty($msg_register_username)):?>error<?php endif;?>">
				<div class="shop-inp clear">
					<i class="icon-text"></i>
					<input id="nickName" type="text" name="user_name">
				</div>
				 <?php if(!empty($msg_register_username)):?>
				<div class="tips"><?php echo $msg_register_username;?></div>
				 <?php endif;?>
			</div>
			<div class="shop-tit"><?php echo lang('email_address');?></div>
			<div class="info <?php if(!empty($msg_register_email)):?>error<?php endif;?>">
				<div class="shop-inp clear">
					<i class="icon-email"></i>
					<input id="email" type="text" name="email">
				</div>
				<?php if(!empty($msg_register_email)):?>
				<div class="tips"><?php echo $msg_register_email;?></div>
				 <?php endif;?>
			</div>
			<div class="shop-tit"><?php echo lang('password');?></div>
			<div class="info <?php if(!empty($msg_register_password)):?>error<?php endif;?>">
				<div class="shop-inp clear">
					<i class="icon-psd"></i>
					<input id="password" type="password" name="password">
				</div>
				<?php if(!empty($msg_register_password)):?>
				<div class="tips"><?php echo $msg_register_password;?></div>
				 <?php endif;?>
			</div>
			<div class="shop-tit"><?php echo lang('confirm_password');?></div>
			<div class="info <?php if(!empty($msg_register_confirm_password)):?>error<?php endif;?>">
				<div class="shop-inp clear">
					<i class="icon-psd"></i>
					<input id="confirmPassword" type="password" name="confirm_password">
				</div>
				<?php if(!empty($msg_register_confirm_password)):?>
				<div class="tips"><?php echo $msg_register_confirm_password;?></div>
				 <?php endif;?>
			</div>
			<div class="remember">
				<p class="info <?php if(!empty($msg_register_confirm_agreement)):?>error<?php endif;?>">
					<input class="check" id="agree" checked type="checkbox" name="agreement">
					<label for="agree"><?php echo lang('register_agree_tips');?> <span><a href="<?php echo genURL('terms_and_conditions');?>"><?php echo lang('terms_and_conditions');?></a></span></label>
					<?php if(!empty($msg_register_confirm_agreement)):?>
					<div class="tips"><?php echo $msg_register_confirm_agreement;?></div>
					 <?php endif;?>
				</p>
				<p><input class="check" id="newsletter" checked type="checkbox" name="subscribe"><label for="newsletter"><?php echo lang('register_subscribe_tips');?></label></p>
			</div>
			<input type="hidden" name="reg_from" value="1" id="reg_from"/>
			<input type="hidden" name="refer" value="<?php echo $refer;?>"/>
			<input type="submit" value="<?php echo lang('register');?>" class="sign icon-view" id="register">
		</form>
	</div>
	<!-- login right end -->
</div>

<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/javascript" src="<?php echo RESOURCE_URL?>js/common/utils.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL?>js/login.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>

</body>
</html>