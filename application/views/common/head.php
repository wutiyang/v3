<!doctype html>
<!--[if lt IE 7]> <html class="ie6 oldIE"> <![endif]-->
<!--[if IE 7]>    <html class="ie7 oldIE"> <![endif]-->
<!--[if IE 8]>    <html class="ie8 oldIE"> <![endif]-->
<!--[if gt IE 8]><!-->
<html>
<!--<![endif]-->
<head>
<?php 
if(isset($title)){
?>
<title><?php echo $title; ?></title>
<?php }else{?>
<title><?php echo lang('shop_title'); ?></title>
<?php 
}
if(isset($seo_keywords)){
?>
<meta name="keywords" content="<?php echo $seo_keywords; ?>" />
<?php }?>
<?php 
if(isset($description)){
?>
<meta name="description" content="<?php echo $description; ?>" />
<?php 
}?>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="msvalidate.01" content="C4D1A74EB0279310FD569745CB65846A" />
<?php if(isset($head) && isset($head['canonical'])){ ?>
<link rel='canonical' href='<?php echo $head['canonical'] ?>' />
<?php } ?>
<?php if(isset($head) && isset($head['alternate_list'])){ ?>
	<?php if(!(isset($noAlternateList) && $noAlternateList == true)){?>
		<?php foreach($head['alternate_list'] as $code => $url){ ?>
		<link rel="alternate"<?php echo $code=='m'?'':' hreflang="'.$code.'"' ?> href="<?php echo $url ?>" />
		<?php } ?>
	<?php } ?>

<?php } ?>
<link rel="shortcut icon" href="<?php echo $current_page=='success'&&SSL_ENABLED?RESOURCE_URL_SSL:RESOURCE_URL ?>images/common/favicon.ico?v=<?php echo STATIC_FILE_VERSION ?>" />
<link rel="icon" href="<?php echo $current_page=='success'&&SSL_ENABLED?RESOURCE_URL_SSL:RESOURCE_URL ?>images/common/animated_favicon.gif?v=<?php echo STATIC_FILE_VERSION ?>" type="image/gif" />
<link rel="stylesheet" media="all" href="<?php echo $current_page=='success'&&SSL_ENABLED?RESOURCE_URL_SSL:RESOURCE_URL ?>css/common/common.css?v=<?php echo STATIC_FILE_VERSION ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo $current_page=='success'&&SSL_ENABLED?RESOURCE_URL_SSL:RESOURCE_URL ?>css/<?php echo $current_page ?>.css?v=<?php echo STATIC_FILE_VERSION ?>">
<!--[if lt IE 9]>
<script src="<?php echo $current_page=='success'&&SSL_ENABLED?RESOURCE_URL_SSL:RESOURCE_URL ?>js/libs/jquery-1.11.1.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<![endif]-->
<script src="<?php echo $current_page=='success'&&SSL_ENABLED?RESOURCE_URL_SSL:RESOURCE_URL ?>js/common/html5shiv.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<!--[if gte IE 9]><!-->
<script src="<?php echo $current_page=='success'&&SSL_ENABLED?RESOURCE_URL_SSL:RESOURCE_URL ?>js/libs/jquery-2.1.1.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<!--<![endif]-->
<script src="<?php echo $current_page=='success'&&SSL_ENABLED?RESOURCE_URL_SSL:RESOURCE_URL ?>js/libs/ec.lib.js?v=<?php echo STATIC_FILE_VERSION ?>" namespace="ec"></script>
<!--[if IE 6]><script>ol.isIE6=true;</script><![endif]-->
<!--[if IE 7]><script>ol.isIE7=true;</script><![endif]-->
<!--[if IE 8]><script>ol.isIE8=true;</script><![endif]-->


<?php if($google_tag_params){ ?>
<script type="text/javascript">
var google_tag_params = <?php echo $google_tag_params;?>;

var dataLayer = window['dataLayer'] || [];
dataLayer = [{
	google_tag_params: window.google_tag_params
}];
</script>
<?php } ?>

<?php if(isset($tongji_userdata) && !empty($tongji_userdata)){ ?>
<script>
	dataLayer = [<?php echo $tongji_userdata;?>];
</script>
<?php } ?>

<?php 
if(isset($ga_dataLayer) && $ga_dataLayer){
?>
<script>
dataLayer = [<?php echo $ga_dataLayer;?>];
</script>
<?php	
}
?>

<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-PTWPVK"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PTWPVK');</script>
<!-- End Google Tag Manager -->
</head>
<body class="lan-<?php echo $language_code ?>">