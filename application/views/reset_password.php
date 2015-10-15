<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/login.css?v=<?php echo STATIC_FILE_VERSION ?>">

<div class="forgetPage clear">
	<h1 class="login-tit"><?php echo lang('reset_your_password');?></h1>
	<!-- login left -->
	<div class="shop-login">
		<h3><?php echo lang('reset_your_password');?></h3>
		<div class="title"></div>
		<form class="shop-form forget" action="/reset_password/process" method="post" id="updateEamilForm">
			<input type="hidden" value="<?php echo $codes;?>" name="code"/>
			<input type="hidden" value="<?php echo $user_id;?>" name="user_id"/>
			<div class="shop-tit"><?php echo lang('new_password');?></div>
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
					<input id="confirmPassword" type="password" name="password_confirm">
				</div>
			</div>
			<div class="sign"><input type="button" value="<?php echo lang('submit');?>" class="sign sub icon-view" id="updataEamil"></div>
		</form>
	</div>
	<!-- login left end -->
</div>


<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/common/utils.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/login.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>