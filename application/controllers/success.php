<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';
class Success extends Dcontroller {
	
	public function index(){
		//获取订单号
		//$this->session->set('order_code','2015092300000843');
		$orderSn = $this->session->get('order_code');
		$this->_view_data['order_code'] = $orderSn;//订单号
		
		//订单号为空的时候返回购物车
		if($orderSn === false) { redirect(genURL('cart')); }
		
		//取出订单的信息
		$this->load->model('ordermodel','ordermodel');
		$order_info = $this->ordermodel->getOrderWithCode($orderSn);
		$this->_view_data['order_price'] = $order_info['order_price'];//支付金额（币种）
		$this->_view_data['order_id'] = $order_info['order_id'];//订单id
		//汇率code,ex:USD
		$order_currency = $this->getCurrencyWithCode($order_info['order_currency']);
		$this->_view_data['order_currency'] = isset($order_currency['currency_format'])?str_replace('%s', '', $order_currency['currency_format']):'$';
		
		//获取右侧购买下单商品
		$order_product_list = $this->ordermodel->getProductListByOrderId($order_info['order_id']);
		//获取商品url
		$product_url_infos = $this->getAllProductBaseInfos($order_product_list);
		foreach ($order_product_list as $p_k=>&$p_v){
			$pid = $p_v['product_id'];
			if(array_key_exists($pid, $product_url_infos)){
				$p_v['product_url'] = $product_url_infos[$pid]['product_url'];
			}else{
				$p_v['product_url'] = '';
			}
		}
		$this->_view_data['order_product_list'] = $order_product_list;

		//支付的方式
		$payment_type = false;//默认非bank支付
		if($order_info['payment_id']==3){//bank支付
			$payment_type = true;
		}else{
			//paypal ec 是否新账户（新账户时，账户信息）
			$paypalEcCreateUser = $this->session->get('paypal_ec_create_user');
			if($paypalEcCreateUser !== false && is_array($paypalEcCreateUser)) {
				$this->_view_data['paypal_ec_create_user'] = $paypalEcCreateUser;
				$this->session->delete('paypal_ec_create_user');
			}
		}

		$this->_view_data['pay_status'] = $order_info['order_status'];
		$this->_view_data['order_info'] = $order_info;
		$this->_view_data['payment_type'] = $payment_type;
		
		//affiliate(网盟)
		$this->_processAffiliateInfo($order_info,$order_product_list);
		//GA统计(google_tag_params)
		//$this->_getGoogleTagParams($cart_data);
		//GA统计-datalayers
		$this->_getGaDatalayers($order_info,$order_product_list);

		//ad
		$this->load->model("imageadmodel","imagead");
		$image_ads = $this->imagead->getLocationWithIds(4);
		$language_id = currentLanguageId();
		$ad_list = array();
		if (isset($image_ads[4])){//是否存在焦点图-右下
			foreach($image_ads[4] as $kt=>$vt){
				$three_start_time = strtotime($vt['ad_time_start']);
				$three_end_time = strtotime($vt['ad_time_end']);
					
				if($three_start_time<=time() && $three_end_time>=time()){
					$tcontent = json_decode($vt['ad_content'],true);
					$vt['lan_content'] = $tcontent[$language_id];
					$ad_list[4]= $vt;
					break;
				}
			}
		}
		$this->_view_data['ad_list'] = $ad_list;
		
		//黑名单用户
		$this->_view_data['black_customer'] = 0;//默认非黑名单用户
		if($order_info['order_status'] != OD_PAID){
			$this->load->model('blacklistmodel','blacklist');
			$black_list_num = $this->blacklist->genInfo($order_info['customer_id'],$order_info['order_email']);
			if(!empty($black_list_num)) $this->_view_data['black_customer'] = 1;
		}
		parent::index();
		$this->session->delete('order_code');
	}
	
