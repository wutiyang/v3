<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';
/**
 * 重新支付
 */
class Repay extends Dcontroller {
	private $shipping_insurance = INSURANCE;//保险定价
	private $repay_currency = '$';
	
	public function __construct() {
		parent::__construct();
	}

	/**
	 * 重新支付统一入口
	 * @param  boolean $orderId 订单的id
	 */
	public function index($orderId = false) {
		//检查用户是否登录  没有登录则跳转到登录页面
		if(!$this->customer->checkUserLogin()) { redirect(genURL('login')); }
		//当订单id为空的时候返回购物车
		if($orderId === false) { redirect(genURL('cart')); }
		
		//获取订单的信息
		$orderId = intval($orderId);
		$this->load->model('ordermodel','ordermodel');
		$order = $this->ordermodel->getOrderById($orderId);
		$this->_view_data['order'] = $order;
		
		//检测用户是否在黑名单
		$black_list_result = $this->checkCustomerInBlacklist();
		if($black_list_result && !empty($black_list_result)){
			//记录订单信息
			$this->session->set('order_code',$order['order_code']);
			redirect(gensslURL('success'));
		}
		
		$address_data = $this->getRepayAddressData($order['address_id']);
		$this->_view_data['address_data'] = $address_data;
		
		//判断下单用户和登录用户是否一直
		if($this->customer->getCurrentUserId() != $order['customer_id']) { redirect(genURL('order_list')); }
		//获取订单商品
		$goodslList = $this->_getGoodsList($orderId);
		$this->_view_data['goods_list'] = $goodslList;
		
		//所有商品重量等信息
		$cart_weight_info = $this->computeWeightInfo($goodslList,$address_data['country']);
		//echo "<pre>testtt";print_r($cart_weight_info);die;
		//校验商品的信息（上下架状态，库存，购买的数量，商品是否违法）
		$this->load->model('goodsmodel','product');
		$productInfo_status = $this->product->getProductInfoWithStatusAndPrice($goodslList, currentLanguageCode());
		if(!$productInfo_status){ redirect(genURL('cart'));}
		
		//判断不能支付就返回购物车
		if(!$this->_checkRepay($order)) { redirect(genURL('cart')); }
		
		//payment
		$adyen_list = $this->adyenList($last_price = 1,$order['order_country_payment']);
		if(!empty($adyen_list)) {
			$adyen_list[$this->prefix.$order['payment_id']]['checked'] = 1;
		}
		$this->_view_data['payment_list'] = $adyen_list;
		
		//物流
		$shipping_list = $this->getShippingList($order,$goodslList,$cart_weight_info);
		$this->_view_data['shipping_list'] = $shipping_list;
		$this->_view_data['shipping_id'] = $order['shipping_id'];
		$this->_view_data['shipping_insurance'] = $this->shipping_insurance;
		$this->_view_data['repay_currency'] = $this->repay_currency;
		$this->_view_data['payment_country_name'] = $this->getCountryName($order['order_country_payment']);
		parent::index();
	}

