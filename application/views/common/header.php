<?php include dirname(__FILE__).'/head.php'; ?>

<div id="topbar" class="topbar">
	<div class="wrap">
	<div class="bar-right">
	<em class="bor">|</em>
	<?php if(isset($header) && isset($header['currency_list'])){ ?>
	<div class="currency-switch">
	<div class="select-block">
		<div class="selected">
			<a rel="nofollow" href="javascript:;" title="Currency"><i class="icon_country tab-<?php echo $currency ?>">&nbsp;</i><em class="currency-name"><?php echo $currency ?></em></a>
			<i class="icon-select-arrow"></i>
		</div>
		<div class="drop-box">
			<div class="drop-content">
				<div class="space">
					<input class="currency-keywords" type="text" id="currencyKeywords" autocomplete="off"/>
					<input id="allCurrency" type="hidden" value="<?php echo implode(',',$header['currency_list']) ?>"/>
				</div>
				<div id="countryList" class="drop-list">
					<?php foreach($header['currency_list'] as $header_currency_code){ ?>
					<a class="tab-<?php echo $header_currency_code ?>" rel="nofollow" onclick="ec.currency('<?php echo $header_currency_code ?>');" href="javascript:;"><i class="icon_country tab-<?php echo $header_currency_code ?>"></i><span><?php echo $header_currency_code ?></span></a>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	</div>
	<?php } ?>
	<em class="bor">|</em>
	<a href="<?php echo genURL('help.html')?>" rel="nofollow" class="help" title="Help">Help</a>
	</div>

	<?php if(isset($header) && isset($header['language_list'])){ ?>
	<ul class="lan-module">
	<?php foreach($header['language_list'] as $language){ ?>
	<?php if($language['current']){ ?>
	<li class="curr"><?php echo $language['title'] ?></li>
	<?php }else{ ?>
	<li>
		<a href="<?php if(isset($noAlternateList) && $noAlternateList == true) {global $base_url; echo $base_url[$language['id']];} else { echo $language['url']; } //stange demand when it is english link to the home?>" title="<?php echo $language['title'] ?>">
		<?php echo $language['title'] ?>
		</a>
	</li>
	<?php } ?>
	<?php } ?>
	</ul>
	<?php } ?>
	</div>
</div>

<!-- 2015-08-10 新增顶部广告位 start -->
<?php 
if(isset($sit_top_imagead) && $top_banner==true){
?>
<div class="sale-banner">
	<a href="<?php echo $sit_top_imagead['content']['url'];?>" onclick='onPromoClick(<?php $productObj['data']['id'] = 'A6';$productObj['data']['name'] = 'slim_banner';echo json_encode($productObj);?>)'><img src="<?php echo COMMON_IMAGE_URL.$sit_top_imagead['content']['img']; ?>"/></a>
	<div class="timer">
		<div class="bglt">
            <div class="bg-center">
		    	<span class="endIn"><?php echo lang('end_in_days_first');?><em><strong> <?php if(isset($sale_banner_date_end)) echo floor($sale_banner_date_end / (24*3600));?> </strong> <?php echo lang('end_in_days_last');?></em></span>
		        <div class="cont hide_day" id="sale_banner_counter" style="visibility: visible;">
					<span data-endtime="<?php if(isset($sale_banner_date_end)) echo ($sale_banner_date_end - floor($sale_banner_date_end / (24*3600))*24*3600 );?>"></span>
				</div>
			</div>
		</div>
    </div>
    <script type="text/javascript">
                $(function() {
					ec.load('ec.ui.countdown', {
							onload : function () {
								ec.ui.countdown('#sale_banner_counter span', {
									"html" : "<span class='day_text'><em class='day'>{#day}</em>&nbsp;{#dayText}</span> <em>{#hours}</em><i>:</i><em>{#minutes}</em><i>:</i><em>{#seconds}</em>",
									"zeroDayHide" : true,
									"callback" : function (json) {
										//计时结束时要执行的方法,比如置灰
										//$(this).parent().addClass('timeend');
									}
								});
							}
						});
					});
				</script>
</div>
<?php }?>
<!-- 2015-08-10 新增顶部广告位 end -->

<div class="header" id="pageHeader">
	<div class="wrap">
	<div class="logo"><a href="<?php echo genURL() ?>" title="<?php echo ucfirst(COMMON_DOMAIN) ?>"><img src="<?php echo RESOURCE_URL ?>images/common/logo.png?v=<?php echo STATIC_FILE_VERSION ?>" alt="<?php echo ucfirst(COMMON_DOMAIN) ?>" title="<?php echo ucfirst(COMMON_DOMAIN) ?>" /></a></div>

	<div class="nav-search">
		<div class="search-module">
			<form id="searchForm" name="searchForm" method="get" action="<?php echo genURL('search') ?>">
				<div class="search-keyword"><input type="text"  autocomplete="off" name="keywords" id="keywords" class="keywords" value="<?php if(isset($keywords) && $keywords){ echo $keywords;}?>"/></div>
				<button type="submit" class="search-btn" title="Search">Search</button>
				<ul class="search-list" id='thinking'>
				</ul>
				<input type='hidden' id='status' value='0'>
			</form>
		</div>
		<ul class="hot">
			<?php if(isset($header) && isset($header['search_keywords_list'])){ ?>
			<?php foreach($header['search_keywords_list'] as $keyword){ ?>
			<li><a href="<?php echo $keyword['keywords_url'] ?>"<?php echo $keyword['keywords_highlight']==STATUS_ACTIVE?' class="curr"':'' ?>><?php echo $keyword['keywords_title'] ?></a></li>
			<?php } ?>
			<?php } ?>
		</ul>
	</div>

	<div class="login-register">
		
		<div class="login" id="ajaxLogin">
			<?php if(isset($user) && !empty($user) && $user!=false):?>
			Hello,<?php echo $this->session->get('user_name');?>
			<?php else:?>
			<a href="<?php echo genURL('login') ?>" rel="nofollow" class="sign-in" title="<?php echo lang('sign_in');?>"><i></i><?php echo lang('sign_in');?></a>
			<a href="<?php echo genURL('login') ?>" rel="nofollow" class="register" title="<?php echo lang('register');?>"><i></i><?php echo lang('register');?></a>
			<?php endif; ?>
		</div>
		
		<div class="my-account">
			<div class="select-block">
				<div class="selected">
					<a rel="nofollow" href="<?php echo genURL('login') ?>" title="My Account"><i class="account-icon"></i><?php echo lang('my_account');?></a>
					<i class="icon-select-arrow"></i>
				</div>
				<div class="drop-box">
					<ul class="drop-content drop-list">
						 <li><a href="<?php echo genURL('order_list') ?>" rel="nofollow"><span><?php echo lang('my_order');?></span></a></li>
						 <li><a href="<?php echo genURL('rewards') ?>" rel="nofollow"><span><?php echo lang('my_rewards');?></span></a></li>
						 <li><a href="<?php echo genURL('review_create') ?>" rel="nofollow"><span><?php echo lang('manage_review');?></span></a></li>
						 <li><a href="<?php echo genURL('wishlist') ?>" rel="nofollow"><span><?php echo lang('my_wishlist');?></span></a></li>
						 <li><a href="<?php echo genURL('manage_address_book') ?>" rel="nofollow"><span><?php echo lang('manage_address_book');?></span></a></li>
						 <li><a href="<?php echo genURL('account_settings') ?>" rel="nofollow"><span><?php echo lang('account_setting');?></span></a></li>
						 <?php if(isset($user) && !empty($user) && $user!=false):?>
						 <li><a href="<?php echo genURL('login/logout') ?>" rel="nofollow"><span><?php echo lang('sign_out');?></span></a></li>
						<?php endif;?>
					</ul>
				</div>
			</div>
		</div>
		
	</div>

	<a href="<?php echo genURL('cart') ?>" rel="nofollow" class="minicart" id="miniCart"><em></em><b class="cart"><?php echo lang('cart');?><span>(<i id="cartTotal"><?php echo $cart_nums?></i>)</span></b></a>
	</div>
</div>

<div id="pageNav" class="nav">
	<div class="wrap">
	<?php include dirname(__FILE__).'/category.php'; ?>
	<ul class="main-nav">
		<?php if(isset($header) && isset($header['header_keywords_list']) && isset($header['header_keywords_list'][STATUS_DISABLE])){ ?>
		<?php foreach($header['header_keywords_list'][STATUS_DISABLE] as $keyword){ ?>
		<?php if(trim($keyword['keywords_url']) !='' ){
		?>
		<li><a href="<?php echo $keyword['keywords_url'];?>"><?php echo $keyword['keywords_title'] ?></a></li>
		<?php
		}else{
		?>
		<li class="default"><?php echo $keyword['keywords_title'];?></li>
		<?php } ?>
		<?php } ?>
		<?php } ?>
	</ul>

	<?php if(isset($header) && isset($header['header_keywords_list']) && isset($header['header_keywords_list'][STATUS_ACTIVE])){ ?>
	<?php $keyword = current($header['header_keywords_list'][STATUS_ACTIVE]); ?>
	<div class="weather">
	<?php if(trim($keyword['keywords_url']) !='' ) {
	?>
	<a href="<?php echo $keyword['keywords_url'];?>"><?php echo $keyword['keywords_title'] ?></a></div>
	<?php
	}else{
		echo $keyword['keywords_title'];
	}?>
	
	<?php } ?>
	</div>
</div>