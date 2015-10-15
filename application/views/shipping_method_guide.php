<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/help.css?v=<?php echo STATIC_FILE_VERSION ?>">
<?php include dirname(__FILE__).'/common/help_crumbs.php'; ?>
<div class="main shipping-method-guide">
	<?php include dirname(__FILE__).'/common/help_menu.php'; ?>
    <div class="content">
        <h2 class="help-tit"><?php echo lang('shipping_method_guide');?></h2>
    	<div class="list-mob fl">
        	<strong><?php echo lang('airmail');?></strong>
            <p><?php echo lang('airmail_desc_1');?><br>
            <?php echo lang('airmail_desc_2');?><br>
            <?php echo lang('airmail_desc_3');?></p>
			<strong><?php echo lang('standard');?></strong>
            <p>
            <?php echo lang('standard_desc_1');?><br>
            <?php echo lang('standard_desc_2');?><br>
            <?php echo lang('standard_desc_3');?><br>
            <?php echo lang('standard_desc_4');?><br>
            <?php echo lang('standard_desc_5');?>
            </p>
            <strong><?php echo lang('express');?></strong>
            <p>
           <?php echo lang('express_desc_1');?><br>
          <?php echo lang('express_desc_2');?> 
            </p>
        </div>
        <div class="list-mob fr">
        	<strong><?php echo lang('shipping_insurance');?></strong>
            <p>
            <?php echo lang('shipping_insurance_desc_1');?>
            </p>
            <strong><?php echo lang('tax');?></strong>
            <p>
            <?php echo lang('tax_desc_1');?>
            </p>
            <strong><?php echo lang('cpf_cnpj');?></strong>
            <p>
            <?php echo lang('cpf_cnpj_desc_1');?><br>
            <?php echo lang('cpf_cnpj_desc_2');?>
            </p>
        </div>
    </div>
</div>


<?php include dirname(__FILE__).'/common/footer.php'; ?>
</body>
</html>