	/**
	 * 订单重新支付处理
	 * @param  boolean $orderId 订单id
	 */
	public function process(){
		$paymentId= $this->input->get("payment_id");
		$orderId= $this->input->get("order_id");

		//订单id为空
		if($orderId === false) { 
			/*$msg = '';
			$data = '';
			$status = 85012;
			$this->ajaxReturn($data = array('msg'=>$msg,'data'=>$data,'status'=>$status));
			*/
			redirect(genURL('cart'),'The order is not exists');
		}

		$this->load->model('ordermodel','ordermodel');
		//订单id强制转化
		$orderId = intval($orderId);
		//取出订单的信息
		$order = $this->ordermodel->getOrderById($orderId);

		//取出订单的商品信息
		$goodslList = $this->ordermodel->getProductListByOrderId($orderId);

		//校验商品的信息（上下架状态，库存，购买的数量，商品是否违法）
		$this->load->model('goodsmodel','product');
		$productInfo_status = $this->product->getProductInfoWithStatusAndPrice($goodslList, currentLanguageCode());
		if(!$productInfo_status){ redirect(genURL('cart'));}
		
		//判断不能支付就返回购物车
		if(!$this->_checkRepay($order)) { redirect(genURL('cart')); }

		//记录订单号
		$this->session->set('order_code',$order['order_code']);

		//支付方式跳转处理 paypalec 没有重支付功能
		global $payment_list;
		$brandCode = id2name($paymentId,$payment_list);
		$curPaymentMethod = 'adyen';
		if($paymentId == 2  ){//paypal 是PPSK，不是adyen里的PPUK
				$curPaymentMethod = $brandCode;//$brandCode == 'paypalsk'
		}
		$redirectUrl = $this->_paymentUrl($curPaymentMethod);
		if($brandCode != 'paypalsk'){
			$redirectUrl .= '/' . $brandCode ;
		}
		//跳转到支付
		redirect($redirectUrl,'refresh');
		
	}
	
	/**
	 * 通过支付方式选取支付url
	 * @param  string $payment 支付方式
	 */
	protected function _paymentUrl($payment = '') {
		$url = gensslURL('success') . '?payment=bank';
		$paymentUrlArray = array(
			'adyen' => genURL('pay/redirect/adyen'),
			'paypalsk' => genURL('paypal_payment'),
		);
		if(
			isset($paymentUrlArray[$payment]) &&
			!empty($paymentUrlArray[$payment])
		) {
			$url = $paymentUrlArray[$payment];
		}
		return $url;
	}

	/**
	 * 检查商品是否可以重新下单
	 * @param array $order 订单信息
	 * @param array $goodslList 订单商品信息
	 * @param array $latestGoodsList 最新获取商品信息
	 */
	protected function _checkRepay($order) {
		//订单支付显示支付时间是14天以内
		$completePaymentLimitTime = 86400*14;
		//用户没有登录
		if(!$this->customer->checkUserLogin()) { return false; }
		//订单为空的时候
		if(empty($order)) { return false; }
		//登录的订单用户和下单时的用户不一直
		if($order['customer_id'] != $this->customer->getCurrentUserId()) { return false; }
		//订单取消等
		//if( $order['order_status'] != OS_UNCONFIRMED ) { return false; }
		//判断是不是已经付款 0未付款
		if($order['order_status'] != OD_CREATE) { return false; }
		//判断是不是已经发货
		//if($order['order_time_shipped'] != SS_UNSHIPPED) { return false; }
		//判断订单是否已经过期
		if(time() - strtotime($order['order_time_create']) > $completePaymentLimitTime) { return false; }
		//判断支付方式是否存在
		if(!$this->checkPaymentMethodAvailable($order['payment_id'], $order['order_address_country'], $order['order_price'] )){ return false; }
	
		//判断是否是银行支付和paypal ec支付
		if(in_array($order['payment_id'],array(1,3))) { return false; }

		return true;
	}
	
	/**
	 * @desc 获取商品sku属性及长宽高等信息
	 * @param unknown $orderId
	 * @return unknown
	 */
	protected function _getGoodsList($orderId){
		$goodslList = $this->ordermodel->getProductListByOrderId($orderId);
		
		//获取商品仓库,长，宽，高等信息
		foreach ($goodslList as $key=>&$val){
			$product_sku = $val['product_sku'];
			$product_id = $val['product_id'];
			$sku_info = $this->skuinfoWithSku($product_sku,$product_id);
			if(!empty($sku_info)) $val['product_sku_info'] = $sku_info;
			
			$attr_value_info = $this->attrAndValueWithSku($product_sku);
			if(!empty($attr_value_info)){
				foreach ($attr_value_info as $attr_key=>$attr_val){
					$val['attr'][$attr_key]['name'] = $attr_val['attr_name'];
					$val['attr'][$attr_key]['value'] = $attr_val['attr_value_name'];
				}
			}else {
				$val['attr'] = "";
			}
			
		}
		//echo "<pre>bdfdfdfdf";print_r($goodslList);die;
		return $goodslList;
	}
	/**
	 * @desc 判断支付方式及返回支付列表数据
	 * @param string $payId
	 * @param string $countryCode
	 * @return boolean
	 */
	protected function checkPaymentMethodAvailable($payId  = '', $countryCode = ''){
		if(empty($countryCode) || empty($payId)){
			return false;
		}
		
		global $payment_list;
		if( empty($payment_list[$payId])){
			return false;
		}

		return true;
	}

