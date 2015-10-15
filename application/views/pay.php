<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
<link rel="shortcut icon" href="/favicon.ico?v=<?php echo STATIC_FILE_VERSION ?>" />
<link rel="icon" href="/animated_favicon.gif?v=<?php echo STATIC_FILE_VERSION ?>" type="image/gif" />
<style type="text/css">
	.loadtext{
		margin-top: 150px;
		padding-top: 110px;
		line-height: 20px;
		text-align: center;
		font-size: 16px;
		font-family: Arial, Helvetica, sans-serif;
		background: url(/resource/default/images/common/topay_loading.gif) center 0 no-repeat;
	}
	@media screen and (max-width: 800px) {
		.loadtext{
			margin-top: 50px;
			padding-top: 65px;
			background-size: 52px 52px;
		}
	}
</style>
</head>
<body>

<p class="loadtext"><?php echo lang('payment_redirect_note') ?></p>
<form id="redirect_form" action="<?php echo $url ?>" method="post">
	<?php foreach($params as $key => $value){ ?>
	<input type="hidden" name="<?php echo $key ?>" value="<?php echo $value ?>" />
	<?php } ?>
</form>
<script type="text/javascript">document.getElementById("redirect_form").submit();</script>
</body>
</html>