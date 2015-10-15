<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';
/**
 * @desc check_out页
 * @author Wty
 *
 */
class Place_order extends Dcontroller {
	public function index(){
		//检查用户是否登录，如果没有登录就返回购物车页面
		if(!$this->customer->checkUserLogin()) redirect(genURL("cart"));
		$user_id = $this->session->get('user_id');
		
		//标识能否place_order按钮
		$able_use_address = $able_use_shipping = $able_use_payment = false;
		
		//*****************country && currency ******start
		$currency_list = $this->_currency();
		$country_list = $this->_country();
		$this->_view_data['currency_list'] = $currency_list;
		$this->_view_data['country_list'] = $country_list;
		//*****************country && currency ******end
		
		//获取用户的地址信息及默认地址
		$address_data = $this->addressList();
		//格式变换
		$new_address_data = array();
		if(!empty($address_data)){
			$able_use_address = true;
			foreach($address_data  as $address_key=>$address_val){
				$address_id = $address_val['address_id'];
				$new_address_data[$address_id]["address_id"] = $address_val['address_id'];
				$new_address_data[$address_id]["first_name"] = $address_val['address_firstname'];
				$new_address_data[$address_id]["last_name"] = $address_val['address_lastname'];
				$new_address_data[$address_id]["address"] = $address_val['address_address'];
				$new_address_data[$address_id]["address2"] = "";
				$new_address_data[$address_id]["city"] = $address_val['address_city'];
				$new_address_data[$address_id]["country"] = $address_val['address_country'];
				$new_address_data[$address_id]['country_name'] = $this->getCountryName($address_val['address_country']);
				$new_address_data[$address_id]["zipcode"] = $address_val['address_zipcode'];
				$new_address_data[$address_id]["mobile"] = $address_val['address_phone'];
				$new_address_data[$address_id]["cpf"] = $address_val['address_cpfcnpj'];
				$new_address_data[$address_id]["region"] = $address_val['address_province'];
				$new_address_data[$address_id]["defaults"] = $address_val['address_default'];
				$new_address_data[$address_id]["checked"] = $address_val['address_default'];
			}
		}
		$this->_view_data['address_data'] = $new_address_data;
		
		//购物车信息（cart summary）
		$cart_data = $this->getCart();
		$this->_view_data['cart_data'] = $cart_data;
		
		//计算商品，长宽高，重量，体积，敏感数据,及总价
		$cart_weight_info = $this->computeWeightInfo($cart_data);
		$subtotal = $cart_weight_info['subtotal_price'];//商品基本价格是经过了汇率计算的
		
		//shipping options (可用物流方式，物流费用，海外本地仓库) start***************
		if(!empty($address_data)){
			$cart_all_warehouse = $this->cartWarehouse($cart_data);//所有存储仓库信息
			//国家对应仓库可选的物流方式(最终得到那种类型的物流：cnmail,freesea,nomail,null;null类型时，获取对应msg错误码及msg)
			$address_warehouse = isset($address_data[0]['address_country'])?$address_data[0]['address_country']:null;
			$payment_addres_country = $this->getCountryName($address_warehouse);
			$shipping_options_list = $this->_shippingoptions($address_warehouse,$cart_all_warehouse);
			//需要判断物流限制规则（只有normal，且仓库有本地仓库时才会做此判断）
			if($shipping_options_list['exists_rule']==1){
				$able_shipping_options = $this->_ruleshipping($address_warehouse,$cart_weight_info);
				if($able_shipping_options){
					$able_use_shipping = true;
					//数据库里存储对airmail及standard的track是有两种形式的，但是展现只是一种形式处理
					foreach($able_shipping_options as $shipping_k=>$shipping_v){
						if(in_array($shipping_v['id'], array(4,5))) unset($able_shipping_options[$shipping_k]);
					}
					$shipping_options_data = array(
							'msg'=>'OK',
							'status'=>200,
							'data'=>$able_shipping_options,
					);
				}else{
					$shipping_options_data = array(
							'msg'=>'Some items can not be shipped to the address you selected.',
							'status'=>2001,
							'data'=>null,
					);
				}
				
			}else{
				//返回对应提示信息(海外，或cnmail)
				if($shipping_options_list['data']!==null){
					$able_use_shipping = true;
					$shipping_json = $this->formatShippigData($shipping_options_data['data'], $price=0, $address_warehouse);
					$shipping_options_data = array(
							'msg'=>'OK',
							'status'=>200,
							'data'=>$shipping_json,
					);
				}else{//无合适配送方式
					$shipping_options_data = array(
							'data'=>null,
							'msg'=>$shipping_options_list['msg'],
							'status'=>$shipping_options_list['status'],
					);
				}
				
			}
		}else{
			$shipping_options_data = array(
					'data'=>null,
					'msg'=>"Please fill in/select the shipping address above",
					'status'=>2002,
			);
		}
		$this->_view_data['shipping_option_data'] = $shipping_options_data;
		//shipping options (可用物流方式，物流费用，海外本地仓库) end***************
		
		//********************payment method start**************************
		$adyen_list = $this->adyenList($last_price = 1,$country_code = $address_warehouse);
		if(!empty($adyen_list)) $able_use_payment = true;
		//echo "<pre>";print_r($adyen_list);die;
		$this->_view_data['adyen_list'] = $adyen_list;
		//********************payment method start**************************
		
		//********************汇率****************start
		$new_currency_code = currentCurrency();
		$currency = "$";
		$currency_format = $this->getCurrencyNumber();
		$insurance = INSURANCE;
		if($currency_format){
			$currency = $currency_format['currency_format'];
			$insurance = round($insurance*$currency_format['currency_rate'],2);
		}
		//********************汇率****************end
		
		//***************rewords**************start
		$user_rewards = $this->customer->getUserById($user_id);
		$total_rewards = 0;
		if(!empty($user_rewards)){
			//可用rewords
			$total_rewards = $user_rewards['customer_rewards'];
			if($currency_format)$total_rewards = round($total_rewards*$currency_format['currency_rate'],2);
		}else{
			redirect(genURL("login"));
		}
		$able_total_rewards = $new_currency_code." ".$currency." ".$total_rewards;
		$this->_view_data['total_rewards'] = $able_total_rewards;
		//***************rewords**************end
		
		$shippingCharges = 0;	
		$insurancePrice = 0;
		$rewardsBalance = 0;
		$couponSavings = 0;
		$payPrice = $subtotal+$shippingCharges+$insurancePrice-$rewardsBalance-$couponSavings;
		$bool = null;
		
		$this->_view_data['insurance'] = $insurance;
		$this->_view_data['payment_country'] = $payment_addres_country?$payment_addres_country:"";
		$this->_view_data['payment_country_code'] = $address_warehouse?$address_warehouse:"";
		$this->_view_data['currency'] = $currency;
		$this->_view_data['new_currency_code'] = $new_currency_code;
		$this->_view_data['subtotal'] = $subtotal;
		$this->_view_data['shippingCharges'] = $shippingCharges;
		$this->_view_data['insurancePrice'] = $insurancePrice;
		$this->_view_data['rewardsBalance'] = $rewardsBalance;
		$this->_view_data['couponSavings'] = $couponSavings;
		$this->_view_data['payPrice'] = $payPrice;
		$this->_view_data['bool'] = ($able_use_payment && $able_use_address && $able_use_shipping )?true:false;
		
		$this->_view_data['place_order_type'] = 'normal';
		parent::index();
	}

	/**
	 * @desc ajax，地址
	 */
	public function ajaxAddress(){
		//检查用户是否登录，如果没有登录就返回购物车页面
		if(!$this->customer->checkUserLogin())
		{
			$return_data['status'] = 1007;//表明没有登录
			$return_data['msg'] = "NO LOGIN";
			$return_data['data'] = array('url'=>gensslURL('login'),"bool"=>false);
			$this->ajaxReturn($return_data);
		}
		//replace
		$address_id = trim($this->input->get("address_id"));//编辑的地址id
		$default_address_id = trim($this->input->get("addressId"));//选中配送的地址id
		$this->load->model("addressmodel","address");
		
		$userId = $this->customer->getCurrentUserId();
		
		$firstName = $this->input->get('first_name');
		$lastName = $this->input->get('last_name');
		$country = $this->input->get('country');
		$province = $this->input->get('region');
		$city = $this->input->get('city');
		$zipCode = $this->input->get('zipcode');
		$phone = $this->input->get('mobile');
		$address = $this->input->get('address');
		$address2 = $this->input->get('address2');
		$cpfcnpj = $this->input->get('cpf');
		$default = $this->input->get('defaultValue');
		
		$edit_customer_addressid = false;
		$this->database->master->trans_begin();//开启事务
		//是否有默认地址，有默认地址时addrss_id多少
		$default_address = $this->address->getDefaultAddress($userId);
		if(!empty($default_address) && $default==1){//曾经存在，不是现在提交的default地址时，需要取消
			if($default==1 && $address_id && is_numeric($address_id) && $address_id!=$default_address[0]['address_id']){
				//取消原默认地址
				$this->address->editAddress($default_address[0]['address_id'], $data =array('address_default'=>0));
				$edit_customer_addressid = true;
			}
		}else{
			$default = 1;
			$edit_customer_addressid = true;
		}
		
		$data = array(
				"address_id"=>$address_id,
				'customer_id'=>$userId,
				'address_firstname' => $firstName,
				'address_lastname' => $lastName,
				'address_country' => $country,
				'address_province' => $province,
				'address_city' => $city,
				'address_address' => $address,
				'address_phone' => $phone,
				'address_zipcode' => $zipCode,
				'address_cpfcnpj' => $cpfcnpj,
				'address_default' => $default,
				'address_time_update' => date('y-m-d h:i:s',time()),
		);
		
		if($address_id && is_numeric($address_id)){//更新
			$result = $this->address->editAddress($address_id, $data);
			
		}else{//新增
			$result = $this->address->createAddress($data);				
		}
		//修改customer表中address_id
		if($edit_customer_addressid){
			$after_update_address_info = $this->address->getDefaultAddress($userId);
			$this->load->model("customermodel","customer");
			$this->customer->editUser($userId, $info=array('address_id'=>$after_update_address_info[0]['address_id']));
		}
		
		
		if ($this->database->master->trans_status() === FALSE)
		{
			$this->database->master->trans_rollback();
		}
		else
		{
			$this->database->master->trans_commit();
		}
		
		//获取用户的地址信息及默认地址
		$address_data = $this->addressList();
		//格式变换
		$new_address_data = array();
		if(!empty($address_data)){
			foreach($address_data  as $address_key=>$address_val){
				$new_addressId = $address_val['address_id'];
				$new_address_data[$new_addressId]["address_id"] = $address_val['address_id'];
				$new_address_data[$new_addressId]["first_name"] = $address_val['address_firstname'];
				$new_address_data[$new_addressId]["last_name"] = $address_val['address_lastname'];
				$new_address_data[$new_addressId]["address"] = $address_val['address_address'];
				$new_address_data[$new_addressId]["address2"] = "";
				$new_address_data[$new_addressId]["city"] = $address_val['address_city'];
				$new_address_data[$new_addressId]["country"] = $address_val['address_country'];
				$new_address_data[$address_id]['country_name'] = $this->getCountryName($address_val['address_country']);
				$new_address_data[$new_addressId]["zipcode"] = $address_val['address_zipcode'];
				$new_address_data[$new_addressId]["mobile"] = $address_val['address_phone'];
				$new_address_data[$new_addressId]["cpf"] = $address_val['address_cpfcnpj'];
				$new_address_data[$new_addressId]["region"] = $address_val['address_province'];
				$new_address_data[$new_addressId]["defaults"] = $address_val['address_default'];
				$new_address_data[$new_addressId]["checked"] = ($new_addressId==$default_address_id)?1:0;
			}
		}
		
		if($result){
			$msg = "OK";
			$status= 200;
		}else{
			$msg = "Error";
			$status = 1008;
		}
		//$data['address'] = $new_address_data;
		//获取其他数据
		$other_data = $this->ajaxFresh($type="place_order");
		unset($other_data['url']);
		$data = array_merge($new_address_data,$other_data);
		$return_data = array('msg'=>$msg,"status"=>$status,"data"=>$data);
		//echo "<pre>";print_r($return_data);die;
		$this->ajaxReturn($return_data);
		
	}
	
