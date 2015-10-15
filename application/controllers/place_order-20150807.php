<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';
/**
 * @desc check_out页
 * @author Wty
 *
 */
class Place_order extends Dcontroller {
	//标识能否place_order按钮
	private $able_use_address = false;
	private $able_use_shipping = false;
	private $able_use_payment = false;
	private $address_warehouse = null;//默认国家仓库（国家编码）
	private $able_coupon_status = false;
	private $use_rewards_status = false;
	private $cart_noempty = false;
	
	private $couponSavings = 0;//转换汇率后的价
	private $base_couponSavings = 0;//转换汇率钱的价（美元）
	private $coupon_type = NORMAL_COUPON;
	
	private $shipping_insurance = INSURANCE;//保险定价
	
	private $able_user_baserewards = 0;//转换汇率前可用rewards
	private $able_user_rewards =0;//总共可用rewards
	private $rewardsBalance = 0;//rewards 优惠价
	private $base_rewardsBalance = 0;
	private $currency_format;//当前汇率详情
	private $current_currency = '$';//货币单位 ex:$
	private $current_currency_code = DEFAULT_CURRENCY;
	
	private $total_rewards = 0;//当前用户可用rewards
	private $gift_product_info = array();//赠品数据
	private $shippingCharges = 0;//运费
	
	public function __construct(){
		parent::__construct();
		
		$this->currency_format = $this->getCurrencyNumber();
		$this->_view_data['new_currency'] = "$";
		$this->_view_data['currency_code'] = $this->current_currency_code = currentCurrency();
		if($this->currency_format){
			$this->shipping_insurance = round($this->shipping_insurance*$this->currency_format['currency_rate'],2);
			$this->_view_data['new_currency'] = $this->current_currency = $this->currency_format['currency_format'];
		}
		
	}
	
	//重新获取汇率信息
	private function frushCurrency(){
		$this->current_currency_code = currentCurrency();//ex:'USD'
		if($this->currency_format){
			$this->current_currency = $this->currency_format['currency_format'];
		}	
	}
	
	/**
	 * @Desc 普通place_order
	 * @see Dcontroller::index()
	 */
	public function index(){
		//检查用户是否登录，如果没有登录就返回购物车页面
		if(!$this->customer->checkUserLogin()) redirect(genURL("cart"));
		$user_id = $this->session->get('user_id');
		
		//*****************country && currency ******start
		$currency_list = $this->_currency();
		$country_list = $this->_country();
		$this->_view_data['currency_list'] = $currency_list;
		$this->_view_data['country_list'] = $country_list;
		//*****************country && currency ******end
		
		//获取用户的地址信息及默认地址
		$address_data = $this->_getUserAddressList();
		$this->_view_data['address_data'] = $address_data;
		
		//购物车信息（cart summary）
		$cart_data = $this->getCart();
		if(empty($cart_data)) redirect(genURL("cart"));
		$this->_view_data['cart_data'] = $cart_data;
		
		//计算商品，长宽高，重量，体积，敏感数据,及总价
		$cart_weight_info = $this->computeWeightInfo($cart_data);
		$subtotal = $cart_weight_info['subtotal_price'];//商品基本价格是经过了汇率计算的

		//shipping options (可用物流方式，物流费用，海外本地仓库) start***************
		$payment_addres_country_name = $this->getCountryName($this->address_warehouse);
		if($this->able_use_address==true){
			$shipping_options_data = $this->_get_shipping_option_data($this->address_warehouse,$cart_data,$cart_weight_info);
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
		$adyen_list = $this->adyenList($last_price = 1,$country_code = $this->address_warehouse);
		if(!empty($adyen_list)) $this->able_use_payment = true;
		$this->_view_data['adyen_list'] = $adyen_list;
		//********************payment method start**************************
		
		//***************rewords**************start
		$this->_getRewardsData($user_id,$subtotal);
		$this->_view_data['total_rewards'] = $this->current_currency." ".$this->total_rewards;
		//***************rewords**************end
		
		$insurancePrice = $rewardsBalance = $couponSavings = 0;
		$payPrice = round($subtotal+$this->shippingCharges,2);
		$bool = null;
		
		$this->_view_data['insurance'] = $this->shipping_insurance;
		$this->_view_data['payment_country'] = $payment_addres_country_name?$payment_addres_country_name:"";
		$this->_view_data['payment_country_code'] = $this->address_warehouse;
		//$this->_view_data['currency'] = $currency;
		//$this->_view_data['new_currency_code'] = $this->current_currency;
		$this->_view_data['subtotal'] = $subtotal;
		$this->_view_data['shippingCharges'] = $this->shippingCharges;
		$this->_view_data['insurancePrice'] = $insurancePrice;
		$this->_view_data['rewardsBalance'] = $rewardsBalance;
		$this->_view_data['couponSavings'] = $couponSavings;
		$this->_view_data['payPrice'] = $payPrice;
		$this->_view_data['bool'] = ($this->able_use_payment && $this->able_use_address && $this->able_use_shipping)?true:false;
		
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
			$return_data['data'] = array('url'=>genURL('login'),"bool"=>false);
			$this->ajaxReturn($return_data);
		}
		//replace
		$address_id = trim($this->input->get("address_id"));//编辑的地址id
		$selected_address_id = trim($this->input->get("addressId"));//选中配送的地址id
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
		$default = $this->input->get('defaultValue');//标志是否设为默认地址
		
		$required_region = array('AT','CA','CH','DE','EE','ES','FI','FR','LT','LV','RO','US');
		$country_is_required = true;
		if(empty($country)){
			$msg = "This country is required";
			$country_is_required = false;
		}
		
		if(in_array($country, $required_region) && empty($province)){
			$msg = "This State/province/Region is required";
			$country_is_required = false;
		}
		if($country=='BR' && (empty($cpfcnpj) || !in_array($cpfcnpj, array('CPF','CNPJ')))){
			$msg = "This Zip Code is required";
			$country_is_required = false;
		}

		//判断国家，州，税号等信息
		if($country_is_required===true){
			$edit_customer_addressid = false;
			$data = array(
					"address_id"=>$address_id,'customer_id'=>$userId,
					'address_firstname' => $firstName,'address_lastname' => $lastName,
					'address_country' => $country,'address_province' => $province,
					'address_city' => $city,'address_address' => $address,
					'address_phone' => $phone,'address_zipcode' => $zipCode,
					'address_cpfcnpj' => $cpfcnpj,'address_time_update' => date('y-m-d h:i:s',time()),
					'address_status' =>1,
					//'address_default' => $default,
			);
			if($address_id && is_numeric($address_id)){//更新
				$data['address_default'] = $default;
				$result = $this->updateAddressAndDefaultStatus($userId,$address_id,$data);
				$selected_address_id = $address_id;
			}else{//新增
				$data['address_default'] = $default;
				$result = $insert_id = $this->addAddress($userId,$data);
				//改变选中地址
				$selected_address_id = $insert_id;
			}
			if($result) {
				$msg = 'OK';
				$status = 200;
			}
		}else{
			$status = 1008;
		}
		
		//返回前端的地址信息（改变选中地址状态，新增地址时改为选中地址  $insert_id）
		$address_data = $this->_getUserAddressList($selected_address_id);
		
		//获取其他数据（新增地址改为选中地址  $insert_id）
		$other_data = $this->ajaxFresh($type="place_order",$selected_address_id);
		
		unset($other_data['url']);
		$data = array_merge($address_data,$other_data);
		$return_data = array('msg'=>$msg,"status"=>$status,"data"=>$data);
		//echo "<pre>";print_r($return_data);die;
		$this->ajaxReturn($return_data);
		
	}
	
	private function addAddress($userId,$data){
		$this->load->model("addressmodel","address");
		//是否有默认地址，有默认地址时addrss_id多少
		$default_address = $this->address->getDefaultAddress($userId);
		//$this->database->master->trans_begin();//开启事务
		if(empty($default_address)){
			$data['address_default'] = 1;
			//创建新地址
			$insert_id = $this->address->createAddress($data);
			
			//修改用户默认地址
			return $this->updateCustomerAddressId($userId);
		}else{
			//创建新地址
			$insert_id = $this->address->createAddress($data);
			
			if($data['address_default']==1){
				//取消默认
				$this->address->editAddress($default_address['address_id'], $data =array('address_default'=>0));
				//修改用户默认地址
				$this->updateCustomerAddressId($userId);
			}
		}
		/*
		if ($this->database->master->trans_status() === FALSE){
			$this->database->master->trans_rollback();
			return false;
		}else{
			$this->database->master->trans_commit();
			return true;
		}*/
		return $insert_id;
	}
	
	/**
	 * @desc 更新地址（包含设置为默认地址情况）
	 * @param unknown $userId
	 * @param unknown $address_id
	 * @param unknown $data
	 */
	private function updateAddressAndDefaultStatus($userId,$address_id,$data){
		$this->load->model("addressmodel","address");
		
		//是否有默认地址，有默认地址时addrss_id多少
		$default_address = $this->address->getDefaultAddress($userId);
		$this->database->master->trans_begin();//开启事务
		
		$result_edit = $this->address->editAddress($address_id, $data);
		if($data['address_default'] == 1 && $address_id != $default_address['address_id']){
			//取消原默认地址
			$result_canceldefalut = $this->address->editAddress($default_address['address_id'], $data =array('address_default'=>0));
			//修改用户默认地址
			$result_updateuser = $this->updateCustomerAddressId($userId);
			//$edit_customer_addressid = true;
		}
		
		if ($this->database->master->trans_status() === FALSE){
			$this->database->master->trans_rollback();
			return false;
		}else{
			$this->database->master->trans_commit();
			return true;
		}
		
	}
	
	/**
	 * @desc 修改customer表中address_id
	 * @param unknown $userId
	 */
	private function updateCustomerAddressId($userId){
		$this->load->model("addressmodel","address");
		$after_update_address_info = $this->address->getDefaultAddress($userId);
		$this->load->model("customermodel","customer");
		return $this->customer->editUser($userId, $info=array('address_id'=>$after_update_address_info['address_id']));
	}
	
	/**
	 * @desc paypal_ec 编辑地址ajax
	 */
	public function ajaxAddress_nologin(){
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
		//$default = $this->input->get('defaultValue');//标志是否设为默认地址
		$default = 1;
		
		$required_region = array('AT','CA','CH','DE','EE','ES','FI','FR','LT','LV','RO','US');
		$country_is_required = true;
		if(empty($country)){
			$msg = "This country is required";
			$country_is_required = false;
		}
		
		if(in_array($country, $required_region) && empty($province)){
			$msg = "This State/province/Region is required";
			$country_is_required = false;
		}
		if($country=='BR' && (empty($cpfcnpj) || !in_array($cpfcnpj, array('CPF','CNPJ')))){
			$msg = "This Zip Code is required";
			$country_is_required = false;
		}
		if($country_is_required==true){
			//返回前端的地址信息（改变选中地址状态）
			$new_address_data['address_0']["first_name"] = $firstName;
			$new_address_data['address_0']["last_name"] = $lastName;
			$new_address_data['address_0']["address"] = $address;
			$new_address_data['address_0']["address2"] = "";
			$new_address_data['address_0']["city"] = $city;
			$new_address_data['address_0']["country"] = $country;
			$new_address_data['address_0']['country_name'] = $this->getCountryName($country);
			$new_address_data['address_0']["zipcode"] = $zipCode;
			$new_address_data['address_0']["mobile"] = $phone;
			$new_address_data['address_0']["cpf"] = $cpfcnpj;
			$new_address_data['address_0']["region"] = $province;
			$new_address_data['address_0']["defaults"] = 1;
			$new_address_data['address_0']['checked'] = 1;
			
			$this->session->set('paypal_ec_address',$new_address_data);
			$address_data['address'] = $new_address_data; 
			$msg = "OK";
			$status= 200;
		}else{
			$address_data['address'] = $this->session->get('paypal_ec_address');
			$status= 1008;
		}
		
		//获取其他数据
		$other_data = $this->ecnologined_ajaxFresh($type="place_order");
		unset($other_data['url']);
		$data = array_merge($address_data,$other_data);
		$return_data = array('msg'=>$msg,"status"=>$status,"data"=>$data);
		//echo "<pre>";print_r($return_data);die;
		$this->ajaxReturn($return_data);
	}
	
