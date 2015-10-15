<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';
/**
 * 返回未支付成功页面
 */
class Unpaid extends Dcontroller {
	/**
	 * 未支付成功页面入口
	 */
	public function index(){
		//获取订单号
		$orderSn = $this->session->get('order_code');
		//订单号为空的时候返回购物车
		if($orderSn === false) { redirect(genURL('cart')); }

		//取出订单的信息
		$order = '';
		//订单的信息为空的时候返回购物车

		//获取支付的方式
		global $payment_list;

		//支付方式不存在就返回购物车
		if(!isset($payment_list[$order['pay_id']])) { redirect(genURL('cart')); }

		//取出订单的商品

		
		$template = '';
		//bank payment
		if (in_array($order['pay_id'], array( 26, 28, 36, 39, 40 , 41, 42, 43, 44) )){
			$template = 'pending';
			
			$orderStatus = '';//adyen回到网站时，支付可能就已支付成功了
			switch( $order['pay_status'] ){
				case 0 : $orderStatus = lang('unpaid');	break;
				case 1 : $orderStatus = lang('pending');break;
				case 2 : $orderStatus = lang('paid');break;
				default: $orderStatus = lang('unpaid');	break;
			}
			$orderStatus = sprintf( lang('pending_status'), $orderStatus);
			$this->_view_data['orderStatus'] = $orderStatus;
		}
		// todo  GA @jinliang
		//render page
		parent::index( $template );
		//$this->session->delete('order_sn');
	}

	/**
	 * 订阅处理
	 */
	protected function _processSubscribe(){
		//表示是否订阅
		$flgIsSubscribed = false;
		//标记是否自动订阅
		$flgAutoSubscribe = false;

		//获取用户的订阅信息
		$subscribeInfo = $this->UserModel->getEmailSubscribeInfo($this->m_app->getCurrentUserEmail());

		//判断是都需要自动订阅
		if(!empty($subscribeInfo) && $subscribeInfo['subscribe_display'] != 1) { $flgAutoSubscribe = true; }

		//订阅邮件的信息处理
		if(empty($subscribeInfo)) {
			//设置coupon
			$couponCode = substr(md5($_SERVER['REQUEST_TIME']),1,10);
			//创建订阅邮件
			$this->UserModel->createEmailSubscribe(array(
				'stat' => 1,
				'is_pointed' => 1,
				'email' => $this->m_app->getCurrentUserEmail(),
				'ip' => $this->input->ip_address(),
				'hash' => $couponCode,
				'source' => NEWSLETTER_SUBSCRIBE_SOURCE_ORDER_SUCCESS_AUTO,
				'language' => $this->m_app->currentLanguageCode(),
				'create_time' => $_SERVER['REQUEST_TIME'],
			));
			//给用户添加订阅积分
			$this->_addPoint();
			//订阅邮件发送
			$this->m_email->sendMail('subscribe_pointed_success',$this->m_app->currentLanguageCode(),$this->m_app->getCurrentUserEmail(),array(
				'point_rule_url' => eb_gen_url('point_rules').'?utm_source=System_Own&utm_medium=Email&utm_campaign=subscribe_pointed_success&utm_nooverride=1',
				'user_name' => $this->m_app->getCurrentUserName(),
				'code' => $couponCode,
			));
			$flgIsSubscribed = true;
			$flgAutoSubscribe = true;
		} elseif($subscribeInfo['stat'] == 0 && $subscribeInfo['is_unsubscribe'] == 0) {
			//更新用户的订阅邮件信息
			$this->UserModel->updateEmailSubscribe($this->m_app->getCurrentUserEmail(), array(
				'stat' => 1,
				'is_pointed' => 1,
				'is_unsubscribe' => 0,
				'count' => $subscribeInfo['count']+1,
				'source' => NEWSLETTER_SUBSCRIBE_SOURCE_ORDER_SUCCESS_AUTO,
				'update_time' => $_SERVER['REQUEST_TIME'],
			));
			//用户的订阅积分的添加
			if($subscribeInfo['is_pointed'] != 1) {
				$this->_addPoint();
				//订阅邮件发送
				$this->m_email->sendMail('subscribe_pointed_success',$this->m_app->currentLanguageCode(),$this->m_app->getCurrentUserEmail(),array(
					'point_rule_url' => eb_gen_url('point_rules').'?utm_source=System_Own&utm_medium=Email&utm_campaign=subscribe_pointed_success&utm_nooverride=1',
					'user_name' => $this->m_app->getCurrentUserName(),
					'code' => $subscribeInfo['hash'],
				));
			} else {
				//订阅邮件发送
				$this->m_email->sendMail('subscribe_pointed_fail',$this->m_app->currentLanguageCode(),$this->m_app->getCurrentUserEmail(),array(
					'user_name' => $this->m_app->getCurrentUserName(),
				));
			}
			$flgIsSubscribed = true;
			$flgAutoSubscribe = true;
		} elseif($subscribeInfo['is_unsubscribe'] != 1) {
			$flgIsSubscribed = true;
		}

		if($flgAutoSubscribe) {
			$this->UserModel->updateEmailSubscribe($this->m_app->getCurrentUserEmail(), array(
				'update_time' => $_SERVER['REQUEST_TIME'],
				'subscribe_display' => 1,
			));
		}

		$this->_view_data['flg_is_subscribed'] = $flgIsSubscribed;
		$this->_view_data['flg_auto_subscribe'] = $flgAutoSubscribe;
	}

