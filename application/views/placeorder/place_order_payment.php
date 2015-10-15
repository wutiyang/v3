<div class="shipping payment" id="payment">
		<h2 class="ship-tit"><i></i><?php echo lang('payment_method');?></h2>
		<?php 
		//if(isset($shipping_option_data) && $shipping_option_data['status']==200){
		?>
		<div class="ship-pay">
			<div class="pay-tit <?php if($place_order_type!='normal') echo 'hide';?>">
				<div class="pay-hint"><?php echo lang('payment_methods_available_for');?></div>
				<div class="change">
					<!-- img src="<?php echo RESOURCE_URL ?>images/order/con-icon.png?v=<?php echo STATIC_FILE_VERSION ?>"-->
					<span class="country" id="countryId" countryId="<?php echo $payment_country_code?>"><?php echo $payment_country?></span> 
					<?php echo lang('in');?>
					<span class="currency" id="currencyId" currencyId="<?php echo $currency_code?>"><?php echo $currency_code?> <?php echo $new_currency?></span>
					<a class="change-box" href="javascript:;" id="changeCountry"><?php echo lang('change_country_currency');?></a>
				</div>
			</div>
				<ul class="method-list">
					<?php
					$payment_nums = 0; 
					foreach ($adyen_list as $adyen_key=>$adyen_val){
					?>
					<li class="remember rem-list <?php if($payment_nums==0) echo 'on';?>" paymentid="<?php echo $adyen_val['id']?>">
						<img class="pay-img" src="<?php echo $adyen_val['picname']?>">
						<input class="method" id="<?php echo $adyen_val['id']?>" type="checkbox" disabled="disabled"><label for="<?php echo $adyen_val['id']?>"></label>
					</li>
					<?php
					$payment_nums++;	
					}
					?>
				</ul>
		</div>
		<script type="text/html" id="changeConfirm">
		<div class="chang-confirm clearfix" id="Pop">
			<a id="close" class="close" title="Close" href="javascript:;"><?php echo lang('close');?></a>
				<h3><?php echo lang('change_country_currency');?></h3>
				<div class="select-list clearfix">
					<div class="recent-select" id="selectCountry">
						<p><?php echo lang('select_your_country');?></p>
                    	<div class="select-box">
                        	<div class="select-block">
                            	<div class="selected">
                                	<a title="" href="javascript:;" rel="nofollow">
                                    	<i class="account-icon"></i>
                                        <span attrid="<?php echo $payment_country_code?>" class="default"><?php echo $payment_country?></span>
                                    </a>
                                    <i class="icon-select-arrow"></i>
                                </div>
                                <div class="drop-box">
                                	<ul class="drop-content drop-list">
										<?php 
										foreach ($country_list as $country_key=>$country_val){
										?>
										<li class="<?php if($country_val['country_iso2']==$payment_country_code) echo 'hide country_selected';?> country_else_selected" attrid="<?php echo $country_val['country_iso2']?>"><a href="javascript:;"><?php echo $country_val['country_name']?></a></li>
										<?php
										}
										?>
									</ul>
                               	</div>
                        	</div>
                        </div>
                	</div>
                	<div class="recent-select clearfix" id="selectCurrency">
						<p><?php echo lang('select_your_currency');?></p>
                    	<div class="select-box">
                        	<div class="select-block">
                            	<div class="selected">
                                	<a title="" href="javascript:;" rel="nofollow">
                                    	<i class="account-icon"></i>
                                        <span attrid="<?php echo $currency_code?>" class="default"><?php echo $currency_code.' '.$new_currency?></span>
                                    </a>
                                    <i class="icon-select-arrow"></i>
                                </div>
                                <div class="drop-box">
                                	<ul class="drop-content drop-list">
										<?php 
										foreach ($currency_list as $currency_key=>$currency_val){
										?>
										<li class="<?php if($currency_val['currency_code']==$currency_code) echo 'hide currency_selected';?>  currency_else_selected" attrid="<?php echo $currency_val['currency_code']?>"><a href="javascript:;"><?php echo $currency_val['currency_code']." ".trim(str_replace("%s", '', $currency_val['currency_format'])); ?></a></li>
										<?php	
										}
										?>
									</ul>
                               	</div>
                        	</div>
                        </div>
                	</div>
				</div>
				<p>
                	<a class="icon-view" id="changeCountrySave" href="javascript:;"><?php echo lang('save');?></a>
                	<a class="icon-view icon-view-dis" id="cancel" href="javascript:;"><?php echo lang('cancel');?></a>
				</p>
		</div>
		</script>
		<?php //} ?>
		<!-- div class="dis-ship">
			<i></i>Please fill in/select the shipping address above.
		</div-->
	</div>