	/**
	 * @desc place_order的ajax动作请求
	 */
	public function ajaxFresh($type=null){
		//检查用户是否登录，如果没有登录就返回购物车页面
		if(!$this->customer->checkUserLogin())
		{
			$return_data['status'] = 1007;//表明没有登录
			$return_data['msg'] = "NO LOGIN";
			$return_data['data'] = array('url'=>genURL('login'),'bool'=>false);
			$this->ajaxReturn($return_data);
		}
		//物流及地址，支付，coupon，rewards默认为不可用
		$able_use_shipping = $able_use_address = $able_use_payment = false;
		$coupon_status = $use_rewards_status = false;
		
		//根据选择地址id（1 获取地址id对应国家code； 2 重新获取物流方式 ； 3 重新获取购物车数据；  4 重新计算总价；5重新计算rewards；6 重新计算coupon数据；）
		$addressId = $this->input->get("addressId");//地址id
		$shippingid = $this->input->get("shippingid");//物流方式id
		$shippingtrack = $this->input->get("shippingtrack");
		$insurance = $this->input->get("insurance");
		$itemsFirst = $this->input->get("itemsFirst");
		$paymentId= $this->input->get("payment");
		$rewardsValue = $this->input->get("rewards");
		$couponValue = $this->input->get("coupon");
		$country = $this->input->get("country");//银行卡支付国家
		$currency = $this->input->get("currency");//支付币种
		//汇率改变  currency
		set_cookie('currency',$currency,864000);
		
		$user_id = $this->session->get('user_id');
		
		//*****汇率********start
		$insurancePrice = INSURANCE;
		$currency_code = currentCurrency();
		$currency = "$";
		$currency_format = $this->getCurrencyNumber();
		//Shipping Insurance费用（作用于不同货币）
		$shipping_insurance = INSURANCE;
		if($currency_format){
			$currency = $currency_format['currency_format'];
			$insurancePrice = round(INSURANCE*$currency_format['currency_rate'],2);//保险费用
			$shipping_insurance = round($shipping_insurance*$currency_format['currency_rate'],2);
		}
		//*****汇率（最终返回的是汇率后价）********end
		
		$return_data = array();
		//获取用户的地址信息及默认地址
		$address_data = $this->addressList();
		//格式变换
		$new_address_data = array();
		if(!empty($address_data)){
			foreach($address_data  as $address_key=>$address_val){
				$address_id = $address_val['address_id'];
				$new_address_data[$address_id]["address_id"] = $address_val['address_id'];
				$new_address_data[$address_id]["first_name"] = $address_val['address_firstname'];
				$new_address_data[$address_id]["last_name"] = $address_val['address_lastname'];
				$new_address_data[$address_id]["address"] = $address_val['address_address'];
				$new_address_data[$address_id]["address2"] = "";
				$new_address_data[$address_id]["city"] = $address_val['address_city'];
				$new_address_data[$address_id]["country"] = $address_val['address_country'];
				$new_address_data[$address_id]['country_name'] = $this->getCountryName($address_val['address_country']);
				$new_address_data[$address_id]["zipcode"] = $address_val['address_zipcode'];
				$new_address_data[$address_id]["mobile"] = $address_val['address_phone'];
				$new_address_data[$address_id]["cpf"] = $address_val['address_cpfcnpj'];
				$new_address_data[$address_id]["region"] = $address_val['address_province'];
				$new_address_data[$address_id]["defaults"] = $address_val['address_default'];
				$new_address_data[$address_id]["checked"] = ($address_id==$addressId)?1:0;
				if($address_id==$addressId) $pay_address_code = $address_val['address_country'];
			}
		}
		$return_data['address'] = $new_address_data;
		
		//购物车信息（cart summary）
		$cart_data = $this->getCart();
		
		//coupon start******************
		$couponSavings = 0;//汇率后价
		$base_couponSavings = 0;
		//$couponValue = "LOVE2015";//全站
		//$couponValue = "BBJA7ZAE8VF";//某分类
		//$couponValue = "6TC333VIFV2";//某商品
		if(trim($couponValue)){
			$coupon_format_result = $this->_userCouponInfo($cart_data,trim($couponValue));
			if($coupon_format_result['status']==200){
				//coupon可用状态
				$coupon_status = true;
				
				//能减多少价钱
				$couponSavings = $coupon_format_result['save_price'];//美元
				$base_couponSavings = $couponSavings;//美元
				if($currency_format){
					$couponSavings = round($couponSavings*$currency_format['currency_rate'],2);
				}
				//有赠品的话，在原购物车数据基础上增加数据（只是显示）
				$cart_data = $coupon_format_result['cart_list'];
				
				$coupon_status = 200;
				$coupon_msg = "OK";
				$coupon_price = $currency_code." ".$currency." ".$couponSavings;
			}else{
				$coupon_status = 404;
				$coupon_msg = "Sorry, the coupon code you entered is not existed. Please enter a valid one.";
				$coupon_price = "";
			}
			
			$return_data['coupon']['status'] = $coupon_status;
			$return_data['coupon']['msg'] = $coupon_msg;
			$return_data['coupon']['price'] = $coupon_price;
		}else{
			//没有coupon，也可支付
			$coupon_status = true;
			$return_data['coupon'] = "";
		}
		//coupon end******************
		
		//计算商品，长宽高，重量，体积，敏感数据,及总价
		$cart_weight_info = $this->computeWeightInfo($cart_data);
		$subtotal = $cart_weight_info['subtotal_price'];//汇率后价
		
		//根据地址id，获取默认地址信息及国家信息
		$this->load->model("addressmodel","address");
		$selected_address_info = $this->address->getAddressById($addressId);
		
		//shipping option  start***************
		if(!empty($selected_address_info)){
			$able_use_address = true;
			$address_country = $selected_address_info['address_country'];
			
			$cart_all_warehouse = $this->cartWarehouse($cart_data);//所有存储仓库信息
			//国家对应仓库可选的物流方式(最终得到那种类型的物流：cnmail,freesea,nomail,null;null类型时，获取对应msg错误码及msg)
			$address_warehouse = isset($selected_address_info['address_country'])?$selected_address_info['address_country']:null;
			$shipping_options_list = $this->_shippingoptions($address_warehouse,$cart_all_warehouse);
			//需要判断物流限制规则（只有normal，且仓库有本地仓库时才会做此判断）
			if($shipping_options_list['exists_rule']==1){
				$able_shipping_options = $this->_ruleshipping($address_warehouse,$cart_weight_info);
				if($able_shipping_options){
					//数据库里存储对airmail及standard的track是有两种形式的，但是展现只是一种形式处理
					foreach($able_shipping_options as $shipping_k=>$shipping_v){
						if(in_array($shipping_v['id'], array(4,5))) unset($able_shipping_options[$shipping_k]);
					}
					$shipping_options_data = array(
							'msg'=>'OK',
							'status'=>200,
							'data'=>$able_shipping_options,
					);
					$able_use_shipping = true;
				}else{
					$shipping_options_data = array(
							'msg'=>'Some items can not be shipped to the address you selected.',
							'status'=>2001,
							'data'=>null,
					);
				}
			
			}else{
				//返回对应提示信息(海外，或cnmail)
				if($shipping_options_list['data']!==null){
					$shipping_json = $this->formatShippigData($shipping_options_data['data'], $price=0, $address_warehouse);
					$shipping_options_data = array(
							'msg'=>'OK',
							'status'=>200,
							'data'=>$shipping_json,
					);
				}else{//无合适配送方式
					$shipping_options_data = array(
							'data'=>null,
							'msg'=>$shipping_options_list['msg'],
							'status'=>$shipping_options_list['status'],
					);
				}
			
			}
		}else{
			//无合适物流配送方式
			$shipping_options_data = array(
					'data'=>null,
					'msg'=>"Please fill in/select the shipping address above",
					'status'=>2002,
			);
		}
		//echo "<pre>shipping";print_r($shipping_options_data);die;
		$return_data['shipping'] = $shipping_options_data;
		//shipping option  end***************
		
		//********************payment method start**************************
		$adyen_list = $this->adyenList($last_price = 1,$country);
		//判断支付方式是否符合范围
		if(isset($adyen_list[$paymentId])){
			$adyen_list[$paymentId]['checked'] = 1;
			$able_use_payment = true;
		}
		$return_data['payment']['data'] = $adyen_list;
		$return_data['payment']['country'] = $this->getCountryName($country);
		$return_data['payment']['countryId'] = $country;
		$return_data['payment']['currency'] = $currency." ".$currency_code;
		$return_data['payment']['currencyId'] = $currency_code;
		//********************payment method end**************************
		
		//****************rewards start****************
		$able_user_baserewards = $able_user_rewards =0;//总共可用rewards
		$rewardsBalance = 0;//rewards 优惠价
		$user_rewards = $this->customer->getUserById($user_id);
		$rewards_status = 200;
		if(!empty($user_rewards)){
			$able_user_baserewards = $able_user_rewards = round($subtotal*REWARDS_DISCOUNT,2);
			//可用rewords
			$total_rewards = $user_rewards['customer_rewards'];
		}
		
		if($currency_format){
			$total_rewards = round($total_rewards*$currency_format['currency_rate'],2);
			$able_user_rewards = round($able_user_rewards*$currency_format['currency_rate'],2);
		}
		$return_data['AvailableBalanceprice'] =  $currency_code." ".$currency." ".$total_rewards;
		if($rewardsValue && is_numeric($rewardsValue)){
			if($rewardsValue > $able_user_rewards){
				$rewards_status = 404;
			}else{
				//coupon可用状态
				$rewards_status = 200;
				$rewardsBalance = $able_user_rewards;//汇率后价
				$use_rewards_status = true;//可以使用rewards
			}
			$return_data['rewards']['status'] = $rewards_status;
			$return_data['rewards']['rewardsPrice'] =  $currency." ".$able_user_rewards;
			if($rewards_status!=200) $return_data['rewards']['msg'] = "Sorry you can use up to ".$currency." ".$able_user_rewards." for this purchase";
			else $return_data['rewards']['msg'] = null;
			
		}else{
			//没有rewards
			$return_data['rewards'] = "";
			$use_rewards_status = true;
		}
		//****************rewards start****************
		
		//物流费用计算 start******
		$shippingCharges = 0;//汇率后价
		if($shipping_options_data['status']==200){
			foreach($shipping_options_data['data'] as $shipping_key=>$shipping_val){
				if($shipping_val['id']==strtolower($shippingid)){
					$shippingCharges += $shipping_val['single_price'];
					if($shippingtrack) {
						$shippingCharges += $shipping_val['single_trackprice'];
						if(in_array($shippingid, array(1,2)))$shippingid +=3;//最终的物流id编号 
					}
				}
			}	
		}
		//物流费用计算 end******
		
		//保险费用计算 start****
		if($insurance==0 || !is_numeric($insurance))$insurancePrice = 0;
		$return_data['insurance'] = $insurance?true:false;
		//保险费用计算 end****
		//最终支付费用 start******* 		
		$payPrice = $subtotal+$shippingCharges+$insurancePrice-$rewardsBalance-$couponSavings;
		//最终支付费用 end*******
		
		//返回购物车数据结构处理
		$json_cart_data =  $this->formatCartData($cart_data);
		//echo "<pre>";print_r($json_cart_data);die;
		$return_data['list'] = $json_cart_data;
		$return_data["subtotal"] = $subtotal;
		$return_data["shippingCharges"] = $shippingCharges;
		$return_data["insurancePrice"] = $insurancePrice;
		$return_data["rewardsBalance"] = $rewardsBalance;
		$return_data["couponSavings"] = $couponSavings;
		$return_data["payPrice"] = $payPrice;
		$return_data['shipping_insurance'] = $shipping_insurance;
		
		//pay url start********
		$bool = false;
		if($able_use_address && $able_use_payment && $able_use_shipping && $use_rewards_status && $coupon_status ){
			$bool = true;
		}
		
		//pay url end********
		$return_data['bool'] = $bool;
		//$return_data["url"] = $url;

		//是否拆包
		$return_data['itemsFirst'] = $itemsFirst?true:0;
		
		//echo "<pre>";print_r($return_data);die;
		$new_data['msg'] = "OK";
		$new_data['status'] = 200;
		$new_data['data'] = $return_data;
		if($type=="place_order"){
			$return_data['couponValue'] = $couponValue;
			$return_data['pay_country'] = $country;//支付国家
			$return_data['pay_address_code'] = $pay_address_code;
			$return_data['compute_list'] = $cart_data;//
			$return_data['shippingid'] = $shippingid;//为1，5时，说明airmail，standard有哦track费用
			$return_data['base_couponSavings'] = $base_couponSavings;
			$return_data['able_user_baserewards'] = $able_user_baserewards;
			$return_data['able_user_rewards'] = $able_user_rewards;
			return $return_data;
		}else{
			//echo "<pre>";print_r($return_data);die;
			$this->ajaxReturn($new_data);
		}
		
	}
	