	private function getRepayAddressData($address_id){
		$addrss = array();
		if(!$address_id || !is_numeric($address_id)) return $addrss;
		$this->load->model('addressmodel','address');
		$address_data = $this->address->getAddressById($address_id);
		
		$addrss["address_id"] = $address_data['address_id'];
		$addrss["first_name"] = isset($address_data['address_firstname'])?addslashes($address_data['address_firstname']):'';
		$addrss["last_name"] = isset($address_data['address_lastname'])?addslashes($address_data['address_lastname']):'';
		$addrss["address"] = isset($address_data['address_address'])?addslashes($address_data['address_address']):'';
		$addrss["address2"] = "";
		$addrss["city"] = isset($address_data['address_city'])?addslashes($address_data['address_city']):'';
		$addrss["country"] = isset($address_data['address_country'])?$address_data['address_country']:'';
		$addrss['country_name'] = isset($address_data['address_country'])?$this->getCountryName($address_data['address_country']):'';
		$addrss["zipcode"] = isset($address_data['address_zipcode'])?addslashes($address_data['address_zipcode']):'';
		$addrss["mobile"] = isset($address_data['address_phone'])?$address_data['address_phone']:'';
		$addrss["cpf"] = isset($address_data['address_cpfcnpj'])?$address_data['address_cpfcnpj']:'';
		$addrss["region"] = isset($address_data['address_province'])?$address_data['address_province']:'';
		$addrss["defaults"] = isset($address_data['address_default'])?$address_data['address_default']:0;
		
		return $addrss;
	}
	
