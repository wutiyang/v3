<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/order_list.css?v=<?php echo STATIC_FILE_VERSION ?>">
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/subscription.css?v=<?php echo STATIC_FILE_VERSION ?>">
<div class="wrap breadcrumbs">
    <i class="icon-home">&nbsp;</i>
    <a href="<?php echo genURL('/')?>"><?php echo lang('home');?></a>
    <i class="icon-arr-right">&nbsp;</i>
    <a href=""><?php echo lang('my_account');?></a>
    <i class="icon-arr-right">&nbsp;</i>
    <span><?php echo lang('newsletter_subscriptions');?></span>
</div>
<div class="main rewards" id="">
	<?php include dirname(__FILE__).'/account/nav.php'; ?>
 	<div class="content personal">
   		<h3 class="column-now"><?php echo lang('newsletter_subscriptions');?></h3>
        <!-- subscribe email information -->
        <div class="per-email subscribe <?php if($subscribe!==true || $validate !== '1') echo 'hide';?>">
            <?php echo sprintf(lang('email_subscribed_tips'),$email);?>
            <a class="unsubscribe" href="javascript:;" id="unsubscribe"><?php echo lang('unsubscribe');?></a>
        </div>
        <!-- unsubscribe information -->
        <div class="per-email per-btn hide">
            <h3><i></i><?php echo lang('unsubscribed_tips');?></h3>
            <p><?php echo lang('unsubscribed_hope_tips');?></p>
        </div>
        <!-- subscribe message -->
        <div class="left per-have ">
            <div class="per-sub clearfix">
                <h2><?php echo lang('subscribe_tips');?></h2>
                <div class="footerSubmit2 <?php if($subscribe===true && $validate == '1') echo 'hide';?>"  id="footerSubmit2">
                    <div class="takebox">
                        <span class="take-inputEmail">
                        <input type="text" data-value="Email Address" id="footEnterInputText2" class="per-text" name="email">
                        </span>
                        <button class="btn34-org" title="Subscribe" type="submit">
                            <span class="btn-right"><span class="btn-text"><?php echo lang('subscribe');?></span></span>
                        </button>
                    </div>
                    <div class="f-error red">
                        <p class="hide"><?php echo lang('subscribe');?><?php echo lang('enter_a_valid_email_tips');?></p>
                        <span class="hide"><?php echo lang('subscribe');?><?php echo lang('enter_your_email_tips');?></span>
                        <div class="hide"></div>
                    </div>
                </div>
            </div>
            <h3><?php echo lang('what_you_have_got');?></h3>
            <ul>
                <li><i></i><?php echo lang('what_you_have_got1');?></li>
                <?php if($subscribe_status_coupon == '0' && $validate == '1'){ ?>
                <li>
                    <i></i><a hrehf="javascript:;"><?php echo sprintf(lang('what_you_have_got2'), $coupon_code);?>(Valid until <?php echo $coupon_time;?>)</a>
                    <p><?php echo lang('what_you_have_got2_description');?></p>
                </li>
                <?php } ?>
                <li><i></i><?php echo lang('what_you_have_got3');?></li>
                <li><i></i><?php echo lang('what_you_have_got4');?></li>
                <li><i></i><?php echo lang('what_you_have_got5');?></li>
                <li><i></i><?php echo lang('what_you_have_got6');?></li>
            </ul>
        </div>
        <div class="per-bottom">
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
</div>
<!--main end-->
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/html" id="emailTips">
<div class="per-confirm" id="Pop">
    <a href="javascript:;" title="Close" class="close" id="close">关闭</a>
    <h3><?php echo lang('earn_more_rewards_tips');?></h3>
    <div class="btn-form">
        <a class="btn34-gray btn-text" href="<?php echo genURL('/');?>"><?php echo lang('go_shopping');?></a>
        <span class="or"><?php echo lang('or_upper');?></span>
        <a class="btn34-org btn-text" href="<?php echo genURL('review_create');?>"><?php echo lang('write_a_review');?></a>
    </div>
</div>
</script>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/common/utils.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/personal.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>
