<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="/favicon.ico?v=<?php echo STATIC_FILE_VERSION ?>" />
<link rel="icon" href="/animated_favicon.gif?v=<?php echo STATIC_FILE_VERSION ?>" type="image/gif" />
</head>
<body>
<?php echo lang('paypal_redirect') ?>
<form id="paypal_standard_checkout" action="<?php echo $url ?>" method="post">
	<input type='hidden' name='cmd' value='_xclick' />
	<input type='hidden' name='business' value='<?php echo $pay_account ?>' />
	<input type='hidden' name='item_name' value='<?php echo $order['order_code'] ?>' />
	<input type='hidden' name='amount' value='<?php echo $order['order_price'] ?>' />
	<input type='hidden' name='currency_code' value='<?php echo $order['order_currency'] ?>' />
	<input type='hidden' name='return' value='<?php echo $url_return ?>' />
	<input type='hidden' name='invoice' value='<?php echo $order['order_code'] ?>' />
	<input type='hidden' name='charset' value='utf-8' />
	<input type='hidden' name='no_shipping' value='1' />
	<input type='hidden' name='no_note' value='' />
	<input type='hidden' name='notify_url' value='<?php echo $url_notify ?>' />
	<input type='hidden' name='rm' value='2' />
	<input type='hidden' name='cancel_return' value='<?php echo $url_cancel ?>' />
	<input type='submit' value='<?php echo lang('paypal_button') ?>' />
</form>
<script type="text/javascript">document.getElementById("paypal_standard_checkout").submit();</script>
</body>
</html>