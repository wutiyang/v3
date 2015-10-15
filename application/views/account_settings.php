<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL?>css/order_list.css?v=<?php echo STATIC_FILE_VERSION ?>">
<div class="wrap breadcrumbs">
    <i class="icon-home">&nbsp;</i>
    <a href="<?php echo genURL('/')?>"><?php echo lang('home');?></a>
    <i class="icon-arr-right">&nbsp;</i>
    <a href="<?php echo genURL('order_list');?>"><?php echo lang('my_account');?></a>
    <i class="icon-arr-right">&nbsp;</i>
    <span><?php echo lang('account_setting');?></span>
</div>
<div class="main account_settings" id="accountSettings">
	<?php include dirname(__FILE__).'/account/nav.php'; ?>
 	<div class="content">
   		<h3 class="column-now"><?php echo lang('account_setting');?></h3>
		<div class="box-module box-w1">
        	<div class="info"><?php echo lang('nickname');?>:<strong><?php echo $username;?></strong></div>
            <div class="btn-form">
            	<button type="button" title="Edit" class="btn30-org btn-w65" id="editUserName"><span class="btn-right"><span class="btn-text"><?php echo lang('edit');?></span></span></button>
            </div>
        </div>
        <div class="box-module box-w2">
        	<div class="info"><?php echo lang('email_address');?>:<strong><?php echo $email;?></strong></div>
            <div class="btn-form">
            	<button type="button" title="Edit" class="btn30-org btn-w65" id="editEamil"><span class="btn-right"><span class="btn-text"><?php echo lang('edit');?></span></span></button>
            </div>
        </div>
        <div class="box-module box-w1">
        	<input type="hidden" name="" value="<?php echo $username;?>" id="userName"/>
        	<div class="info"><?php echo lang('password');?>:***********</div>
            <div class="btn-form">
            	<button type="button" title="Edit" class="btn30-org btn-w65" id="editPassword"><span class="btn-right"><span class="btn-text"><?php echo lang('edit');?></span></span></button>
            </div>
        </div>
        <script type="text/html" id="editUserNameBox">
        <div class="box-module nickname-box" id="Pop">
        	<form action="#" method="post" id="userNameForm">
                <div class="info"><input type="text" class="input-text i-w1" id="nickName" name="nickName" title="Nickname" data-value="<?php echo $username;?>"/></div>
                <div class="btn-form">
                    <button type="button" title="Save" class="btn34-org btn-w86" id="updateName"><span class="btn-right"><span class="btn-text"><?php echo lang('save');?></span></span></button>
                    <button type="button" title="Cancel" class="btn34-gray btn-w86 cancel"><span class="btn-right"><span class="btn-text"><?php echo lang('cancel');?></span></span></button>
                </div>
            </form>
        </div>
		</script>
        <script type="text/html" id="editEamilBox">
		<div class="box-module email-box" id="Pop">
        	<form action="#" method="post" id="emailForm">
                <div class="info"><input type="text" class="input-text i-w1" id="email" name="email" title="Email address" data-value="<?php echo $email;?>" /></div>
                <div class="btn-form">
                    <button type="button" title="Save" class="btn34-org btn-w86" id="updateEmail"><span class="btn-right"><span class="btn-text"><?php echo lang('save');?></span></span></button>
                    <button type="button" title="Cancel" class="btn34-gray btn-w86 cancel" ><span class="btn-right"><span class="btn-text"><?php echo lang('cancel');?></span></span></button>
                </div>
            </form>
        </div>
		</script>
        <script type="text/html" id="editPasswordBox">
        <div class="box-module password-box" id="Pop">
        	<form action="#" method="post" id="passwordForm">
                <div class="info">
					<input type="password" class="input-text i-w1" id="currentPassword" name="currentPassword" title="Current password" data-value="<?php echo lang('current_password');?>" />
				</div>
                <div class="info space2"><input type="password" class="input-text i-w1" id="password" name="password" title="New Password" data-value="<?php echo lang('new_password');?>" /></div>
                <div class="info space2"><input type="password" class="input-text i-w1" id="confirmPassword" name="confirmPassword" title="Comfirm New password" data-value="<?php echo lang('confirm_new_password');?>" /></div>
                <div class="btn-form">
                    <button type="button" title="Save" class="btn34-org btn-w86" id="updatePassword"><span class="btn-right"><span class="btn-text"><?php echo lang('save');?></span></span></button>
                    <button type="button" title="Cancel" class="btn34-gray btn-w86 cancel"><span class="btn-right"><span class="btn-text"><?php echo lang('cancel');?></span></span></button>
                </div>
            </form>
        </div>
		</script>
        
    </div>
</div>
<!--main end-->
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script src="<?php echo RESOURCE_URL?>js/common/utils.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script src="<?php echo RESOURCE_URL?>js/account_settings.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>