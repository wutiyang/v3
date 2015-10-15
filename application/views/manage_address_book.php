<?php include dirname(__FILE__).'/common/header.php'; ?>
<script type="text/javascript">
var region_data = <?php echo $province_list;?>;
</script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/order_list.css?v=<?php echo STATIC_FILE_VERSION ?>">
<div class="wrap breadcrumbs">
    <i class="icon-home">&nbsp;</i>
    <a href="<?php echo genURL('/')?>"><?php echo lang('home');?></a>
    <i class="icon-arr-right">&nbsp;</i>
    <a href=""><?php echo lang('my_account');?></a>
    <i class="icon-arr-right">&nbsp;</i>
    <span><?php echo lang('manage_address_book');?></span>
</div>
<div class="main manage_address_book" id="manageAddressBook">
	<?php include dirname(__FILE__).'/account/nav.php'; ?>
 	<div class="content">
   		<h3 class="column-now"><?php echo lang('manage_address_book');?></h3>
   		<?php if(!empty($tips)):?>
   		<div class="success">
            <i></i><?php echo $tips;?>
        </div>
        <?php endif;?>
        <!--index：数据id-->
        <table cellspacing=10>
            <tr>
                <?php foreach ($address_list as $key=>$address):?>
                <td class="box-module box-w1" index="<?php echo $address['address_id'];?>" firstName="<?php echo $address['address_firstname'];?>" lastName="<?php echo $address['address_lastname'];?>" zipcode="<?php echo $address['address_zipcode'];?>" address_1="<?php echo $address['address_address'];?>" address_2="" city="<?php echo $address['address_city'];?>" province="<?php echo $address['address_province'];?>" country="<?php echo $address['address_country'];?>" phone="<?php echo $address['address_phone'];?>" cpf="<?php echo $address['address_cpfcnpj'];?>">
                    <div class="info"><strong><?php echo $address['address_firstname'];?> <?php echo $address['address_lastname'];?></strong></div>
                    <div class="infor">
                        <p><?php echo $address['address_address'];?></p>
                        <p><?php echo $address['address_city'];?>,<?php echo $address['address_province'];?>,<?php echo $address['address_zipcode'];?></p>
                        <p><?php echo $address['address_country'];?></p>
                    </div>
                    <div class="phone"><i></i>Phone:<?php echo $address['address_phone'];?></div>
                    <?php if($address['address_default']):?>
                    <div class="preferred">Preferred Address</div>
                    <?php endif;?>
                    <div class="btn-form">
                        <button type="button" title="Edit" class="btn30-org btn-w65"><span class="btn-right"><span class="btn-text"><?php echo lang('edit');?></span></span></button>
                        <?php if(!$address['address_default']):?>
                        <button type="button" title="Delete" class="btn30-gray btn-w65 ml9"><span class="btn-right"><span class="btn-text"><?php echo lang('delete');?></span></span></button>
                        <?php endif;?>
                    </div>
                </td>
                <?php if($key%4 == 3) echo '</tr><tr>';?>
                <?php endforeach;?>
                <td class="add-address" id="addAddress">
                    <p><em>+</em> <?php echo lang('add_new_address');?></p>
                </td>
            </tr>
        </table>
        <script type="text/html" id="delete">
        <div class="box-module box-w2 delete-box" id="Pop">
        	<form action="bb" method="post" id="passwordForm">
                <div class="info"><h5><?php echo lang('delete_address_tips');?></h5></div>
                <div class="btn-form">
                    <button type="button" index='a' title="Detele" class="btn34-org btn-w86" id="deleteOk"><span class="btn-right"><span class="btn-text"><?php echo lang('delete');?></span></span></button>
                    <button type="button" title="Cancel" class="btn34-gray btn-w86 cancel"><span class="btn-right"><span class="btn-text"><?php echo lang('cancel');?></span></span></button>
                </div>
            </form>
        </div>
        </script>
        <script type="text/html" id="addressPop">
            <div class="addressPop" id="Pop">
            	<div class="pop-icon"><i>&nbsp;</i></div>
                <div class="con">
				<!--
				actionEdit:编辑action
				actionAdd:新建action
				-->
				<form action="" method="post" id="addressForm" actionEdit="/manage_address_book/edit" actionAdd="/manage_address_book/add">
               		<table class="address-table">
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
                                    <?php foreach ($country_list as $country):?>
                                    <option value="<?php echo $country['country_iso2'];?>"><?php echo $country['country_name'];?></option>
                                    <?php endforeach;?>
                                </select>
                            </td>
                            <td class="info">
                                <select autocomplete="off" defaultvalue="0" title="State/Province" name="province" id="province" style="display: none;" class="select-text select-w" ></select>
                                <input type="text" class="input-text i-w1" id="region" name="province" autocomplete="off" title="State/Province">
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo lang('zip_code');?></td>
                        	<td><?php echo lang('phone');?><span class="font12">Ex:415-444-55555</span></td>
                        </tr>
                        <tr>
                            <td class="info"><input type="text" class="input-text i-w1" id="zipcode" name="zipcode" autocomplete="off" title="Zip/Postal Code"></td>
                        	<td class="info"><input type="text" class="input-text i-w1" id="mobile" name="phone" autocomplete="off" title="Mobile Phone" ></td>
                        </tr>

                        <tr>
							<td class="cpf-box"><?php echo lang('cpf_cnpj');?>:</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="info cpf-box"><input type="text" class="input-text i-w1" id="cpf" name="cpfcnpj" autocomplete="off" title="CPF/CNPJ" name="cpf"></td>
                            <td></td>
                        </tr>
                    </table> 
                    <div class="btn-form">
						<input type="hidden" name="address_id" id="addressId" value="">
                        <button type="button" title="Save" class="btn34-org btn-w86" ><span class="btn-right"><span class="btn-text"><?php echo lang('save');?></span></span></button>
                        <button type="button" title="Cancel" class="btn34-gray btn-w86 cancel"><span class="btn-right"><span class="btn-text"><?php echo lang('cancel');?></span></span></button>
                    </div>
                    <div class="default">
                        <input type="checkbox" title="Use this Address" id="default" class="check">
                        <label for="default"><?php echo lang('preferred_address');?></label>
                        <input type="hidden" name="defaultValue" id="defaultValue" value="0">
                    </div>
					</form>
               </div>
            </div>
			</script>
    </div>
</div>

<!--main end-->
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script src="<?php echo RESOURCE_URL ?>js/common/utils.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script src="<?php echo RESOURCE_URL ?>js/manage_address_book.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>