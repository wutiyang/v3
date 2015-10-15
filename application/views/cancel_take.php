<?php
$realmName= RESOURCE_URL;
?>
<!doctype html>
<!--[if lt IE 7]> <html class="ie6 oldIE"> <![endif]-->
<!--[if IE 7]>    <html class="ie7 oldIE"> <![endif]-->
<!--[if IE 8]>    <html class="ie8 oldIE"> <![endif]-->
<!--[if gt IE 8]><!-->
<html>
<!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <link rel="shortcut icon" href="<?php echo $realmName; ?>images/common/favicon.ico?v=20151028102610" />
    <link rel="icon" href="<?php echo $realmName; ?>images/common/animated_favicon.gif?v=20141028102610" type="image/gif" />
    <title>EachBuyer - Cool Gadgets, LED, Home &amp; Garden, Electronics at Affordable Prices, Free Shipping!</title>
    <meta name="keywords" content />
    <!-- narrow search mate is use -->
    <link rel="stylesheet" media="all" href="<?php echo $realmName; ?>css/common/common.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $realmName; ?>/css/subscription.css">
    <!--[if lt IE 9]>
    <script src="<?php echo $realmName;?>js/libs/jquery-1.11.1.js"></script>
    <![endif]-->

    <script src="<?php echo $realmName; ?>js/common/html5shiv.js"></script>
    <!--[if gte IE 9]><!-->
    <script src="<?php echo $realmName; ?>js/libs/jquery-2.1.1.js"></script>
    <!--<![endif]-->
    <script src="<?php echo $realmName; ?>/js/libs/ec.lib.js" namespace="ec"></script>

    <!--[if IE 6]><script>ol.isIE6=true;</script><![endif]-->
    <!--[if IE 7]><script>ol.isIE7=true;</script><![endif]-->
    <!--[if IE 8]><script>ol.isIE8=true;</script><![endif]-->
</head>
<script>
    //定义路径
    var baseurl='<?php echo $realmName; ?>'+'json/';
</script>

<body class="lan-us">
<div class="main cancelTake">
    <div class="take-box">
        <div class="take-logo">
            <a title="Eachbuyer.com" href="/"><img title="Eachbuyer.com" alt="Eachbuyer.com" src="<?php echo $realmName; ?>images/common/logo.png"></a>
        </div>
        

        <div class="take-ft" id="takeFt">
            <h2><?php echo lang('unsubscribe_from_eachbuyer_newsletter');?></h2>
            <p><?php echo lang('your_current_email_address');?>：<?php echo $message_email;?></p>
            <p class="co1"><?php echo lang('subscribe_newsletter_tips');?>:</p>
            <input type="hidden" name="hash" value="<?php echo $hash;?>"/>
            <input type="hidden" name="unsubscribe_mail" value="<?php echo $message_email;?>"/>
            <p><a href="javascript:;" id="unsubBtn"><?php echo lang('unsubscribe');?></a></p>
        </div>
        <div class="take-bd hide" id="takeBd">
            <div class="per-btn" id="succeeTakeFt">
                <h3><i></i><?php echo lang('unsubscribed_tips');?></h3>
                <p><?php echo lang('unsubscribed_hope_tips');?></p>
            </div>
            <div class="left take-per-have" >
                <div class="per-sub clearfix" >
                    <h2><?php echo lang('subscribe_tips');?></h2>
                    <div class="footerSubmit2"  id="footerSubmit2">
                        <span class="take-inputEmail">
                        <input type="text" data-value="Email Address" id="footEnterInputText2" class="per-text" name="email">
                        </span>
                        <button class="btn34-org" title="Subscribe" type="submit">
                            <span class="btn-right"><span class="btn-text"><?php echo lang('subscribe');?></span></span>
                        </button>
                        <div class="f-error red">
                            <p class="hide"><?php echo lang('enter_a_valid_email_tips');?></p>
                            <span class="hide"><?php echo lang('enter_your_email_tips');?></span>
                            <div class="hide"></div>
                        </div>
                    </div>
                </div>
                <h3><?php echo lang('subscribe_and_get');?></h3>
                <ul>
                    <li><i></i><a href="javascript:;"><?php echo lang('subscribe_and_get1');?></a></li>
                    <li><i></i><a href="javascript:;"><?php echo lang('subscribe_and_get2');?></a></li>
                    <li><i></i><a href="javascript:;"><?php echo lang('subscribe_and_get3');?></a></li>
                    <li><i></i><a href="javascript:;"><?php echo lang('subscribe_and_get4');?></a></li>
                    <li><i></i><a href="javascript:;"><?php echo lang('subscribe_and_get5');?></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script type="text/html" id="delete">
        <div class="box-module box-w2 delete-box" id="Pop" index="567985">
            <form action="bb" method="post" id="passwordForm">
                <div class="info">
                    <h5><?php echo lang('unsubscribe_notice_tips');?></h5>
                    <p><?php echo lang('unsubscribe_desc_tips');?></p>
                </div>
                <div class="btn-form">
                    <button type="button" index="a" title="unsubscribe" class="btn34-org btn-w86" id="btnUnsubscribe"><span class="btn-right"><span class="btn-text"><?php echo lang('unsubscribe');?></span></span></button>
                    <button type="button" title="Cancel" class="btn34-gray btn-w86 cancel"><span class="btn-right"><span class="btn-text"><?php echo lang('cancel');?></span></span></button>
                </div>
            </form>
        </div>
        </script>
<script type="text/javascript" src="<?php echo $realmName; ?>js/lang/us.js"></script>
<script type="text/javascript" src="<?php echo $realmName; ?>js/common/ec.base.js"></script>
<script type="text/javascript" src="<?php echo $realmName; ?>js/common/utils.js"></script>
<script type="text/javascript" src="<?php echo $realmName; ?>js/personal.js"></script>
</body>
</html>