	/**
	 * @desc 提交订单
	 */
	 public function processOrder(){
		//检查用户是否登录，如果没有登录就返回购物车页面
		if(!$this->customer->checkUserLogin())
		{
			$return_data['status'] = 1007;//表明没有登录
			$return_data['msg'] = "NO LOGIN";
			$return_data['data'] = null;
			$this->ajaxReturn($return_data);
		}
		$user_id = $this->session->get('user_id');
		$email = $this->session->get('email');
		//重新刷新数据
		$result = $this->ajaxFresh($type="place_order");
		//注册订单处理错误回调监控
		
		//支付日志
		
		//获取购物车信息
		$cart_data = $result['list'];
		if(empty($cart_data)) redirect(gensslURL('cart'));//购物车中的商品为空
		
		//地址处理
		$address_data = $result['address'];
		foreach ($address_data as $address_k=>$address_v){
			if($address_v['checked']==1) $address = $address_v; 
		}
		
		// 判断当美国或者澳大利亚的时候州信息是否有
		
		//地址里面国家和省份的判断
		
		//创建订单
		$order = $this->_generateOrder($user_id, $email, $result, $address);
		//创建订单商品
		$orderGoodsList = $this->_generateOrderProduct($order['order_id'], $result, $order);
		$payment_id = $order['payment_id'];
		
		//coupon(下单使用coupon)
		if(isset($result['couponValue']) && !empty($result['couponValue'])){
			$this->load->model('couponmodel','couponmodel');
			$this->couponmodel->updateCouponUseTimes($result['couponValue']);
		}	
		//积分（rewards,往customer表中rewards加1%）
		if(isset($order['order_rewards']) && $order['order_rewards']){
			$this->customer->updateUserRewards($user_id,$order['order_rewards']);
		}				
		//清空购物车(该用户)
			//$this->load->model('cartmodel','cartmodel');
			//$this->cartmodel->bathDelCartWithUserId($user_id);
		//订单邮件发送(后续)
			//$this->_newSendMail($order, $orderGoodsList);
		//支付跳转url返回
		global $payment_list;
		$is_adyen_pay = false;
		if(in_array($payment_id, array_keys($payment_list))){
			switch ($payment_id){
				case 3:
					$payment = "bank";
					break;
				case 1:
					$payment = "paypal_ec";
					break;
				case 2:
					$payment = "paypalsk";
					break;
				default:
					$payment = "adyen";
					$is_adyen_pay = true;
			}
				
			$url = $this->_paymentUrl($payment);
			if($is_adyen_pay==true) $url .="/".$payment_list[$payment_id];
		}else{
			$url = '';
		}
		$return_data['status'] = 200;//表明没有登录
		$return_data['msg'] = "OK";
		$return_data['data'] = array('url'=>$url);
		$this->ajaxReturn($return_data);
	}
	
	
	/**
	 * paypal_ec 没有登录的时候
	 */
	public function paypal_ec(){
		//判断用户没有登录的时候返回购物车
		
		//加载paypal ec的第三方扩展
		$this->load->library('Payment/paypal_ec');
		$paypalEcToken = $this->session->get('paypal_ec_token'); //获取token
		if($paypalEcToken === false || $paypalEcToken == '') { redirect(genURL('cart')); }
		
		//获取支付者id
		$paypalEcPayerid = $this->input->get('PayerID');
		if($paypalEcPayerid === false || $paypalEcPayerid == '') { redirect(genURL('cart')); }
		
		//获取支付信息 第二次交互
		$response = $this->paypal_ec->callPaypalRequest('GetExpressCheckoutDetails', array('TOKEN'=>$paypalEcToken));
		if(empty($response) || !isset($response['ACK']) || $response['ACK'] != 'Success') { redirect(genURL('cart')); }
		//判断支付邮件为空的时候返回购物车
		if(!isset($response['EMAIL'])) { redirect(genURL('cart')); }
		//判断支付用户是不是在黑名单中
		//?????
		//设置paypal ec信息在session中
		$this->session->set('paypal_ec_email', $response['EMAIL']);
		$this->session->set('paypal_ec_payerid', $paypalEcPayerid);
		//地址信息处理（paypal ec）
		echo "<pre>";print_r($response);die;
		//$address = $this->_fetchAddressInfoFromPaypalECResponse($response);
		
		$this->_view_data['place_order_type'] = 'paypal_ec_nologin';
	}
	
	public function ecnologined_ajaxFresh(){
		
	}
	
	public function ecnologined_processOrder(){
			
	}
	
	/**
	 * paypal_ec登录用户支付
	 */
	public function paypal_ec_logined(){
		//检查用户是否登录，如果没有登录就返回购物车页面
		if(!$this->customer->checkUserLogin()) redirect(genURL("cart"));
		$user_id = $this->session->get('user_id');
		
		//加载paypal ec的第三方扩展
		$this->load->library('Payment/paypal_ec');
		//paypal ec支付token
		$paypalEcToken = $this->session->get('paypal_ec_token');
		if($paypalEcToken === false || $paypalEcToken == '') { redirect(genURL('cart')); }
		//支付id获取
		$paypalEcPayerid = $this->input->get('PayerID');
		if($paypalEcPayerid === false || $paypalEcPayerid == '') { redirect(genURL('cart')); }
		//获取解析支付详细信息
		$response = $this->paypal_ec->callPaypalRequest('GetExpressCheckoutDetails',array('TOKEN'=>$paypalEcToken));
		if(empty($response) || !isset($response['ACK']) || $response['ACK'] != 'Success') { redirect(genURL('cart')); }
		if(!isset($response['EMAIL'])) { redirect(genURL('cart')); }
		
		//记录paypal支付信息
		$this->session->set('paypal_ec_email', $response['EMAIL']);
		$this->session->set('paypal_ec_payerid', $paypalEcPayerid);
		
		//redirect('/place_order/index');
		//
		
		$this->_view_data['place_order_type'] = 'paypal_ec_login';
		parent::index( 'placeorder' );
	}
	
	public function eclogined_ajaxFresh(){
		
	}
	public function eclogined_processOrder(){
			
	}
	
	/**
	 * 解析paypalec支付的地址信息
	 * @param  array $response 支付信息数组
	 */
	protected function _fetchAddressInfoFromPaypalECResponse($response) {
		echo "fefdfdfdfdfdfdf";die;
		//初始化地址数组
		$address = array();
		list($address['first_name'], $address['last_name']) = explode(' ', id2name('SHIPTONAME', $response));
	
		//取出详细地址信息
		$address['address'] = id2name('SHIPTOSTREET', $response);
		$address['address2'] = id2name('SHIPTOSTREET2', $response);
		$address['city'] = id2name('SHIPTOCITY', $response);
		$address['province'] = id2name('SHIPTOSTATE', $response);
		$address['zipcode'] = id2name('SHIPTOZIP', $response);
		$address['country'] = id2name('SHIPTOCOUNTRYCODE', $response);
	
		// 加载place order页面的时候 地址信息合法检查
		$address_state_is_none = false;
		if(isset($address['country']) && in_array($address['country'],array('US','AU'))) {
			if(!isset($address['province']) || empty($address['province']) ) {
				$address_state_is_none = true;
			}
		}
		$this->_view_data['address_state_is_none'] = $address_state_is_none;
	
		//格式化地址信息
		$address['address_desc'] = $this->m_address->formatAddress($address);
	
		$this->_view_data['address'] = $address;
		$this->_view_data['flg_single_address'] = true;
		$this->_view_data['flg_show_shipping_list'] = true;
		$this->_view_data['flg_show_payment_list'] = true;
		$this->_view_data['flg_show_paypalec_address'] = true; //是否展示paypalec未登录用户地址弹窗
		$this->_view_data['flg_show_argentina_note'] = (!empty($address) && $address['country'] == 'AR'?true:false);
	
		return $address;
	}
	
