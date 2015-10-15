<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/subscription.css?v=<?php echo STATIC_FILE_VERSION ?>">

<div class="main subscription">
	<div class="subscription-left">
		<h2 class="success"><i></i><?php echo lang('subscribe_succ');?><?php if($newsletter_first!=true) echo ' & Welcome Back'; ?>!</h2>
		<div class="left">
			<div class="left-tit"><?php echo lang('subscribe_succ_description1');?></div>
			<p><?php echo sprintf(lang('subscribe_succ_description2'),$email);?></p>
			<p><?php echo lang('subscribe_succ_description3');?></p>
		</div>
		<div class="line"></div>
		<div class="left have">
			<h3><?php echo lang('what_you_have_got');?></h3>
            <?php
            if(isset($newsletter_first) && $newsletter_first==true){
                ?>
			<ul class="first">
				<li><i></i><?php echo lang('what_you_have_got1');?></li>
				<li>
					<i></i><?php echo sprintf(lang('what_you_have_got2'), isset($coupon_code)?$coupon_code:'',isset($coupon_time)?$coupon_time:'');?>
					<p><?php echo lang('what_you_have_got2_description');?></p>
				</li>
				<li><i></i><?php echo lang('what_you_have_got3');?></li>
				<li><i></i><?php echo lang('what_you_have_got4');?></li>
				<li><i></i><?php echo lang('what_you_have_got5');?></li>
				<li><i></i><?php echo lang('what_you_have_got6');?></li>
			</ul>
            <?php
            }else{
                ?>
            <ul class="second">
                <li><i></i><?php echo lang('what_you_have_got3');?></li>
                <li><i></i><?php echo lang('what_you_have_got4');?></li>
                <li><i></i><?php echo lang('what_you_have_got5');?></li>
                <li><i></i><?php echo lang('what_you_have_got6');?></li>
            </ul>
            <?php
            }
            ?>
		</div>
	</div>
	<!-- left end -->
	<div class="subscription-right">
		<div class="right-main clearfix">
			<div class="right-sus">
				<h3 class="title"><?php echo lang('subscribe_title');?></h3>
				<p class="sus-tit"><?php echo lang('subscribe_description1');?></p>
				<p><?php echo lang('subscribe_description2');?></p>
			</div>
			<div class="list">
				<h3><?php echo lang('winners_list');?></h3>
				<p>de******@gmail.com</p>
				<p>ser******@hotmail.com</p>
				<p>gerio******@Yahoo.com </p>
			</div>
		</div>
		<div class="sus-img"></div>
	</div>
</div>

<?php include dirname(__FILE__).'/common/footer.php'; ?>
</body>
</html>
