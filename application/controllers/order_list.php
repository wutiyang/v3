<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Order_list extends Dcontroller {
	
	public function index(){
		//判断用户是否登录
		if(!$this->customer->checkUserLogin()) {
			redirect(genURL('login'));
		}
		$this->load->model("ordermodel","order");
		$this->load->model("goodsmodel","product");
		
		$page = $this->input->get("page");
		$page = intval($page);
		$page = max(1,$page);
		$pagesize = 8;
		
		$userId = $this->customer->getCurrentUserId();
		
		$orderList = $this->order->getOrderList($userId,array('page'=>$page,'pagesize'=>$pagesize));
		$nums = $this->order->getOrderCount($userId);

		$order_ids = extractColumn($orderList,'order_id');
		$product_list = $this->order->getProductListByOrderId($order_ids);
		$product_ids = array();
		$product_skus = array();
		foreach($product_list as $record){
			$product_ids[] = $record['product_id'];
			$product_skus[] = $record['product_sku'];
		}
		$products = $this->product->getUncachedProductById($product_ids);
		$skus = $this->product->getUncachedProductSKUByCode($product_skus);
		$products = reindexArray($products,'product_id');
		$skus = reindexArray($skus,'product_sku_code');
		$product_list = spreadArray($product_list,'order_id');
		foreach($orderList as $key => $order){
			$flg_repay = true;

			if($order['order_status'] != OD_CREATE) $flg_repay = false;
			if(time() - strtotime($order['order_time_create']) > 86400*14) $flg_repay = false;
			if(in_array($order['payment_id'],array(1,3))) $flg_repay = false;

			$order_detail = id2name($order['order_id'],$product_list,array());
			foreach($order_detail as $record){
				if(!isset($products[$record['product_id']]) || $products[$record['product_id']]['product_status'] != PRODUCT_STATUS_ACTIVE) $flg_repay = false;
				if(!isset($skus[$record['product_sku']]) || $skus[$record['product_sku']]['product_sku_status'] != PRODUCT_STATUS_ACTIVE) $flg_repay = false;
			}

			$orderList[$key]['flg_repay'] = $flg_repay;
		}

		$this->_view_data['orderList'] = $orderList;
		$this->_view_data['nums'] = $nums;
		$this->_view_data['currentPage'] = 'order';
		
		//分页处理
		$this->_basepagination("order_list/",$page,$nums,$pagesize);

		//个人中心广告位
		$this->load->model("imageadmodel","imagead");
		$image_ads = $this->imagead->getLocationWithId(5);

		$image_ad = '';
		foreach ($image_ads as $ad) {
			if(strtotime($ad['ad_time_start']) < time() && strtotime($ad['ad_time_end']) > time()){
				$ad['ad_content'] = json_decode($ad['ad_content'],true);
				$image_ad = $ad['ad_content'][currentLanguageId()];
				break;
			}
		}
		$this->_view_data['image_ad'] = $image_ad;
		parent::index();
	}
	
}