	/**
	 * @desc 获取物流方式
	 * @param unknown $selected_shipping_id
	 * @param unknown $currency
	 * @param unknown $price
	 * @param unknown $warehouse
	 * @return Ambigous <multitype:, multitype:Ambigous <multitype:number string , multitype:number string unknown > >
	 */
	private function getShippingList($order , $goodsList, $cart_weight_info){
		$weight = $cart_weight_info['weight'];
		$volume_weight = $cart_weight_info['volume'];
		$selected_shipping_id = $order['shipping_id'];//物流id
		$order_currency_rate = $order['order_currency_rate'];//下单时汇率
		$currency_rate = $this->_getCnyRate();//人民币对美元汇率
		$warehouse = $order['order_country_shipping'];//物流运输国家
		//下单时货币
		$currency_code = $order['order_currency'];//ex:USD
		$this->load->model('currencymodel','currencymodel');
		$currency_info = $this->currencymodel->getConfigCurrency($currency_code);
		$currency = str_replace('%s', '', $currency_info['currency_format']);
		$this->repay_currency = $currency;
		
		//standard运费
		$register_standard_price = $standard_price = round((10+120*$weight)/$currency_rate-$weight*12,2);
		$register_standard_price = $standard_price = round($register_standard_price*$order_currency_rate,2);
		
		//express物流运费
		$new_price = $this->_expressZonePrice($warehouse ,$volume_weight);
		if($new_price!=false){
			$express_price = $new_price/$currency_rate-$weight*12;
		}else{
			$express_price = 0;
		}
		$express_price = round($express_price * $order_currency_rate,2);
		
		//airmail运费
		$airmail = $base_airmail = BASE_TRACK;
		
		//track价格
		$standard = (strtoupper($warehouse)=="DE")?8.69:2.19;
		$base_standard = $standard;
		
		if($currency_code!='USD'){
			$this->shipping_insurance = round($this->shipping_insurance*$order_currency_rate,2);
			$airmail= round($airmail*$order_currency_rate,2);//airmail价钱
			$standard= round($standard*$order_currency_rate,2);//standard中track价钱
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
						'single_price'=>0,'single_trackprice'=>$airmail,'base_price'=>0,'base_trackprice'=>0,'true_trackprice'=>0,
				),
				'register_airmail'=>array(
						"id"=>4,"title"=>"airmail",'selected'=>1,"day"=>"10-20 working days",
						'tips'=>"",'track'=>1,"trackTitle"=>"Track my package",'available'=>1,//是否禁用
						'price'=>$currency." 0.00",'trackPrice'=>"+".$currency.$airmail,
						'single_price'=>0,'single_trackprice'=>$airmail,'base_price'=>0,'base_trackprice'=>$base_airmail,'true_trackprice'=>$airmail,
				),
				
				
				
				'standard'=>array(
						"id"=>2,"title"=>"standard",'selected'=>1,"day"=>"6-10 working days",
						'tips'=>"",'track'=>0,"trackTitle"=>"Track my package",'available'=>1,//是否禁用
						'price'=>$currency.$standard_price,'trackPrice'=>"+".$currency.$standard,
						'single_price'=>$standard_price,'single_trackprice'=>$standard,'base_price'=>$standard_price,'base_trackprice'=>0,'true_trackprice'=>0,
				),
				
				'register_standard'=>array(
						"id"=>5,"title"=>"standard",'selected'=>1,"day"=>"6-10 working days",
						'tips'=>"",'track'=>1,"trackTitle"=>"Track my package",'available'=>1,//是否禁用
						'price'=>$currency.$standard_price,'trackPrice'=>"+".$currency.$standard,
						'single_price'=>$standard_price,'single_trackprice'=>$standard,'base_price'=>$standard_price,'base_trackprice'=>$base_standard,'true_trackprice'=>$standard,
				),
				'express'=>array(
						"id"=>3,"title"=>"expedited",'selected'=>1,"day"=>"3-7 working days",
						'tips'=>"",'track'=>-1,"trackTitle"=>"",'available'=>1,//是否禁用
						'price'=>$currency.$express_price,'trackPrice'=>"",
						'single_price'=>$express_price,'single_trackprice'=>0,'base_price'=>$express_price,'base_trackprice'=>0,'true_trackprice'=>0,
				),
				
		);
		
