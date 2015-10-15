<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/order_list.css?v=<?php echo STATIC_FILE_VERSION ?>">
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/subscription.css?v=<?php echo STATIC_FILE_VERSION ?>">
<div class="main" id="first">
	<div class="first">
        <h3><i></i><?php echo lang('just_one_more_step');?></h3>
        <p><?php echo sprintf(lang('just_one_more_step_description1'),$message_email);?></p>
        <p><strong><?php echo lang('just_one_more_step_description2');?></strong></p>
        <p><?php echo lang('just_one_more_step_description3');?></a></p>   
        <div><a class="icon-view icon-view-dis" id="continue" href="<?php echo genURL('/')?>"><?php echo lang('continue_shopping');?></a></div>
    </div>
</div>
<!--main end-->
<?php include dirname(__FILE__).'/common/footer.php'; ?>
</body>
</html>
