<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/help.css?v=<?php echo STATIC_FILE_VERSION ?>">
<?php include dirname(__FILE__).'/common/help_crumbs.php'; ?>
<div class="main payment-method">
	<?php include dirname(__FILE__).'/common/help_menu.php'; ?>
    <div class="content payment">
        <h2 class="help-tit"><?php echo lang('payment_method');?></h2>
        <div class="help-info">
        <?php echo lang('payment_method_desc');?>
        </div>
       	<strong>
        1. <?php echo lang('payment_method_item1');?>
		</strong>
        <p>
A. <?php echo lang('payment_method_item1_A');?><br />
B. <?php echo lang('payment_method_item1_B');?><br />
C. <?php echo lang('payment_method_item1_C');?>
		</p>
    	<strong>
            2. <?php echo lang('payment_method_item2');?>
    	</strong>
        <div class="method">
            A. <?php echo lang('payment_method_item2_A');?><br />
            B. <?php echo lang('payment_method_item2_B');?><br />
            <div class="choose"><span><?php echo lang('payment_method_item2_B_select');?></span>
                <div class="divselect input-num">
                    <div>
                        <i class="icon-plus"></i>
                        <cite><?php echo lang('payment_method_item2_B_option');?></cite>
                    </div>
                    <ul>
                        <li><a href="javascript:;"><?php echo lang('payment_method_item2_B_sort1');?></a></li>
                        <li><a href="javascript:;"><?php echo lang('payment_method_item2_B_sort2');?></a></li>
                        <li><a href="javascript:;"><?php echo lang('payment_method_item2_B_sort3');?></a></li>
                        <li><a href="javascript:;"><?php echo lang('payment_method_item2_B_sort4');?></a></li>
                        <li><a href="javascript:;"><?php echo lang('payment_method_item2_B_sort5');?></a></li>
                    </ul>
                </div>
            </div>
    C. <?php echo lang('payment_method_item2_C');?>
        </div>
        <div class="payment-tit"><?php echo lang('payment_method_item2_C_1');?></div>
        <div class="payment-pic"></div>
        <p>
        D. <?php echo lang('payment_method_item2_D');?>
		<br>
		E. <?php echo lang('payment_method_item2_E');?>
        <br>F. <?php echo lang('payment_method_item2_F');?>
        </p>
        <strong>3. <?php echo lang('payment_method_item3');?></strong>
        <p class="account">
		A. <?php echo lang('payment_method_item3_A');?>
		<br>
		B. <?php echo lang('payment_method_item3_B');?>
        </p>
        <p class="payment-btm">
			<?php echo lang('payment_method_desc2');?>
		<br>
			<?php echo lang('payment_method_desc3');?>
		</p>
    </div>
</div>
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/help.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>