	/**
	 * @desc place_order的ajax动作请求
	 */
	public function ajaxFresh($type=null,$insert_id = 0){
		//检查用户是否登录，如果没有登录就返回购物车页面
		if(!$this->customer->checkUserLogin())
		{
			$return_data['status'] = 1007;//表明没有登录
			$return_data['msg'] = "NO LOGIN";
			$return_data['data'] = array('url'=>genURL('login'),'bool'=>false);
			$this->ajaxReturn($return_data);
		}
		$user_id = $this->session->get('user_id');
		
		//根据选择地址id（1 获取地址id对应国家code； 2 重新获取物流方式 ； 3 重新获取购物车数据；  4 重新计算总价；5重新计算rewards；6 重新计算coupon数据；）
		$addressId = ($insert_id==0)?$this->input->get("addressId"):$insert_id;//地址id(新增地址时，新增地址为选中物流地址)
		
		$shippingid = $this->input->get("shippingid");//物流方式id
		$shippingtrack = $this->input->get("shippingtrack");
		if(stripos($shippingid, 'shippingid')!==false){
			$shippingid = str_replace('shippingid', '', $shippingid);
		}
		if(($shippingid==1 || $shippingid==2)&& $shippingtrack==1){
			$shippingid +=3;
		}
		if(($shippingid==4 || $shippingid==5)&& $shippingtrack!=1){
			$shippingid -=3;
		}
		
		$insurance = $this->input->get("insurance");
		$itemsFirst = $this->input->get("itemsFirst");
		$paymentId= $this->input->get("payment");
		$rewardsValue = $this->input->get("rewards");
		$couponValue = $this->input->get("coupon");
		$country = $this->input->get("country");//银行卡支付国家,ex:CA
		$currency = $this->input->get("currency");//支付币种,单位 ex:$
		//汇率改变  currency
		global $currency_list;
		if(!empty($currency) && in_array($currency, $currency_list))set_cookie('currency',$currency,864000);
		
		//*****汇率********start(重新获取汇率等信息)
		$this->frushCurrency();
		//*****汇率（最终返回的是汇率后价）********end
		
		$return_data = array();
		$new_address_data = $this->_getUserAddressList($addressId);
		$return_data['address'] = $new_address_data;
		
		//购物车信息（cart summary）
		$cart_data = $this->getCart();
		if(!empty($cart_data)) $this->cart_noempty = true;
		//coupon start******************
		if($this->cart_noempty){
			$coupon_data = $this->_computeCoupon($couponValue,$cart_data);
			//echo "<pre>bbbsssss";print_r($coupon_data);die;
			$return_data['coupon'] = $coupon_data;
		}else{
			$return_data['coupon'] = '';
		}
		$cart_data = array_merge($cart_data,$this->gift_product_info);
		//coupon end******************
		
		//计算商品，长宽高，重量，体积，敏感数据,及总价
		$cart_weight_info = $this->computeWeightInfo($cart_data);
		$subtotal = $cart_weight_info['subtotal_price'];//汇率后价
		
		//根据地址id，获取默认地址信息及国家信息
		$this->load->model("addressmodel","address");
		$selected_address_info = $this->address->getAddressById($addressId);
		//shipping option  start***************
		$shipping_options_data = $this->_get_shipping_option_data($selected_address_info['address_country'],$cart_data,$cart_weight_info,$shippingtrack,$shippingid);
		$return_data['shipping'] = $shipping_options_data;
		//shipping option  end***************

		//********************payment method start**************************
		$adyen_list = $this->adyenList($last_price = 1,$country);
		//判断支付方式是否符合范围
		if(isset($adyen_list[$this->prefix.$paymentId])){
			$adyen_list[$this->prefix.$paymentId]['checked'] = 1;
			$this->able_use_payment = true;
		}
		$return_data['payment']['data'] = $adyen_list;
		$return_data['payment']['country'] = $this->getCountryName($country);
		$return_data['payment']['countryId'] = $country;
		$return_data['payment']['currency'] = $this->current_currency_code.' '.$this->current_currency;//返回给前端的"USD $"格式数据 
		$return_data['payment']['currencyId'] = $this->current_currency_code;
		//********************payment method end**************************
		
		//****************rewards start(优先使用coupon，减掉coupon的价钱)****************
		if($this->cart_noempty){
			if(!empty($return_data['coupon'])){
				$rewards_subtotal = round( ($subtotal*100 - $return_data['coupon']['msg']*100)/100,2);
			}else{
				$rewards_subtotal = $subtotal;
			}
			$rewards_data = $this->_getRewardsData($user_id,$rewards_subtotal,$rewardsValue);
			$return_data['rewards'] = $rewards_data;
		}else{
			$return_data['rewards'] = '';
		}
		$return_data['AvailableBalanceprice'] = $this->current_currency_code." ".$this->current_currency." ".$this->total_rewards;
		//****************rewards end****************
		
		//物流费用计算 start******
		/*$shippingCharges = 0;//汇率后价
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
		}*/
		//物流费用计算 end******
		
		//保险费用计算 start****
		if($insurance==0 || !is_numeric($insurance)){
			$insurancePrice = 0;
			$return_data['insurance'] = false;
		}else{
			$insurancePrice = $this->shipping_insurance;
			$return_data['insurance'] = true;
		}
		//保险费用计算 end****
		
		//最终支付费用 start******* 		
		$payPrice = $subtotal + $this->shippingCharges +$insurancePrice-$this->rewardsBalance-$this->couponSavings;
		if($this->currency_format){
			$base_payPrice = round($subtotal/($this->currency_format['currency_rate']),2)+round($this->shippingCharges /($this->currency_format['currency_rate']),2);
			$base_payPrice += round($insurancePrice/($this->currency_format['currency_rate']),2);
			$base_payPrice -= round($this->rewardsBalance/($this->currency_format['currency_rate']),2);
			$base_payPrice -= round($this->couponSavings/($this->currency_format['currency_rate']),2);
		}else{
			$base_payPrice = $payPrice;
		}
		
		//最终支付费用 end*******
		
		//返回购物车数据结构处理
		$json_cart_data =  $this->formatCartData($cart_data);
		$return_data['list'] = $json_cart_data;
		$return_data["subtotal"] = $this->current_currency.' '.$subtotal;
		$return_data["shippingCharges"] = $this->current_currency.' '.$this->shippingCharges;
		$return_data["insurancePrice"] = $this->current_currency.' '.$insurancePrice;
		$return_data["rewardsBalance"] = $this->current_currency.' '.$this->rewardsBalance;
		$return_data["couponSavings"] = $this->current_currency.' '.$this->couponSavings;
		$return_data["payPrice"] = $this->current_currency.' '.$payPrice;
		$return_data['shipping_insurance'] = $this->shipping_insurance;
		$return_data['shipping_insurance_txt'] = $this->current_currency.' '.$this->shipping_insurance;//Shipping Insurance 文案
		
		//pay url start********
		$bool = false;
		if($this->able_use_address && $this->able_use_payment && $this->able_use_shipping && $this->use_rewards_status && $this->able_coupon_status ){
			$bool = true;
		}
		$return_data['bool'] = $bool;

		//是否拆包
		$return_data['itemsFirst'] = $itemsFirst?true:0;
		
		$new_data['msg'] = "OK";
		$new_data['status'] = 200;
		$new_data['data'] = $return_data;
		if($type=="place_order"){
			$return_data['payPrice_num'] = $payPrice;
			$return_data['base_payPrice_num'] = $base_payPrice;
			$return_data['couponValue'] = $couponValue;
			$return_data['pay_country'] = $country;//支付国家
			//$return_data['pay_address_code'] = $pay_address_code;
			$return_data['pay_address_code'] =  $this->address_warehouse;//配送地址国家
			$return_data['compute_list'] = $cart_data;//
			$return_data['shippingid'] = str_replace('shippingid', '', $shippingid);//为1，5时，说明airmail，standard有哦track费用
			$return_data['base_couponSavings'] = $this->base_couponSavings;
			$return_data['able_user_baserewards'] = $this->base_rewardsBalance;
			$return_data['able_user_rewards'] = $this->rewardsBalance;
			return $return_data;
		}else{
			//echo "<pre>";print_r($new_data);die;
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
		
		//获取购物车信息
		$cart_data = $result['list'];
		if(empty($cart_data)) {
			$return_data['status'] = 1008;//表明没有登录
			$return_data['msg'] = "Cart is empty";
			$return_data['data'] = null;
			$this->ajaxReturn($return_data);
		}
		
		//地址处理
		$address_data = $result['address'];
		foreach ($address_data as $address_k=>$address_v){
			if($address_v['checked']==1) $address = $address_v; 
		}
		
	 	//判断当美国或者澳大利亚的时候州信息是否有
		if(isset($address['address_country']) && in_array($address['address_country'],array('US','AU'))) {
			if(!isset($address['address_province']) || empty($address['address_province']) ) {
				$return_data['status'] = 85002;
				$return_data['msg'] = "check_address_province";
				$return_data['data'] = null;
				$this->ajaxReturn($return_data);
			}
		}

		//地址里面国家和省份的判断
		/*if(in_array($address['address_country'],array('US','AU')) && $address['address_province'] == '') {
			$return_data['status'] = 85004;
			$return_data['msg'] = "check_address_province";
			$return_data['data'] = null;
			$this->ajaxReturn($return_data);
		}*/
		
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
						
		//订单邮件发送(后续)
			$this->_newSendMail($order, $orderGoodsList);
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
				case 2://非adyen中paypal
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
		//清空购物车(该用户)
		$this->load->model('cartmodel','cartmodel');
		$this->cartmodel->bathDelCartWithUserId($user_id);
		set_cookie('cart_merge', 0);
		
		$return_data['status'] = 200;//表明没有登录
		$return_data['msg'] = "OK";
		$return_data['data'] = array('url'=>$url);
		$this->ajaxReturn($return_data);
	}
	
	/**
	 * @desc 非登录  paypal_ec支付  place_order页面  
	 */
	public function paypal_ec(){
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
		
		//设置paypal ec信息在session中
		$this->session->set('paypal_ec_email', $response['EMAIL']);
		$this->session->set('paypal_ec_payerid', $paypalEcPayerid);
		
		//*****汇率********start
		$this->frushCurrency();
		//*****汇率（最终返回的是汇率后价）********end
		
		//*****************country && currency ******start
		$currency_list = $this->_currency();
		$country_list = $this->_country();
		$this->_view_data['currency_list'] = $currency_list;
		$this->_view_data['country_list'] = $country_list;
		//*****************country && currency ******end
		//购物车信息（cart summary）
		$cart_data = $this->getCart($type='paypal_ec_nologin');
		if(empty($cart_data)) redirect(genURL('cart'));
		$this->_view_data['cart_data'] = $cart_data;
		
		//地址信息处理（paypal ec）*******************
		$address = $this->_fetchAddressInfoFromPaypalECResponse($response);
		$address_data = $this->session->get('paypal_ec_address');
		$this->able_use_address = true;
		$this->_view_data['address_data'] = $address_data;
		
		//计算商品，长宽高，重量，体积，敏感数据,及总价
		$cart_weight_info = $this->computeWeightInfo($cart_data);
		$subtotal = $cart_weight_info['subtotal_price'];//商品基本价格是经过了汇率计算的
		//shipping options (可用物流方式，物流费用，海外本地仓库) start***************
		$payment_addres_country_name = $this->getCountryName($this->address_warehouse);
		if($this->able_use_address==true){
			$shipping_options_data = $this->_get_shipping_option_data($this->address_warehouse,$cart_data,$cart_weight_info);
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
		$adyen_list[1] = array(
				'picname'=>RESOURCE_URL ."images/paymentMethod/"."paypalsk.jpg",
				'name'=>'paypalsk',
				'checked'=>1,
				'id'=>1
		);
		$this->able_use_payment = true;
		$this->_view_data['adyen_list'] = $adyen_list;
		//********************payment method start**************************
		
		//***************rewords**************start
		$this->_view_data['total_rewards'] = $this->current_currency_code." ".$this->current_currency." ".$this->total_rewards;
		//***************rewords**************end
		
		$insurancePrice = $rewardsBalance = $couponSavings = 0;
		$payPrice = $subtotal;
		$payPrice = round($subtotal+$this->shippingCharges,2);
		$bool = null;
		
		$this->_view_data['insurance'] = $this->shipping_insurance;
		$this->_view_data['payment_country'] = $payment_addres_country_name?$payment_addres_country_name:"";
		$this->_view_data['payment_country_code'] = $this->address_warehouse;
		
		//$this->_view_data['currency'] = $currency;
		$this->_view_data['new_currency_code'] = $this->current_currency_code;
		
		$this->_view_data['subtotal'] = $subtotal;
		$this->_view_data['shippingCharges'] = $this->shippingCharges;
		$this->_view_data['insurancePrice'] = $insurancePrice;
		$this->_view_data['rewardsBalance'] = $rewardsBalance;
		$this->_view_data['couponSavings'] = $couponSavings;
		$this->_view_data['payPrice'] = $payPrice;
		$this->_view_data['bool'] = ($this->able_use_payment && $this->able_use_address && $this->able_use_shipping)?true:false;
		
		$this->_view_data['place_order_type'] = 'paypal_ec_nologin';
		//parent::index( 'place_order' );
		parent::index();
	}
	
	/**
	 * @desc 非登录  paypal_ec支付 place_order页面  ajax动作请求
	 * @param string $type
	 * @return number
	 */
	public function ecnologined_ajaxFresh($type = null){
		//根据选择地址id（1 获取地址id对应国家code； 2 重新获取物流方式 ； 3 重新获取购物车数据；  4 重新计算总价；5重新计算rewards；6 重新计算coupon数据；）
		$shippingid = $this->input->get("shippingid");//物流方式id
		$shippingtrack = $this->input->get("shippingtrack");
		if(stripos($shippingid, 'shippingid')!==false){
			$shippingid = str_replace('shippingid', '', $shippingid);
		}
		if(($shippingid==1 || $shippingid==2)&& $shippingtrack==1){
			$shippingid +=3;
		}
		if(($shippingid==4 || $shippingid==5)&& $shippingtrack!=1){
			$shippingid -=3;
		}
		$insurance = $this->input->get("insurance");
		$itemsFirst = $this->input->get("itemsFirst");
		$paymentId= $this->input->get("payment");
		$rewardsValue = $this->input->get("rewards");
		$couponValue = $this->input->get("coupon");
		$country = $this->input->get("country");//银行卡支付国家
		$currency = $this->input->get("currency");//支付币种
		//汇率改变  currency
		global $currency_list;
		if(!empty($currency) && in_array($currency, $currency_list))set_cookie('currency',$currency,864000);
		
		//*****汇率********start
		$this->frushCurrency();
		//*****汇率（最终返回的是汇率后价）********end
		$return_data = array();
		$this->able_use_address = true;
		$address_data = $this->session->get('paypal_ec_address');
		$this->address_warehouse = $address_data['address_0']['country'];
		$return_data['address'] = $address_data;
		
		//购物车信息（cart summary）
		$cart_data = $this->getCart('paypal_ec_nologin');
		
		//coupon start******************
		$coupon_data = $this->_computeCoupon($couponValue,$cart_data);
		$return_data['coupon'] = $coupon_data;
		$cart_data = array_merge($cart_data,$this->gift_product_info);
		//coupon end******************
		
		//计算商品，长宽高，重量，体积，敏感数据,及总价
		$cart_weight_info = $this->computeWeightInfo($cart_data);
		$subtotal = $cart_weight_info['subtotal_price'];//汇率后价
		
		//根据地址id，获取默认地址信息及国家信息
		$address_data = $this->session->get('paypal_ec_address');
		
		//shipping option  start***************
		$shipping_options_data = $this->_get_shipping_option_data($address_data['address_0']['country'],$cart_data,$cart_weight_info,$shippingtrack,$shippingid);
		$return_data['shipping'] = $shipping_options_data;
		//shipping option  end***************
		
		//********************payment method start**************************
		$adyen_list[$this->prefix.'1'] = array(
				'picname'=>RESOURCE_URL ."images/paymentMethod/"."paypalsk.jpg",
				'name'=>'paypalsk',
				'checked'=>1,
				'id'=>1
		);
		$this->able_use_payment = true;
		$return_data['payment']['data'] = $adyen_list;
		$return_data['payment']['country'] = $this->getCountryName($country);
		$return_data['payment']['countryId'] = $this->current_currency;
		$return_data['payment']['currency'] = $this->current_currency." ".$this->current_currency_code;
		$return_data['payment']['currencyId'] = $this->current_currency_code;
		//********************payment method end**************************
		
		//****************rewards start****************
		$return_data['AvailableBalanceprice'] = $this->current_currency_code." ".$this->current_currency." ".$this->total_rewards;
		$return_data['rewards'] = '';
		//****************rewards start****************
		
		//物流费用计算 start******
		/*$shippingCharges = 0;//汇率后价
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
		}*/
		//物流费用计算 end******
		
		//保险费用计算 start****
		if($insurance==0 || !is_numeric($insurance)){
			$insurancePrice = 0;
			$return_data['insurance'] = false;
		}else{
			$insurancePrice = $this->shipping_insurance;
			$return_data['insurance'] = true;
		}
		//保险费用计算 end****
		
		//最终支付费用 start*******
		$payPrice = $subtotal + $this->shippingCharges + $insurancePrice-$this->rewardsBalance-$this->couponSavings;
		if($this->currency_format){
			$base_payPrice = round($subtotal/($this->currency_format['currency_rate']),2)+round( $this->shippingCharges / ($this->currency_format['currency_rate']),2);
			$base_payPrice += round($insurancePrice/($this->currency_format['currency_rate']),2);
			$base_payPrice -= round($this->rewardsBalance/($this->currency_format['currency_rate']),2);
			$base_payPrice -= round($this->couponSavings/($this->currency_format['currency_rate']),2);
		}else{
			$base_payPrice = $payPrice;
		}
		
		//最终支付费用 end*******
		
		//返回购物车数据结构处理
		$json_cart_data =  $this->formatCartData($cart_data);
		$return_data['list'] = $json_cart_data;
		$return_data["subtotal"] = $this->current_currency.' '.$subtotal;
		$return_data["shippingCharges"] = $this->current_currency.' '.$this->shippingCharges;
		$return_data["insurancePrice"] = $this->current_currency.' '.$insurancePrice;
		$return_data["rewardsBalance"] = $this->current_currency.' '.$this->rewardsBalance;
		$return_data["couponSavings"] = $this->current_currency.' '.$this->couponSavings;
		$return_data["payPrice"] = $this->current_currency.' '.$payPrice;
		$return_data['shipping_insurance'] = $this->shipping_insurance;
		
		//pay url start********
		$bool = false;
		if($this->able_use_address && $this->able_use_payment && $this->able_use_shipping ){
			$bool = true;
		}
		$return_data['bool'] = $bool;
		
		//是否拆包
		$return_data['itemsFirst'] = $itemsFirst?true:0;
		
		$new_data['msg'] = "OK";
		$new_data['status'] = 200;
		$new_data['data'] = $return_data;
		if($type=="place_order"){
			$return_data['payPrice_num'] = $payPrice;
			$return_data['base_payPrice_num'] = $base_payPrice;
			$return_data['couponValue'] = $couponValue;
			$return_data['pay_country'] = $country;//支付国家
			//$return_data['pay_address_code'] = $pay_address_code;
			$return_data['pay_address_code'] =  $this->address_warehouse;//配送地址国家
			$return_data['compute_list'] = $cart_data;//
			$return_data['shippingid'] = $shippingid;//为1，5时，说明airmail，standard有哦track费用
			$return_data['base_couponSavings'] = $this->base_couponSavings;
			$return_data['able_user_baserewards'] = $this->able_user_baserewards;
			$return_data['able_user_rewards'] = $this->able_user_rewards;
			return $return_data;
		}else{
			$this->ajaxReturn($new_data);
		}
	}
	
	/**
	 * @desc 非登录  paypal ec支付  提交支付按钮（palce_order按钮） 
	 */
	public function ecnologined_processOrder(){
		//获取paypaec支付id
		$paypalEcPayerid = $this->session->get('paypal_ec_payerid');
		//paypaec支付id为空
		if($paypalEcPayerid === false || $paypalEcPayerid == ''){
			$return_data['status'] = 85006;
			$return_data['msg'] = "check_address_province";
			$return_data['data'] = null;
			$this->ajaxReturn($return_data);
		}
		//获取paypaec支付token
		$paypalEcToken = $this->session->get('paypal_ec_token');
		//paypaec支付token为空
		if($paypalEcToken === false || $paypalEcToken == '') {
			$return_data['status'] = 85007;
			$return_data['msg'] = "check_address_province";
			$return_data['data'] = null;
			$this->ajaxReturn($return_data);
		}
		
		//重新刷新数据
		$result = $this->ecnologined_ajaxFresh($type="place_order");
		//注册订单处理错误回调监控
		
		//获取购物车信息
		$cart_data = $result['list'];
		if(empty($cart_data)) redirect(gensslURL('cart'));//购物车中的商品为空
		
		//根据地址id，获取默认地址信息及国家信息
		$address_data = $this->session->get('paypal_ec_address');
		$address = $address_data['address_0'];
		$email = $this->session->get('paypal_ec_email');		
		
		//判断当美国或者澳大利亚的时候州信息是否有
		if(isset($address['address_country']) && in_array($address['address_country'],array('US','AU'))) {
			if(!isset($address['address_province']) || empty($address['address_province']) ) {
				$return_data['status'] = 85002;
				$return_data['msg'] = "check_address_province";
				$return_data['data'] = null;
				$this->ajaxReturn($return_data);
			}
		}

		//地址里面国家和省份的判断
		/*if(in_array($address['address_country'],array('US','AU')) && $address['address_province'] == '') {
			$return_data['status'] = 85004;
			$return_data['msg'] = "check_address_province";
			$return_data['data'] = null;
			$this->ajaxReturn($return_data);
		}*/
		
		//创建用户及地址,登录
		$user_id = $this->_createPaypalecCustomer($email,$address);
		
		//创建订单
		$order = $this->_generateOrder($user_id, $email, $result, $address);
		//创建订单商品
		$orderGoodsList = $this->_generateOrderProduct($order['order_id'], $result, $order);
		//coupon(下单使用coupon)
		if(isset($result['couponValue']) && !empty($result['couponValue'])){
			$this->load->model('couponmodel','couponmodel');
			$this->couponmodel->updateCouponUseTimes($result['couponValue']);
		}
		//积分（rewards,往customer表中rewards加1%）
		/*if(isset($order['order_rewards']) && $order['order_rewards']){
			$this->customer->updateUserRewards($user_id,$order['order_rewards']);
		}*/
		//订单邮件发送(后续)
		$this->_newSendMail($order, $orderGoodsList);
		
		//paypal ec(判断支付是否成功)
		$payResult = $this->_processPaypalECRequest($paypalEcToken, $paypalEcPayerid, $order);
		if( $payResult ){
			$url = $this->_paymentUrl('paypal_ec');
		}else{
			$url = $this->_paymentUrl('paypal_ec');
			//$url = $this->_paymentUrl('paypal_ec_unpaid');
		}
		//清空购物车(该用户)
		$this->session->delete('paypal_ec_address');//清空session paypalec用户地址
		$this->load->model('cartmodel','cartmodel');
		$session_id = $this->session->sessionID();
		$this->cartmodel->delCartWithSessionId($session_id);
		set_cookie('cart_merge', 0);
		
		$return_data['status'] = 200;//表明没有登录
		$return_data['msg'] = "OK";
		$return_data['data'] = array('url'=>$url);
		$this->ajaxReturn($return_data);
		
	}
	
	/**
	 * @desc登录用户  paypal ec支付方式   place_order页面
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
		
		//*****汇率********start
		$this->frushCurrency();
		//*****汇率（最终返回的是汇率后价）********end
		
		//*****************country && currency ******start
		$currency_list = $this->_currency();
		$country_list = $this->_country();
		$this->_view_data['currency_list'] = $currency_list;
		$this->_view_data['country_list'] = $country_list;
		//*****************country && currency ******end
		
		//购物车信息（cart summary）
		$cart_data = $this->getCart();
		if(empty($cart_data)) redirect(genURL('cart'));
		$this->_view_data['cart_data'] = $cart_data;
		//获取用户的地址信息
		$address_data = $this->_getUserAddressList();
		$this->_view_data['address_data'] = $address_data;
		
		//计算商品，长宽高，重量，体积，敏感数据,及总价
		$cart_weight_info = $this->computeWeightInfo($cart_data);
		$subtotal = $cart_weight_info['subtotal_price'];//商品基本价格是经过了汇率计算的
		//shipping options (可用物流方式，物流费用，海外本地仓库) start***************
		$payment_addres_country_name = $this->getCountryName($this->address_warehouse);
		if($this->able_use_address==true){
			$shipping_options_data = $this->_get_shipping_option_data($this->address_warehouse,$cart_data,$cart_weight_info);
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
		$adyen_list[$this->prefix.'1'] = array(
				'picname'=>RESOURCE_URL ."images/paymentMethod/"."paypalsk.jpg",
				'name'=>'paypalsk',
				'checked'=>1,
				'id'=>1
		);
		$this->able_use_payment = true;
		$this->_view_data['adyen_list'] = $adyen_list;
		//********************payment method start**************************
		
		//***************rewords**************start
		$this->_getRewardsData($user_id,$subtotal);
		$this->_view_data['total_rewards'] = $this->current_currency_code." ".$this->current_currency." ".$this->total_rewards;
		//***************rewords**************end
		
		$insurancePrice = $rewardsBalance = $couponSavings = 0;
		$payPrice = round($subtotal + $this->shippingCharges,2);
		$bool = null;
		
		$this->_view_data['insurance'] = $this->shipping_insurance;
		$this->_view_data['payment_country'] = $payment_addres_country_name?$payment_addres_country_name:"";
		$this->_view_data['payment_country_code'] = $this->address_warehouse;
		//$this->_view_data['currency'] = $currency;
		$this->_view_data['new_currency_code'] = $this->current_currency_code;
		$this->_view_data['subtotal'] = $subtotal;
		$this->_view_data['shippingCharges'] = $this->shippingCharges;
		$this->_view_data['insurancePrice'] = $insurancePrice;
		$this->_view_data['rewardsBalance'] = $rewardsBalance;
		$this->_view_data['couponSavings'] = $couponSavings;
		$this->_view_data['payPrice'] = $payPrice;
		$this->_view_data['bool'] = ($this->able_use_payment && $this->able_use_address && $this->able_use_shipping)?true:false;
		
		$this->_view_data['place_order_type'] = 'paypal_ec_login';
		//parent::index( 'placeorder' );
		parent::index();
	}
	
	public function eclogined_ajaxFresh($type=null){
		//检查用户是否登录，如果没有登录就返回购物车页面
		if(!$this->customer->checkUserLogin())
		{
			$return_data['status'] = 1007;//表明没有登录
			$return_data['msg'] = "NO LOGIN";
			$return_data['data'] = array('url'=>genURL('login'),'bool'=>false);
			$this->ajaxReturn($return_data);
		}
		$user_id = $this->session->get('user_id');
		
		//根据选择地址id（1 获取地址id对应国家code； 2 重新获取物流方式 ； 3 重新获取购物车数据；  4 重新计算总价；5重新计算rewards；6 重新计算coupon数据；）
		$addressId = $this->input->get("addressId");//地址id
		$shippingid = $this->input->get("shippingid");//物流方式id
		$shippingtrack = $this->input->get("shippingtrack");
		if(stripos($shippingid, 'shippingid')!==false){
			$shippingid = str_replace('shippingid', '', $shippingid);
		}
		if(($shippingid==1 || $shippingid==2)&& $shippingtrack==1){
			$shippingid +=3;
		}
		if(($shippingid==4 || $shippingid==5)&& $shippingtrack!=1){
			$shippingid -=3;
		}
		$insurance = $this->input->get("insurance");
		$itemsFirst = $this->input->get("itemsFirst");
		$paymentId= $this->input->get("payment");
		$rewardsValue = $this->input->get("rewards");
		$couponValue = $this->input->get("coupon");
		$country = $this->input->get("country");//银行卡支付国家
		$currency = $this->input->get("currency");//支付币种
		//汇率改变  currency
		if(!empty($currency))set_cookie('currency',$currency,864000);
		
		//*****汇率********start
		$currency_code = currentCurrency();
		$currency = "$";
		if($this->currency_format){
			$currency = $this->currency_format['currency_format'];
		}
		//*****汇率（最终返回的是汇率后价）********end
		
		$return_data = array();
		$new_address_data = $this->_getUserAddressList($addressId);
		$return_data['address'] = $new_address_data;
		
		//购物车信息（cart summary）
		$cart_data = $this->getCart();
		
		//coupon start******************
		$cart_weight_info = $this->computeWeightInfo($cart_data);
		$subtotal = $cart_weight_info['subtotal_price'];
		$this->_getRewardsData($user_id,$subtotal);
		$coupon_data = $this->_computeCoupon($couponValue,$cart_data);
		$return_data['coupon'] = $coupon_data;
		$cart_data = array_merge($cart_data,$this->gift_product_info);
		//coupon end******************
		
		//计算商品，长宽高，重量，体积，敏感数据,及总价
		$cart_weight_info = $this->computeWeightInfo($cart_data);
		$subtotal = $cart_weight_info['subtotal_price'];//汇率后价
		
		//根据地址id，获取默认地址信息及国家信息
		$this->load->model("addressmodel","address");
		$selected_address_info = $this->address->getAddressById($addressId);
		
		//shipping option  start***************
		$shipping_options_data = $this->_get_shipping_option_data($selected_address_info['address_country'],$cart_data,$cart_weight_info,$shippingtrack,$shippingid);
		$return_data['shipping'] = $shipping_options_data;
		//shipping option  end***************
		
		//********************payment method start**************************
		$adyen_list[$this->prefix.'1'] = array(
				'picname'=>RESOURCE_URL ."images/paymentMethod/"."paypalsk.jpg",
				'name'=>'paypalsk',
				'checked'=>1,
				'id'=>1
		);
		$this->able_use_payment = true;
		$return_data['payment']['data'] = $adyen_list;
		$return_data['payment']['country'] = $this->getCountryName($country);
		$return_data['payment']['countryId'] = $country;
		$return_data['payment']['currency'] = $currency." ".$currency_code;
		$return_data['payment']['currencyId'] = $currency_code;
		//********************payment method end**************************
		
		//****************rewards start****************
		$return_data['AvailableBalanceprice'] = $currency_code." ".$currency." ".$this->total_rewards;
		if(!empty($return_data['coupon'])){
			$rewards_subtotal = round( ($subtotal*100 - $return_data['coupon']['msg']*100)/100,2);
		}else{
			$rewards_subtotal = $subtotal;
		}
		$rewards_data = $this->_getRewardsData($user_id,$rewards_subtotal,$rewardsValue);
		$return_data['rewards'] = $rewards_data;
		//****************rewards start****************
		
		//物流费用计算 start******
		/*$shippingCharges = 0;//汇率后价
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
		}*/
		//物流费用计算 end******
		
		//保险费用计算 start****
		if($insurance==0 || !is_numeric($insurance)){
			$insurancePrice = 0;
			$return_data['insurance'] = false;
		}else{
			$insurancePrice = $this->shipping_insurance;
			$return_data['insurance'] = true;
		}
		//保险费用计算 end****
		
		//最终支付费用 start*******
		$payPrice = $subtotal + $this->shippingCharges + $insurancePrice-$this->rewardsBalance-$this->couponSavings;
		if($this->currency_format){
			$base_payPrice = round($subtotal/($this->currency_format['currency_rate']),2)+round( $this->shippingCharges /($this->currency_format['currency_rate']),2);
			$base_payPrice += round($insurancePrice/($this->currency_format['currency_rate']),2);
			$base_payPrice -= round($this->rewardsBalance/($this->currency_format['currency_rate']),2);
			$base_payPrice -= round($this->couponSavings/($this->currency_format['currency_rate']),2);
		}else{
			$base_payPrice = $payPrice;
		}
		
		//最终支付费用 end*******
		
		//返回购物车数据结构处理
		$json_cart_data =  $this->formatCartData($cart_data);
		//echo "<pre>";print_r($json_cart_data);die;
		$return_data['list'] = $json_cart_data;
		$return_data["subtotal"] = $currency.' '.$subtotal;
		$return_data["shippingCharges"] = $currency.' '.$this->shippingCharges;
		$return_data["insurancePrice"] = $currency.' '.$insurancePrice;
		$return_data["rewardsBalance"] = $currency.' '.$this->rewardsBalance;
		$return_data["couponSavings"] = $currency.' '.$this->couponSavings;
		$return_data["payPrice"] = $currency.' '.$payPrice;
		$return_data['shipping_insurance'] = $this->shipping_insurance;
		
		//pay url start********
		$bool = false;
		if($this->able_use_address && $this->able_use_payment && $this->able_use_shipping && $this->use_rewards_status && $this->able_coupon_status ){
			$bool = true;
		}
		$return_data['bool'] = $bool;
		
		//是否拆包
		$return_data['itemsFirst'] = $itemsFirst?true:0;
		
		$new_data['msg'] = "OK";
		$new_data['status'] = 200;
		$new_data['data'] = $return_data;
		if($type=="place_order"){
			$return_data['payPrice_num'] = $payPrice;
			$return_data['base_payPrice_num'] = $base_payPrice;
			$return_data['couponValue'] = $couponValue;
			$return_data['pay_country'] = $country;//支付国家
			//$return_data['pay_address_code'] = $pay_address_code;
			$return_data['pay_address_code'] =  $this->address_warehouse;//配送地址国家
			$return_data['compute_list'] = $cart_data;//
			$return_data['shippingid'] = $shippingid;//为1，5时，说明airmail，standard有哦track费用
			$return_data['base_couponSavings'] = $this->base_couponSavings;
			$return_data['able_user_baserewards'] = $this->base_rewardsBalance;
			$return_data['able_user_rewards'] = $this->rewardsBalance;
			return $return_data;
		}else{
			//echo "<pre>";print_r($return_data);die;
			$this->ajaxReturn($new_data);
		}
	}
	
	public function eclogined_processOrder(){
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
		//获取paypaec支付id
		$paypalEcPayerid = $this->session->get('paypal_ec_payerid');
		//paypaec支付id为空
		if($paypalEcPayerid === false || $paypalEcPayerid == ''){ 
			$return_data['status'] = 85006;
			$return_data['msg'] = "check_address_province";
			$return_data['data'] = null;
			$this->ajaxReturn($return_data);
		}
		//获取paypaec支付token
		$paypalEcToken = $this->session->get('paypal_ec_token');
		//paypaec支付token为空
		if($paypalEcToken === false || $paypalEcToken == '') { 
			$return_data['status'] = 85007;
			$return_data['msg'] = "check_address_province";
			$return_data['data'] = null;
			$this->ajaxReturn($return_data);
		}
		
		//重新刷新数据
		$result = $this->eclogined_ajaxFresh($type="place_order");
		//注册订单处理错误回调监控
		
		//获取购物车信息
		$cart_data = $result['list'];
		if(empty($cart_data)) redirect(gensslURL('cart'));//购物车中的商品为空
		
		//地址处理
		$address_data = $result['address'];
		foreach ($address_data as $address_k=>$address_v){
			if($address_v['checked']==1) $address = $address_v;
		}
		
		//判断当美国或者澳大利亚的时候州信息是否有
		if(isset($address['address_country']) && in_array($address['address_country'],array('US','AU'))) {
			if(!isset($address['address_province']) || empty($address['address_province']) ) {
				$return_data['status'] = 85002;
				$return_data['msg'] = "check_address_province";
				$return_data['data'] = null;
				$this->ajaxReturn($return_data);
			}
		}

		//地址里面国家和省份的判断
		/*if(in_array($address['address_country'],array('US','AU')) && $address['address_province'] == '') {
			$return_data['status'] = 85004;
			$return_data['msg'] = "check_address_province";
			$return_data['data'] = null;
			$this->ajaxReturn($return_data);
		}*/
		
		//创建订单
		$order = $this->_generateOrder($user_id, $email, $result, $address);
		//创建订单商品
		$orderGoodsList = $this->_generateOrderProduct($order['order_id'], $result, $order);
		//coupon(下单使用coupon)
		if(isset($result['couponValue']) && !empty($result['couponValue'])){
			$this->load->model('couponmodel','couponmodel');
			$this->couponmodel->updateCouponUseTimes($result['couponValue']);
		}

		//订单邮件发送(后续)
		$this->_newSendMail($order, $orderGoodsList);
		//paypal ec 支付跳转url返回
		$payResult = $this->_processPaypalECRequest($paypalEcToken, $paypalEcPayerid, $order);
		if( $payResult ){
			$url = $this->_paymentUrl('paypal_ec');
		}else{
			$url = $this->_paymentUrl('paypal_ec');
			//$url = $this->_paymentUrl('paypal_ec_unpaid');
		}
		//清空购物车(该用户)
		$this->load->model('cartmodel','cartmodel');
		$this->cartmodel->bathDelCartWithUserId($user_id);
		set_cookie('cart_merge', 0);
		$return_data['status'] = 200;//表明没有登录
		$return_data['msg'] = "OK";
		$return_data['data'] = array('url'=>$url);
		$this->ajaxReturn($return_data);
		
	}
	
	/**
	 * 解析paypalec支付的地址信息
	 * @param  array $response 支付信息数组
	 */
	protected function _fetchAddressInfoFromPaypalECResponse($response) {
		//初始化地址数组
		$address = array();
		list($address['address_firstname'], $address['address_lastname']) = explode(' ', id2name('SHIPTONAME', $response));
			
		//取出详细地址信息
		$address['address_address'] = id2name('SHIPTOSTREET', $response);
		//$address['address2'] = id2name('SHIPTOSTREET2', $response);
		$address['address_city'] = id2name('SHIPTOCITY', $response);
		$address['address_province'] = id2name('SHIPTOSTATE', $response);
		$address['address_zipcode'] = id2name('SHIPTOZIP', $response);
		$address['address_country'] = id2name('SHIPTOCOUNTRYCODE', $response);
		
		// 加载place order页面的时候 地址信息合法检查
		/*$address_state_is_none = false;
		if(isset($address['address_country']) && in_array($address['address_country'],array('US','AU'))) {
			if(!isset($address['address_province']) || empty($address['address_province']) ) {
				$address_state_is_none = true;
			}
		}*/
		
		//格式化地址信息
		//$session_id = $this->session->sesssionID();
		//$new_address_data["address_id"] = $address['address_id'];
		$new_address_data['address_0']["first_name"] = $address['address_firstname'];
		$new_address_data['address_0']["last_name"] = $address['address_lastname'];
		$new_address_data['address_0']["address"] = $address['address_address'];
		$new_address_data['address_0']["address2"] = "";
		$new_address_data['address_0']["city"] = $address['address_city'];
		$new_address_data['address_0']["country"] = $address['address_country'];
		$new_address_data['address_0']['country_name'] = $this->getCountryName($address['address_country']);
		$new_address_data['address_0']["zipcode"] = $address['address_zipcode'];
		$new_address_data['address_0']["mobile"] = '';
		$new_address_data['address_0']["cpf"] = '';
		$new_address_data['address_0']["region"] = $address['address_province'];
		$new_address_data['address_0']["defaults"] = 1;
		$new_address_data['address_0']['checked'] = 1;
		//$address['address_desc'] = $this->m_address->formatAddress($address);
		$this->session->set('paypal_ec_address',$new_address_data);
		
		$this->address_warehouse = id2name('SHIPTOCOUNTRYCODE', $response);
		return $new_address_data;
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
		$order['order_address_country'] = $this->getCountryName(addslashes(id2name('country',$address)));
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
			$hundred_discount = ($cart_v['product_info']['product_new_currency_price']*100 -  $cart_v['product_info']['product_discount_price']*100);
			//单个商品的汇率后折扣数
			$single_discount = round($hundred_discount/100,2);
			//单个商品的汇率前折扣数
			$single_base_discount = round($cart_v['product_info']['product_baseprice'] -  $cart_v['product_info']['product_basediscount_price'],2);
			//总数
			$qunatity = $cart_v['product_quantity'];
			//折扣前的汇率前的price
			$order_price_product += round($qunatity*$cart_v['product_info']['product_new_currency_price'],2);
			//折扣前的汇率后的price
			$order_baseprice_product += round($qunatity*$cart_v['product_info']['product_baseprice'],2);
			//折扣前的汇率前的总price
			$order_price_subtotal += round($qunatity*$cart_v['product_info']['product_discount_price'],2);
			//折扣前的汇率后的总price
			$order_baseprice_subtotal += round($qunatity*$cart_v['product_info']['product_basediscount_price'],2);
			//汇率后的总折扣数
			$order_price_discount += round($qunatity*$single_discount,2);
			//汇率前的总折扣数
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
		//coupon
		$order['order_coupon'] = $result['couponValue'];
		$baseprice_coupon = 0;
		if($result['couponValue']) {
			$baseprice_coupon = $order['order_baseprice_coupon'] = $result['base_couponSavings'];
			$order['order_price_coupon'] = $this->couponSavings; //coupon减的折扣价
		}

		//rewards
		$order['order_price_rewards'] = $result['able_user_rewards'];//rewards 折扣价 正数，保留两位小数
		$order['order_baseprice_rewards'] = $result['able_user_baserewards'];
		$order['order_country_payment'] = $result['pay_country'];//支付国家  支付时选择支付国家
		//用户级别
		$order['order_customer_type'] = $this->customer->getRewordsRate($userId);
		$order['order_country_shipping'] = $result['pay_address_code'];//配送国家 address  country_code
		//最终支付
		$order['order_price'] =  $result['payPrice_num']; ////用户 最终支付金额(subtotal+insureance+shipping-discount-coupon-rewards)
		//$hundred_baseprice = $order_baseprice_subtotal*100-$result['able_user_baserewards']*100-$baseprice_coupon*100;
		$order['order_baseprice'] = $result['base_payPrice_num'];//汇率之前 用户最终支付金额
		$order['order_rewards'] = round($order['order_baseprice']*$order['order_customer_type']/100,2);//积分（ordertotal -运费-保险）* 汇率？？
		
		//支付方式处理
		$payment_list = $result['payment']['data'];
		$payment_id = 3;//bank（默认bank支付方式）
		foreach ($payment_list as $k=>$v){
			//if($v['checked']==1) $payment_id = $k;
			if($v['checked']==1) $payment_id = str_replace('payment_', '', $k);
		}
		$order['payment_id'] = $payment_id;
		
		//跟踪
		$order['order_gaid'] = generateGACid();
		
		//入口
		$order['order_entrance'] = EBPLATEFORM;
		
		//是否拆包
		$order['order_flg_separate_package'] = $result['itemsFirst'];
		
		//保险（保险价，是否保险）
		$order['order_flg_insurance'] = $result['insurance']?1:0;
		if($result['insurance']==1){
			$order['order_price_insurance'] = round(INSURANCE*$order['order_currency_rate'],2);
			$order['order_baseprice_insurance'] = INSURANCE;
		}else{
			$order['order_price_insurance'] = $order['order_baseprice_insurance'] = 0;
		}
		
		
		//订单状态
		$order['order_status'] = OD_CREATE;//新建
		
		$order['order_time_create'] = $order['order_time_lastmodified'] = date("Y-m-d H:i:s",time());
		$this->load->model("ordermodel","ordermodel");
		
		//创建订单
		$orderId = $this->ordermodel->createOrder($order);
		
		//订单号生成
		$orderSn = ORDER_PREFIX . date('Ymd',requestTime()) . str_pad($orderId,8,'0',STR_PAD_LEFT);
		//更新订单sn
		/*$this->ordermodel->updateOrder($orderId, array(
				'order_code' => $orderSn,
		));*/
		
		//记录coupon使用
		if(!empty($order['order_coupon'])){
			if($this->coupon_type==SUBSCRIBE_COUPON){//更新subscribe表coupon使用状态
				$this->load->model('subscribemodel','subscribe');
				$this->subscribe->updateSubscribeCouponStatus($order['order_coupon']);
			}else{//更新普通使用状态
				$history_data['customer_id'] = $userId;
				$history_data['coupon_code'] = $order['order_coupon'];
				$history_data['history_create_time'] = date('Y-m-d H:i:s',time());
				$this->load->model('couponhistorymodel','couponhistory');
				$this->couponhistory->createHistory($history_data);
			}
		}
		
		//rewards使用记录
		if($order['order_baseprice_rewards'] > 0){
			$rewards_data['customer_id'] = $order['customer_id'];
			$rewards_data['rewards_history_type'] = 2;
			$rewards_data['order_id'] = $orderId;
			$rewards_data['rewards_history_value'] = $order['order_baseprice_rewards'];
			$rewards_data['rewards_history_time_create'] = date('Y-m-d H:i:s',time());
			$this->load->model('rewardsmodel','rewardsmodel');
			$this->rewardsmodel->createRewardsHistory($rewards_data);
			//更新用户rewards
			$this->customer->updateUserRewards($order['customer_id'],$order['order_baseprice_rewards'],'-');
		}

		//order_action'动作记录
		$this->_writeOrderAction($orderId);
		
		//记录订单信息
		$this->session->set('order_code',$orderSn);
		
		//记录订单日志
		$this->log->write(Log::LOG_TYPE_ORDER,json_encode($order));
		
		$order['order_id'] = $orderId;
		$order['order_code'] = $orderSn;
		
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
			
			$price_discount = $val['product_info']['product_new_currency_price']-$val['product_info']['product_discount_price'];
			$baseprice_discount = $val['product_info']['product_baseprice']-$val['product_info']['product_basediscount_price'];
			$product['order_product_price_discount'] = round($qunatity*$price_discount,2);
			$product['order_product_baseprice_discount'] = round($qunatity*$baseprice_discount,2);
			
			$discount_info = $this->singleProductDiscount($product_id,$product['order_product_baseprice_market']);
			if($discount_info) {
				$product['order_product_promote_id'] = $discount_info['discount_id'];
				$product['order_product_promote_type'] = 1;//暂时只有1
			}
			
			$product['order_product_warehouse'] = $val['product_sku_info']['product_sku_warehouse'];
			$product['order_product_time_create'] = date('Y-m-d H:i:s',requestTime());
			//echo "<pre>bbbbbbbbbbbbb";print_r($product);die;
			$orderGoodsList[] = $product;
			//更新商品销量
			$this->product->updatePorductSales($product_id ,$qunatity);
		}

		//添加订单中的商品
		$this->load->model("orderodel","ordermodel");
		//echo "<pre>bbbb";print_r($orderGoodsList);die;
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
			//查看限制次数
			if($base_coupon_info['coupon_limit_total']){
				//使用次数
				$this->load->model('couponhistorymodel','couponhistory');
				$coupon_history_list = $this->couponhistory->couponHistoryList($coupon);
				if(count($coupon_history_list) >= $base_coupon_info['coupon_limit_total']){
					$result['save_price'] = 0;//美元
					$result['cart_list'] = $cart_list;
					
					$result['status'] = 501;
					$result['msg'] = 'You has used !';
					return $result;
				}
			}
			
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
				$effect_info = $this->_cartEffectInfo($coupon_id,$in_cart_list);
				$saving_price = $effect_info['save_price'];//美元
				$in_cart_list = $effect_info['cart_list'];
				$effect_status = $effect_info['status'];
				
			}
			if($effect_status != 200){
				$result['status'] = 404;
				$result['msg'] = "Your coupon value shouldn't exceed than your order total amount";
			}else{
				$result['status'] = 200;
			}
			
		}else{
			$result['status'] = 404;
			$result['msg'] = 'Sorry, the coupon code you entered is not existed. Please enter a valid one.';
		}
		
		$result['save_price'] = $saving_price;//美元
		$result['cart_list'] = array_merge($noin_cart_list,$in_cart_list);
		//echo "<pre>bbsdfdfwww";print_r($result);die;
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
		$status = 200;
		
		$coupon_effect_info = $this->coupon->couponeffectInfoWithCouponid($coupon_id);
		if(!empty($coupon_effect_info)){
			$coupon_type = $coupon_effect_info[0]['coupon_effect_type'];
			$total_price = $this->_computeCartPrice($cart_list);//美元价
			
			switch($coupon_type){
				case COUPON_TYPE_REDUCE://满减
					//直接"减钱"(美元)
					if($coupon_effect_info[0]['coupon_effect_price'] < $total_price){//判断条件
						//判断减免的价钱与购物总价大小
						if($total_price >  $coupon_effect_info[0]['coupon_effect_value'])$saving_price = $coupon_effect_info[0]['coupon_effect_value'];
						else $status = 501;//商品总价小于coupon价
					}else{
						$status = 501;//商品总价小于coupon价
					}
					
					$return_cart_list = $cart_list;
					break;
				case COUPON_TYPE_DISCOUNT://满折扣
					//直接"打折"(美元)
					$saving_price = round($total_price*$coupon_effect_info[0]['coupon_effect_value']/100,2);
					$return_cart_list = $cart_list;	
					break;
				case COUPON_TYPE_REDUCE_LEVEL://条件满减
					//美元，没有汇率转换计算的
					$saving_price = $this->_filterCouponEffect($total_price ,$coupon_effect_info);
					$return_cart_list = $cart_list;
					break;
				case COUPON_TYPE_DISCOUNT_LEVEL://条件下满折扣
					//美元，没有汇率转换计算的
					$effect_value = $this->_filterCouponEffect($total_price ,$coupon_effect_info);
					if($effect_value!=0)$saving_price = round($effect_value*$total_price/100,2);
					$return_cart_list = $cart_list;
					break;
				case COUPON_TYPE_GIFT://赠品
					//判断条件
					if( ($coupon_effect_info[0]['coupon_effect_price']==0) || $total_price > $coupon_effect_info[0]['coupon_effect_price']){
						$gift_product_array = json_decode($coupon_effect_info[0]['coupon_effect_value'],true);
						//根据sku获取商品信息 （数据结构与购物车商品一致）
						$gift_product_info = $this->_giftInfoWithSku($gift_product_array['sku'],$gift_product_array['limit'],$gift_product_array['price']);
						if(!empty($gift_product_info)){
							$gift_product_info['product_coupon_price'] = $gift_product_array['price'];
							$cart_list[] = $gift_product_info;
							$this->gift_product_info[] = $gift_product_info;//赠品数据
							//美元
							//$saving_price = $gift_product_info['product_quantity']*($gift_product_info['product_info']['product_basediscount_price']-$gift_product_array['price']);
							$saving_price = 0;
						}
					}else{
						$status = 501;//商品总价小于coupon价
					}
					
					$return_cart_list = $cart_list;
					break;
			}
			
		}else{
			$status = 404;
		}
		
		$result = array(
			'save_price'=>$saving_price,//美元
			'cart_list'=>$return_cart_list,
			'status'=>$status,	
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
	private function _giftInfoWithSku($sku,$quantity = 1,$price = 0){
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
		//改变赠品价格
		$product_info['product_discount_price'] = $price;//美元
		$product_info['product_basediscount_price'] = $price;
		
		$cart_list['product_info'] = $product_info;
		//获取sku对应属性信息
		$attr_value_info = $this->attrAndValueWithSku($product_sku);
		if(!empty($attr_value_info)){
			foreach ($attr_value_info as $attr_key=>$attr_val){
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
		foreach ($coupon_effect_list as $k=>$v){
			if($v['coupon_effect_price']<=$price) $coupon_effect_value = max($coupon_effect_value,$v['coupon_effect_value']);
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
	private function  _ruleshipping($address_warehouse,$cart_weight_info,$track = 0){
		$rules = $unrules = array();
		//根据购物车商品的长，宽，高等条件，筛选normal物流方式
		$able_shipping_list = $this->_filterNormalShipping($address_warehouse,$cart_weight_info);
		if(isset($able_shipping_list['able']) && !empty($able_shipping_list['able'])){
			//计算快递价格
			foreach ($able_shipping_list['able'] as $able_shipp_key=>$able_shipp_val){
				$type = strtolower($able_shipp_val['country_shipping_rule_shipping_code']);
				//if(in_array(strtolower($type), $clear_type)) continue;
				$shipping_price = $this->_shippingPrice($cart_weight_info['weight'],$type,$address_warehouse,$cart_weight_info['price_weight']);
				//组合返回给前端的数据 start
				if($shipping_price!==false){
					$rules[] = $this->formatShippigData($type,$shipping_price,$address_warehouse);
				}
			}
		}
		if(isset($able_shipping_list['unable']) && !empty($able_shipping_list['unable'])){
			//不可用物流方式
			foreach ($able_shipping_list['unable'] as $unable_shipp_key=>$unable_shipp_val){
				$un_type = strtolower($unable_shipp_val['country_shipping_rule_shipping_code']);
				//if(in_array(strtolower($un_type), $clear_type)) continue;
				$un_shipping_price = $this->_shippingPrice($cart_weight_info['weight'],$un_type,$address_warehouse,$cart_weight_info['price_weight']);
				//组合返回给前端的数据 start
				if($un_shipping_price!==false){
					$unrules[] = $this->formatUnShippigData($un_type,$un_shipping_price,$address_warehouse);
				}
			}
		}
		
		return array('able'=>$rules,'unable'=>$unrules);
	}
	
	/**
	 * @desc  计算对应快递运费
	 * @param unknown $weight (体积重 = 长*宽*高/5000*数量 ;  weight = max(实重,体积重);)
	 * @param unknown $type (对应快递方式)
	 */
	private function _shippingPrice($weight,$type,$warehouse,$volume_weight){
		$price = 0;
		//从数据库中的单位为人民币，最终需要转换为美元
		switch ($type){
			case "standard":
				$ratecny = $this->_getCnyRate();
				//(10+120*weight)/RateCNY-weight*12
				$price = round((10+120*$weight)/$ratecny-$weight*12,2);
				break;
			case "express":
				$ratecny = $this->_getCnyRate();
				$new_price = $this->_expressZonePrice($warehouse ,$volume_weight);//max(实重,体积重)（只有此处用）
				if($new_price!=false){
					$price = round($new_price/$ratecny-$weight*12,2);
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
				$price = round((10+120*$weight)/$ratecny-$weight*12,2);
				//standard+(DE?+8.69:+2.19)
				//$price = (strtoupper($warehouse)=="DE")?($price+8.69):($price+2.19);
				break;
			case "airmail":
			default:
				//其他价格都为0
				$price = 0;	
		}
		return $price;
		
	}
	
	/**
	 * @desc 根据国家code及weight = max(实重,体积重)，返回express价格(人民币)
	 * @param unknown $country_code
	 * @param unknown $weight
	 * @return boolean|number
	 */
	private function _expressZonePrice($country_code ,$weight){
		if(empty($country_code)) return false;
		$this->load->model("expresszonemodel","expresszone");
		$zone_info = $this->expresszone->expressCountry2Zone($country_code);
		$zone_id = 24;//默认zoneid
		if(!empty($zone_info)){
			$zone_id = $zone_info[0]['express_zone_id'];
		}
		//获取zone对应的价格
		$all_zone_price_info = $this->expresszone->expressZoneList($zone_id);
		if(!empty($all_zone_price_info)){
			$info = $all_zone_price_info[0];
			$express_zone_price_first = $info['express_zone_price_first'];
			$express_zone_price_step_20 = $info['express_zone_price_step_20'];
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
				$price = $express_zone_price_first+ceil( ( $weight - 0.5 ) / 0.5 ) * $express_zone_price_step_20;
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
			
	}
	
	/**
	 * @desc 需要判断商品是否符合物流配送规则（只针对 普通物流Normal）
	 * @param unknown $country_code 国家code
	 * @param unknown $weight_info  重量，体积，length，敏感值范围数组
	 * @return array 符合条件及不符合条件的normal物流方式(可用物流方式返回为空时，结果为不可送，没有物流运送方式)
	 */
	private function _filterNormalShipping($country_code,$weight_info,$sensitive = false){
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
		//符合规则与不符合规则的物流方式
		if(!empty($country_rule)){
			foreach ($country_rule as $key=>$val){
				if($val['country_shipping_rule_status_active']==1){//不用判断直接可用
					$result['able'][] = $val;
					continue;
				}
				if($val['country_shipping_rule_status_disable']==1){//不用判断直接不可用
					$result['unable'][] = $val;
					continue;
				}
				if(!empty($weight_info['sentitve_range']) && $val['country_shipping_rule_status_sensitive_disable']>0){//严格匹配敏感品（任何类型的敏感品都不可用）
					$result['unable'][] = $val;
					continue;
				}
				$sentitve_count = array_intersect($weight_info['sentitve_range'],array(1,5)); 
				if($val['country_shipping_rule_status_battery_disable']>0 && count($sentitve_count)){//判断敏感品类型是否是1，5（电池类）
					$result['unable'][] = $val;
					continue;
				}
				
				$weight = $val['country_shipping_rule_limit_weight'];
				$volume = $val['country_shipping_rule_limit_volume'];
				$length = $val['country_shipping_rule_limit_length'];
				//重，体积，长度不限制
				$able_value = true;
				if($weight!=0 && $weight < $weight_info['weight']){
					$able_value = false;
				}			
				if($volume!=0 && $volume < $weight_info['volume']){//体积
					$able_value = false;
				}
				if($length != 0 && $length < $weight_info['length']){//长宽
					$able_value = false;
				}
				if($able_value===true){
					$result['able'][] = $val;
				}else{
					$result['unable'][] = $val;
				}
				
			}
		}

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
		$base_subtotal_price = 0;//
		
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
					$total_weight += $quantity*$weight;
					$total_volume += $width*$height*$length*$quantity*200;
					$total_length = max($total_length,$width,$height,$length);
					if($sentitve_type>0) array_push($sensitive_type_array, $sentitve_type);
					$volume_weight += $total_volume; 
				}	
				
				$subtotal_price += $quantity*$val['product_info']['product_discount_price'];
				$base_subtotal_price += $quantity*$val['product_info']['product_basediscount_price'];
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
			'base_subtotal_price'=>$base_subtotal_price,
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
				if(array_key_exists(strtolower($seaval), $warehouse_range_array)){
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
				if(strtolower($address_code)==strtolower('CN')){//地址为中国
					//cnmail 免费
					$shipping_name = 'cnmail';
				}else{
					//显示五种物流方式(后续还需判断是否有可配送物流)
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
	private function getCart($type = null){
		if($this->customer->checkUserLogin()){
			$this->load->model("cartmodel","cart");
			
			$user_id = $this->session->get("user_id");
			$cart_list = $this->cart->cartListWithLoginUser($user_id);
		}else{
			if($type==null){
				//redirect(genURL("login"));
				return null;
			}else{
				$this->load->model("cartmodel","cart");
					
				$session_id = $this->session->sessionID();
				$cart_list = $this->cart->cartListWithSessionid($session_id);
			}
		}
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
				
			if(isset($new_address_list[$default_address_id])){//不存在默认地址
				$address[] = $new_address_list[$default_address_id];
				//$news[] = $new_address_list[$default_address_id];
				unset($new_address_list[$default_address_id]);
				if(!empty($new_address_list))$address = array_merge($address,$new_address_list);
			}elseif(!empty($default_address_info)){
				$address[] = $default_address_info;
				unset($new_address_list[$default_addrss_info_id]);
				//if(!empty($new_address_list)) $address = array_merge($address,$new_address_list);
				if(!empty($new_address_list)) $address = $address+$new_address_list;
			}else{
				$address[] = $address_list[0];
				unset($address_list[0]);
				if(!empty($address_list))$address = $address+$address_list;
			}
			
		}
		//echo "<pre>dfdf";print_r($address);die;
		return $address;
		
	}
	
	/**
	 * @desc 定义返回给前端的物流方式数据格式(并计算track跟踪费用)
	 * @return array
	 */
	private function formatShippigData($shipping_name,$price,$warehouse){
		$airmail = BASE_TRACK;
		$base_price = $price;
		$base_airmail = $airmail;
		$standard = (strtoupper($warehouse)=="DE")?8.69:2.19;
		$base_standard = $standard;
		//汇率转换
		$currency_format = $this->getCurrencyNumber();
		$currency = "$";
		if($currency_format){
			$currency = $currency_format['currency_format'];
			$price = round($price*$currency_format['currency_rate'],2, PHP_ROUND_HALF_DOWN);
			$airmail= round($airmail*$currency_format['currency_rate'],2, PHP_ROUND_HALF_DOWN);
			$standard= round($standard*$currency_format['currency_rate'],2, PHP_ROUND_HALF_DOWN);
		}
		
		$data = array(
			'cnmail'=>array(
				"id"=>6,"title"=>"CNmail",'selected'=>1,"day"=>"1-5 working days",
				'tips'=>"",'track'=>0,"trackTitle"=>"",'available'=>1,//是否禁用,0:不可用；1：可用
				'price'=>$currency." 0.00",'trackPrice'=>"",
				'single_price'=>0,'single_trackprice'=>0,//前端显示使用
				'base_price'=>0,'base_trackprice'=>0,'true_trackprice'=>0,	//计算汇率处理前使用
			),
			'freesea'=>array(
					"id"=>7,"title"=>"free expedited shipping",'selected'=>1,"day"=>"3-5 working days",
					'tips'=>"",'track'=>0,"trackTitle"=>"",'available'=>1,//是否禁用
					'price'=>$currency." 0.00",'trackPrice'=>"",
					'single_price'=>0,'single_trackprice'=>0,'base_price'=>0,'base_trackprice'=>0,'true_trackprice'=>0,
			),
			'airmail'=>array(
				"id"=>1,"title"=>"airmail",'selected'=>1,"day"=>"10-20 working days",
				'tips'=>"",'track'=>0,"trackTitle"=>"Track my package",'available'=>1,//是否禁用
				'price'=>$currency." 0.00",'trackPrice'=>"+".$currency.$airmail,
				//'single_price'=>0,'single_trackprice'=>$airmail,'base_price'=>0,'base_trackprice'=>$base_airmail,
					'single_price'=>0,'single_trackprice'=>$airmail,'base_price'=>0,'base_trackprice'=>0,'true_trackprice'=>0,
			),
			'standard'=>array(
				"id"=>2,"title"=>"standard",'selected'=>1,"day"=>"6-10 working days",
				'tips'=>"",'track'=>0,"trackTitle"=>"Track my package",'available'=>1,//是否禁用
				'price'=>$currency.$price,'trackPrice'=>"+".$currency.$standard,
				//'single_price'=>$price,'single_trackprice'=>$standard,'base_price'=>$base_price,'base_trackprice'=>$base_standard,
				'single_price'=>$price,'single_trackprice'=>$standard,'base_price'=>$base_price,'base_trackprice'=>0,'true_trackprice'=>0,
			),
			'register_airmail'=>array(
					"id"=>4,"title"=>"airmail",'selected'=>1,"day"=>"10-20 working days",
					'tips'=>"",'track'=>1,"trackTitle"=>"Track my package",'available'=>1,//是否禁用
					'price'=>$currency." 0.00",'trackPrice'=>"+".$currency.$airmail,
					'single_price'=>0,'single_trackprice'=>$airmail,'base_price'=>0,'base_trackprice'=>$base_airmail,'true_trackprice'=>$airmail,
			),
			'register_standard'=>array(
					"id"=>5,"title"=>"standard",'selected'=>1,"day"=>"6-10 working days",
					'tips'=>"",'track'=>1,"trackTitle"=>"Track my package",'available'=>1,//是否禁用
					'price'=>$currency.$price,'trackPrice'=>"+".$currency.$standard,
					'single_price'=>$price,'single_trackprice'=>$standard,'base_price'=>$base_price,'base_trackprice'=>$base_standard,'true_trackprice'=>$standard,
			),
			'express'=>array(
				"id"=>3,"title"=>"expedited",'selected'=>1,"day"=>"3-7 working days",
				'tips'=>"",'track'=>-1,"trackTitle"=>"",'available'=>1,//是否禁用
				'price'=>$currency.$price,'trackPrice'=>"",
				'single_price'=>$price,'single_trackprice'=>0,'base_price'=>$base_price,'base_trackprice'=>0,'true_trackprice'=>0,
			),
		);
		
		$shipping_name = strtolower($shipping_name);
		if(isset($data[$shipping_name])) $result = $data[$shipping_name];
		else $result = array(); 
		//echo "<pre>adsa";print_r($result);
		return $result;
		
	}
	
	/**
	 * @desc 定义返回给前端的物流方式数据格式(并计算track跟踪费用)
	 * @return array
	 */
	private function formatUnShippigData($shipping_name,$price,$warehouse){
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
			$price = round($price*$currency_format['currency_rate'],2, PHP_ROUND_HALF_DOWN);
			$airmail= round($airmail*$currency_format['currency_rate'],2, PHP_ROUND_HALF_DOWN);
			$standard= round($standard*$currency_format['currency_rate'],2, PHP_ROUND_HALF_DOWN);
		}
	
		$data = array(
				'cnmail'=>array(
						"id"=>6,"title"=>"CNmail",'selected'=>0,"day"=>"1-5 working days",
						'tips'=>"",'track'=>-1,"trackTitle"=>"",'available'=>0,//是否禁用
						'price'=>$currency." 0.00",'trackPrice'=>"",
						'single_price'=>0,'single_trackprice'=>0,//前端显示使用
						'base_price'=>0,'base_trackprice'=>0,'true_trackprice'=>0,	//计算汇率处理前使用
				),
				'freesea'=>array(
						"id"=>7,"title"=>"free expedited shipping",'selected'=>0,"day"=>"3-5 working days",
						'tips'=>"",'track'=>-1,"trackTitle"=>"",'available'=>0,//是否禁用
						'price'=>$currency." 0.00",'trackPrice'=>"",
						'single_price'=>0,'single_trackprice'=>0,'base_price'=>0,'base_trackprice'=>0,'true_trackprice'=>0,
				),
				'airmail'=>array(
						"id"=>1,"title"=>"airmail",'selected'=>0,"day"=>"10-20 working days",
						'tips'=>"Not available for this address",'track'=>-1,"trackTitle"=>"Track my package",'available'=>0,//是否禁用
						'price'=>$currency." 0.00",'trackPrice'=>"+".$currency.$airmail,
						//'single_price'=>0,'single_trackprice'=>$airmail,'base_price'=>0,'base_trackprice'=>$base_airmail,
						'single_price'=>0,'single_trackprice'=>$airmail,'base_price'=>0,'base_trackprice'=>0,'true_trackprice'=>0,
				),
				'standard'=>array(
						"id"=>2,"title"=>"standard",'selected'=>0,"day"=>"6-10 working days",
						'tips'=>"Not available for this address",'track'=>-1,"trackTitle"=>"Track my package",'available'=>0,//是否禁用
						'price'=>$currency.$price,'trackPrice'=>"+".$currency.$standard,
						//'single_price'=>$price,'single_trackprice'=>$standard,'base_price'=>$base_price,'base_trackprice'=>$base_standard,
						'single_price'=>$price,'single_trackprice'=>$standard,'base_price'=>$base_price,'base_trackprice'=>0,'true_trackprice'=>0,
				),
				'register_airmail'=>array(
						"id"=>4,"title"=>"airmail",'selected'=>0,"day"=>"10-20 working days",
						'tips'=>"Not available for this address",'track'=>-1,"trackTitle"=>"Track my package",'available'=>0,//是否禁用
						'price'=>$currency." 0.00",'trackPrice'=>"+".$currency.$airmail,
						'single_price'=>0,'single_trackprice'=>$airmail,'base_price'=>0,'base_trackprice'=>$base_airmail,'true_trackprice'=>$airmail,
				),
				'register_standard'=>array(
						"id"=>5,"title"=>"standard",'selected'=>0,"day"=>"6-10 working days",
						'tips'=>"Not available for this address",'track'=>0,"trackTitle"=>"Track my package",'available'=>0,//是否禁用
						'price'=>$currency.$price,'trackPrice'=>"+".$currency.$standard,
						'single_price'=>$price,'single_trackprice'=>$standard,'base_price'=>$base_price,'base_trackprice'=>$base_standard,'true_trackprice'=>$standard,
				),
				'express'=>array(
						"id"=>3,"title"=>"expedited",'selected'=>0,"day"=>"3-7 working days",
						'tips'=>"Not available for this address",'track'=>-1,"trackTitle"=>"",'available'=>0,//是否禁用
						'price'=>$currency.$price,'trackPrice'=>"",
						'single_price'=>$price,'single_trackprice'=>0,'base_price'=>$base_price,'base_trackprice'=>0,'true_trackprice'=>0,
				),
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
	
	/**
	 * new订单邮件发送
	 * @param  array $order 订单信息
	 * @param  array $orderGoodsList 订单商品
	 */
	protected function _newSendMail( $order, $orderGoodsList ) {
		//echo "<pre>23456";print_r($order);print_r($orderGoodsList);die;
		try
		{
			$type = 1; //订单提交成功
			$currentLanguageId = currentLanguageId();
			
			//获取email模板id
			$this->load->model('emailmodel','emailmodel');
			$this->load->model('emailtemplatemodel','emailtemplate');
			$eid = $this->emailmodel->getEmailTemplateId($type , $currentLanguageId );
			if($eid && is_numeric($eid)){
				$order['country'] = $this->getCountryName($order['order_address_country']);
				
				$order = eb_htmlspecialchars( $order );
				//订单商品信息dom
				$orderInfoDomArray = $this->emailtemplate->getEmailOrderInfoDom( $order, $orderGoodsList, 'order' );
				
				//推荐商品
				$productIds = array();
				$recommendProDom = null;
				foreach($orderGoodsList as $key=>$val){
					$productIds[] = $val['product_id'];
				}
				if(!empty($productIds)){
					$this->load->model('goodsmodel','product');
					$recommendProList = $this->product->getEmailRecommendProduct( $currentLanguageId, $productIds, FALSE, array(), '', $order['order_currency'], $order['language_id'], '', 'order' );
					$recommendProDom = $this->emailtemplate->getEmailRecommendProductDom( $recommendProList );
				}
				
				global $base_url,$shipping_list,$payment_list;
				//邮件模版参数
				$shipping_id = str_replace('shippingid', '', $order['shipping_id']);
				$payment_id = str_replace('payment_', '', $order['payment_id']);
				$contentParam = array(
						'SITE_DOMAIN' => rtrim( $base_url[ $currentLanguageId ], '/' ), //域名链接
						'SITE_DOMAIN1' => COMMON_DOMAIN, //域名
						'CS_EMAIL' => 'cs@'.COMMON_DOMAIN,
						'USER_NAME' => $order['order_address_firstname']." ".$order['order_address_lastname'],
						'ORDER_NUM' => $order['order_code'],
						'ORDER_TIME' => $order['order_time_create'],
						'ORDER_INFO' => $orderInfoDomArray['order_info'],
						'SHIP_ADDRESS' => $orderInfoDomArray['address'],
						'SHIP_WAY' => $shipping_list[$shipping_id],
						'PAY_WAY' => $payment_list[$payment_id],
						'ITEM_REO' => $recommendProDom,
				);
				
				$result = eb_email($eid,$order['order_email'],$contentParam);
				
				//发送失败重试一次
				if( stripos($result, 'OK')===false){
					$result = eb_email($eid,$order['order_email'],$contentParam);
				}
				if( $result !='OK'){
					$logInfo = '[order] ORDERID:#'.$order['order_code'].' - EMAIL:'.$order['order_email'].' - EID:'.$eid.' - ERROR:'.$result;
					$this->log->write( Log::LOG_TYPE_SYSTEM_EMAIL , $logInfo, true );
				}
			}
			
			return true;
		}catch( Exception $e ){
			return ;
		}
		
	}
	
	/**
	 * @desc 获取用户地址，并转换为前端需要的格式
	 * @return Ambigous <multitype:, unknown>
	 */
	private function _getUserAddressList($selected_address_id = 0){
		//获取用户的地址信息及默认地址
		$address_data = $this->addressList();
		//格式变换
		$new_address_data = array();
		$selected_address_data = array();
		$first_address_id = 0;
		$prefix = 'address_';
		$has_checked = false;
		
		if(!empty($address_data)){
			//$able_use_address = true;
			$this->able_use_address = true;
			foreach($address_data  as $address_key=>$address_val){
				$address_id = $address_val['address_id'];
				if($first_address_id==0)$first_address_id = $address_id;//作为不存在时的默认地址id
				
				$new_address_data[$prefix.$address_id]["address_id"] = $address_val['address_id'];
				$new_address_data[$prefix.$address_id]["first_name"] = isset($address_val['address_firstname'])?addslashes($address_val['address_firstname']):'';
				$new_address_data[$prefix.$address_id]["last_name"] = isset($address_val['address_lastname'])?addslashes($address_val['address_lastname']):'';
				$new_address_data[$prefix.$address_id]["address"] = isset($address_val['address_address'])?addslashes($address_val['address_address']):'';
				$new_address_data[$prefix.$address_id]["address2"] = "";
				$new_address_data[$prefix.$address_id]["city"] = isset($address_val['address_city'])?addslashes($address_val['address_city']):'';
				$new_address_data[$prefix.$address_id]["country"] = isset($address_val['address_country'])?$address_val['address_country']:'';
				$new_address_data[$prefix.$address_id]['country_name'] = isset($address_val['address_country'])?$this->getCountryName($address_val['address_country']):'';
				$new_address_data[$prefix.$address_id]["zipcode"] = isset($address_val['address_zipcode'])?addslashes($address_val['address_zipcode']):'';
				$new_address_data[$prefix.$address_id]["mobile"] = isset($address_val['address_phone'])?$address_val['address_phone']:'';
				$new_address_data[$prefix.$address_id]["cpf"] = isset($address_val['address_cpfcnpj'])?$address_val['address_cpfcnpj']:'';
				$new_address_data[$prefix.$address_id]["region"] = isset($address_val['address_province'])?$address_val['address_province']:'';
				$new_address_data[$prefix.$address_id]["defaults"] = isset($address_val['address_default'])?$address_val['address_default']:0;
				if($selected_address_id==0){
					$new_address_data[$prefix.$address_id]["checked"] = $address_val['address_default'];
					if($address_val['address_default']==1) $has_checked = true;
					//默认进来时的国家编码（物流配送时计算使用）
					if($address_val['address_default']){
						$this->address_warehouse = $address_val['address_country'];
						$first_address_id = $address_id;
					}
				}else{
					if($address_id==$selected_address_id){
						$new_address_data[$prefix.$address_id]["checked"] = 1;
						$has_checked = true;
						$this->address_warehouse = $address_val['address_country'];
					}else{
						$new_address_data[$prefix.$address_id]["checked"] = 0;
					}
					if($address_val['address_default'])$first_address_id = $address_id;
				}
				
				//默认进来时的国家编码（物流配送时计算使用）
				if($address_val['address_default']==null){
					$this->address_warehouse = $address_val['address_country'];
				}
			}
			
			if($this->address_warehouse==null){
				$this->address_warehouse = $address_data[0]['address_country'];
			}
		}
		if($has_checked === false)$new_address_data[$prefix.$first_address_id]['checked'] = 1;
		if($first_address_id==0)return array();
		$first_address_data[$prefix.$first_address_id] = $new_address_data[$prefix.$first_address_id];
		if($selected_address_id)$selected_address_data[$prefix.$selected_address_id] = $new_address_data[$prefix.$selected_address_id];
		unset($new_address_data[$prefix.$first_address_id],$new_address_data[$prefix.$selected_address_id]);
		//$new_address_data = array_merge($first_address_data,$new_address_data);
		if(!empty($selected_address_data)) $new_address_data = $first_address_data+$selected_address_data+$new_address_data;
		else $new_address_data = $first_address_data+$new_address_data;
		
		return $new_address_data;
	}
	
	/**
	 * @desc 计算coupon
	 * @param unknown $couponValue
	 * @param unknown $cart_data
	 * @return string
	 */
	private function _computeCoupon($couponValue,$cart_data){
		if(trim($couponValue)){
			$currency_code = currentCurrency();
			$currency = "$";
			
			$subscribe_coupon_price = SUBSCRIBE_COUPON_PRICE;
			if($this->currency_format){
				$currency = $this->currency_format['currency_format'];
				$subscribe_coupon_price = round($this->currency_format['currency_rate']*SUBSCRIBE_COUPON_PRICE,2);
			}
			//订阅coupon检查
			$subscribe_coupon_info = $this->checkSubscribeCouponInfo($couponValue);
			if($subscribe_coupon_info){
				$return_data = $this->_computeSubscribeCoupon($cart_data);
				return $return_data;
			}
			
			//正式coupon
			$coupon_format_result = $this->_userCouponInfo($cart_data,trim($couponValue));
			//echo "<pre>bbbb";print_r($coupon_format_result);die;
			if($coupon_format_result['status']==200){
				//coupon可用状态
				$this->able_coupon_status = true;
		
				//能减多少价钱
				$this->couponSavings = $coupon_format_result['save_price'];//美元
				$this->base_couponSavings = $this->couponSavings;//美元
				if($this->currency_format){
					$this->couponSavings = round($this->couponSavings*$this->currency_format['currency_rate'],2);
				}
				//有赠品的话，在原购物车数据基础上增加数据（只是显示）
				$cart_data = $coupon_format_result['cart_list'];
		
				$coupon_status = 200;
				$coupon_msg = $this->couponSavings;
				$coupon_price = $currency_code." ".$currency." ".$this->couponSavings;
			}else{
				$coupon_status = 404;
				//$coupon_msg = "Sorry, the coupon code you entered is not existed. Please enter a valid one.";
				$coupon_msg = $coupon_format_result['msg'];
				$coupon_price = "";
			}
				
			$return_data['status'] = $coupon_status;
			$return_data['msg'] = $coupon_msg;//纯粹价钱，不包含货币单位
			$return_data['price'] = $coupon_price;
			return $return_data;
		}else{
			//没有coupon，也可支付
			$this->able_coupon_status = true;
			return '';
		}
		
	}
	
	/**
	 * @desc 计算subscribe coupon情况
	 * @param unknown $cart_data
	 * @return string
	 */
	private function _computeSubscribeCoupon($cart_data){
		$subscribe_coupon_price = SUBSCRIBE_COUPON_PRICE;
		$currency_code = currentCurrency();
		$currency = "$";
		
		//计算购物车总价($)
		$cart_total_price = $this->_computeCartPrice($cart_data);
		if($this->currency_format){
			$currency = $this->currency_format['currency_format'];
			$subscribe_coupon_price = round($this->currency_format['currency_rate']*SUBSCRIBE_COUPON_PRICE,2);
			$cart_total_price = round($this->currency_format['currency_rate']*$cart_total_price,2);
		}
		if($cart_total_price > $subscribe_coupon_price){
			$this->coupon_type = SUBSCRIBE_COUPON;
			$this->base_couponSavings = $this->couponSavings = $subscribe_coupon_price;//美元
			if($this->currency_format){
				$this->couponSavings = round($subscribe_coupon_price*$this->currency_format['currency_rate'],2);
			}
			$return_data['status'] = 200;
			$return_data['msg'] = 'OK';
			$return_data['price'] = $currency_code." ".$currency." ".$subscribe_coupon_price;
		}else{
			$return_data['status'] = 1010;
			$return_data['msg'] = 'The Coupon gt Subtotal';
			$return_data['price'] = $currency_code." ".$currency." 0";
		}
		
		return $return_data;
	}
	
	/**
	 * @desc 检测订阅coupon存在否
	 * @param unknown $coupon
	 * @return Ambigous <multitype:, unknown>
	 */
	private function checkSubscribeCouponInfo($coupon){
		$result = array();
		if(empty($coupon)) $result;
		$this->load->model('subscribemodel','subscribe');
		$data = $this->subscribe->getSubscribeCoupon($coupon);
		if(!empty($data)){
			$result = $data;
		}
		return $result;
	}
	
	/**
	 * @desc 获取物流
	 * @param unknown $selected_address_info
	 */
	private function _get_shipping_option_data($address_country,$cart_data,$cart_weight_info,$track = 0,$selected_shipping_id = 0){
		if(!empty($address_country)){
			$this->able_use_address = true;
			$cart_all_warehouse = $this->cartWarehouse($cart_data);//所有存储仓库信息
			//国家对应仓库可选的物流方式(最终得到那种类型的物流：cnmail,freesea,nomail,null;null类型时，获取对应msg错误码及msg)
			$address_warehouse = isset($address_country)?$address_country:null;
			$shipping_options_list = $this->_shippingoptions($address_warehouse,$cart_all_warehouse);
			//需要判断物流限制规则（只有normal，且仓库有本地仓库时才会做此判断）
			if($shipping_options_list['exists_rule']==1){
				$able_shipping_options = $this->_ruleshipping($address_warehouse,$cart_weight_info,$track);
				if($able_shipping_options['able']){
					$shipping_options_data = $this->_normalShippingFormat($able_shipping_options,$selected_shipping_id,$track);
				}else{
					$shipping_options_data = array(
							//'msg'=>'Some items can not be shipped to the address you selected.',
							'msg'=>'No shipping method available at this time',
							'status'=>2001,
							'data'=>null,
					);
				}
					
			}else{
				//返回对应提示信息(海外，或cnmail)
				if($shipping_options_list['data']!==null){
					$this->able_use_shipping = true;
					$shipping_json = $this->formatShippigData($shipping_options_list['data'], $price=0, $address_warehouse);
					$shipping_options_data = array(
							'msg'=>'OK',
							'status'=>200,
							'data'=>array($shipping_json),
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
		return $shipping_options_data;
	}
	
	//页面上显示效果，最终处理(track,哪种物流的展示)
	private function _normalShippingFormat($able_shipping_options,$selected_shipping_id,$track = 0){
		$able_id_array = array();
		//根据track，排除不需要
		if(in_array($selected_shipping_id, array(4,5))){//非track
			$clear_id_array = array(1,2);
		}else{
			$clear_id_array = array(4,5);
		}
		
		//数据库里存储对airmail及standard的track是有两种形式的，但是展现只是一种形式处理
		foreach($able_shipping_options['able'] as $shipping_k=>&$shipping_v){
			array_push($able_id_array,$shipping_v['id']);
			if(in_array($shipping_v['id'], $clear_id_array)) unset($able_shipping_options['able'][$shipping_k]);
			
			//对没有默认选中物流方式时，第一个可用物流默认选中
			if(($selected_shipping_id==0 || !$selected_shipping_id )){
				if($shipping_k==0) {
					$able_shipping_options['able'][0]['selected'] = 1;
					$this->shippingCharges = $able_shipping_options['able'][0]['single_price'];//当前汇率下价格
					if($able_shipping_options['able'][0]['track']==1) $this->shippingCharges += $able_shipping_options['able'][0]['single_trackprice'];
					 
				}
			}elseif($selected_shipping_id && is_numeric($selected_shipping_id)){
				//其他selected选中状态处理
				if($selected_shipping_id && is_numeric($selected_shipping_id) && $shipping_v['id']==$selected_shipping_id){
					$shipping_v['selected'] = 1;
					$this->shippingCharges = $shipping_v['single_price'];//当前汇率下价格
					if(in_array($selected_shipping_id,array(4,5))){
						$shipping_v['track'] =1;
						$this->shippingCharges += $shipping_v['single_trackprice'];
					}
					
				}else{
					$shipping_v['selected'] = 0;
					$shipping_v['track'] =0;
				}
			}else{
				$shipping_v['selected'] = 0;
				$shipping_v['track'] =0;
			}
		}
		foreach($able_shipping_options['unable'] as $un_shipping_k=>&$unshipping_v){
			if(in_array($unshipping_v['id'], $able_id_array))unset($unshipping_v);
			$unshipping_v['selected'] = 0;
			$unshipping_v['track'] =0;
		}
		//重新排序
		$return_shippin_data = array_merge($able_shipping_options['able'],$able_shipping_options['unable']);
		$able_shipping_options = array();
		foreach ($return_shippin_data as $again_key=>$again_val){
			if(in_array($again_val['id'],array(1,4))) {
				$able_shipping_options[0] = $again_val;
			}else if(in_array($again_val['id'],array(2,5))) {
				$able_shipping_options[1] = $again_val;
			}else{
				$able_shipping_options[2] = $again_val;
			}
		
		}
		$able_shipping_options = array($able_shipping_options[0],$able_shipping_options[1],$able_shipping_options[2]);
		$shipping_options_data = array(
				'msg'=>'OK',
				'status'=>200,
				'data'=>$able_shipping_options,
		);
		$this->able_use_shipping = true;
		return $shipping_options_data;
	}
	
	/**
	 * @desc 获取用户rewards
	 * @param unknown $user_id
	 */
	private function _getRewardsData($user_id,$subtotal,$rewardsValue = null){
		$currency_code = currentCurrency();
		$currency = "$";
		if($this->currency_format){
			$currency = $this->currency_format['currency_format'];
		}
		
		$user_rewards = $this->customer->getUserById($user_id);
		$rewards_status = 200;
		$total_rewards = 0;
		if(!empty($user_rewards)){
			 $product_rewards = round($subtotal*REWARDS_DISCOUNT,2);//转换汇率后总价
			
			//可用rewords
			$total_rewards = $user_rewards['customer_rewards'];//$
			if($total_rewards){
				$old_total_rewards = $total_rewards;//customer表里可用rewards美元
				if($this->currency_format) {
					$total_rewards = round($total_rewards*$this->currency_format['currency_rate'],2);
				}
				//本次可用使用rewards（汇率后价钱）
				if($total_rewards > $product_rewards){
					$this->able_user_rewards = $product_rewards;
					if($this->currency_format)$this->able_user_baserewards = round( ($subtotal*10000)/($this->currency_format['currency_rate']*10000),2);
					$this->able_user_baserewards = $product_rewards;
				}else{
					$this->able_user_rewards = $total_rewards;
					$this->able_user_baserewards = $old_total_rewards;
				}
			}
			
		}
		$this->total_rewards = $total_rewards;//总共可用rewards，汇率后价钱 
		$rewardsPrice = 0;
		if($rewardsValue && is_numeric($rewardsValue)){
			if($rewardsValue > $this->able_user_rewards){
				$rewards_status = 404;
			}else{
				//coupon可用状态
				$rewards_status = 200;
				//$this->rewardsBalance = $this->able_user_rewards;//汇率后价
				$this->rewardsBalance = $rewardsValue;//汇率后价
				$base_rewardsbalance = $rewardsValue;
				if($this->currency_format) {
					$base_rewardsbalance = round( ($rewardsValue*10000)/($this->currency_format['currency_rate']*10000), 2);
				}
				$this->base_rewardsBalance = $base_rewardsbalance;
				$rewardsPrice = $rewardsValue;
				$this->use_rewards_status = true;//可以使用rewards
			}
			
			$return_data['rewards']['status'] = $rewards_status;
			$return_data['rewards']['rewardsPrice'] =  $currency." ".$rewardsPrice;//前端展示消费掉的rewards
			if($rewards_status!=200) $return_data['rewards']['msg'] = "Sorry you can use up to ".$currency." ".$this->able_user_rewards." for this purchase";
			else $return_data['rewards']['msg'] = null;
				
		}else{
			//没有rewards
			$return_data['rewards'] = "";
			$this->use_rewards_status = true;
		}
		return $return_data['rewards'];
	}
	
	public function _createPaypalecCustomer($email,$address){
		if(empty($email)) return false;
		$exists_paypalec_user = $this->customer->getUserByEmail($email);
		$this->load->model('addressmodel','addressmodel');
		if(!empty($exists_paypalec_user)){
			//存在paypal账号
			$user_id = $exists_paypalec_user['customer_id'];
			$user_name = $exists_paypalec_user['customer_name'];
			//取消原默认地址
			$this->addressmodel->editAddress($user_id, array('address_default'=>0));
		}else{
			//创建账号,初始密码为12345678
			$user_name = $data['customer_name'] = $address['first_name'].' '.$address['last_name'];
			$data['customer_email'] = $email;
			$data['customer_password'] = $this->customer->hashPassword(PAYPALEC_LOGIN_PASSWORD);
			$data['customer_time_create'] = $data['customer_time_lastlogin'] = date('Y-m-d H:i:s',time());
			$data['customer_ip'] = $this->input->ip_address();
			$data['customer_status'] = 1;
			
			$user_id = $this->customer->createUser($data);
			$this->session->set('paypal_ec_create_user',array_merge(array('email'=>$email,$address)));
		}
		
		//创建地址
		$address_data = array(
				"address_id"=>'',
				'customer_id'=>$user_id,
				'address_firstname' => $address['first_name'],
				'address_lastname' => $address['last_name'],
				'address_country' => $address['country'],
				'address_province' => $address['region'],
				'address_city' => $address['city'],
				'address_address' => $address['address'],
				'address_phone' => $address['mobile'],
				'address_zipcode' => $address['zipcode'],
				'address_cpfcnpj' => $address['cpf'],
				'address_default' => 1,
				'address_status' => 1,
				'address_time_update' => date('y-m-d h:i:s',time()),
		);
		$address_id = $this->addressmodel->createAddress($address_data);
		
		//更新用户默认地址
		$this->customer->editUser($user_id, array('address_id'=>$address_id));
		//自动登录
		$this->session->set('user_id',$user_id);
		$this->session->set('user_name',$user_name);
		$this->session->set('email',$email);
		
		return $user_id;
	}
	
	/**
	 * 处理paypal ec接口请求
	 * @param  string $paypalEcToken  Paypal　ec token
	 * @param  integer $paypalEcPayerid Paypal　ec id
	 * @param  array $order 订单信息
	 */
	protected function _processPaypalECRequest($paypalEcToken, $paypalEcPayerid, $order) {
		$this->load->library('Payment/paypal_ec');//加载paypal ec支付模块
		//注意两种货币*******
		$currency = $this->paypal_ec->getPaypalECCurrency(); //获取paypal ec 访问国家
		$price = $order['order_price'];
	
		//接口参数处理
		$params = array();
		$params['AMT'] = $price;
		$params['CURRENCYCODE'] = $currency;
		$params['INVNUM'] = $order['order_code'];
		$params['TOKEN'] = $paypalEcToken;
		$params['PAYERID'] = $paypalEcPayerid;
		$params['PAYMENTACTION'] = 'Sale';
		$response = $this->paypal_ec->callPaypalRequest('DoExpressCheckoutPayment', $params);
		
		//接口返回成功处理（回写，改变数据库，并发送邮件******（待续）************）
		$this->load->model('ordermodel','ordermodel');
		if(isset($response['ACK']) && $response['ACK'] == 'Success') {
			$change_order_array=  array(
					'order_status'=>OD_PAID,
					'order_transnumber'=>$response['TRANSACTIONID'],
					'order_time_pay'=>date('Y-m-d H:i:s' ,time()),
			);
			$this->ordermodel->updateOrderWithCode($order['order_code'],$change_order_array);
			
			//积分
			if(isset($order['order_rewards']) && $order['order_rewards']){
			 	//记录rewards奖励history
			 	$this->load->model('rewardsmodel','rewardsmodel');
			 	$rewards_data['customer_id'] = $order['customer_id'];
			 	$rewards_data['rewards_history_type'] = 1;
			 	$rewards_data['order_id'] = $order['order_id'];
			 	$rewards_data['rewards_history_value'] = $order['order_rewards'];
			 	$rewards_data['rewards_history_time_create'] = date('Y-m-d H:i:s',time());
			 	$this->rewardsmodel->createRewardsHistory($rewards_data);
				//更新用户rewards
				$this->customer->updateUserRewards($order['customer_id'],$order['order_rewards']);
			}
			
			//新增order_action动作
			$order_action_info = json_encode(array('payment_id'=>$order['payment_id'],'transnumber'=>$response['TRANSACTIONID']));
			$this->_writeOrderAction($order['order_id'],ORDER_ACTION_TYPE_PAID,$order_action_info);
			
			//记录loger
			$this->log->write(Log::LOG_TYPE_PAYPAL_EC,json_encode($response));
			//支付成功邮件
			$type = 2;
			$email = $order['order_email'];
			$LanguageId = $order['language_id'];
			$params = array(
					//'SITE_DOMAIN' => COMMON_DOMAIN,
					'USER_NAME' => $order['order_address_firstname']." ".$order['order_address_lastname'],
					//'SUBSCRIBE_LINK' => genURL('common/validateSubscribe').'?email='.encrypt($email).'&utm_source=System_Own&utm_medium=Email&utm_campaign=Newsletter-confirm&utm_nooverride=1',
					'CS_EMAIL' => 'cs@eachbuyer.com',
			);
			$this->load->model('emailmodel','emailmodel');
			$result = $this->emailmodel->subscribe_sendMail( $type, $LanguageId ,$email,$params);
			
			//GA订单数据
			$this->_processGAInfo($order);
			
			return TRUE;
		}
	
		return false;
	}
	
	//点击“place order”按钮提交订单支付时：
		
		//1 中间form表单提交
		
		//2 提交给paype，adyen，本地银行，处理
		
		//3 返回处理页面，及支付平台请求本站服务器请求
		
		//4 success页面（使用session，不能刷新，使用https安全）
		
}