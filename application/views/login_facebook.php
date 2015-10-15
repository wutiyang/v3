<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/login.css?v=<?php echo STATIC_FILE_VERSION ?>">

<div class="loginPage clear" id="login">
	<h1 class="login-tit"><?php echo lang('title_email_used') ?></h1>
	<!-- login left -->
	<div class="shop-login login-left">
		<h3><?php echo lang('title_password') ?></h3>
        <div class="info" id="logFormError"></div>
		<form class="shop-form" method="post" id="loginForm">
			<div class="shop-tit"><?php echo lang('txt_email') ?>:<?php echo $facebook_email ?></div>
			<div class="shop-tit"><?php echo lang('txt_password');?></div>
			<div class="info">
				<div class="shop-inp clear">
					<i class="icon-psd"></i>
					<input id="currentPassword" type="password" name="password">
				</div>
				<p class="require" id="psd_info"><?php echo lang('infomation_required_tips');?></p>
			</div>
			<div class="remember">
				<p>
					<a href="<?php echo genURL("forget_password");?>"><?php echo lang('forgot_password');?></a>
				</p>
			</div>
			<div class="sign"><input type="button" value="<?php echo lang('txt_submit');?>" class="sign icon-view" id="sign"></div>
		</form>
		<script type="text/javascript">
		$('#sign').on('click',function(){
			$.ajax({
				url: "/login_facebook/authenticate",
				data: {password:$('#currentPassword').val()},
				type: 'POST',
				async:false, 
				dataType: 'json',
				success: function(json){
					if (json.status!="200") {
						ec.utils.addError($('#logFormError'),json.msg);	
						return false;
					}else{
						window.location.href=json.data.url;		
					}
				}	
			});
		})
		</script>
	</div>
	<!-- login left end -->

	<!-- login right -->
	<div class="shop-login login-right">
		<h3><?php echo lang('title_email') ?></h3>
        <div class="info" id="regFormError"></div>
		<form class="shop-form" method="post" id="resgisterForm">
			<div class="shop-tit"><?php echo lang('txt_email_address');?></div>
			<div class="info">
				<div class="shop-inp clear">
					<i class="icon-email"></i>
					<input id="email" type="text" name="email">
				</div>
			</div>
			<div class="remember"></div>
			<input type="button" value="<?php echo lang('txt_submit');?>" class="sign icon-view" id="register">
		</form>
	</div>
	<!-- login right end -->
	<script type="text/javascript">
	$('#register').on('click',function(){
		$.ajax({
			url: "/login_facebook/register",
			data: {email:$('#email').val()},
			type: 'POST',
			async:false, 
			dataType: 'json',
			success: function(json){
				if (json.status!="200") {
					ec.utils.addError($('#regFormError'),json.msg);	
					return false;
				}else{
					window.location.href=json.data.url;		
				}
			}	
		});
	})
	</script>
</div>

<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/javascript" src="<?php echo RESOURCE_URL?>js/common/utils.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>