	//订单处理
	private function _generateOrder($userId, $email, $result, $address, $payId = false, $payName = false){
		//初始化订单信息
		$order = array();
		$cart = $result['compute_list'];
		//初始化环境信息
		$order['customer_id'] = $userId;//用户id
		$order['language_id'] = currentLanguageId();//语言id
		$order['order_currency'] = currentCurrency();//当前货币类型
		$this->load->model("currencymodel","currencymodel");
		$currencyInfo = $this->currencymodel->todayCurrency();
		$order['order_currency_rate'] = $currencyInfo['currency_rate'];//当前汇率
		$order['order_email'] = $email;//邮件
		
		//地址处理
		$order['address_id'] = addslashes(id2name('address_id',$address,0));
		$order['order_address_firstname'] = addslashes(id2name('first_name',$address));
		$order['order_address_lastname'] = addslashes(id2name('last_name',$address));
		$order['order_address_phone'] = addslashes(id2name('mobile',$address));
		$order['order_address_country'] = addslashes(id2name('country',$address));
		$order['order_address_state'] = addslashes(id2name('region',$address));
		$order['order_address_city'] = addslashes(id2name('city',$address));
		$order['order_address_street'] = addslashes(id2name('address',$address));
		$order['order_address_postalcode'] = addslashes(id2name('zipcode',$address));
		$order['order_address_cpfcnpj'] = addslashes(id2name('cpf',$address));
		//购物车级别的促销  暂时为0
		$order['order_price_discount'] = 0;
		//购物车信息处(注意赠品价钱)
		$order_price_product = 0;//库 中price*数量
		$order_price_subtotal = 0;//discount后的price*qunatity
		$order_price_discount = 0;//discount减掉的价
		$order_baseprice_product = 0;
		$order_baseprice_subtotal = 0;
		$order_baseprice_discount = 0;
		foreach ($cart as $cart_k=>$cart_v){
			if(isset($cart_v['product_coupon_price'])) continue;//赠品时，oder的价钱处理？？？
			$single_discount = round($cart_v['product_info']['product_price'] -  $cart_v['product_info']['product_discount_price'],2);
			$single_base_discount = round($cart_v['product_info']['product_baseprice'] -  $cart_v['product_info']['product_basediscount_price'],2);
			$qunatity = $cart_v['product_quantity'];
			$order_price_product += round($qunatity*$cart_v['product_info']['product_price'],2);
			$order_price_subtotal += round($qunatity*$cart_v['product_info']['product_discount_price'],2);
			$order_price_discount += round($qunatity*$single_discount,2);
			$order_baseprice_product += round($qunatity*$cart_v['product_info']['product_baseprice'],2);
			$order_baseprice_subtotal += round($qunatity*$cart_v['product_info']['product_basediscount_price'],2);
			$order_baseprice_discount += round($qunatity*$single_base_discount,2);
		}
		$order['order_price_product'] = $order_price_product;
		$order['order_price_subtotal'] = $order_price_subtotal;
		$order['order_price_discount'] = $order_price_discount;
		$order['order_baseprice_product'] = $order_baseprice_product;
		$order['order_baseprice_subtotal'] = $order_baseprice_subtotal;
		$order['order_baseprice_discount'] = $order_baseprice_discount;
		//shipping 处理
		$shipping_data = $result['shipping']['data'];
		$order['shipping_id'] = $result['shippingid'];
		$order_baseprice_shipping = 0;
		$order_price_shipping = 0;
		foreach($shipping_data as $shiping_k=>$shipping_v){
			if($shipping_v['id']==$result['shippingid']){
				$order_baseprice_shipping = $shipping_v['base_trackprice']+$shipping_v['base_price'];
				$order_price_shipping = $shipping_v['single_price']+$shipping_v['true_trackprice'];
			}
		}
		//物流 运费
		$order['order_price_shipping'] = $order_price_shipping;
		$order['order_baseprice_shipping'] = $order_baseprice_shipping;
		//保险
		$order['order_price_insurance'] = INSURANCE*$order['order_currency_rate'];
		$order['order_baseprice_insurance'] = INSURANCE;
		//coupon
		$order['order_coupon'] = $result['couponValue'];
		$baseprice_coupon = 0;
		if($result['couponValue']) {
			$baseprice_coupon = $order['order_baseprice_coupon'] = $result['base_couponSavings'];
			$order['order_price_coupon'] = $result['couponSavings']; //coupon减的折扣价
		} 
		//rewards
		$order['order_price_rewards'] = $result['able_user_rewards'];//rewards 折扣价 正数，保留两位小数
		$order['order_baseprice_rewards'] = $result['able_user_baserewards'];
		$order['order_country_payment'] = $result['pay_country'];//支付国家  支付时选择支付国家
		//用户级别
		$order['order_customer_type'] = $this->customer->getRewordsRate($userId);
		$order['order_country_shipping'] = $result['pay_address_code'];//配送国家 address  country_code
		//最终支付
		$order['order_price'] =  $result["payPrice"]; ////用户 最终支付金额(subtotal+insureance+shipping-discount-coupon-rewards)
		$order['order_baseprice'] = round($order_baseprice_subtotal-$result['able_user_baserewards']-$baseprice_coupon,2);//汇率之前 用户最终支付金额
		$order['order_rewards'] = $order['order_baseprice']*$order['order_customer_type']/100;//积分（ordertotal -运费-保险）* 汇率？？
		/*
		//order_transnumber//交易号，成功支付后才有
		//order_status_email_pay1
		//order_status_email_pay2
		//order_status_email_review
		//order_time_pay//
		//order_time_shipped//
		*/
		
		//支付方式处理
		$payment_list = $result['payment']['data'];
		$payment_id = 3;//bank（默认bank支付方式）
		foreach ($payment_list as $k=>$v){
			if($v['checked']==1) $payment_id = $k;
		}
		$order['payment_id'] = $payment_id;
		
		//跟踪
		$order['order_gaid'] = generateGACid();
		
		//入口
		$order['order_entrance'] = EBPLATEFORM;
		
		//是否拆包
		$order['order_flg_separate_package'] = $result['itemsFirst'];
		
		//保险（保险价，是否保险）
		$order['order_baseprice_insurance'] = $result['insurancePrice'];
		$order['order_flg_insurance'] = $result['insurancePrice']?1:0;
		
		//订单状态
		$order['order_status'] = OD_CREATE;//新建
		
		$order['order_time_create'] = $order['order_time_lastmodified'] = date("Y-m-d H:i:s",time());
		
		$this->load->model("ordermodel","ordermodel");
		//创建订单
		$orderId = $this->ordermodel->createOrder($order);
		
		//订单号生成
		$orderSn = ORDER_PREFIX . date('Ymd',requestTime()) . str_pad($orderId,8,'0',STR_PAD_LEFT);
		//更新订单sn
		$this->ordermodel->updateOrder($orderId, array(
				'order_code' => $orderSn,
		));
		
		//记录订单信息
		$this->session->set('order_code',$orderSn);
		
		$order['order_id'] = $orderId;
		$order['order_code'] = $orderSn;
		//echo "<pre>";print_r($order);die;
		return $order;
	}
	
	//订单商品处理
	private function _generateOrderProduct($orderId, $cart, $order){
		$orderGoodsList = array();
		$goodsSaleList = array();
		$this->load->model('goodsmodel','product');
		$this->load->model('discountrangemodel','discountrangemodel');
		foreach($cart['compute_list'] as $key=>$val){
			$product_id = $val['product_id'];
			$qunatity = $val['product_quantity'];
			$product = array();
			$product['order_id'] = $orderId;
			$product['product_id'] = $product_id;
			$product['product_sku'] = $val['product_sku'];
			$product['order_product_name'] = $val['product_info']['product_description_name'];
			$product['order_product_image'] = $val['product_info']['product_image'];
			$product['order_product_quantity'] = $qunatity;
			$product['order_product_price'] = $val['product_info']['product_discount_price'];
			$product['order_product_price_market'] = $val['product_info']['product_price_market'];
			$product['order_product_baseprice'] = $val['product_info']['product_basediscount_price'];
			$product['order_product_baseprice_market'] = $val['product_info']['product_baseprice_market'];
			$product['order_product_price_subtotal'] = round($qunatity*$val['product_info']['product_discount_price'],2);
			$product['order_product_baseprice_subtotal'] = round($qunatity*$val['product_info']['product_basediscount_price'],2);
			
			$price_discount = $val['product_info']['product_price']-$val['product_info']['product_discount_price'];
			$baseprice_discount = $val['product_info']['product_baseprice']-$val['product_info']['product_basediscount_price'];
			$product['order_product_price_discount'] = round($qunatity*$price_discount,2);
			$product['order_product_baseprice_discount'] = round($qunatity*$baseprice_discount,2);
			
			$product['order_product_promote_type'] = 1;//暂时只有1
			$discount_info = $this->discountrangemodel->getRangeExistsWithId($product_id);
			if($discount_info) $product['order_product_promote_id'] = $discount_info[0]['promote_discount_id'];
			
			$product['order_product_warehouse'] = $val['product_sku_info']['product_sku_warehouse'];
			$product['order_product_time_create'] = date('Y-m-d H:i:s',requestTime());
			$orderGoodsList[] = $product;
			//更新商品销量
			$this->product->updatePorductSales($product_id ,$qunatity);
		}

		//添加订单中的商品
		$this->load->model("orderodel","ordermodel");
		$this->ordermodel->createOrderProductbatch($orderGoodsList);
		
		return $orderGoodsList;
	}
	
