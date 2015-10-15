<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/help.css?v=<?php echo STATIC_FILE_VERSION ?>">
<?php include dirname(__FILE__).'/common/help_crumbs.php'; ?>
<div class="main help">
	<?php include dirname(__FILE__).'/common/help_menu.php'; ?>
    <div class="content">
        <h2 class="help-tit"><?php echo lang('contact_us');?></h2>
        <p class="contace-tit"><?php echo lang('contact_us_description');?></p>
        <div class="con-content">
            <div class="con-left">
                <i class="con-email"></i>
                <p><?php echo lang('contact_us_email');?></p>
            </div>
            <div class="con-right">
                <i class="con-map"></i>
                <p><?php echo lang('contact_us_location');?></p>
            </div>
        </div>
        <div class="con-content con2">
            <div class="con-left">
                <i class="con-sky"></i>
                <p><?php echo lang('contact_us_skype');?></p>
            </div>
            <div class="con-right">
                <i class="con-phone"></i>
                <p><?php echo lang('contact_us_mobile');?></p>
            </div>
        </div>
        <div class="con-content">
            <div class="con-left">
                <i class="con-fb"></i>
                <p><?php echo lang('contact_us_facebook');?>
</p>
            </div>
            <div class="con-right">
                <i class="con-tw"></i>
                <p><?php echo lang('contact_us_twitter');?>
</p>
            </div>
        </div>
    </div>
</div>


<?php include dirname(__FILE__).'/common/footer.php'; ?>
</body>
</html>