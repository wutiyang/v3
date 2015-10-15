<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL?>css/wholesale.css?v=<?php echo STATIC_FILE_VERSION ?>">
<div class="wrap breadcrumbs">
    <i class="icon-home">&nbsp;</i>
    <a class="vam" href="<?php echo genURL('/')?>"><?php echo lang('home');?></a>
    <i class="icon-arr-right">&nbsp;</i>
    <span class="vam"><?php echo lang('wholesale');?></span>
</div>
<!--main start-->
<div class="wrap wholesale" id="wholesale">
	<div class="title"><?php echo lang('wholesale_question');?></div>
    <p class="space"><?php echo lang('wholesale_answer_desc');?></p>
    <p>1 <?php echo lang('wholesale_answer1');?></p>
    <p>2 <?php echo lang('wholesale_answer2');?></p>
    <p>3 <?php echo lang('wholesale_answer3');?></p>
	<p>4 <?php echo lang('wholesale_answer4');?></p>
	<p>5 <?php echo lang('wholesale_answer5');?></p>
	<P>6 <?php echo lang('wholesale_answer6');?></P>
	<p>7 <?php echo lang('wholesale_answer7');?></p>
    <h5><?php echo lang('wholesale_procedure');?></h5>
    <div class="flow clearfix">
    	<div class="flow-email">
        	<div class="flow-hd"><?php echo lang('wholesale_procedure_email');?></div>
            <div class="flow-cn"></div>
            <div class="flow-ft"><?php echo lang('wholesale_procedure_email_desc');?></div>
        </div>
        <div class="arrows space-bargaining">&nbsp;</div>
        <div class="flow-bargaining">
        	<div class="flow-hd"><?php echo lang('wholesale_procedure_bargaining');?></div>
            <div class="flow-cn"></div>
            <div class="flow-ft"><?php echo lang('wholesale_procedure_bargaining_desc');?></div>
        </div>
        <div class="arrows space-pay">&nbsp;</div>
        <div class="flow-pay">
        	<div class="flow-hd"><?php echo lang('wholesale_procedure_pay');?>*</div>
            <div class="flow-cn"></div>
            <div class="flow-ft"><?php echo lang('wholesale_procedure_pay_desc');?></div>
        </div>
        <div class="arrows space-transit">&nbsp;</div>
        <div class="flow-transit">
        	<div class="flow-hd"><?php echo lang('wholesale_procedure_transit');?></div>
            <div class="flow-cn"></div>
            <div class="flow-ft"><?php echo lang('wholesale_procedure_transit_desc');?></div>
        </div>
        <div class="arrows space-receive">&nbsp;</div>
        <div class="flow-receive">
        	<div class="flow-hd"><?php echo lang('wholesale_procedure_receive');?></div>
            <div class="flow-cn"></div>
            <div class="flow-ft"><?php echo lang('wholesale_procedure_receive_desc');?></div>
        </div>
    </div>
    <p>* <?php echo lang('wholesale_payment_tips');?></p>
	<p><?php echo lang('wholesale_paypal_id');?></p>
    <!--英语外的其他语种请在下面这个p标签上加 class="hide" 隐藏，或者程序直接判断是否输出-->
	<p><?php echo lang('wholesale_bank');?></p>
    <h5><?php echo lang('wholesale_contact_tips');?></h5>
    <p><?php echo lang('wholesale_contact_email');?></p>
	<p><?php echo lang('wholesale_contact_skype');?></p>
</div>
<!--main end-->
<?php include dirname(__FILE__).'/common/footer.php'; ?>
</body>
</html>