	/**
	 * @desc 对购物车数据格式处理，转换成前端需要json数据格式
	 * @param unknown $cart_data
	 * @return multitype:|Ambigous <multitype:, string>
	 */
	private function formatCartData($cart_data){
		$result = array();
		if(empty($cart_data)) return $result; 
		foreach ($cart_data as $key=>$val){
			$result[$key]['pid'] = $val['product_id'];
			$result[$key]['pic'] = PRODUCT_IMAGE_URL.MIN_PRODUCT_IMG."/".$val['product_info']['product_image'];
			$result[$key]['title'] = $val['product_info']['product_description_name'];
			$result[$key]['attr'] = $val['attr'];
			$result[$key]['itemPrice'] = $val['product_info']['product_currency'].$val['product_info']['product_discount_price'];
			$result[$key]['quantity'] = $val['product_quantity'];
			$result[$key]['price'] = $val['product_info']['product_currency'].round($val['product_quantity']*$val['product_info']['product_discount_price'],2);
		}	
		
		return $result;
	}
	
	/**
	 * 通过支付方式选取支付url
	 * @param  string $payment 支付方式
	 */
	protected function _paymentUrl($payment = 'adyen') {
		$url =  gensslURL('success') . '?payment=bank';
		$paymentUrlArray = array(
				'bank'=>gensslURL('success') . '?payment=bank',
				'adyen' => gensslURL('pay/redirect/adyen'),
				'paypalsk' => gensslURL('paypal_payment'),//本地paypal
				'paypal_ec' =>gensslURL('success') . '?payment=paypal_ec',//paypel_ec
				'paypal_ec_unpaid' =>gensslURL('unpaid') . '?payment=paypal_ec',
		);
		return isset($paymentUrlArray[$payment]) && !empty($paymentUrlArray[$payment])? $paymentUrlArray[$payment]:$url;
	}
	
	/**
	 * @desc coupon
	 */
	private function _userCouponInfo($cart_list,$coupon){
		$saving_price = 0;
		$return_cart_list = array();
		$base_coupon_info = $this->_ableUseBaseCoupon($coupon);
		
		$in_cart_list = array();
		$noin_cart_list = array();
		if(!empty($base_coupon_info)){
			//范围：全站，某商品，某分类？
			$coupon_type = $base_coupon_info['coupon_range_type'];
			$coupon_id = $base_coupon_info['coupon_id'];
			$coupon_range = $base_coupon_info['coupon_range'];
			switch ($coupon_type){
				case COUPON_RANGE_TYPE_SITE://全站
					$in_cart_list = $cart_list;
					break;
				case COUPON_RANGE_TYPE_CATEGORY://某分类
					$compute_cart_list = $this->_cartInCategory($cart_list, $coupon_range);
					$in_cart_list = $compute_cart_list['in'];
					$noin_cart_list = $compute_cart_list['noin'];
					break;
				case COUPON_RANGE_TYPE_PRODUCT://某商品
					$compute_cart_list = $this->_cartInProduct($cart_list, $coupon_range);
					$in_cart_list = $compute_cart_list['in'];
					$noin_cart_list = $compute_cart_list['noin'];
					break;
			}	
			
			//计算coupon能省多少价或赠品(对符合条件的商品列表)
			if(!empty($in_cart_list)){
			//if(!empty($noin_cart_list)){
				$effect_info = $this->_cartEffectInfo($coupon_id,$in_cart_list);
				//$effect_info = $this->_cartEffectInfo($coupon_id,$noin_cart_list);
				$saving_price = $effect_info['save_price'];//美元
				$in_cart_list = $effect_info['cart_list'];
				
			}
			$result['status'] = 200;
		}else{
			$result['status'] = 404;
		}
		
		$result['save_price'] = $saving_price;//美元
		$result['cart_list'] = array_merge($noin_cart_list,$in_cart_list);
		return $result;
	}
	
	/**
	 * @desc 计算商品折扣，赠品情况
	 * @param unknown $coupon_id
	 * @param unknown $cart_list（符合coupon range type条件的商品列表）
	 */
	private function _cartEffectInfo($coupon_id,$cart_list){
		$saving_price = 0;//省多少钱
		$return_cart_list = array();//商品列表（如果有赠品时，会添加进到里面）
		
		$this->load->model("couponmodel","coupon");
		//$coupon_id = 188;
		//$coupon_id = 171;
		$coupon_effect_info = $this->coupon->couponeffectInfoWithCouponid($coupon_id);
		if(!empty($coupon_effect_info)){
			$coupon_type = $coupon_effect_info[0]['coupon_effect_type'];
			switch($coupon_type){
				case COUPON_TYPE_REDUCE://满减
					//直接"减钱"(美元)
					$saving_price = $coupon_effect_info[0]['coupon_effect_value'];
					$return_cart_list = $cart_list;
					break;
				case COUPON_TYPE_DISCOUNT://满折扣
					//直接"打折"(美元)
					$total_price = $this->_computeCartPrice($cart_list);
					$saving_price = $total_price*$coupon_effect_info[0]['coupon_effect_value']/100;
					$return_cart_list = $cart_list;	
					break;
				case COUPON_TYPE_REDUCE_LEVEL://条件满减
					//美元，没有汇率转换计算的
					$total_price = $this->_computeCartPrice($cart_list);
					$saving_price = $this->_filterCouponEffect($total_price ,$coupon_effect_info);
					$return_cart_list = $cart_list;
					break;
				case COUPON_TYPE_DISCOUNT_LEVEL://条件下满折扣
					//美元，没有汇率转换计算的
					$total_price = $this->_computeCartPrice($cart_list);
					$effect_value = $this->_filterCouponEffect($total_price ,$coupon_effect_info);
					$saving_price = $effect_value*$total_price/100;
					$return_cart_list = $cart_list;
					break;
				case COUPON_TYPE_GIFT://赠品
					$gift_product_array = json_decode($coupon_effect_info[0]['coupon_effect_value'],true);
					//根据sku获取商品信息 （数据结构与购物车商品一致）
					$gift_product_info = $this->_giftInfoWithSku($gift_product_array['sku'],$gift_product_array['limit']);
					if(!empty($gift_product_info)){
						$gift_product_info['product_coupon_price'] = $gift_product_array['price'];
						$cart_list[] = $gift_product_info;
						//美元
						$saving_price = $gift_product_info['product_quantity']*($gift_product_info['product_info']['product_basediscount_price']-$gift_product_array['price']);
					}
					$return_cart_list = $cart_list;
					break;
			}
			
		}
		
		$result = array(
			'save_price'=>$saving_price,//美元
			'cart_list'=>$return_cart_list,
		);
		//echo "<pre>";print_r($result);die;
		return $result;
	}
	
	/**
	 * @desc 根据sku获取商品信息（****数据格式与购物车商品信息一致***）
	 * @param unknown $sku
	 * @param number $quantity
	 * @return multitype:|multitype:string number unknown NULL
	 */
	private function _giftInfoWithSku($sku,$quantity = 1){
		//获取product_id（类似cart表数据）
		$cart_list = array(
				'customer_id'=>'',
				'cart_type'=>1,
				'product_id'=>'',
				'product_sku'=>$sku,
				'product_quantity'=>$quantity,
		);
		$product_sku = $cart_list['product_sku'];
		
		//获取商品仓库,长，宽，高等信息
		$this->load->model("attributeproductmodel","productsku");
		$sku_info = $this->productsku->productSkuinfoWithOnlySku($product_sku);
		if(empty($sku_info)) return array();
		
		$cart_list['product_sku_info'] = $sku_info[0];
		$cart_list['product_id'] = $sku_info[0]['product_id'];
		$this->load->model("goodsmodel","product");
		$language_code = currentLanguageCode();
		
		$product_id = $cart_list['product_id'];
		//商品基本信息
		$product_info = $this->product->getinfoNostatus($product_id,$language_code);
		$product_info = $this->singleProductWithPrice($product_info);//扩展价格
		$cart_list['product_info'] = $product_info;
		
		//获取sku对应属性信息
		$attr_value_info = $this->attrAndValueWithSku($product_sku);
		if(!empty($attr_value_info)){
			foreach ($attr_value_info as $attr_key=>$attr_val){
				//$cart_list['attr'][$attr_key]['attr_name'] = $attr_val['attr_name'];
				//$cart_list['attr'][$attr_key]['attr_value_name'] = $attr_val['attr_value_name'];
				$cart_list['attr'][$attr_key]['name'] = $attr_val['attr_name'];
				$cart_list['attr'][$attr_key]['value'] = $attr_val['attr_value_name'];
			}
		}else{
			$cart_list['attr'] = "";
		}
			
		return $cart_list;
	}
	/**
	 * @desc 根据价钱，判断最符合条件的coupon_effect的折扣或便宜价
	 * @param unknown $price
	 * @param unknown $coupon_effect_list(根据coupon_id获取的coupon_effect表的数据)
	 * @return number
	 */
	private function _filterCouponEffect($price ,$coupon_effect_list){
		$coupon_effect_value = 0;
		//echo "<pre>fil_coupon_list";print_r($coupon_effect_list);die;
		foreach ($coupon_effect_list as $k=>$v){
			if($v['coupon_effect_price']<=$price) $coupon_effect_value = max($coupon_effect_value,$v['coupon_effect_price']);
		}
		
		return $coupon_effect_value;
	}
	
	/**
	 * @desc 计算商品列表总价
	 * @param unknown $cart_list
	 */
	private function _computeCartPrice($cart_list){
		$total_price = 0;
		foreach ($cart_list as $k=>$v){
			$price = $v['product_info']['product_basediscount_price'];
			$qunatity = $v['product_quantity'];
			$total_price += $qunatity*$price;	
		}
			
		return $total_price;
	}
	
	/**
	 * @desc 计算在属于某些分类下的商品列表
	 * @param unknown $cart_list
	 * @param unknown $category_string（以逗号形式链接的分类字符串）
	 * @return 返回属于分类及不属于分类下的商品列表
	 */
	private function _cartInCategory($cart_list,$category_string){
		$result = array('in'=>array(),'noin'=>array());
		$category_array = array();
		if(stripos($category_string, ",")){
			$category_array = explode(",", $category_string);
		}else{
			$category_array = array($category_string);
		}
		foreach ($cart_list as $k=>$v){
			$category_path = $v['product_info']['product_path'];
			if(stripos($category_path, "/")){
				$category_path_array = explode("/", $category_path);
			}else{
				$category_path_array = array($category_path);
			}
			
			$intersect = array_intersect($category_array,$category_path_array);
			if(!empty($intersect)){
				$result['in'][] = $v;				
			}else{
				$result['noin'][] = $v;
			}
		}

		//echo "<pre>";print_r($result);die;
		return $result;
	}
	
	/**
	 * @desc 计算在属于某些商品的列表
	 * @param unknown $cart_list
	 * @param unknown $product_string（以逗号形式链接的分类字符串）
	 * @return 返回属于商品及不属于商品下的商品列表
	 */
	private function _cartInProduct($cart_list,$product_string){
		//echo "<pre>";print_r($cart_list);die;
		$result = array('in'=>array(),'noin'=>array());
		$product_array = array();
		if(stripos($product_string, ",")){
			$product_array = explode(",", $product_string);
		}else{
			$product_array = array($product_string);
		}
		foreach ($cart_list as $k=>$v){
			$product_id = $v['product_info']['product_id'];
			if(in_array($product_id, $product_array)){
				$result['in'][] = $v;
			}else{
				$result['noin'][] = $v;
			}
		}
		
		//echo "<pre>";print_r($result);die;
		return $result;
	}
	