	protected function _processGAInfo($order,$goodsList,$affiliate){
		$order_price_without_shipping = $order['base_order_amount'] - $order['base_shipping_fee'];
		$order_price_without_shipping_eur = exchangePrice($order_price_without_shipping,'EUR');
		$order_discount_n_integral = $order['base_discount'] + $order['base_integral_money'];
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

		$webgains_sku_list = array();
		$webgains_price_list = array();
		$webgains_quantity_list = array();
		$webgains_cj_goods = array();
		$vizury_goods = array();
		$webgains_items = array();
		$datalayer_productId = array();
		$datalayer_price = array();
		$datalayer_quantity = array();
		$affiliate_log = array();
		$arr_other = array (
			'productIds' => array(),
			'prices' => array(),
			'quantities' => array(),
		);
		$key = 0;

		// 循环商品取出商品的pid
		$orderGooodsIds = array();
		if(is_array($goodsList) && count($goodsList) > 0) {
			foreach ($goodsList as $key => $value) {
				$orderGooodsIds[] = (int)$value['product_id'];
			}
		}

		// 通过商品id取出商品的一级分类id
		if(!empty($orderGooodsIds)) {
			$orderGoodsCategoryIdLevel1 = $this->OrderModel->getOrderCategoryIdLevel1($orderGooodsIds);
		}

		$vizury_goods = array();
		$vizury_goods['pid1'] = '';
		$vizury_goods['price1'] = '';
		$vizury_goods['quantity1'] = '';
		$vizury_goods['catid1'] = '';
		$vizury_goods['pid2'] = '';
		$vizury_goods['price2'] = '';
		$vizury_goods['quantity2'] = '';
		$vizury_goods['catid2'] = '';
		$vizury_goods['pid3'] = '';
		$vizury_goods['price3'] = '';
		$vizury_goods['quantity3'] = '';
		$vizury_goods['catid3'] = '';
		foreach($goodsList as $key => $record){
			//$cats = explode('/',$record['id_path']);
			$cats = '';
			$webgains_sku_list[] = $record['product_id'];
			$webgains_price_list[] = $record['final_price'];
			$webgains_quantity_list[] = $record['goods_number'];

			$arr_other['productIds'][] = $record['product_id'];
			$arr_other['prices'][] = $record['final_price'];
			$arr_other['quantities'][] = $record['goods_number'];
			if($key < 3){
				$webgains_cj_goods['ITEM'.($key+1)] = $record['product_id'];
				$webgains_cj_goods['AMT'.($key+1)] = $record['final_price'];
				$webgains_cj_goods['QTY'.($key+1)] = $record['goods_number'];

				$vizury_goods['pid'.($key+1)] = $record['product_id'];
				$vizury_goods['price'.($key+1)] = $record['final_price'];
				$vizury_goods['quantity'.($key+1)] = $record['goods_number'];
				$vizury_goods['catid'.($key+1)] = isset($orderGoodsCategoryIdLevel1[$record['product_id']]) && !empty($orderGoodsCategoryIdLevel1[$record['product_id']]) ? (int)$orderGoodsCategoryIdLevel1[$record['product_id']]:'';

				$datalayer_productId['productId'.($key+1)] = '\'' . $record['product_id'] . '\'';
				$datalayer_price['price'.($key+1)] = $record['final_price'];
				$datalayer_quantity['quantity'.($key+1)] = $record['goods_number'];
			}
			$webgains_items[] = $webgains_eventid.'::'. exchangePrice($record['final_price'],$webgains_wgcurrency) . '::' . preg_replace('/[\'|\"|\/]/','',$record['goods_name']) . '::' . $record ['product_id'] . '::' . $order['currency_code'];
			if($affiliate !== false){
				if(id2name('s',$affiliate) == 'mediaffiliation'){
					$price = $order_price_without_shipping_eur;
				}else{
					$price = $order_price_without_shipping;
				}
				$affiliate_log[] = array(
					'utm_source' => addslashes(id2name('s',$affiliate)),
					'publisher_id' => addslashes(id2name('c',$affiliate)),
					'order_id' => $order['order_id'],
					'order_sn' => $order['order_sn'],
					'money' => $price,
					'order_lang' => $order['language_code'],
					'sku'=> $record['product_id'],
					'goods_name' => addslashes($record['goods_name']),
					'price' => $record['final_price'],
					'amount' => $record['goods_number'],
					'top_cat' => id2name(0,$cats,0),
					'se_cat' => id2name(1,$cats,0),
					'th_cat' => id2name(2,$cats,0),
					'submit_time' => $order['add_time'],
				);
			}
			$key++;
		}
		if(!empty($affiliate_log)) { $this->OrderModel->createAffiliateLogBatch($affiliate_log); }

		$discount_str = '';
		if($order_discount_n_integral > 0){
			$discount_str = '|'.$webgains_eventid.'::-'.$order_discount_n_integral.'::'.$order['currency_code'].'::1::'.$order['currency_code'];
		}

		$arr_other['productIds'] = empty($arr_other['productIds'])?'':'\''.implode('|',$arr_other['productIds']).'\'';
		$arr_other['prices'] = empty($arr_other['prices'])?'':'\''.implode('|',$arr_other['prices']).'\'';
		$arr_other['quantities'] = empty($arr_other['quantities'])?'':'\''.implode('|',$arr_other['quantities']).'\'';

		$this->_view_data['datalayer_goods_info'] = array_merge($datalayer_productId,$datalayer_price,$datalayer_quantity,$arr_other);
		$this->_view_data['datalayer_order_sn'] = $order['order_sn'];
		$this->_view_data['datalayer_new_customer'] = $this->OrderModel->getUserHasOrder($this->m_app->getCurrentUserId(),time()-86400)?0:1;
		$this->_view_data['webgains_sku_str'] = implode(',',$webgains_sku_list);
		$this->_view_data['webgains_price_str'] = implode(',',$webgains_price_list);
		$this->_view_data['webgains_quantity_str'] = implode(',',$webgains_quantity_list);
		$this->_view_data['webgains_cj_goods_str'] = http_build_query($webgains_cj_goods);
		// print_r($this->_view_data['webgains_cj_goods_str']);
		$this->_view_data['webgains_eventid'] = $webgains_eventid;
		$this->_view_data['webgains_programid'] = $webgains_programid;
		$this->_view_data['webgains_wgcurrency'] = $webgains_wgcurrency;
		$this->_view_data['webgains_wgOrderValue'] = $webgains_wgOrderValue;
		$this->_view_data['webgains_items'] = implode('|',$webgains_items).$discount_str;

		$vizury_goods_str = empty($vizury_goods) ? '':http_build_query($vizury_goods);
		$this->_view_data['vizury_goods_str'] = $vizury_goods_str;
		$this->_view_data['order_discount_n_integral'] = $order_discount_n_integral;
		$this->_view_data['order_price_without_shipping'] = $order_price_without_shipping;
		$this->_view_data['order_price_without_shipping_eur'] = $order_price_without_shipping_eur;
	}