		$return_shipping_data = array();
		switch ($selected_shipping_id){
			case 1:
				$return_shipping_data = array(
						$data['airmail']//,$data['standard'],$data['express']
				);
				break;
			case 4:
				$return_shipping_data = array(
					$data['register_airmail']//,$data['standard'],$data['express']
				);
				break;
			case 2:
				$return_shipping_data = array(
						$data['standard']//,$data['express']
				);
				break;
			case 5:
				$return_shipping_data = array(
						$data['register_standard']//,$data['express']
				);
				break;
			case 3:
				$return_shipping_data = array(
						/*$data['register_airmail'],$data['register_standard'],*/$data['express']
				);
				break;
			case 6:
				$return_shipping_data = array(
						$data['cnmail']
				);
				break;
			case 7:
				$return_shipping_data = array(
						$data['freesea']
				);
				break;
		}
		//echo "<pre>bdfdfdf";print_r($return_shipping_data);die;
		return $return_shipping_data;
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
					$price = $express_zone_price_first+ceil( ( $weight*10000 - 0.5*10000 ) / 5000 ) * $express_zone_price_step_20;
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
	 * @desc 计算商品：重量，体积，长度,敏感类型值数组（非海外仓库商品需要计算）
	 * @param unknown $cart_list 商品列表数据
	 * @return multitype:multitype: number Ambigous <number, mixed>
	 */
	private function computeWeightInfo($cart_list,$address_warehouse){
		$result = array();
		$total_weight = 0;
		$total_volume = 0;
		$total_length = 0;
		$sensitive_type_array = array();//所有商品敏感值
		$price_weight = 0;//用于快递运费的价格计算
		$volume_weight = 0;//体积重
		$subtotal_price = 0;//当前汇率，总价（满减前）
		$base_subtotal_price = 0;//当前汇率，总价（满减前）
		$single_max_weight = 0;
	
		global $warehouse_range_array;
		if(!empty($cart_list)){
			foreach ($cart_list as $key=>$val){
				//echo "<pre>dddd";print_r($val);die;
				if(!isset($val['product_sku_info']) || empty($val['product_sku_info'])) {
					$this->sku_status = false;
					continue;
				}
				$weight = $val['product_sku_info']['product_sku_weight'];
				$width = $val['product_sku_info']['product_sku_width'];
				$height = $val['product_sku_info']['product_sku_height'];
				$length = $val['product_sku_info']['product_sku_length'];
				$quantity = $val['order_product_quantity'];
				$sentitve_type = $val['product_sku_info']['product_sku_sensitive_type'];
	
				$subtotal_price += $quantity*$val['order_product_price'];
	
				if(!in_array($val['product_sku_info']['product_sku_warehouse'], $warehouse_range_array)){
					if( !empty($address_warehouse) &&$val['product_sku_info']['product_sku_warehouse'] == $address_warehouse) continue;
					$total_weight += $quantity*$weight;
					$total_volume += $width*$height*$length*$quantity*200;
					$total_length = max($total_length,$width,$height,$length);
					if($sentitve_type>1) array_push($sensitive_type_array, $sentitve_type);
					//$volume_weight += $total_volume;
					$single_max_weight = max($single_max_weight,$weight);
						
					//计算，每个商品的最大体积重
					//$single_max_price_weight = max($weight,$width*$height*$length*200);
				}
	
	
			}
				
			$price_weight = max($total_volume,$total_weight);
		}
		$result = array(
				'weight'=>$total_weight,
				'volume'=>$total_volume,
				'length'=>$total_length,
				'sentitve_range'=>$sensitive_type_array,
				'price_weight'=>$price_weight,
				'subtotal_price'=>round($subtotal_price,2),//满减前，当前汇率，总价
				'single_max_weight'=>$single_max_weight,
		);
	
		return $result;
	
	}
	
	/**
	 * 检查是否可以重新支付
	 * @param  boolean $orderId 订单的id
	 */
	public function checkRepayAvailable($orderId = false) {
		//订单id为空
		if($orderId === false) { HelpOther::returnJson(array(), '', 85012); }
	
		//订单id强制转化
		$orderId = intval($orderId);
	
		//订单相关数据库操作实例
		$this->OrderModel = new OrderModel();
	
		//取出订单的信息
		$order = $this->OrderModel->getOrder($orderId);
	
		//取出订单的商品信息
		$goodslList = $this->OrderModel->getOrderGoodsListLatest($orderId);
	
		//格式化商品信息
		$skuOrderGoodsInfo = $this->_formatOrderGoodsSkuInfo($goodslList);
	
		//校验商品的信息（上下架状态，库存，购买的数量，商品是否违法）
		$this->ProductModel = new ProductModel();
	
		//购物车加载
		$this->CartModel = new CartModel();
		// $productInfo = $this->ProductModel->getLatestPromotionByInfo($skuOrderGoodsInfo, $this->m_app->currentLanguageId());
		//检测商品的库存和上下加状态促销信息
		$productInfo = $this->CartModel->checkStockAndStatus($skuOrderGoodsInfo, $this->m_app->currentLanguageId() , 3 );
	
		//判断不能支付就返回购物车
		if(!$this->_checkRepay($order,$goodslList,$productInfo)) { HelpOther::returnJson(array(), '', 85013); }
	
		HelpOther::returnJson(array(), '', 0);
	}

}

/* End of file repay.php */
/* Location: ./application/controllers/default/repay.php */