	//符合全站
	private function _cartFormatInSite($coupon_id,$cart_list){
		$result = array();
		//effect表具体值（）
		$this->load->model("couponmodel","coupon");
		$coupon_effect_info = $this->coupon->couponeffectInfoWithCouponid($coupon_id);
		$discount_price = 0;
		$gift_product = array();
		if(!empty($coupon_effect_info)){
			$coupon_type = $coupon_effect_info[0]['coupon_effect_type'];
			switch($coupon_type){
				case COUPON_TYPE_REDUCE://满减
					
					break;
				case COUPON_TYPE_DISCOUNT://满折扣
					
					break;
				case COUPON_TYPE_REDUCE_LEVEL://条件满减
					
					break;
				case COUPON_TYPE_DISCOUNT_LEVEL://条件下满折扣
					
					break;
				case COUPON_TYPE_GIFT://赠品
					
					break;
			}
				
				
		}
	}
	
	/**
	 * @desc 返回coupon基本信息，符合时间，语言限制条件
	 * @param unknown $coupon_code
	 * @return multitype:|Ambigous <multitype:, unknown>
	 */
	private function _ableUseBaseCoupon($coupon_code){
		$info = array();
		if(empty($coupon_code)) return $info;
		
		$this->load->model("couponmodel","coupon");
		$base_coupon_info = $this->coupon->couponInfoWithCode($coupon_code);
		//echo "<pre>";print_r($base_coupon_info);die;
		if($base_coupon_info){
			//时间
			$start_time = strtotime($base_coupon_info[0]['coupon_time_start']);
			$end_time = strtotime($base_coupon_info[0]['coupon_time_end']);
			if($start_time<time() && $end_time>time()){
				//语言限制及使用次数限制
				$language_id = currentLanguageId();
				$coupon_language_range =  explode("/", $base_coupon_info[0]['coupon_language']);
				$use_times = $this->coupon->couponUseTime($coupon_code);
				if(in_array($language_id, $coupon_language_range) && $base_coupon_info[0]['coupon_limit_total']>=$use_times){
					$info = $base_coupon_info[0];
				}
			}
		}
		return $info;
	}
	
	//国家
	private function _country(){
		$this->load->model("currencymodel","currencymodel");
		$country_list = $this->currencymodel->allCountry();
		//echo "<pre>country";print_r($country_list);die;
		return $country_list;
	}
	
	//货币
	private function _currency(){
		$this->load->model("currencymodel","currencymodel");
		$currency_list = $this->currencymodel->currencyList();
		//echo "<pre>";print_r($currency_list);die;
		return $currency_list;
	}
	
	/**
	 * @desc normal物流方式时，根据地址，购物车商品（长宽高，体积，重量，敏感品），筛选normal配送方式
	 * @param unknown $address_warehouse
	 * @param unknown $cart_data
	 * @return multitype:Ambigous <multitype:, multitype:number string >
	 */
	private function  _ruleshipping($address_warehouse,$cart_weight_info){
		$rules = array();
		//根据购物车商品的长，宽，高等条件，筛选normal物流方式
		$able_shipping_list = $this->_filterNormalShipping($address_warehouse,$cart_weight_info);
		if(!empty($able_shipping_list)){
			//计算快递价格
			foreach ($able_shipping_list as $able_shipp_key=>$able_shipp_val){
				$type = strtolower($able_shipp_val['country_shipping_rule_shipping_code']);
				//if(in_array($type, array('register_airmail','register_standard'))) continue;
				$shipping_price = $this->_shippingPrice($cart_weight_info['price_weight'],$type,$address_warehouse);
				//组合返回给前端的数据 start
				if($shipping_price!==false){
					$rules[] = $this->formatShippigData($type,$shipping_price,$address_warehouse);
				}
				//组合返回给前端的数据 end
			}
			//echo "<pre>last";print_r($rules);die;
		}	
		
		return $rules;
	}
	
	/**
	 * @desc  计算对应快递运费
	 * @param unknown $weight (体积重 = 长*宽*高/5000*数量 ;  weight = max(实重,体积重);)
	 * @param unknown $type (对应快递方式)
	 */
	private function _shippingPrice($weight,$type,$warehouse){
		$price = 0;
		//单位为$
		switch ($type){
			case "standard":
				$ratecny = $this->_getCnyRate();
				//(10+120*weight)/RateCNY-weight*12
				$price = round((10+120*weight)/$ratecny-weight*12,4);
				break;
			case "express":
				$ratecny = $this->_getCnyRate();
				$new_price = $this->_expressZonePrice($warehouse ,$weight);
				if($new_price!=false){
					$price = $new_price/$ratecny-weight*12;
				}else{
					$price = false;
				}
				break;
			case "register_airmail":
				$price = 1.69;
				break;
			case "register_standard":
				$ratecny = $this->_getCnyRate();
				//(10+120*weight)/RateCNY-weight*12
				$price = round((10+120*weight)/$ratecny-weight*12,4);
				//standard+(DE?+8.69:+2.19)
				$price = (strtoupper($warehouse)=="DE")?($price+8.69):($price+2.19);
				break;
			case "airmail":
			default:
				//其他价格都为0
				$price = 0;	
		}
		return $price;
		
	}
	
	/**
	 * @desc 根据国家code及weight = max(实重,体积重)，返回express价格
	 * @param unknown $country_code
	 * @param unknown $weight
	 * @return boolean|number
	 */
	private function _expressZonePrice($country_code ,$weight){
		if(empty($country_code)) return false;
		$this->load->model("expresszonemodel","expresszone");
		$zone_info = $this->expresszone->expressCountry2Zone($country_code);
		if(!empty($zone_info)){
			$zone_id = $zone_info[0]['express_zone_id'];
			//获取zone对应的价格
			$all_zone_price_info = $this->expresszone->expressZoneList($zone_id);
			if(!empty($all_zone_price_info)){
				$info = $all_zone_price_info[0];
				$express_zone_price_first = $info['express_zone_price_first'];
				$express_zone_price_step_30 = $info['express_zone_price_step_30'];
				$express_zone_price_step_50 = $info['express_zone_price_step_50'];
				$express_zone_price_step_70 = $info['express_zone_price_step_70'];
				$express_zone_price_step_100 = $info['express_zone_price_step_100'];
				$express_zone_price_step_200 = $info['express_zone_price_step_200'];
				$express_zone_price_step_299 = $info['express_zone_price_step_299'];
				$express_zone_price_step_300 = $info['express_zone_price_step_300'];
				$express_zone_price_step_300p = $info['express_zone_price_step_300p'];
				
				if($weight < 20.5){
					//重量小于0.5按0.5计算
					$price = $express_zone_price_first+ceil( ( $weight - 0.5 ) / 0.5 ) * express_zone_price_step_20;
				}elseif($weight>=20.5 && $weight <= 30){
					$price = ceil($weight) * $express_zone_price_step_30;
				}elseif($weight>30 && $weight <= 50){
					$price = ceil($weight) * $express_zone_price_step_50;
				}elseif($weight>50 && $weight <= 70){
					$price = ceil($weight) * $express_zone_price_step_70;
				}elseif($weight >70 && $weight <=100){
					$price = ceil($weight) * $express_zone_price_step_100;
				}elseif($weight>100 && $weight <= 200){
					$price = ceil($weight) * $express_zone_price_step_200;
				}elseif($weight>200 && $weight <= 299){
					$price = ceil($weight) * $express_zone_price_step_299;
				}elseif($weight>299 && $weight <= 300){
					$price = ceil($weight) * $express_zone_price_step_300;
				}elseif($weight>300){
					$price = ceil($weight) * $express_zone_price_step_300p;
				}
				
				return $price;
			}else{
				return false;	
			}
			
		}else{
			return false;
		}
			
	}
	
	/**
	 * @desc 人民币对美元汇率
	 * @return Ambigous <number, unknown>
	 */
	private function _getCnyRate(){
		$ratecny = 1;
		$this->load->model("currencymodel","currencymodel");
		$currency = $this->currencymodel->currencyList();
		foreach ($currency as $currency_key=>$currency_val){
			if($currency_val['currency_code'] == strtoupper("cny"))
				$ratecny = 	$currency_val['currency_rate'];
		}	
		
		return $ratecny;
	}
	
	/**
	 * @desc 需要判断商品是否符合物流配送规则（只针对 普通物流Normal）
	 * @param unknown $country_code 国家code
	 * @param unknown $weight_info  重量，体积，length，敏感值范围数组
	 * @return array 符合条件的normal物流方式(返回为空时，结果为不可送，没有物流运送方式)
	 */
	private function _filterNormalShipping($country_code,$weight_info){
		$result = array();
		if(empty($country_code))  return $result;
		if(empty($weight_info)){
			$weight_info = array(
					'weight'=>0,'volume'=>0,'length'=>0,'sentitve_range'=>array()
			);
		}
		
		//物流规则
		$this->load->model("countryshippmodel","shipping");
		$country_rule = $this->shipping->countryRuleList($country_code);
		
		if(!empty($country_rule)){
			foreach ($country_rule as $key=>$val){
				if($val['country_shipping_rule_status_active']==1){//不用判断直接可用
					$result[] = $val;
					continue;
				}
				if($val['country_shipping_rule_status_disable']==1){//不用判断直接不可用
					continue;
				}
				if($val['country_shipping_rule_status_sensitive_disable']>0){//严格匹配敏感品（任何类型的敏感品都不可用）
					continue;
				}
				$sentitve_count = array_intersect($weight_info['sentitve_range'],array(1,5)); 
				if($val['country_shipping_rule_status_battery_disable']>0 && count($sentitve_count)){//判断敏感品类型是否是1，5（电池类）
					continue;
				}
				$weight = $val['country_shipping_rule_limit_weight'];
				$volume = $val['country_shipping_rule_limit_volume'];
				$length = $val['country_shipping_rule_limit_length'];
				if($weight>0 && $weight<$weight_info['weight']){//重量
					continue;
				}				
				if($volume>0 && $volume < $weight_info['volume']){//体积
					continue;
				}
				if($length>0 && $length < $weight_info['length']){//长宽
					continue;
				}
				
				$result[] = $val;
			}
		}
		
		//echo "<pre>";print_r($result);die;
		return $result;
	}
	
