<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL?>css/login.css?v=<?php echo STATIC_FILE_VERSION ?>">

<div class="forgetPage clear">
	<h1 class="login-tit"><?php echo lang('forget_your_password');?></h1>
	<!-- login left -->
	<div class="shop-login">
		<h3><?php echo lang('reset_your_password');?></h3>

		<?php if(!empty($tips)):?>
		<div class="success"><i></i><?php echo $tips;?></div>
		<?php endif;?>

		<div class="title"><?php echo lang('enter_email_send_new_link_tips');?></div>
		<form class="shop-form forget" action="<?php echo genURL('/forget_password/findPassword');?>" method="post" id="updateEamilForm">
			<div class="shop-tit"><?php echo lang('email_address');?></div>
			<div class="info">
				<div class="shop-inp clear">
					<i class="icon-email"></i>
					<input id="email1" type="text" name="email">
				</div>
			</div>
			<div class="sign"><input type="button" value="<?php echo lang('submit');?>" class="sign sub icon-view" id="updataEamil"></div>

		</form>
	</div>
	<!-- login left end -->
</div>


<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/javascript" src="<?php echo RESOURCE_URL?>js/common/utils.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL?>js/login.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>