	/**
	 * 添加用户积分
	 */
	protected function _addPoint() {
		//用户订阅积分
		$subscribePoint = 10;
		//给用户添加订阅积分
		$this->UserModel->addPoint($this->m_app->getCurrentUserId(), array(
			'active' => 'active + '.$subscribePoint,
			'total' => 'total + '.$subscribePoint,
		));
		//添加用户订阅积分的日志
		$this->UserModel->createPointLog(array(
			'customer_id' => $this->m_app->getCurrentUserId(),
			'store_id' => $this->m_app->currentLanguageId(),
			'type' => -6,
			'status' => 'active',
			'action' => '+'.$subscribePoint,
			'ref_id' => '',
			'updated_at' => NOW,
		));
		//删除用户的积分session
		$this->session->delete('user_point');
	}

	/*
	 * chang user type (New customer and Return customer)
	 * New customer : 1
	 * Return customer : 0
	 */
	protected function _changUserType($orderId) {
		//检查用户的类型
		$userType = $this->OrderModel->getUserHasOrder($this->m_app->getCurrentUserId(),time()-86400)?0:1;

		if($userType == 1) {
			$info = array('user_type'=>'New customer');
		} else {
			$info = array('user_type'=>'Return customer');
		}

		//更新用户的类型
		$this->UserModel->editUser($this->m_app->getCurrentUserId(), $info);

		//更新订单中的用户类型
		$this->OrderModel->updateOrder($orderId, $info);
	}

