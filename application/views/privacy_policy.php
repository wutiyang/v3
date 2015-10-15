<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/help.css?v=<?php echo STATIC_FILE_VERSION ?>">
<?php include dirname(__FILE__).'/common/help_crumbs.php'; ?>
<div class="main privacy-policy">
	<?php include dirname(__FILE__).'/common/help_menu.php'; ?>
    <div class="content privew">
        <h2 class="help-tit"><?php echo lang('privacy_policy');?></h2>
        <div class="help-info">
        <?php echo lang('privacy_polic_desc');?>
        </div>
        <p><strong><?php echo lang('question1');?></strong></p>
        <p><?php echo lang('answer1');?></p>
    	<p><strong><?php echo lang('question2');?></strong></p>
        <p><?php echo lang('answer2_item1');?></p>
    	<p><span>•</span> <?php echo lang('answer2_item2');?></p>
        <p><span>•</span> <?php echo lang('answer2_item3');?> </p>
        <p><span>•</span> <?php echo lang('answer2_item4');?> </p>
        <p><span>•</span> <?php echo lang('answer2_item5');?></p>
        <p><?php echo lang('answer2_item6');?></p>
    	<p><strong><?php echo lang('question3');?></strong></p>
    	<p><?php echo lang('answer3_item1');?></p>
        <p><?php echo lang('answer3_item2');?></p>
        <p><strong><?php echo lang('question4');?></strong></p>
        <p><?php echo lang('answer4_item1');?></p>
        <p><?php echo lang('answer4_item2');?></p>
        <p><strong><?php echo lang('question5');?></strong></p>
        <p><?php echo lang('answer5');?>
		</p>
		<p><strong><?php echo lang('question6');?></strong></p>
        <p><?php echo lang('answer6');?></p>
	</div>
</div>


<?php include dirname(__FILE__).'/common/footer.php'; ?>
</body>
</html>