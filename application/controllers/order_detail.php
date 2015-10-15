<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Order_Detail extends Dcontroller {
	
	public function index($order_id = false){
		if(!$this->customer->checkUserLogin()) {
			redirect(genURL('login'));
		}
		
		global $payment_name;
		$this->load->model('ordermodel','order');
		$this->load->model("goodsmodel","product");
		$this->load->model('reviewmodel','review');
		
		$order = $this->order->getOrderById($order_id);

		if(empty($order)) redirect(genURL('order_list'));
		if($order['customer_id'] != $this->customer->getCurrentUserId()) redirect(genURL('order_list'));

		$order_detail = $this->_getOrderProductList($order_id);

		$order['payment_name'] = id2name($order['payment_id'],$payment_name);
		$this->_view_data['order'] = $order;
		$this->_view_data['flg_repay'] = $this->_checkOrderRepay($order,$order_detail);
		$this->_view_data['order_detail'] = spreadArray($order_detail,'order_package_id');
		$this->_view_data['currentPage'] = 'order';

		parent::index();
	}

	public function cancel($order_id = false){
		if(!$this->customer->checkUserLogin()) {
			redirect(genURL('login'));
		}
		
		$this->load->model('ordermodel','order');
		$order = $this->order->getOrderById($order_id);

		if(empty($order)) redirect(genURL('order_list'));
		if($order['customer_id'] != $this->customer->getCurrentUserId()) redirect(genURL('order_list'));
		if($order['order_status'] != OD_CREATE) redirect(genURL('order_detail/'.$order['order_id']));

		$this->order->updateOrder($order['order_id'],array(
			'order_status' => OD_CANCEL,
			'order_time_lastmodified' => NOW,
		));
		$this->order->createOrderAction(array(
			'order_id' => $order['order_id'],
			'order_action_type' => ORDER_ACTION_TYPE_CANCEL,
			'order_action_time_create' => NOW,
		));

		redirect(genURL('order_detail/'.$order['order_id']));
	}

	protected function _getOrderProductList($order_id){
		$product_list = $this->order->getProductListByOrderId($order_id);
		$product_list = $this->_addAttrInfo($product_list);
		$sku_package_map = $this->_initSKUPackageMap($product_list);

		$package_list = $this->order->getPackageListByOrderId($order_id);
		sortArray($package_list,'order_package_time_process',SORT_ASC);

		foreach($package_list as $record){
			$sku_queue = $this->_genSKUQueue($record['order_package_content']);
			foreach($sku_queue as $sku){
				$sku_package_map = $this->_updateSKUPackageMap($sku,$record['order_package_id'],$sku_package_map);
			}
		}

		$package_list = reindexArray($package_list,'order_package_id');
		foreach($sku_package_map as $key => $record){
			$sku_package_map[$key]['order_package_time_process'] = '';
			$sku_package_map[$key]['order_package_tracking_number'] = '';
			if(!isset($package_list[$record['order_package_id']])) continue;

			$sku_package_map[$key]['order_package_time_process'] = $package_list[$record['order_package_id']]['order_package_time_process'];
			$sku_package_map[$key]['order_package_tracking_number'] = $package_list[$record['order_package_id']]['order_package_tracking_number'];
		}

		$list = array();
		foreach($sku_package_map as $record){
			if(!isset($list[$record['order_product_id'].'_'.$record['order_package_id']])){
				$record['quantity'] = 1;
				$list[$record['order_product_id'].'_'.$record['order_package_id']] = $record;
			}else{
				$list[$record['order_product_id'].'_'.$record['order_package_id']]['quantity']++;
			}
		}
		$list = array_values($list);

		return $list;
	}

	protected function _addAttrInfo($product_list){
		$language_id = currentLanguageId();

		$skus = extractColumn($product_list, 'product_sku');
		
		$this->load->model('attributeproductmodel','attributeproduct');
		$attrSkuList = $this->attributeproduct->getAttrAndValueWithSkus($skus);
		//格式化数据形式
		$complexattr_sku_list = array();
		foreach ($attrSkuList as $val){
			foreach ($val as $attrSku){
				$complexattr_sku_list[] = $attrSku;
			}
		}
		
		$complexattr_ids = extractColumn($complexattr_sku_list, 'complexattr_id');
		$complexattr_value_ids = extractColumn($complexattr_sku_list, 'complexattr_value_id');
		$complexattr_list = $this->attributeproduct->complexattrBatchInfo($complexattr_ids,$language_id);
		$complexattr_value_list = $this->attributeproduct->complexattrBatchValueInfo($complexattr_value_ids,$language_id);

		$complexattr_sku_list = spreadArray($complexattr_sku_list, 'product_sku');
		$complexattr_list = reindexArray($complexattr_list, 'complexattr_id');
		$complexattr_value_list = reindexArray($complexattr_value_list, 'complexattr_value_id');

		foreach($product_list as $key => $record){
			$product_list[$key]['complexattr_list'] = array();
			$complexattr_sku = id2name($record['product_sku'],$complexattr_sku_list,array());
			foreach($complexattr_sku as $attr){
				if(!isset($complexattr_list[$attr['complexattr_id']])) continue;
				if(!isset($complexattr_value_list[$attr['complexattr_value_id']])) continue;

				$product_list[$key]['complexattr_list'][] = array(
					'complexattr' => $complexattr_list[$attr['complexattr_id']]['complexattr_lang_title'],
					'complexattr_value' => $complexattr_value_list[$attr['complexattr_value_id']]['complexattr_value_lang_title'],
				);
			}
		}

		return $product_list;
	}

	protected function _initSKUPackageMap($product_list){
		$buffer = array();
		foreach($product_list as $record){
			for($i=1;$i<=$record['order_product_quantity'];$i++){
				$record['order_package_id'] = 0;
				$buffer[] = $record;
			}
		}

		return $buffer;
	}

	protected function _genSKUQueue($order_package_content){
		$sku_list = json_decode($order_package_content,true);
		$process_list = array();
		foreach($sku_list as $sku => $quantity){
			for($i=1;$i<=$quantity;$i++){
				$process_list[] = $sku;
			}
		}

		return $process_list;
	}

	protected function _updateSKUPackageMap($sku,$order_package_id,$sku_package_map){
		$min_id_key = false;
		$update_key = false;
		foreach($sku_package_map as $key => $record){
			if($record['product_sku'] == $sku && $record['order_package_id'] == 0){
				$update_key = $key;
				break;
			}
			if($min_id_key === false || $sku_package_map[$min_id_key]['order_package_id'] >= $record['order_package_id']) $min_id_key = $key;
		}
		if($update_key === false) $update_key = $min_id_key;

		$sku_package_map[$update_key]['order_package_id'] = $order_package_id;
		return $sku_package_map;
	}

	protected function _checkOrderRepay($order,$order_detail){
		if($order['order_status'] != OD_CREATE) return false;
		if(time() - strtotime($order['order_time_create']) > 86400*14) return false;
		if(in_array($order['payment_id'],array(1,3))) return false;

		$product_ids = array();
		$product_skus = array();
		foreach($order_detail as $record){
			$product_ids[] = $record['product_id'];
			$product_skus[] = $record['product_sku'];
		}
		$products = $this->product->getUncachedProductById($product_ids);
		$skus = $this->product->getUncachedProductSKUByCode($product_skus);
		$products = reindexArray($products,'product_id');
		$skus = reindexArray($skus,'product_sku_code');

		foreach($order_detail as $record){
			if(!isset($products[$record['product_id']]) || $products[$record['product_id']]['product_status'] != PRODUCT_STATUS_ACTIVE) return false;
			if(!isset($skus[$record['product_sku']]) || $skus[$record['product_sku']]['product_sku_status'] != PRODUCT_STATUS_ACTIVE) return false;
		}

		return true;
	}


	/*
	public function ajaxCancel(){
		$arr = array('status'=>200,'msg'=>'','data'=>array());
		//判断用户是否登录
		if(!$this->customer->checkUserLogin()) {
			$arr['status'] = 1007;
			$this->ajaxReturn($arr);
		}
		$this->load->model("ordermodel","order");
		
		$orderId = $this->input->post('order_id');
		
		//获取当前登陆者信息
		$userId = $this->customer->getCurrentUserId();
		
		$order = $this->order->getOrderById($orderId);
		
		if(!empty($order) && $order['customer_id'] == $userId && ($order['order_status'] == 1 || $order['order_status'] == 2)){
			$this->order->updateOrder($orderId,array(
				'order_status'=>0,
			));
			$arr['msg'] = 'cancel success';
		}else{
			$arr['msg'] = 'cancel failed';
		}
		
		$this->ajaxReturn($arr);
	}
	*/
}