<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_controller {

	public function __construct(){
		parent::__construct();
		$this->load->helper(array('cookie','array','app','other'));
		$this->load->library(array('log','database'));
		$this->load->library('memcache');
	}

	public function index(){}
/*
| -------------------------------------------------------------------
|  MC->EB Data Sync
| -------------------------------------------------------------------
*/
	public function syncData(){
		@set_time_limit(0);
		@ini_set('memory_limit', '-1');
		$id = $this->input->post('id');
		$name = $this->input->post('name');
		$md5 = $this->input->post('md5');
		$size = $this->input->post('size');
		$time = $this->input->post('time');
		$signature = $this->input->post('signature');

		if($id === false) die('[ERROR]');
		if($name === false) die('[ERROR]');
		if($md5 === false) die('[ERROR]');
		if($size === false) die('[ERROR]');
		if($time === false) die('[ERROR]');
		if($signature === false) die('[ERROR]');

		$file = SYNC_PATH.'orig/'.$name;
		if(!file_exists($file)) die('[File Not Exists]');
		if(md5_file($file) != $md5) die('[File Damaged]');
		if(filesize($file) != $size) die('[File Damaged]');
		if(md5($md5.SALT) != $signature) die('[Wrong Signature]');

		$this->database->master->insert('eb_synclog',array(
			'synclog_reference' => $id,
			'synclog_file' => $name,
			'synclog_md5' => $md5,
			'synclog_size' => $size,
			'synclog_time_post' => $time,
			'synclog_time_process' => NOW,
		));

		$sandbox = SYNC_PATH.'sandbox/';
		exec("tar zxf $file -C $sandbox");
		$sandbox .= $id.'/';

		if(!is_dir($sandbox)) die('[Permission Error]');

		global $sync_map;
		$this->load->helper('directory');
		$file_list = directory_map($sandbox);

		foreach($file_list as $json){
			if(strpos($json,'.json') === false) continue;
			$type = str_replace('.json','',$json);
			if(!isset($sync_map[$type])) continue;

			$column_list = $sync_map[$type];

			$rows = array();
			$file = fopen($sandbox.$json,FOPEN_READ);
			while(!feof($file)){
				$line = fgets($file);
				if($line === false) continue;
				$line = json_decode($line,true);
				if(!is_array($line)) continue;

				$row = array();
				foreach($line as $item) $row[] = $this->database->master->escape($item);
				$rows[] = '('.implode(',',$row).')';

				if(count($rows) >= 100){
					$sql = 'replace into eb_'.$type.'('.implode(',',$column_list).') values'.implode(',',$rows);
					$this->database->master->query($sql);
					$rows = array();
				}
			}
			if(!empty($rows)){
				$sql = 'replace into eb_'.$type.'('.implode(',',$column_list).') values'.implode(',',$rows);
				$this->database->master->query($sql);
			}

			fclose($file);
		}
		exec("rm -rf $sandbox");

		$this->database->close();
		echo '[success]';
	}
/*
| -------------------------------------------------------------------
|  Download Order Package From Order Center
| -------------------------------------------------------------------
*/
	public function downLoadOrderPackage(){
		@set_time_limit(0);
		@ini_set('memory_limit', '-1');

		$this->database->master->select('crontab_threshold');
		$this->database->master->from('eb_crontab');
		$this->database->master->where('crontab_id',1);
		$this->database->master->where('crontab_status',STATUS_ACTIVE);
		$this->database->master->limit(1);
		$query = $this->database->master->get();
		$threshold = $query->row_array();

		if(empty($threshold)) die();

		$threshold = $threshold['crontab_threshold'];

		$this->database->master->set('crontab_status',STATUS_DISABLE);
		$this->database->master->where('crontab_id',1);
		$this->database->master->update('eb_crontab');

		$params = array(
			'user_code' => 'api_site',
			'user_password' => 'yS5pRyDBpc79aQxV5GQe',
			'start' => $threshold,
			'end' => NOW,
			'order_platform' => SITE_ID,
		);

		$page = 1;
		while(true){
			$params['page'] = $page;
			$this->load->library('curl');
			$res = $this->curl->simple_post('http://order.kairh.com/api/getOrderList',$params,array(CURLOPT_TIMEOUT=>60));
			if($res === false || $res == '') continue;
			$res = json_decode($res,true);
			if(!is_array($res)) continue;
			if(empty($res['items'])) break;

			$update_arr = array();
			$replace_arr = array();
			foreach($res['items'] as $order){
				if($order['order_time_lastmodified'] > $threshold) $threshold = $order['order_time_lastmodified'];

				$update_arr[] = array(
					'order_id' => $order['order_reference'],
					'shipping_id' => $order['order_shipping_id'],
					//'payment_id' => $order['order_payment_id'],
					'order_address_firstname' => $order['order_address_shipping_firstname'],
					'order_address_lastname' => $order['order_address_shipping_lastname'],
					'order_address_phone' => $order['order_address_shipping_phone'],
					'order_address_state' => $order['order_address_shipping_state'],
					'order_address_city' => $order['order_address_shipping_city'],
					'order_address_street' => $order['order_address_shipping_street'],
					'order_address_postalcode' => $order['order_address_shipping_postalcode'],
					'order_address_cpfcnpj' => $order['order_address_shipping_cpfcnpj'],
					'order_status' => $order['order_status'],
					'order_time_pay' => $order['order_time_pay'],
					'order_time_shipped' => $order['order_time_ship'],
					'order_time_lastmodified' => NOW,
				);
				foreach($order['package_list'] as $package){
					$replace_arr[] = '('.implode(',',array($package['id'],'\''.$order['order_reference'].'\'','\''.json_encode($package['product_list']).'\'','\''.$package['tracking_number'].'\'','\''.$package['time'].'\'')).')';
				}
			}
			if(!empty($replace_arr)){
				$sql = 'replace into eb_order_package(order_package_id,order_id,order_package_content,order_package_tracking_number,order_package_time_process) values'.implode(',',$replace_arr);
				$this->database->master->query($sql);
			}
			$this->database->master->update_batch('eb_order',$update_arr,'order_id');

			$page++;
		}

		$this->database->master->set('crontab_threshold',$threshold);
		$this->database->master->set('crontab_status',STATUS_ACTIVE);
		$this->database->master->where('crontab_id',1);
		$this->database->master->update('eb_crontab');
		echo "downLoadOrderPackage @".NOW."\n";
	}
/*
| -------------------------------------------------------------------
|  Order Sync API For Order Center
| -------------------------------------------------------------------
*/
	public function orderList(){
		$result = array(
			'items' => array(),
			'totalCount' => 0,
			'detail' => '',
			'success' => true,
		);
		$time = $this->input->post('time');
		$secret = $this->input->post('secret');
		$order_id = $this->input->post('order_id');
		$this->load->helper('array');
		$this->load->library('database');

		$base_string = date('YmdH') . '_$FUEqv00TkuCdVhwN1&_' . $time;
		if(md5($base_string) != $secret){
			$result['success'] = false;
			$result['detail'] = 'Permission Deny';
			echo json_encode($result);
			die();
		}

		$time = date('Y-m-d H:i:s',time()-300);

		$this->database->slave->select('order_id');
		$this->database->slave->from('eb_order');
		$this->database->slave->where('order_id >',$order_id);
		$this->database->slave->where('order_time_create <=',$time);
		$this->database->slave->order_by('order_id','desc');
		$this->database->slave->limit(1);
		$query = $this->database->slave->get();
		$max_order = $query->row_array();
		if(empty($max_order)) die();
		$max_order_id = $max_order['order_id'];

		$this->database->slave->from('eb_order');
		$this->database->slave->where('order_id >',$order_id);
		$this->database->slave->where('order_id <=',$max_order_id);
		$this->database->slave->order_by('order_id','asc');
		$query = $this->database->slave->get();
		$order_list = $query->result_array();

		$order_ids = extractColumn($order_list,'order_id');
		$order_ids[] = -1;
		$this->database->slave->from('eb_order_product');
		$this->database->slave->where_in('order_id',$order_ids);
		$this->database->slave->order_by('order_product_id','asc');
		$query = $this->database->slave->get();
		$order_product_list = $query->result_array();

		$skus = extractColumn($order_product_list,'product_sku');
		$skus[] = '-1';
		$this->database->slave->select('product_sku_code,product_sku_weight');
		$this->database->slave->from('eb_product_sku');
		$this->database->slave->where_in('product_sku_code',$skus);
		$query = $this->database->slave->get();
		$product_list = $query->result_array();


		$list = array();
		$product_list = reindexArray($product_list,'product_sku_code');
		$order_product_list = spreadArray($order_product_list,'order_id');
		foreach($order_list as $order){
			$detail_list = array();
			$order_products = id2name($order['order_id'],$order_product_list,array());

			$total_weight = 0;
			foreach($order_products as $key => $order_product){
				$product_weight = 0;
				if(isset($product_list[$order_product['product_sku']])){
					$product_weight = $product_list[$order_product['product_sku']]['product_sku_weight'];
				}
				$product_weight = $product_weight*$order_product['order_product_quantity'];
				$order_products[$key]['product_weight'] = $product_weight;
				$total_weight += $product_weight;
			}

			$total_apportion_discount = 0;
			$total_apportion_shipping = 0;
			$order_discount = $order['order_price_coupon']+$order['order_price_rewards']+$order['order_price_discount'];
			$order_shipping = $order['order_price_shipping']+$order['order_price_insurance'];
			foreach($order_products as $order_product){
				$product_apportion_rate = $order_product['order_product_price_subtotal']/$order['order_price_subtotal'];
				$order_product_apportionprice_discount = round($order_discount*$product_apportion_rate,2);
				$total_apportion_discount += $order_product_apportionprice_discount;
				
				$order_product_apportionprice_shipping = 0;
				if($order_product['product_weight'] > 0 && $total_weight > 0){
					$weight_apportion_rate = $order_product['product_weight']/$total_weight;
					$order_product_apportionprice_shipping = round($order_shipping*$weight_apportion_rate,2);
				}
				$total_apportion_shipping += $order_product_apportionprice_shipping;

				$detail_list[] = array(
					'product_id' => $order_product['product_id'],
					'product_sku' => $order_product['product_sku'],
					'order_product_name' => $order_product['order_product_name'],
					'order_product_image' => $order_product['order_product_image'],
					'order_product_quantity' => $order_product['order_product_quantity'],
					'order_product_promote_type' => $order_product['order_product_promote_type'],
					'order_product_promote_id' => $order_product['order_product_promote_id'],
					'order_product_price' => $order_product['order_product_price'],
					'order_product_baseprice' => $order_product['order_product_baseprice'],
					'order_product_apportionprice' => $order_product['order_product_price_subtotal'],
					'order_product_apportionprice_shipping' => $order_product_apportionprice_shipping,
					'order_product_apportionprice_discount' => $order_product_apportionprice_discount,
				);
			}
			if($total_apportion_discount < $order_discount){
				$detail_list[0]['order_product_apportionprice_discount'] += ($order_discount-$total_apportion_discount);
			}
			if($total_apportion_shipping < $order_shipping){
				$detail_list[0]['order_product_apportionprice_shipping'] += ($order_shipping-$total_apportion_shipping);
			}
			foreach($detail_list as $key => $record){
				$detail_list[$key]['order_product_apportionprice'] = $record['order_product_apportionprice']+$record['order_product_apportionprice_shipping']-$record['order_product_apportionprice_discount'];
			}

			$list[] = array(
				'order_id' => $order['order_id'],
				'order_code' => $order['order_code'],
				'order_entrance' => $order['order_entrance'],
				'language_id' => $order['language_id'],
				'customer_id' => $order['customer_id'],
				'shipping_id' => $order['shipping_id'],
				'payment_id' => $order['payment_id'],
				'order_email' => $order['order_email'],
				'order_country_shipping' => $order['order_country_shipping'],
				'order_country_payment' => $order['order_country_payment'],
				'order_address_firstname' => $order['order_address_firstname'],
				'order_address_lastname' => $order['order_address_lastname'],
				'order_address_phone' => $order['order_address_phone'],
				'order_address_country' => $order['order_address_country'],
				'order_address_state' => $order['order_address_state'],
				'order_address_city' => $order['order_address_city'],
				'order_address_street' => $order['order_address_street'],
				'order_address_postalcode' => $order['order_address_postalcode'],
				'order_address_cpfcnpj' => $order['order_address_cpfcnpj'],
				'order_flg_separate_package' => $order['order_flg_separate_package'],
				'order_flg_insurance' => $order['order_flg_insurance'],
				'order_currency' => $order['order_currency'],
				'order_currency_rate' => $order['order_currency_rate'],
				'order_coupon' => $order['order_coupon'],
				'order_price' => $order['order_price'],
				'order_baseprice' => $order['order_baseprice'],
				'order_price_product' => $order['order_price_product'],
				'order_price_subtotal' => $order['order_price_subtotal'],
				'order_price_shipping' => $order['order_price_shipping'],
				'order_price_insurance' => $order['order_price_insurance'],
				'order_price_discount' => $order['order_price_discount'],
				'order_price_coupon' => $order['order_price_coupon'],
				'order_price_rewards' => $order['order_price_rewards'],
				'order_time_create' => $order['order_time_create'],
				'detail_list' => $detail_list,
			);
		}

		$result['items'] = $list;
		$result['totalCount'] = count($list);
		echo json_encode($result);
		die();
	}

	public function orderActionList(){
		$result = array(
			'items' => array(),
			'totalCount' => 0,
			'detail' => '',
			'success' => true,
		);
		$time = $this->input->post('time');
		$secret = $this->input->post('secret');
		$order_action_id = $this->input->post('order_action_id');
		$this->load->helper('array');
		$this->load->library('database');

		$base_string = date('YmdH') . '_$FUEqv00TkuCdVhwN1&_' . $time;
		if(md5($base_string) != $secret){
			$result['success'] = false;
			$result['detail'] = 'Permission Deny';
			echo json_encode($result);
			die();
		}

		$this->database->slave->from('eb_order_action');
		$this->database->slave->where('order_action_id >',$order_action_id);
		$this->database->slave->order_by('order_action_id','asc');
		$query = $this->database->slave->get();
		$list = $query->result_array();

		$result['items'] = $list;
		$result['totalCount'] = count($list);
		echo json_encode($result);
		die();
	}
/*
| -------------------------------------------------------------------
|  Product Info For ERP
| -------------------------------------------------------------------
*/
	public function rsyncProductInfo(){
		global $base_url;
		$username = $this->input->post('username');
		$passwd = $this->input->post('passwd');
		$location = $this->input->post('location');
		if($location===false) $location = 'AU';

		$warehouse_list = array('US','AU','DE','ES','UK');
		$result = array(
			'error_code' => 0,
			'msg' => 'success',
			'data' => array(),
		);

		if($username != 'eachbuyer' || $passwd != 'j5YvCnBLM43jh95'){
			$result['error_code'] = 10;
			$result['msg'] = '[username/passwd] parameter error';
			echo json_encode($result);
			die();
		}
		if(!in_array($location,$warehouse_list)){
			$result['error_code'] = 10;
			$result['msg'] = '[location] parameter error';
			echo json_encode($result);
			die();
		}

		$this->database->slave->select('product_id,product_sku_code,product_sku_price,product_sku_price_market');
		$this->database->slave->from('eb_product_sku');
		$this->database->slave->where('product_sku_warehouse',$location);
		$query = $this->database->slave->get();
		$sku_list = $query->result_array();

		$product_ids = extractColumn($sku_list,'product_id');
		$product_ids = array_unique($product_ids);

		$this->database->slave->select('product_id,product_url,product_price,product_price_market');
		$this->database->slave->from('eb_product');
		$this->database->slave->where_in('product_id',$product_ids);
		$query = $this->database->slave->get();
		$products = $query->result_array();
		$products = reindexArray($products,'product_id');

		$this->database->slave->select('product_id,product_description_name');
		$this->database->slave->from('eb_product_description_us');
		$this->database->slave->where_in('product_id',$product_ids);
		$query = $this->database->slave->get();
		$descs = $query->result_array();
		$descs = reindexArray($descs,'product_id');

		$this->database->slave->select('discount_id,product_id');
		$this->database->slave->from('eb_discount_range');
		$this->database->slave->where_in('product_id',$product_ids);
		$this->database->slave->where('discount_range_status',STATUS_ACTIVE);
		$query = $this->database->slave->get();
		$ranges = $query->result_array();
		$discount_ids = extractColumn($ranges,'discount_id');
		$ranges = spreadArray($ranges,'product_id');

		$this->database->slave->select('discount_id,discount_effect');
		$this->database->slave->from('eb_discount');
		$this->database->slave->where_in('discount_id',$discount_ids);
		$this->database->slave->where('discount_status',STATUS_ACTIVE);
		$this->database->slave->where('discount_type',1);
		$this->database->slave->where('discount_time_start <',NOW);
		$this->database->slave->where('discount_time_finish >',NOW);
		$query = $this->database->slave->get();
		$discounts = $query->result_array();
		$discounts = reindexArray($discounts,'discount_id');

		$list = array();
		foreach($sku_list as $sku){
			if(!isset($products[$sku['product_id']])) continue;
			if(!isset($descs[$sku['product_id']])) continue;

			$discount = 0;
			if(isset($ranges[$sku['product_id']])){
				foreach($ranges[$sku['product_id']] as $range){
					if(!isset($discounts[$range['discount_id']])) continue;

					$discount = max($discount,$discounts[$range['discount_id']]['discount_effect']);
				}
			}

			$price = 0;
			if($discount > 0){
				$price = $products[$sku['product_id']]['product_price_market'] + $sku['product_sku_price_market'];
				$price = round($price*(100-$discount)/100,2);
			}else{
				$price = $products[$sku['product_id']]['product_price'] + $sku['product_sku_price'];
			}

			$list[] = array(
				'sku' => $sku['product_sku_code'],
				'goods_name' => $descs[$sku['product_id']]['product_description_name'],
				'price' => $price,
				'url' => $base_url[1].$products[$sku['product_id']]['product_url'],
			);
		}

		echo json_encode($list);
		die();
	}
/*
| -------------------------------------------------------------------
|  Send Pay Email
| -------------------------------------------------------------------
*/
	/**
	 * @desc 催款邮件功能（$argv[1]值为1，表明催款1；值为2，表明是催款2；get方式时，传参为paytype）
	 */
	public function repayEmail($pay_type = 1){
		@set_time_limit(0);
		@ini_set('memory_limit', '-1');
		try
		{
			//催款1：1  or 催款2：2
			if(!in_array($pay_type, array(1,2))) $pay_type = 1;
				
			$page = 1;//页数
			$pagesize = 100;//每页数量
			$this->load->model('ordermodel','ordermodel');
			$this->load->model('emailtemplatemodel','emailtemplatemodel');
			$this->load->model('currencymodel','currencymodel');
			$this->load->model('goodsmodel','product');
			
			//获取总记录数
			$total_num = $this->ordermodel->getUnpaidOrderNum($pay_type);
			$max_page = ceil($total_num/$pagesize);
			//获取未付款订单信息
			while($max_page >= $page){
				$order_product_status = true;//该订单中商品状态

				$info = $this->ordermodel->getOrderListWithPage($pay_type , $page, $pagesize);
				
				//批量获取订单中商品
				$all_id_array = array();
				foreach ($info as $order_key=>$order_val){
					array_push($all_id_array, $order_val['order_id']);
				}
				$all_product_infos = $this->ordermodel->getProductListByOrderIdArray($all_id_array);
				
				//对订单中商品做映射
				$all_order_map_product = array();
				foreach($all_product_infos as $map_key=>$map_val){
					$map_order_id = $map_val['order_id'];
					$all_order_map_product[$map_order_id][] = $map_val;
				}
				
				//循环遍历，每个订单
				foreach ($info as $key=>$val){
					$currency_info = $this->currencymodel->getConfigCurrency($val['order_currency']);
					$order_currency = isset($currency_info['currency_format'])?str_replace('%s', '', $currency_info['currency_format']):'$';
					$info_order_id = $val['order_id'];
					$order_currency_rate = isset($currency_info['currency_rate'])?$currency_info['currency_rate']:1;
					
					//获取订单中所有商品
					if(!isset($all_order_map_product[$info_order_id])) continue;
					$order_products = $all_order_map_product[$info_order_id];
					
					//获取该订单中商品所有sku信息
					$product_skus = $productIds = array();
					foreach ($order_products as $sku_key=>$sku_val){
						array_push($product_skus, $sku_val['product_sku']);
						array_push($productIds, $sku_val['product_id']);
					}
					if(empty($product_skus)) continue;
					$product_sku_infos = $this->product->getBatchSkuInfo($product_skus);
					if(count($product_sku_infos) < count($product_skus)){//判断sku是否有下架
						$order_product_status = false;
					}
					
					//获取推荐商品dom
					$recommend_goods_list = $this->product->getEmailRecommendProduct($productIds, $limit = 4, $val['language_id']);
					
					//获取推荐商品促销价格
					$recommend_goods_list = $this->allProductWithPrice($recommend_goods_list,$order_currency_rate,$order_currency);
					
					$item_reo = $this->emailtemplatemodel->getEmailRecommendProductDomSSI( $recommend_goods_list ,$order_currency,$val['language_id']);
					//发送邮件（催款1 or 催款2） type值不同：催款1  重新下单（type = 9），继续支付（type = 3 ） ;催款2 type = 4
					if($pay_type==1){
						if($order_product_status==true){
							$email_type = 3;
							$source_string = 'paying24checkout';
						}else{
							$email_type = 9;
							$source_string = 'paying24reorder';
						}
					}else{
						$email_type = 4;
						$source_string = 'paying48';
					}
					
					//获取催支付dom详情
					$order_products_dom = $this->emailtemplatemodel->getEmailReminderOrderInfoDom($order_products,$val['language_id'],$order_currency,$source_string);
					$result = $this->sendRepayMail($email_type , $val , $item_reo , $order_products_dom);
					
					//更新表时间
					if($pay_type==1) {
						$update_data['order_status_email_pay1'] = 1;
					} else {
						$update_data['order_status_email_pay2'] = 1;
						$update_data['order_status_email_pay1'] = 1;
					}

					if($result){//更新库
						$this->ordermodel->updateOrder($val['order_id'],$update_data);
						//日志记录
						echo $val['order_id'].'-'.$val['order_email'].'-'.date('Y-m-d H:i:s',time())."\n";
					}
					
					unset($recommend_goods_list,$item_reo);
				}
				
				sleep(1);
				$page++;
				unset($info);
			}
			/*echo '<pre>';
			print_r($this->database->slave);
			die();*/
			echo "done!\n";die;
		}catch(Exception $e)
		{
			print $e->getMessage();exit();
		}	
	}
	
	//发送邮件
	private function sendRepayMail($email_type , $order , $item_reo , $orderInfoDomArray){
		global $payment_list,$base_url;
		$payment_id = $order['payment_id'];
		$currentLanguageId = $order['language_id'];
		$params = array(
				'SITE_DOMAIN' => rtrim( $base_url[ $currentLanguageId ], '/' ),
				'USER_NAME' => $order['order_address_firstname']." ".$order['order_address_lastname'],
				'SITE_DOMAIN1' => COMMON_DOMAIN, //域名
				//'SUBSCRIBE_LINK' => genURL('common/validateSubscribe').'?email='.encrypt($email).'&utm_source=System_Own&utm_medium=Email&utm_campaign=Newsletter-confirm&utm_nooverride=1',
				'CS_EMAIL' => 'cs@eachbuyer.com',
				'ITEM_REO' =>$item_reo,
				'ORDER_NUM' => $order['order_code'],
				'ORDER_TIME' => date('F j, Y h:i:s A e', strtotime($order['order_time_create'])),
				'ORDER_ID' =>$order['order_id'],
				'ORDER_INFO2' => $orderInfoDomArray,
		);
		$this->load->model('emailmodel','emailmodel');
		
		$email = $order['order_email'];
		$result = $this->emailmodel->subscribe_sendMail( $email_type, $order['language_id'] ,$email,$params);
		if(stripos($result, 'OK')!==false) {
			RETURN true;
		}else{
			return false;
		}
		
	}
	
	//新版商品折扣处理方式（多条折扣查询合并为一条）
	private function allProductWithPrice($data,$order_currency_rate = 1,$order_currency = '$'){
		if(!count($data) && !is_array($data)) return false;
		$this->load->model("discountmodel","discount");
		$this->load->model("discountrangemodel","discountrange");
	
		//所有商品折扣范围信息
		$promote_range_infos = $this->getAllProductRangeInfos($data);
		//处理每个商品折扣情况
		foreach ($data as $p_k=>&$p_v){
			$product_id = $p_v['product_id'];
			$front_price = $p_v['product_price'];
			$market_price = $p_v['product_price_market'];
				
			//判断该商品是否存在折扣范围信息,获取该商品所有折扣id信息
			$product_all_discount_ids = array();
			foreach($promote_range_infos as $promote_k=>$promote_v){
				if($promote_v['product_id']==$product_id) array_push($product_all_discount_ids, $promote_v['discount_id']);
			}
			if(!empty($product_all_discount_ids)){
				//根据所有折扣id，获取最大折扣数
				$discount_num = $this->singleProductBatchDiscount($product_all_discount_ids);
				$discount_price = $front_price;
				if($discount_num!=0){
					$real_discount = (100-$discount_num)/100;
					$discount_price = $market_price*$real_discount;
					$discount_price = round($discount_price,2);
				}
	
				$p_v['product_basediscount_price'] = $p_v['product_discount_price'] = $discount_price;
				$p_v['product_discount_number'] = $discount_num;
			}else{
				$front_price = $p_v['product_price'];
				$market_price = $p_v['product_price_market'];
				$p_v['product_basediscount_price'] = $p_v['product_discount_price'] = $front_price;
				$p_v['product_discount_number'] = 0;
			}
				
			$p_v['product_currency'] = $order_currency;
			//汇率转换
			if($order_currency_rate!=1){
				$p_v['product_discount_price'] = round($p_v['product_discount_price']*$order_currency_rate,2);
				$p_v['product_price_market'] = round($p_v['product_price_market']*$order_currency_rate,2);
			}
				
			//次要商品价格处理
			if(isset($p_v['children']) && count($p_v['children'])){
				foreach ($p_v['children'] as $key=>&$val){
					$child_front_price = $val['product_price'];
						
					$children_all_discount_ids = array();
					//该商品所有折扣
					foreach ($promote_range_infos as $c_p_k=>&$c_p_v){
						if($c_p_v['product_id']==$val['product_id']){//有折扣
							array_push($children_all_discount_ids, $c_p_v['promote_discount_id']);
						}
					}
					if(!empty($children_all_discount_ids)){
						//根据所有折扣id，获取最大折扣数
						$child_discount_num = $this->singleProductBatchDiscount($children_all_discount_ids);
						$child_discount_price = $child_front_price;
						if($child_discount_num!=0){
							$real_discount = (100-$child_discount_num)/100;
							$child_discount_price = $market_price*$real_discount;
							$child_discount_price = round($child_discount_price,2);
						}
							
						$val['product_basediscount_price'] = $val['product_discount_price'] = $child_discount_price;
						$val['product_discount_number'] = $child_discount_num;
					}else{
						$val['product_basediscount_price'] = $val['product_discount_price'] = $child_front_price;
						$val['product_discount_number'] = 0;
					}
					$val['product_currency'] = $order_currency;
						
					//汇率转换
					if($order_currency_rate!=1){
						$val['product_discount_price'] = round($val['product_discount_price']*$order_currency_rate,2);
						$val['product_price_market'] = round($val['product_price_market']*$order_currency_rate,2);
					}
						
				}
			}
				
		}
		return $data;
	}
	
	/**
	 * @desc 批量获取商品列表的折扣范围信息
	 * @param unknown $data
	 * @return unknown
	 */
	private function getAllProductRangeInfos($data){
		$all_product_ids = array();
		//获取所有商品id
		foreach ($data as $k=>&$v){
			if(isset($v['product_id']) && $v['product_id'] && is_numeric($v['product_id'])){
				array_push($all_product_ids, $v['product_id']);
			}
			if(isset($v['children']) && count($v['children'])){
				foreach ($v['children'] as $c_k=>$c_v){
					array_push($all_product_ids, $c_v['product_id']);
				}
			}
		}
	
		//所有商品折扣范围信息
		$promote_range_infos = $this->discountrange->getRangeExistsWithArray($all_product_ids);
		return $promote_range_infos;
	
	}
	
	/**
	 * @desc 对所有discount_ids列表中找出最大的折扣数
	 * @param unknown $all_discount_ids
	 * @return Ambigous <number, unknown>
	 */
	public function singleProductBatchDiscount($all_discount_ids){
		$max_discount_id = 0;
		$max_discount_num = 0;
			
		$this->load->model("discountmodel","discount");
			
		//所有折扣discount_id详情
		$all_discount_infos = $this->discount->getBatchDiscountWithIds($all_discount_ids);
		$nowTime = requestTime();
		//找出符合条件，最大折扣
		foreach ($all_discount_infos as $discount_k=>$discount_v){
			$start_time = isset($discount_v['discount_time_start'])?strtotime($discount_v['discount_time_start']):0;
			$end_time = isset($discount_v['discount_time_finish'])?strtotime($discount_v['discount_time_finish']):0;
			$discount_effect_value = isset($discount_v['discount_effect'])?$discount_v['discount_effect']:0;
			$discount_id = isset($discount_v['discount_id'])?$discount_v['discount_id']:0;
	
			if($discount_effect_value > $max_discount_num && $start_time<$nowTime && $end_time > $nowTime){
				$max_discount_num = $discount_effect_value;
				$max_discount_id = $discount_id;
			}
		}
			
		return $max_discount_num;
	}
	
}
/* End of file api.php */
/* Location: ./application/controllers/api.php */