<div class="shipping ship-ads" id="address">
		<h2 class="ship-tit">
			<i></i><span><?php echo lang('shipping_address');?></span><a class="view-more hide" href="javascript:;" id="more"><?php echo lang('view_more_address');?></a><a class="view-more hide" href="javascript:;" id="less"><?php echo lang('view_less_address');?></a>
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
									<option value="AF">Afghanistan</option>
									<option value="AL">Albania</option>
									<option value="DZ">Algeria</option>
									<option value="AS">American Samoa</option>
									<option value="AD">Andorra</option>
									<option value="AO">Angola</option>
									<option value="AI">Anguilla</option>
									<option value="AQ">Antarctica</option>
									<option value="AG">Antigua and Barbuda</option>
									<option value="AR">Argentina</option>
									<option value="AM">Armenia</option>
									<option value="AW">Aruba</option>
									<option value="AU">Australia</option>
									<option value="AT">Austria</option>
									<option value="AZ">Azerbaijan</option>
									<option value="BS">Bahamas</option>
									<option value="BH">Bahrain</option>
									<option value="BD">Bangladesh</option>
									<option value="BB">Barbados</option>
									<option value="BY">Belarus</option>
									<option value="BE">Belgium</option>
									<option value="BZ">Belize</option>
									<option value="BJ">Benin</option>
									<option value="BM">Bermuda</option>
									<option value="BT">Bhutan</option>
									<option value="BO">Bolivia</option>
									<option value="BA">Bosnia and Herzegovina</option>
									<option value="BW">Botswana</option>
									<option value="BV">Bouvet Island</option>
									<option value="BR">Brazil</option>
									<option value="IO">British Indian Ocean Territory</option>
									<option value="VG">British Virgin Islands</option>
									<option value="BN">Brunei</option>
									<option value="BG">Bulgaria</option>
									<option value="BF">Burkina Faso</option>
									<option value="BI">Burundi</option>
									<option value="KH">Cambodia</option>
									<option value="CM">Cameroon</option>
									<option value="CA">Canada</option>
									<option value="CV">Cape Verde</option>
									<option value="KY">Cayman Islands</option>
									<option value="CF">Central African Republic</option>
									<option value="TD">Chad</option>
									<option value="CL">Chile</option>
									<option value="CN">China</option>
									<option value="CX">Christmas Island</option>
									<option value="CC">Cocos [Keeling] Islands</option>
									<option value="CO">Colombia</option>
									<option value="KM">Comoros</option>
									<option value="CG">Congo - Brazzaville</option>
									<option value="CD">Congo - Kinshasa</option>
									<option value="CK">Cook Islands</option>
									<option value="CR">Costa Rica</option>
									<option value="HR">Croatia</option>
									<option value="CU">Cuba</option>
									<option value="CY">Cyprus</option>
									<option value="CZ">Czech Republic</option>
									<option value="CI">Côte d'Ivoire'</option>
									<option value="DK">Denmark</option>
									<option value="DJ">Djibouti</option>
									<option value="DM">Dominica</option>
									<option value="DO">Dominican Republic</option>
									<option value="EC">Ecuador</option>
									<option value="EG">Egypt</option>
									<option value="SV">El Salvador</option>
									<option value="GQ">Equatorial Guinea</option>
									<option value="ER">Eritrea</option>
									<option value="EE">Estonia</option>
									<option value="ET">Ethiopia</option>
									<option value="FK">Falkland Islands</option>
									<option value="FO">Faroe Islands</option>
									<option value="FJ">Fiji</option>
									<option value="FI">Finland</option>
									<option value="FR">France</option>
									<option value="GF">French Guiana</option>
									<option value="PF">French Polynesia</option>
									<option value="TF">French Southern Territories</option>
									<option value="GA">Gabon</option>
									<option value="GM">Gambia</option>
									<option value="GE">Georgia</option>
									<option value="DE">Germany</option>
									<option value="GH">Ghana</option>
									<option value="GI">Gibraltar</option>
									<option value="GR">Greece</option>
									<option value="GL">Greenland</option>
									<option value="GD">Grenada</option>
									<option value="GP">Guadeloupe</option>
									<option value="GU">Guam</option>
									<option value="GT">Guatemala</option>
									<option value="GG">Guernsey</option>
									<option value="GN">Guinea</option>
									<option value="GW">Guinea-Bissau</option>
									<option value="GY">Guyana</option>
									<option value="HT">Haiti</option>
									<option value="HM">Heard Island and McDonald Islands</option>
									<option value="HN">Honduras</option>
									<option value="HK">Hong Kong</option>
									<option value="HU">Hungary</option>
									<option value="IS">Iceland</option>
									<option value="IN">India</option>
									<option value="ID">Indonesia</option>
									<option value="IR">Iran</option>
									<option value="IQ">Iraq</option>
									<option value="IE">Ireland</option>
									<option value="IM">Isle of Man</option>
									<option value="IL">Israel</option>
									<option value="IT">Italy</option>
									<option value="JM">Jamaica</option>
									<option value="JP">Japan</option>
									<option value="JE">Jersey</option>
									<option value="JO">Jordan</option>
									<option value="KZ">Kazakhstan</option>
									<option value="KE">Kenya</option>
									<option value="KI">Kiribati</option>
									<option value="KW">Kuwait</option>
									<option value="KG">Kyrgyzstan</option>
									<option value="LA">Laos</option>
									<option value="LV">Latvia</option>
									<option value="LB">Lebanon</option>
									<option value="LS">Lesotho</option>
									<option value="LR">Liberia</option>
									<option value="LY">Libya</option>
									<option value="LI">Liechtenstein</option>
									<option value="LT">Lithuania</option>
									<option value="LU">Luxembourg</option>
									<option value="MO">Macau SAR China</option>
									<option value="MK">Macedonia</option>
									<option value="MG">Madagascar</option>
									<option value="MW">Malawi</option>
									<option value="MY">Malaysia</option>
									<option value="MV">Maldives</option>
									<option value="ML">Mali</option>
									<option value="MT">Malta</option>
									<option value="MH">Marshall Islands</option>
									<option value="MQ">Martinique</option>
									<option value="MR">Mauritania</option>
									<option value="MU">Mauritius</option>
									<option value="YT">Mayotte</option>
									<option value="MX">Mexico</option>
									<option value="FM">Micronesia</option>
									<option value="MD">Moldova</option>
									<option value="MC">Monaco</option>
									<option value="MN">Mongolia</option>
									<option value="ME">Montenegro</option>
									<option value="MS">Montserrat</option>
									<option value="MA">Morocco</option>
									<option value="MZ">Mozambique</option>
									<option value="MM">Myanmar [Burma]</option>
									<option value="NA">Namibia</option>
									<option value="NR">Nauru</option>
									<option value="NP">Nepal</option>
									<option value="NL">Netherlands</option>
									<option value="AN">Netherlands Antilles</option>
									<option value="NC">New Caledonia</option>
									<option value="NZ">New Zealand</option>
									<option value="NI">Nicaragua</option>
									<option value="NE">Niger</option>
									<option value="NG">Nigeria</option>
									<option value="NU">Niue</option>
									<option value="NF">Norfolk Island</option>
									<option value="KP">North Korea</option>
									<option value="MP">Northern Mariana Islands</option>
									<option value="NO">Norway</option>
									<option value="OM">Oman</option>
									<option value="PK">Pakistan</option>
									<option value="PW">Palau</option>
									<option value="PS">Palestinian Territories</option>
									<option value="PA">Panama</option>
									<option value="PG">Papua New Guinea</option>
									<option value="PY">Paraguay</option>
									<option value="PE">Peru</option>
									<option value="PH">Philippines</option>
									<option value="PN">Pitcairn Islands</option>
									<option value="PL">Poland</option>
									<option value="PT">Portugal</option>
									<option value="PR">Puerto Rico</option>
									<option value="QA">Qatar</option>
									<option value="RO">Romania</option>
									<option value="RU">Russian Federation</option>
									<option value="RW">Rwanda</option>
									<option value="RE">Réunion</option>
									<option value="BL">Saint Barthélemy</option>
									<option value="SH">Saint Helena</option>
									<option value="KN">Saint Kitts and Nevis</option>
									<option value="LC">Saint Lucia</option>
									<option value="MF">Saint Martin</option>
									<option value="PM">Saint Pierre and Miquelon</option>
									<option value="VC">Saint Vincent and the Grenadines</option>
									<option value="WS">Samoa</option>
									<option value="SM">San Marino</option>
									<option value="SA">Saudi Arabia</option>
									<option value="SN">Senegal</option>
									<option value="RS">Serbia</option>
									<option value="SC">Seychelles</option>
									<option value="SL">Sierra Leone</option>
									<option value="SG">Singapore</option>
									<option value="SK">Slovakia</option>
									<option value="SI">Slovenia</option>
									<option value="SB">Solomon Islands</option>
									<option value="SO">Somalia</option>
									<option value="ZA">South Africa</option>
									<option value="GS">South Georgia and the South Sandwich Islands</option>
									<option value="KR">South Korea</option>
									<option value="ES">Spain</option>
									<option value="LK">Sri Lanka</option>
									<option value="SD">Sudan</option>
									<option value="SR">Suriname</option>
									<option value="SJ">Svalbard and Jan Mayen</option>
									<option value="SZ">Swaziland</option>
									<option value="SE">Sweden</option>
									<option value="CH">Switzerland</option>
									<option value="SY">Syria</option>
									<option value="ST">São Tomé and Príncipe</option>
									<option value="TW">Taiwan</option>
									<option value="TJ">Tajikistan</option>
									<option value="TZ">Tanzania</option>
									<option value="TH">Thailand</option>
									<option value="TL">Timor-Leste</option>
									<option value="TG">Togo</option>
									<option value="TK">Tokelau</option>
									<option value="TO">Tonga</option>
									<option value="TT">Trinidad and Tobago</option>
									<option value="TN">Tunisia</option>
									<option value="TR">Turkey</option>
									<option value="TM">Turkmenistan</option>
									<option value="TC">Turks and Caicos Islands</option>
									<option value="TV">Tuvalu</option>
									<option value="UM">U.S. Minor Outlying Islands</option>
									<option value="VI">U.S. Virgin Islands</option>
									<option value="UG">Uganda</option>
									<option value="UA">Ukraine</option>
									<option value="AE">United Arab Emirates</option>
									<option value="GB">United Kingdom</option>
									<option value="US">United States</option>
									<option value="UY">Uruguay</option>
									<option value="UZ">Uzbekistan</option>
									<option value="VU">Vanuatu</option>
									<option value="VA">Vatican City</option>
									<option value="VE">Venezuela</option>
									<option value="VN">Vietnam</option>
									<option value="WF">Wallis and Futuna</option>
									<option value="EH">Western Sahara</option>
									<option value="YE">Yemen</option>
									<option value="ZM">Zambia</option>
									<option value="ZW">Zimbabwe</option>
									<option value="AX">Åland Islands</option>
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