<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/help.css?v=<?php echo STATIC_FILE_VERSION ?>">
<?php include dirname(__FILE__).'/common/help_crumbs.php'; ?>
<div class="main help">
	<?php include dirname(__FILE__).'/common/help_menu.php'; ?>
    <div class="content">
        <h2 class="help-tit"><?php echo lang('help');?></h2>
        <div class="help-list">
        <ul>
        	<li><a href="<?php echo genUrl('about_us.html') ?>"><i class="icon-1"></i><p><?php echo lang('help_tips1');?></p></a></li>
            <li><a href="<?php echo genUrl('return_policy.html') ?>"><i class="icon-2"></i><p><?php echo lang('help_tips2');?></p></a></li>
            <li><a href="<?php echo genUrl('privacy_policy.html') ?>"><i class="icon-3"></i><p><?php echo lang('help_tips3');?></p></a></li>
            <li><a href="<?php echo genUrl('faq.html') ?>"><i class="icon-4"></i><p><?php echo lang('help_tips4');?></p></a></li>
			<li><a href="<?php echo genUrl('payment_method.html') ?>"><i class="icon-5"></i><p><?php echo lang('help_tips5');?></p></a></li>
			<li><a href="<?php echo genUrl('contact_us.html') ?>"><i class="icon-6"></i><p><?php echo lang('help_tips6');?></p></a></li>
            <li><a href="<?php echo genUrl('shipping_method_guide.html') ?>"><i class="icon-7"></i><p><?php echo lang('help_tips7');?></p></a></li>
			<li><a href="<?php echo genUrl('terms_and_conditions.html') ?>"><i class="icon-8"></i><p><?php echo lang('help_tips8');?></p></a></li>
        </ul>
        </div>
    </div>
</div>
<?php include dirname(__FILE__).'/common/footer.php'; ?>
</body>
</html>