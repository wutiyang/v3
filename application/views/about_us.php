<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/help.css?v=<?php echo STATIC_FILE_VERSION ?>">
<?php include dirname(__FILE__).'/common/help_crumbs.php'; ?>
<div class="main help">
	<?php include dirname(__FILE__).'/common/help_menu.php'; ?>
    <div class="content">
        <h2 class="help-tit"><?php echo lang('about_us');?></h2>
        <div class="help-info">
            <?php echo lang('about_us_description1');?>
        </div>
        <div class="help-tab">
            <div class="tab-lt">
                <h5><?php echo lang('about_us_meaning_title');?></h5>
                <p><?php echo lang('about_us_meaning_description');?></p>
            </div>
            <div class="tab-rt">
                <h5><?php echo lang('what_can_offer_title');?></h5>
                <p><?php echo lang('what_can_offer_description');?>
</p>
            </div>
        </div>
        <p class="serve"><?php echo lang('about_us_description2');?></p>
        <p class="serve"><?php echo lang('about_us_description3');?></p>
    </div>
</div>


<?php include dirname(__FILE__).'/common/footer.php'; ?>
</body>
</html>