	/**
	 * @desc 计算商品：重量，体积，长度,敏感类型值数组（非海外仓库商品需要计算）
	 * @param unknown $cart_list 商品列表数据
	 * @return multitype:multitype: number Ambigous <number, mixed>
	 */
	private function computeWeightInfo($cart_list){
		$result = array();
		$total_weight = 0;
		$total_volume = 0;
		$total_length = 0;
		$sensitive_type_array = array();//所有商品敏感值
		$price_weight = 0;//用于快递运费的价格计算
		$volume_weight = 0;//体积重
		$subtotal_price = 0;//
		
		global $warehouse_range_array;
		if(!empty($cart_list)){
			foreach ($cart_list as $key=>$val){
				$weight = $val['product_sku_info']['product_sku_weight'];
				$width = $val['product_sku_info']['product_sku_width'];
				$height = $val['product_sku_info']['product_sku_height'];
				$length = $val['product_sku_info']['product_sku_length'];
				$quantity = $val['product_quantity'];
				$sentitve_type = $val['product_sku_info']['product_sku_sensitive_type'];
				
				if(!in_array($val['product_sku_info']['product_sku_warehouse'], $warehouse_range_array)){
					$total_weight += round($quantity*$weight,4);
					$total_volume += round($width*$height*$length*$quantity,4);
					$total_length = max($total_length,$width,$height,$length);
					if($sentitve_type>0) array_push($sensitive_type_array, $sentitve_type);
					$volume_weight += round($total_volume/500,4); 
				}	
				
				$subtotal_price += $quantity*$val['product_info']['product_discount_price'];
			}
			$price_weight = max($volume_weight,$total_weight);
		}
		$result = array(
			'weight'=>$total_weight,
			'volume'=>$total_volume,
			'length'=>$total_length,
			'sentitve_range'=>$sensitive_type_array,
			'price_weight'=>$price_weight,
			'subtotal_price'=>round($subtotal_price,2),
		);
		
		//echo "<pre>compute";print_r($result);die;
		return $result;
		
	}
	
	/**
	 * @desc 根据国家地址及所有商品仓库，判断可用物流方式
	 * @param string $address_code （小写 国家code） 
	 * @param array $all_warehouse  (仓库数组)
	 * @return multitype:Ambigous <number, string> string Ambigous <NULL, string> boolean
	 */
	private function _shippingoptions($address_code , $all_warehouse){
		$result = array();
		$shipping_status = 200;
		$shipping_msg = "OK";
		$shipping_exists_rule = false;
		
		//物流数据格式
		//$shipping_format_data = $this->formatShippigData();
		if(empty($address_code)){//没有配送地址
			$shipping_msg = "Please fill in/select the shipping address above";
			$shipping_status = "2002";
			$shipping_name = null;
			
		}else{
			//判断海外仓库信息
			$cart_overseas = array();
			$cart_cn_warehouse = array();
			foreach ($all_warehouse as $seakey=>$seaval){
				global $warehouse_range_array;
				if(in_array(strtolower($seaval), $warehouse_range_array)){
					$cart_overseas[] = $seaval;
				}else{
					$cart_cn_warehouse[] = $seaval;
				}
			}
			$cart_overseas_num = count($cart_overseas);
			
			if($cart_overseas_num > 1){//多个海外仓库
				$shipping_msg = "Some items can not be shipped to the address you selected.";
				$shipping_status = "2001";
				$shipping_name = null;
			}elseif($cart_overseas_num==1){//一个海外仓库时
				//判断与国家地址是否一致
				if($cart_overseas[0]==$address_code){
					if(count($cart_cn_warehouse)==0){
						//海外，免费
						$shipping_name = 'freesea';
					}else{
						//部分海外仓库，部分中国 （显示五种物流方式）
						$shipping_name = 'normal';
						$shipping_exists_rule = true;
					}
					
				}else{//国家与海外仓库不一致
					$shipping_msg = "Some items can not be shipped to the address you selected.";
					$shipping_status = "2001";
					$shipping_name = null;
				}
			}else{//本地仓库
				if($address_code==strtolower('CN')){//地址为中国
					//cnmail 免费
					$shipping_name = 'cnmail';
				}else{
					//显示五种物流方式
					$shipping_name = 'normal';
					$shipping_exists_rule = true;
				}
				
			}
			
		}
		
		$result['status'] = $shipping_status;
		$result['msg'] = $shipping_msg;
		$result['data'] = $shipping_name;
		$result['exists_rule'] = $shipping_exists_rule;//true: 需要判断，规则（长，宽，高，体积，是否敏感品）
		
		return $result;
	}
	
	/**
	 * @desc 根据购物车数据，返回购物车所有商品的所在仓库
	 * @param unknown $cart_list
	 */
	private function cartWarehouse($cart_list){
		$result = array();
		if(empty($cart_list)) $result;
		
		foreach ($cart_list as $k=>$v){
			if(isset($v['product_sku_info']['product_sku_warehouse'])) $result[] = $v['product_sku_info']['product_sku_warehouse'];
		}
		
		$result = array_unique($result);
		return $result;
	}
	
	/**
	 * @desc 获取购物车商品信息
	 */
	private function getCart(){
		if($this->customer->checkUserLogin()){
			$this->load->model("cartmodel","cart");
			
			$user_id = $this->session->get("user_id");
			$cart_list = $this->cart->cartListWithLoginUser($user_id);
			$cart_data = $this->productInfoWithCartinfo($cart_list);//扩展价格
			//获取sku对应属性信息
			foreach ($cart_data as $key=>&$val){
				$sku = $val['product_sku'];
				$attr_value_info = $this->attrAndValueWithSku($sku);
				
				if(!empty($attr_value_info)){
					foreach ($attr_value_info as $attr_key=>$attr_val){
						//$val['attr'][$attr_key]['attr_name'] = $attr_val['attr_name'];
						//$val['attr'][$attr_key]['attr_value_name'] = $attr_val['attr_value_name'];
						$val['attr'][$attr_key]['name'] = $attr_val['attr_name'];
						$val['attr'][$attr_key]['value'] = $attr_val['attr_value_name'];
					}
					//echo "<pre>attr";print_r($val);die;
				}else {
					$val['attr'] = "";
				}
			}
			
			return $cart_data;
		}else{
			redirect(genURL("login"));
		}
		
	}
	
	/**
	 * @desc 根据商品id获取该商品信息及价格
	 * @param unknown $cart_data(二维数组)
	 */
	private function productInfoWithCartinfo($cart_data,$need_extend_price = 1){
		$result = array();
		if(empty($cart_data)) return $result;
	
		$this->load->model("goodsmodel","product");
		$language_code = currentLanguageCode();
	
		foreach ($cart_data as $k=>&$v){
			$product_id = $v['product_id'];
			$product_sku = $v['product_sku'];
			//商品基本信息
			$product_info = $this->product->getinfoNostatus($product_id,$language_code);
			if($need_extend_price)$product_info = $this->singleProductWithPrice($product_info);//扩展价格
			$v['product_info'] = $product_info;
			//获取商品仓库,长，宽，高等信息
			$sku_info = $this->skuinfoWithSku($product_sku,$product_id);
			if(!empty($sku_info)) $v['product_sku_info'] = $sku_info;
		}
		
		return $cart_data;
	}
	
	/**
	 * @desc 获取当前用户地址
	 * @param number $default_address_id
	 * @return multitype:|multitype:multitype: unknown Ambigous <> multitype:unknown  Ambigous <multitype:, unknown>
	 */
	private function addressList($default_address_id = 0){
		$address = array();
		if(!$this->customer->checkUserLogin()) return $address;
		$user_id = $this->customer->getCurrentUserId();
		
		if($default_address_id==0 || !is_numeric($default_address_id)){
			//用户customer表中默认地址id编号
			$user_info = $this->customer->getUserById($user_id);
			$default_address_id = $user_info['address_id'];
		}
		
		//用户地址列表
		$this->load->model("addressmodel","address");
		$address_list = $this->address->getAddressList($user_id);
		
		if(!empty($address_list)){
			$default_address_info = array();
			$default_addrss_info_id = null;
			$new_address_list = array();
			
			foreach ($address_list as $key=>$val){
				$address_id = $val['address_id'];
				$new_address_list[$address_id] = $val;
				
				if($val['address_default']==1) {
					$default_address_info = $val;
					$default_addrss_info_id = $val['address_id'];
				}
			}	
			//echo "<pre>";print_r($new_address_list);die;
			if(isset($new_address_list[$default_address_id])){
				$address[] = $new_address_list[$default_address_id];
				//$news[] = $new_address_list[$default_address_id];
				unset($new_address_list[$default_address_id]);
				if(!empty($new_address_list))$address = array_merge($address,$new_address_list);
			}elseif(!empty($default_address_info)){
				$address[] = $default_address_info;
				unset($new_address_list[$default_addrss_info_id]);
				if(!empty($new_address_list)) $address[] = array_merge($address,$new_address_list);
				
			}else{
				$address[] = $address_list[0];
				unset($address_list[0]);
				if(!empty($address_list))$address[] = $address_list;
			}
			
		}
		//echo "<pre>";print_r($address);die;
		return $address;
		
	}
	
