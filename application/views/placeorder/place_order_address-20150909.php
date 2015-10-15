<script type="text/javascript">
var region_data = <?php echo $province_list?>;
</script>
<div class="shipping ship-ads" id="address">
		<h2 class="ship-tit">
			<i></i><?php echo lang('shipping_address');?><a class="view-more hide" href="javascript:;" id="more"><?php echo lang('view_more_address');?></a><a class="view-more hide" href="javascript:;" id="less"><?php echo lang('view_less_address');?></a>
		</h2>
		<div class="address">
			<ul>
			<?php
			if(isset($place_order_type) && $place_order_type == 'paypal_ec_nologin'){
				$new_address_data = array();
			}else{
				$new_address_data = $address_data;
			} 
			foreach ($new_address_data as $address_key=>$address_val){
			?>
			<li index="<?php if(isset($address_val['address_id']))echo $address_val['address_id'];else echo '0';?>" <?php if($address_val['checked']==1)echo "class='on'"?>>
				<div class="ads-user"><i></i><strong><?php echo $address_val['first_name']." ".$address_val['last_name']?></strong></div>
				<div class="ads-info">
					<p><?php echo $address_val['address'];?></p>
					<p><?php echo $address_val['city'].' '.$address_val['region'].' '.$address_val['zipcode']?></p>
					<p><?php echo $address_val['country_name']?></p>
				</div>
				<div class="ads-phone"><?php echo lang('phone');?>:<?php echo $address_val['mobile']?></div>
				<?php 
				if($address_val['defaults']==1){
				?>
				<div class="preferred"><?php echo lang('preferred_address');?></div>				
				<?php
				}
				?>
				<p class="edit"><a href="javascript:;"><?php echo lang('edit');?></a></p>
			</li>
			<?php	
			}
			?>
			<li class="add-ads" id="addAddress" title="Add New Address"><em>+</em><span><?php echo lang('add_new_address');?></span></li>
			</ul>
			<script type="text/javascript">
			var addressList = <?php echo json_encode($address_data);?>
			</script>
		</div>
        <script type="text/html" id="addressPop">
		<div class="addressPop" id="Pop">
			<a href="javascript:;" title="Close" class="close" id="close"><?php echo lang('close');?></a>
			<h3 id="add_address_title_name"><?php echo lang('add_new_address');?></h3>
			<h3 id="edit_address_title_name" class="hide"><?php echo lang('edit_address');?></h3>
			<form action="" method="get" id="addressForm">	
           		<table class="address-table">
                	<tbody>
                		<tr>
	                    	<td class="address-w1"><?php echo lang('first_name');?></td>
	                    	<td><?php echo lang('last_name');?></td>
	                    </tr>
	                    <tr>
	                    	<td class="info"><input type="text" class="input-text i-w1" id="firstName" name="first_name" autocomplete="off" maxlength="50" title="First Name"></td>
	                    	<td class="info"><input type="text" class="input-text i-w1" id="lastName" name="last_name" autocomplete="off" maxlength="50" title="Last Name"></td>
	                    </tr>
	                    <tr>
	                    	<td><?php echo lang('address1');?></td>
							<td><?php echo lang('city');?></td>
	                    </tr>
	                    <tr>
	                    	<td class="info"><input type="text" class="input-text i-w1" id="street_1" name="address" autocomplete="off" maxlength="100" title="Street Address 1"></td>
	                    	<td class="info">
								<select style="display:none" name="city" id="citySelect" disabled="" class="select-text select-w">
									<option value=""></option>
									<option value="Guangzhou">Guangzhou</option>
									<option value="Shenzhen">Shenzhen</option>
								</select>
								<input type="text" class="input-text i-w1" id="City" name="city" autocomplete="off" maxlength="100" title="City">
							</td>
							
	                    </tr>
	                    <tr>
							<td><?php echo lang('country');?></td>
							<td><?php echo lang('state_province_region');?></td>
	                    </tr>
	                    <tr>
	                    	<td class="info">
								<select class="select-text select-w" id="country" name="country" autocomplete="off" title="Country" style="padding:0px;">
									<option value=""></option>
									<?php
									if(isset($country_list)){
									foreach ($country_list as $country_info){
										echo '<option value="'.$country_info['country_iso2'].'">'.$country_info['country_name'].'</option>';
									}	
									} 
									?>
								</select>
							</td>
	                    	<td class="info">
								<select autocomplete="off" defaultvalue="0" title="State/Province" name="region" id="province" style="display: none;" class="select-text select-w"></select>
								<input type="text" class="input-text i-w1" id="region" name="region" autocomplete="off" title="State/Province">
							</td>
	                    </tr>
	                    <tr>
							<td><?php echo lang('zip_code');?></td>
	                    	<td><?php echo lang('phone');?><span class="font12">Ex:415-444-55555</span></td>
	                    </tr>
	                    <tr>
							<td class="info"><input type="text" class="input-text i-w1" id="zipcode" name="zipcode" autocomplete="off" title="Zip/Postal Code"></td>
	                    	<td class="info"><input type="text" class="input-text i-w1" id="mobile" name="mobile" autocomplete="off" title="Mobile Phone"></td>
	                    </tr>
	                    <tr>
							<td class="cpf-box"><?php echo lang('cpf_cnpj');?></td>
							<td></td>
	                    </tr>
	                    <tr>
							<td class="info cpf-box"><input type="text" class="input-text i-w1" id="cpf" name="cpf" autocomplete="off" title="CPF/CNPJ"></td>
	                    	<td></td
						</tr>
	                </tbody>
	            </table> 
                <div class="btn-form">
                	<div>
						<input type="hidden" value="" id="address_id" name="address_id">
						<input type="button" title="Use this Address" value="<?php echo lang('use_this_address');?>" class="use-address icon-view" id="useAddress">
						<a class="view-a" href="javascript:;" id="cancel"><?php echo lang('cancel');?></a>
                	</div>
                	<div class="default">
                		<input class="check" id="default" type="checkbox" title="Use this Address">
                		<label for="default"><?php echo lang('preferred_address');?></label>
						<input type="hidden" value="0" id="defaultValue" name="defaultValue">
                	</div>
                </div>
			 </form>
        </div>
	
		</script>
	</div>