	/**
	 * GA 统计
	 * @param array $cat goods
	 */
	protected function _addGAInfo( $catGoods , $order) {
		//print_r($catGoods);
		if( count( $catGoods ) > 0 ) {
			$catGoods = array_splice($catGoods , 0, 5);
			$prodidArray = array();
			$prodNameArray = array();
			$prodSku = array();
			$categoryNameStrArray = array();
			$categroyName = array();
			$prodidStr = '';
			$goodsName = '';
			$categroyNameStr = '';
			foreach ( $catGoods as $key => $value ) {
				$prodidArray[] = 'eachbuyer_'. Appmodel::$gaEcommProdid[ strtolower( $order['language_code'] ) ] .'_'.$value['product_id'].'_'.$order['language_code'];
				$prodNameArray[] = eb_htmlspecialchars($value['goods_name']);
				$prodSku[$value['product_id']] = '';
				//$categoryList = $this->CategoryModel->getParentsCategoryList($value['id_path'],1);
				//$categoryList = extractColumn($categoryList,'cat_name');
				//$categroyName[$value['cat_id']] = eb_htmlspecialchars(implode('_',array_reverse( $categoryList)));
			}

			if( count( $prodidArray ) > 1 ) {
				$prodidStr = "['".implode("','", $prodidArray)."']";
			} else {
				$prodidStr = "'".current($prodidArray)."'";
			}
			$this->_view_data['head']['ga_ecomm_prodid'] = $prodidStr;

			if( count( $prodNameArray ) > 1 ) {
				$goodsName = "['".implode("','", $prodNameArray)."']";
			} else {
				$goodsName = "'".current($prodNameArray)."'";
			}
			$this->_view_data['head']['ga_ecomm_pname'] = $goodsName;
			/*
			if( !empty( $prodSku ) ) {
				foreach ($prodSku as $key => $value) {
					$categoryNameStrArray[$key] = $categroyName[$value];
				}
			}

			if( count( $categoryNameStrArray ) > 1 ) {
				$categroyNameStr = "['".implode("','", $categoryNameStrArray)."']";
			} else {
				$categroyNameStr = "'".current($categoryNameStrArray)."'";
			}
			*/
			$this->_view_data['head']['ga_ecomm_pcat'] = '0';
		}

		$this->_view_data['head']['ga_ecomm_pagetype'] = "'purchase'";
		$this->_view_data['head']['ga_ecomm_pvalue'] = $order['base_goods_amount'];
		//print_r($this->_view_data['head']);
	}
}

/* End of file success.php */
/* Location: ./application/controllers/default/success.php */