	/**
	 * @desc 定义返回给前端的物流方式数据格式(并计算track跟踪费用)
	 * @return array
	 */
	private function formatShippigData($shipping_name,$price,$warehouse){
		$currency_code = currentCurrency();
		$currency = "$";
		$airmail = BASE_TRACK;
		$base_price = $price;
		$base_airmail = $airmail;
		$standard = (strtoupper($warehouse)=="DE")?8.69:2.19;
		$base_standard = $standard;
		//汇率转换
		$currency_format = $this->getCurrencyNumber();
		if($currency_format){
			$currency = $currency_format['currency_format'];
			$price = round($price*$currency_format['currency_rate'],4, PHP_ROUND_HALF_DOWN);
			$airmail= round($airmail*$currency_format['currency_rate'],2, PHP_ROUND_HALF_DOWN);
			$standard= round($standard*$currency_format['currency_rate'],2, PHP_ROUND_HALF_DOWN);
		}
		
		$data = array(
			'cnmail'=>array(
				"id"=>6,"title"=>"CNmail",'selected'=>1,"day"=>"1-5 working days",
				'tips'=>"",'track'=>0,"trackTitle"=>"",'available'=>0,//是否禁用
				'price'=>$currency_code." ".$currency." 0.00",'trackPrice'=>"",
				'single_price'=>0,'single_trackprice'=>0,//前端显示使用
				'base_price'=>0,'base_trackprice'=>0,'true_trackprice'=>0,	//计算汇率处理前使用
			),
			'freesea'=>array(
					"id"=>7,"title"=>"free expedited shipping",'selected'=>1,"day"=>"3-5 working days",
					'tips'=>"",'track'=>0,"trackTitle"=>"",'available'=>0,//是否禁用
					'price'=>$currency_code." ".$currency." 0.00",'trackPrice'=>"",
					'single_price'=>0,'single_trackprice'=>0,'base_price'=>0,'base_trackprice'=>0,'true_trackprice'=>0,
			),
			'airmail'=>array(
				"id"=>1,"title"=>"airmail",'selected'=>1,"day"=>"10-20 working days",
				'tips'=>"",'track'=>0,"trackTitle"=>"Track my package",'available'=>0,//是否禁用
				'price'=>$currency_code." ".$currency." 0.00",'trackPrice'=>"+".$currency_code." ".$currency.$airmail,
				//'single_price'=>0,'single_trackprice'=>$airmail,'base_price'=>0,'base_trackprice'=>$base_airmail,
					'single_price'=>0,'single_trackprice'=>$airmail,'base_price'=>0,'base_trackprice'=>0,'true_trackprice'=>0,
			),
			'standard'=>array(
				"id"=>2,"title"=>"standard",'selected'=>1,"day"=>"6-10 working days",
				'tips'=>"",'track'=>0,"trackTitle"=>"Track my package",'available'=>0,//是否禁用
				'price'=>$currency_code." ".$currency.$price,'trackPrice'=>"+".$currency_code." ".$currency.$standard,
				//'single_price'=>$price,'single_trackprice'=>$standard,'base_price'=>$base_price,'base_trackprice'=>$base_standard,
				'single_price'=>$price,'single_trackprice'=>$standard,'base_price'=>$base_price,'base_trackprice'=>0,'true_trackprice'=>0,
			),
			'express'=>array(
				"id"=>3,"title"=>"expedited",'selected'=>1,"day"=>"3-7 working days",
				'tips'=>"",'track'=>0,"trackTitle"=>"",'available'=>0,//是否禁用
				'price'=>$currency_code." ".$currency.$price,'trackPrice'=>"",
				'single_price'=>$price,'single_trackprice'=>0,'base_price'=>$base_price,'base_trackprice'=>0,'true_trackprice'=>0,
			),
			'register_airmail'=>array(
				"id"=>4,"title"=>"airmail",'selected'=>1,"day"=>"10-20 working days",
				'tips'=>"",'track'=>1,"trackTitle"=>"Track my package",'available'=>0,//是否禁用
				'price'=>$currency_code." ".$currency." 0.00",'trackPrice'=>"+".$currency_code." ".$currency.$airmail,
				'single_price'=>0,'single_trackprice'=>$airmail,'base_price'=>0,'base_trackprice'=>$base_airmail,'true_trackprice'=>$airmail,
			),
			'register_standard'=>array(
				"id"=>5,"title"=>"standard",'selected'=>1,"day"=>"6-10 working days",
				'tips'=>"",'track'=>1,"trackTitle"=>"Track my package",'available'=>0,//是否禁用
				'price'=>$currency_code." ".$currency." 0.00",'trackPrice'=>"+".$currency_code." ".$currency.$standard,
				'single_price'=>$price,'single_trackprice'=>$standard,'base_price'=>$base_price,'base_trackprice'=>$base_standard,'true_trackprice'=>$standard,
			)
		);
		
		$shipping_name = strtolower($shipping_name);
		/*if($shipping_name=="register_airmail"){
			$shipping_name="airmail";
		}
		if($shipping_name=="register_standard"){
			$shipping_name="standard";
		}*/
		if(isset($data[$shipping_name])) $result = $data[$shipping_name];
		else $result = array(); 
		//echo "<pre>aa";print_r($result);
		return $result;
		
	}
	
	//获取adyen列表
	private function adyenList($price,$country_code){
		//全部列表
		$all_adyen_list = $this->_allAdyenList($price,$country_code);
		//筛选列表
		$adyen_list = $this->_filterAdyenList($all_adyen_list);
		return $adyen_list;
		
	}
	
	/**
	 * @desc 筛选支付方式
	 * @param unknown $adyen_list
	 * @return multitype:
	 */
	private function _filterAdyenList($adyen_list){
		$list = array();
		if(empty($adyen_list)) return $list;

		global $new_payment_list;
		$exists_iban = false;//默认不存在
		$exists_adyen_paypal = false;//默认不存在
		$return_adyen_list = array();
		foreach ($adyen_list['paymentMethods'] as $k=>$v){
			//根据最低金额及本站的范围列表筛选排除adyen中列表(配置文件中)**（暂时去掉）*
			//判断国际银行(bankTransfer_IBAN),3
			if(strpos($v['brandCode'], 'bankTransfer')!==false){
				$exists_iban = true;
			}
			//判断adyen中是否paypal，不存在显示本地paypal
			if($v['brandCode']=='paypal'){
				$exists_adyen_paypal = true;
			}
			
			$code = $v['brandCode'];
			$key = $new_payment_list[$code];
			$return_adyen_list[$key] = array(
				'picname'=>RESOURCE_URL ."images/paymentMethod/".$code.".jpg",
				'name'=>$code,
				'id'=>$key,
				'checked'=>0,		
				 //'id'=>$new_payment_list[$v['brandCode']]
			);
		}
		
		if($exists_iban===false){//不存在时，需要增加本地银行到支付列表  br.png
			$language_code = strtolower(currentLanguageCode());//国家
			$return_adyen_list[3] = array(
					'picname'=>RESOURCE_URL ."images/paymentMethod/bank.jpg",
					'name'=>'bank',
					'checked'=>0,
					'id'=>3,
			);
		}
		if($exists_adyen_paypal===false){
			$return_adyen_list[2] = array(
					'picname'=>RESOURCE_URL ."images/paymentMethod/"."paypalsk.jpg",
					'name'=>'paypalsk',
					'checked'=>0,
					'id'=>2
			);
		}
		//echo "<pre>";print_r($return_adyen_list);die;
		return $return_adyen_list;
	}
	
	/**
	 * @desc 从adyen接口获取列表
	 * @return mixed
	 */
	private function _allAdyenList($price,$country_code){
		$price = 1;
		$currencyCode = strtoupper($this->currency);//货币code
		$this->load->library('Payment/adyen');
		$adyen_list = $this->adyen->callDirectoryLookupRequest($country_code,$currencyCode,$price);
		$adyen_list = json_decode($adyen_list,true);
		return $adyen_list;
	}
	
	/**
	 * @desc 根据国家编码返回国家名称
	 * @param unknown $country_code
	 * @return NULL|Ambigous <string, unknown>
	 */
	private function getCountryName($country_code){
		if(empty($country_code)) return null;
		$all_country_list = $this->_country();
		$country_name = '';
		foreach ($all_country_list as $key=>$val){
			if($val['country_iso2']==$country_code) $country_name = $val['country_name'];
		}
		return $country_name;
	}
	
	/**
	 * new订单邮件发送
	 * @param  array $order 订单信息
	 * @param  array $orderGoodsList 订单商品
	 */
	protected function _newSendMail( $order, $orderGoodsList ) {
		return true;
		/*try
		{
			$type = 1; //订单提交成功
			$currentLanguageId = $this->m_app->getLanguageCodeByCode( $order['language_code'] );
			//获得邮件模板信息
			$EmailtemplateModel = new EmailtemplateModel();
			$templateInfo = $EmailtemplateModel->getSystemEmailTemplateInfo( $type, $currentLanguageId );
	
			//模板启用
			if( isset( $templateInfo['status'] ) && isset( $templateInfo['eid'] ) && $templateInfo['status'] == 1 && !empty( $templateInfo['eid'] ) ){
				$order['country'] = $this->m_address->getCountryName($order['country']);
				//格式订单价格等信息
				foreach($orderGoodsList as $key => $record) { $orderGoodsList[$key]['goods_price'] = formatPrice($record['final_price']); }
				$order['goods_amount'] = formatPrice($order['base_goods_amount']);
				$order['shipping_fee'] = formatPrice($order['base_shipping_fee']);
				$order['insure_fee'] = formatPrice($order['base_insure_fee']);
				$order['integral_money'] = formatPrice($order['base_integral_money']);
				$order['base_discount'] = $order['base_discount']-$order['base_integral_money'];
				$order['discount'] = formatPrice($order['base_discount']);
				$order['order_amount'] = formatPrice($order['base_order_amount']);
				$order['pay_name'] =  $this->paymentTitle;
	
				$productIds = extractColumn( $orderGoodsList, 'product_id' );
				$order = eb_htmlspecialchars( $order );
				//订单商品信息dom
				$orderInfoDomArray = $EmailtemplateModel->getEmailOrderInfoDom( $order, $orderGoodsList, 'order' );
				//推荐商品
				$ProductModel = new ProductModel();
				$recommendProList = $ProductModel->getEmailRecommendProduct( $currentLanguageId, $productIds, FALSE, array(), '', $order['currency_code'], $order['language_code'], '', 'order' );
				$recommendProDom = $EmailtemplateModel->getEmailRecommendProductDom( $recommendProList );
	
				global $lang_basic_url;
				//邮件模版参数
				$contentParam = array(
						'SITE_DOMAIN' => rtrim( $lang_basic_url[ $order['language_code'] ], '/' ), //域名链接
						'SITE_DOMAIN1' => COMMON_DOMAIN, //域名
						'CS_EMAIL' => 'cs@'.COMMON_DOMAIN,
						'USER_NAME' => $order['consignee'],
						'ORDER_NUM' => $order['order_sn'],
						'ORDER_TIME' => date('F j, Y h:i:s A e', $order['add_time']),
						'ORDER_INFO' => $orderInfoDomArray['order_info'],
						'SHIP_ADDRESS' => $orderInfoDomArray['address'],
						'SHIP_WAY' => $order['shipping_name'],
						'PAY_WAY' => $order['pay_name'],
						'ITEM_REO' => $recommendProDom,
				);
	
				//发送 $order['email'] luowenyong@hofan.cn
				$result = HelpOther::sendSystemEmail( $order['email'], $templateInfo['eid'], $contentParam );
				//发送失败重试一次
				if( trim( $result ) !== 'OK' ){
					$result = HelpOther::sendSystemEmail( $order['email'], $templateInfo['eid'], $contentParam );
				}
				if( trim( $result ) !== 'OK' ){
					$logInfo = '[order] ORDERID:#'.$order['order_sn'].' - EMAIL:'.$order['email'].' - EID:'.$templateInfo['eid'].' - ERROR:'.$result;
					$this->log->write( Log::LOG_TYPE_SYSTEM_EMAIL , $logInfo, true );
				}
			}
		}
		catch( Exception $e )
		{
			return ;
		}
		*/
	}
	
	//点击“place order”按钮提交订单支付时：
		
		//1 中间form表单提交
		
		//2 提交给paype，adyen，本地银行，处理
		
		//3 返回处理页面，及支付平台请求本站服务器请求
		
		//4 success页面（使用session，不能刷新，使用https安全）
		
}