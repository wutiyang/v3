<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';
/**
 * paypay ec支付跳转处理
 */
class Paypal_ec_payment extends Dcontroller {
	private $sku_status = true;

	public function __construct(){
		parent::__construct();
		//加载多语言
		$this->load->language('place_order',currentLanguageCode());
	}

	/**
	 * 跳转到paypal支付
	 */
	public function redirectToPaypal() {
		//加载paypaec支付扩展
		$this->load->library('Payment/paypal_ec');
		//判断是不是可以支付
		if(!$this->paypal_ec->checkPaymentAvailable()) { redirect(genURL('cart')); }

		//set_cookie('currency','EUR',864000);//测试用下
		//没有登录用户，登录用户的购物车
		$cart_data = $this->getCart();
		$price = $this->computeCartTotalPrice($cart_data);
		
		if($this->sku_status===false) redirect(genURL("cart"));//存在下架sku商品
		
		//当前货币为BRL或INR时
		$currency = currentCurrency();
		if(in_array($currency,array('BRL' , 'INR'))){
			$currency = DEFAULT_CURRENCY;
			$currency_format = $this->getCurrencyNumber();
			$price = round($price/$currency_format['currency_rate'],2);
		}
		
		//初始化参数
		$params = array();
		$params['L_NAME0'] = 'Total';
		$params['L_AMT0'] = $price;
		$params['L_QTY0'] = '1';
		$params['ITEMAMT'] = $price;
		$params['AMT'] = $price;
		$params['SHIPPINGAMT'] = 0.00;
		$params['TAXAMT'] = 0.00;
		$params['LOCALECODE'] = $this->paypal_ec->getPaypalECLocaleCode();

		$params['PAYMENTACTION'] = 'Sale';
		$params['ReturnUrl'] = $this->customer->checkUserLogin()?genURL('place_order/paypal_ec_logined'):genURL('place_order/paypal_ec');
		$params['CANCELURL'] = genURL('cart');
		$params['CURRENCYCODE'] = currentCurrency();
		
		$params['ButtonSource'] = 'EACHBUYER_cart_EC_C2';
		//设置Paypal ec请求 第一次交互
		$response = $this->paypal_ec->callPaypalRequest('SetExpressCheckout', $params);
		//echo "<pre>";print_r($response);die;
		if(isset($response['ACK']) && isset($response['TOKEN']) && $response['ACK'] == 'Success') {
			//设置paypalec的token
			$this->session->set('paypal_ec_token',$response['TOKEN']);
			//paypalec地址跳转
			redirect($this->paypal_ec->getPaypalECUrl($response['TOKEN']));
		} else {
			redirect(genURL('cart'));
		}
	}
	
	/**
	 * @desc 获取购物车商品信息
	 */
	private function getCart(){
		$this->load->model("cartmodel","cart");
		if($this->customer->checkUserLogin()){
			$user_id = $this->session->get("user_id");
			$cart_list = $this->cart->cartListWithLoginUser($user_id);
		}else{
			//没登录用户购物车数据
			$session_id = $this->session->sessionID();
			$cart_list = $this->cart->cartListWithSessionid($session_id);
		}
		//扩展价格
		$cart_data = $this->productInfoWithCartinfo($cart_list);
		
		//获取sku对应属性信息(改成多个sku合并查询)
		$all_sku_array = array();
		foreach ($cart_data as $k=>$v){
			array_push($all_sku_array, $v['product_sku']);
		}
		$all_sku_infos = $this->batchAttrAndValueWithSkuArray($all_sku_array);
		
		foreach($cart_data as $key=>&$val){
			//加上自己属性值内容
			$sku = $val['product_sku'];
			if(array_key_exists($sku, $all_sku_infos)){
				foreach ($all_sku_infos[$sku] as $attr_key=>$attr_val){
					$val['attr'][$attr_key]['name'] = $attr_val['attr_name'];
					$val['attr'][$attr_key]['value'] = $attr_val['attr_value_name'];
				}
			}else{
				$val['attr'] = "";
			}
		}
		
		return $cart_data;
	}
	
	/**
	 * @desc 计算购物车中商品价格（当前汇率下的价格）
	 * @param unknown $cart_list
	 * @return number
	 */
	public function computeCartTotalPrice($cart_list){
		$price = 0;
		if(empty($cart_list)) return $price;
		foreach ($cart_list as $k=>$v){
			$qunatity = $v['product_quantity'];
			$product_price = $v['product_info']['product_discount_price'];//当前货币下价格
			$price += $qunatity*$product_price;
		}
		return $price;
	}
	
	/**
	 * @desc 根据商品id获取该商品信息及价格
	 * @param unknown $cart_data(二维数组)
	 */
	private function productInfoWithCartinfo_bak($cart_data,$need_extend_price = 1){
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
		}
	
		return $cart_data;
	}
	
	private function productInfoWithCartinfo($cart_data){
		$result = array();
		if(empty($cart_data)) return $result;
	
		$this->load->model("goodsmodel","product");
		$language_code = currentLanguageCode();

		$all_pids = $all_product_infos = array();
		$sku_array = array();
		foreach ($cart_data as $k=>$v){
			array_push($all_pids, $v['product_id']);
			array_push($sku_array, $v['product_sku']);
		}
		
		//获取购物车中所有商品详情
		$all_product_infos = $this->product->getAllInfoWithArray($all_pids,$language_code);
		//获取商品仓库,长，宽，高等信息
		$this->load->model("attributeproductmodel","productsku");
		$all_sku_infos = $this->productsku->batchProductSkuInfoWithSkuArray($sku_array);
		//把所有的信息综合计算
		foreach ($cart_data as $last_key=>&$last_val){
			$sku = $last_val['product_sku'];
			$pid = $last_val['product_id'];
			//详情
			if(isset($all_product_infos[$pid]))$last_val['old_product_info'] = $all_product_infos[$pid];
			//sku信息
			if(isset($all_sku_infos[$sku]) && !empty($all_sku_infos[$sku])){
				$last_val['product_sku_info'] = $all_sku_infos[$sku];
			}else{
				$this->sku_status = false;
			}
		}
		
		//批量获取商品扩展价格
		$extend_all_product_infos = array();
		//foreach ($all_product_infos as $extend_key=>$extend_val){
		foreach ($cart_data as $extend_key=>$extend_val){
			$extend_pid = $extend_val['product_id'];
			$extend_p_sku = $extend_val['product_sku'];
			//无sku信息，说明sku下架
			if($this->sku_status ==false && !isset($extend_val['product_sku'])) redirect(genURL('cart'));
			//对应sku价格
			$extend_all_product_infos[$extend_p_sku] = $this->singleProductWithPrice($extend_val['old_product_info'],$extend_val['product_sku_info']);//扩展价格
		}
		foreach ($cart_data as $cart_key=>&$cart_val){
			$cart_pid = $cart_val['product_id'];
			$cart_sku = $cart_val['product_sku'];
			if(isset($extend_all_product_infos[$cart_sku])){
				$cart_val['product_info'] = $extend_all_product_infos[$cart_sku];
			}
			unset($cart_data[$cart_key]['old_product_info']);
		}
			
		return $cart_data;
	}
	
}

/* End of file paypal_ec_payment.php */
/* Location: ./application/controllers/default/paypal_ec_payment.php */