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
		
		//获取用户的地址信息及默认地址
		$address_data = $this->addressList();
	
		//购物车信息（cart summary）
		$cart_data = $this->getCart();
		
		//shipping options (可用物流方式，物流费用，海外本地仓库) start***************
		$cart_all_warehouse = $this->cartWarehouse($cart_data);//所有存储仓库信息
		//国家对应仓库可选的物流方式
		$shipping_options_list = $this->_shippingoptions($address_data,$cart_all_warehouse);
		//需要判断物流限制规则（只有normal，且仓库有本地仓库时才会做此判断）
		if($shipping_options_list['exists_rule']==1){
			$address_warehouse = $address_data[0]['address_country'];
			$able_shipping_options = $this->_ruleshipping($address_warehouse,$cart_data);
			
			
		}else{
			//返回对应提示信息，或cnmail，或海外免费信息？？？？？
			
		}
		
		//echo "<pre>place_order:";print_r($shipping_options_list);die;
		
		$shipping_data = "";
		//shipping options (可用物流方式，物流费用，海外本地仓库) end***************
		
		//payment method
		
		//rewords
		
		//copon
		
		//total price
		
		echo "check_out";die;
		parent::index();
	}

	public function ajaxFresh(){
		//地址id
		$addrss_id = 11;
		//物流方式id
		$shippingid = 0;
		
		
	}
	
	private function  _ruleshipping($address_warehouse,$cart_data){
		$cart_weight_info = $this->computeWeightInfo($cart_data);//计算商品，长宽高，重量，体积，敏感数据
		//根据购物车商品的长，宽，高等条件，筛选normal物流方式
		$able_shipping_list = $this->_filterNormalShipping($address_warehouse,$cart_weight_info);
		if(!empty($able_shipping_list)){
			//计算快递价格
			foreach ($able_shipping_list as $able_shipp_key=>$able_shipp_val){
				$type = strtolower($able_shipp_val['country_shipping_rule_shipping_code']);
				$shipping_price = $this->_shippingPrice($cart_weight_info['price_weight'],$type,$address_warehouse);
				//start ???????????????
				//组合返回给前端的数据
				if($shipping_price!=false){
					//当前币种，当前国家，换算成当前汇率价钱
					$currency_code = currentCurrency();
					$currency = "$";
					//汇率转换
					$currency_format = $this->getCurrencyNumber();
					if($currency_format){
						$currency = $currency_format['currency_format'];
						$shipping_price = round($shipping_price*$currency_format['currency_rate'],4, PHP_ROUND_HALF_DOWN);
					}
		
				}else{
					//没有可用快递方式
		
				}
				//end ???????????????
			}
		
		}else{
			//没有可用快递方式？？？？？？
		
		}	
		
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
	
	//计算商品：重量，体积，长度,敏感类型值数组（非海外仓库商品需要计算）
	private function computeWeightInfo($cart_list){
		$result = array();
		$total_weight = 0;
		$total_volume = 0;
		$total_length = 0;
		$sensitive_type_array = array();//所有商品敏感值
		$price_weight = 0;//用于快递运费的价格计算
		$volume_weight = 0;//体积重
		
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
				
			}
			$price_weight = max($volume_weight,$total_weight);
		}
		$result = array(
			'weight'=>$total_weight,
			'volume'=>$total_volume,
			'length'=>$total_length,
			'sentitve_range'=>$sensitive_type_array,
			'price_weight'=>$price_weight,
		);
		
		//echo "<pre>compute";print_r($result);die;
		return $result;
		
	}
	
	//根据国家配送地址与商品仓库，判断物流地址
	private function _shippingoptions($address_list , $all_warehouse){
		$result = array();
		$shipping_status = 200;
		$shipping_msg = "OK";
		$shipping_exists_rule = false;
		
		//物流数据格式
		$shipping_format_data = $this->formatShippigData();
		if(empty($address_list)){//没有配送地址
			$shipping_msg = "Please fill in/select the shipping address above";
			$shipping_status = "2002";
			$shipping_data = null;
			
		}else{
			$address_country_code = strtolower($address_list[0]['address_country']);
			
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
				$shipping_data = null;
				
			}
			if($cart_overseas_num==1){//一个海外仓库时
				//判断与国家地址是否一致
				if($cart_overseas[0]==$address_country_code){
					if(count($cart_cn_warehouse)==0){
						//海外，免费
						$shipping_data = $shipping_format_data['freesea'];
					}else{
						//部分海外仓库，部分中国 （显示五种物流方式）
						$shipping_data = $shipping_format_data['normal'];
						$shipping_exists_rule = true;
					}
					
				}else{//国家与海外仓库不一致
					$shipping_msg = "Some items can not be shipped to the address you selected.";
					$shipping_status = "2003";
					$shipping_data = null;
				}
			}else{//本地仓库
				if($address_country_code==strtolower('CN')){//地址为中国
					//cnmail 免费
					$shipping_data = $shipping_format_data['cn'];
				}else{
					//显示五种物流方式
					$shipping_data = $shipping_format_data['normal'];
					$shipping_exists_rule = true;
				}
				
			}
			
		}
		
		$result['status'] = $shipping_status;
		$result['msg'] = $shipping_msg;
		$result['data'] = $shipping_data;
		$result['exists_rule'] = $shipping_exists_rule;//true: 需要判断，规则（长，宽，高，体积，是否敏感品）
		
		return $result;
	}
	
	/**
	 * @desc 根据购物车数据，返回购物车商品的仓库情况
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
		
		if($default_address_id==0 && !is_numeric($default_address_id)){
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
			
			if(isset($new_address_list[$default_address_id])){
				$address[] = $new_address_list[$default_address_id];
				unset($new_address_list[$default_address_id]);
				if(!empty($new_address_list))$address[] = $new_address_list;
			}elseif(!empty($default_address_info)){
				$address[] = $default_address_info;
				unset($new_address_list[$default_addrss_info_id]);
				if(!empty($new_address_list)) $address[] = $new_address_list;
				
			}else{
				$address[] = $address_list[0];
				unset($address_list[0]);
				if(!empty($address_list))$address[] = $address_list;
			}
			
		}
			
		return $address;
		
	}
	
	/**
	 * @desc 定义返回给前端的物流方式数据格式
	 * @return array
	 */
	private function formatShippigData(){
		return array(
			'cn'=>array(
				"id"=>4,"title"=>lang("CNmail"),'selected'=>1,"day"=>lang("1-5 working days"),
				'tips'=>"",'track'=>0,"trackTitle"=>"",'available'=>0,//是否禁用
				'price'=>"USD $ 0.00",'trackPrice'=>"+USD $ 12.22",
			),
			'freesea'=>array(
					"id"=>5,"title"=>lang("free expedited shipping"),'selected'=>1,"day"=>lang("3-5 working days"),
					'tips'=>"",'track'=>0,"trackTitle"=>"",'available'=>0,//是否禁用
					'price'=>"USD $ 0.00",'trackPrice'=>"+USD $ 12.22",
			),
			'normal'=>array(
				array(
					"id"=>1,"title"=>lang("airmail"),'selected'=>1,"day"=>lang("10-20 working days"),
					'tips'=>"",'track'=>1,"trackTitle"=>lang("Track my package"),'available'=>0,//是否禁用
					'price'=>"USD $ 0.00",'trackPrice'=>"+USD $ 12.22",
				),
				array(
					"id"=>2,"title"=>lang("standard"),'selected'=>1,"day"=>lang("6-10 working days"),
					'tips'=>"",'track'=>1,"trackTitle"=>lang("Track my package"),'available'=>0,//是否禁用
					'price'=>"USD $ 0.00",'trackPrice'=>"+USD $ 12.22",
				),
				array(
					"id"=>3,"title"=>lang("expedited"),'selected'=>1,"day"=>lang("3-7 working days"),
					'tips'=>"",'track'=>0,"trackTitle"=>"",'available'=>0,//是否禁用
					'price'=>"USD $ 0.00",'trackPrice'=>"+USD $ 12.22",
				),
			),
		);
	}
	
	//获取adyen列表
	
	//根据最低金额及本站的范围列表筛选排除adyen中列表
	
	//判断国际银行，本地银行
	
	//判断adyen中是否paype，不存在显示本地peype
	
	//点击“place order”按钮提交订单支付时：
	
		//1 中间form表单提交
		
		//2 提交给paype，adyen，本地银行，处理
		
		//3 返回处理页面，及支付平台请求本站服务器请求
		
		//4 success页面（使用session，不能刷新，使用https安全）
		
}