	//对多个商品查询基本信息
	private function getAllProductBaseInfos($order_product_list){
		$result = $all_product_ids = array();
		foreach ($order_product_list as $all_key=>$all_val){
			array_push($all_product_ids, $all_val['product_id']);
		}
		$this->load->model('goodsmodel','productmodel');
		if(!empty($all_product_ids)) $result = $this->productmodel->getBaseInfoWithArray($all_product_ids);
		
		return $result;
	}
	
	private function _getGoogleTagParams(){
		
	}
	
	private function _getGaDatalayers($order_info,$order_product_list){
		$this->ga_dataLayer = array();
		$this->ga_dataLayer['new_customer'] = 0;
		$this->ga_dataLayer['ordervalue'] = $order_info['order_baseprice'];
		$this->ga_dataLayer['orderId'] = $order_info['order_id'];
		//新增ga代码
		$this->ga_dataLayer['currency'] = $order_info['order_currency'];
		$this->ga_dataLayer['ordervalue_origin'] = $order_info['order_price'];
		
		$k = 1;
		$productIds = array();
		$prices = array();
		$quantities = array();
		//新增ga代码
		$origin_price = array();
		foreach ($order_product_list as $key=>$val){
			if($k<4){
				$pid = 'productId'.$k;
				$price = 'price'.$k;
				$quantity = 'quantity'.$k;
				$this->ga_dataLayer[$pid] = $val['product_id'];
				$this->ga_dataLayer[$price] = $val['order_product_baseprice'];
				$this->ga_dataLayer[$quantity] = $val['order_product_quantity'];
				array_push($productIds,$val['product_id']);
				array_push($prices, $val['order_product_baseprice']);
				array_push($quantities, $val['order_product_quantity']);
			}
			//新增ga代码
			array_push($origin_price, $val['order_product_price']);
			$k++;
		}
		$this->ga_dataLayer['productIds'] = implode('|', $productIds);
		$this->ga_dataLayer['prices'] = implode('|', $prices);
		$this->ga_dataLayer['quantities'] = implode('|', $quantities);
		//新增ga代码
		$this->ga_dataLayer['prices_origin'] = implode('|', $origin_price);
		//echo "<pre>sdfgh";print_r($this->ga_dataLayer);die;
		$this->_view_data['ga_dataLayer'] = json_encode($this->ga_dataLayer);
	}
	
	//获取网盟cookie等数据信息
	private function _getAffiliate(){
		$affiliate = $this->input->cookie('eb_smclog');
		$affiliate_source = '';
		$affiliate_campaign = 0;
		if($affiliate !== false){
			parse_str(stripcslashes($affiliate),$affiliate);
			if(in_array(id2name('m',$affiliate),array('NetworkAffiliates','aff','mediaffiliation'))){
				$affiliate_source = id2name('s',$affiliate);
				$affiliate_campaign = id2name('c',$affiliate,0);
			}
		}
		$this->_view_data['affiliate_source'] = $affiliate_source;
		$this->_view_data['affiliate_campaign'] = $affiliate_campaign;
		return $affiliate;
	}	
	
	//专门处理网盟（从原网站copy代码，进行修改）
	protected function _processAffiliateInfo($order,$goodsList){
		$affiliate = $this->_getAffiliate();
		//去除运费$价
		$order_price_without_shipping = round(($order['order_baseprice']*100 - $order['order_baseprice_shipping']*100 - $order['order_baseprice_insurance']*100)/100,2);
		//转换为eur汇率
		$order_price_without_shipping_eur = exchangePrice($order_price_without_shipping,'EUR');
		//折扣+rewards价
		$order_discount_n_integral = $order['order_baseprice_discount'] + $order['order_baseprice_rewards'];
		$affiliate_source_params = array(
				'wguk' => array('wgProgramID'=>8227,'wgEventID'=>13233,'wgCurrency'=>'GBP'),
				'wgde' => array('wgProgramID'=>8239,'wgEventID'=>13247,'wgCurrency'=>'EUR'),
				'wgfr' => array('wgProgramID'=>8233,'wgEventID'=>13273,'wgCurrency'=>'EUR'),
				'wges' => array('wgProgramID'=>8241,'wgEventID'=>13275,'wgCurrency'=>'EUR'),
				'wgit' => array('wgProgramID'=>8235,'wgEventID'=>13271,'wgCurrency'=>'EUR'),
		);
	
		$webgains_eventid = 0;
		$webgains_programid = 0;
		$webgains_wgcurrency = 'GBP';
		$affiliate_source = id2name('s',$affiliate);
		if(in_array( $affiliate_source, array('wgde','wguk','wgfr','wges','wgit') )) {
			$webgains = id2name($affiliate_source,$affiliate_source_params,array());
			$webgains_eventid = id2name('wgEventID',$webgains,13233);
			$webgains_programid = id2name('wgProgramID',$webgains,8227);
			$webgains_wgcurrency = id2name('wgCurrency',$webgains,'GBP');
		}
		$webgains_wgOrderValue = exchangePrice($order_price_without_shipping,$webgains_wgcurrency);
	
		$webgains_items = array();
		$this->load->model('goodsmodel','goodsmodel');
		foreach($goodsList as $key => $record){
			$webgains_items[] = $webgains_eventid.'::'. exchangePrice($record['order_product_baseprice'],$webgains_wgcurrency) . '::' . preg_replace('/[\'|\"|\/]/','',$record['order_product_name']) . '::' . $record ['product_id'] . '::' . $order['order_currency'];
		}
		
		$discount_str = '';
		if($order_discount_n_integral > 0){
			$discount_str = '|'.$webgains_eventid.'::-'.$order_discount_n_integral.'::'.$order['order_currency'].'::1::'.$order['order_currency'];
		}
		
		$this->_view_data['webgains_eventid'] = $webgains_eventid;
		$this->_view_data['webgains_programid'] = $webgains_programid;
		$this->_view_data['webgains_wgcurrency'] = $webgains_wgcurrency;
		$this->_view_data['webgains_wgOrderValue'] = $webgains_wgOrderValue;
		$this->_view_data['webgains_items'] = implode('|',$webgains_items).$discount_str;

		$webgains_cj_goods = array();
		$vizury_goods = array();
		$vizury_goods['pid1'] = '';
		$vizury_goods['price1'] = '';
		$vizury_goods['quantity1'] = '';
		$vizury_goods['pid2'] = '';
		$vizury_goods['price2'] = '';
		$vizury_goods['quantity2'] = '';
		$vizury_goods['pid3'] = '';
		$vizury_goods['price3'] = '';
		$vizury_goods['quantity3'] = '';
		$webgains_sku_list = array();
		$webgains_price_list = array();
		$webgains_quantity_list = array();
		foreach($goodsList as $key => $record){
			if($key < 3){
				$webgains_cj_goods['ITEM'.($key+1)] = $record['product_id'];
				$webgains_cj_goods['AMT'.($key+1)] = $record['order_product_price'];
				$webgains_cj_goods['QTY'.($key+1)] = $record['order_product_quantity'];
				
				$vizury_goods['pid'.($key+1)] = $record['product_id'];
				$vizury_goods['price'.($key+1)] = $record['order_product_price'];
				$vizury_goods['quantity'.($key+1)] = $record['order_product_quantity'];
			}
			$webgains_sku_list[] = $record['product_id'];
			$webgains_price_list[] = $record['order_product_price'];
			$webgains_quantity_list[] = $record['order_product_quantity'];
		}
		$this->_view_data['vizury_goods_str'] = http_build_query($vizury_goods);
		$this->_view_data['webgains_cj_goods_str'] = http_build_query($webgains_cj_goods);
		$this->_view_data['webgains_sku_str'] = implode(',',$webgains_sku_list);
		$this->_view_data['webgains_price_str'] = implode(',',$webgains_price_list);
		$this->_view_data['webgains_quantity_str'] = implode(',',$webgains_quantity_list);
		$this->_view_data['order_price_without_shipping_eur'] = $order_price_without_shipping_eur;
	}
	
}