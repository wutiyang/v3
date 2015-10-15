<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';
/**
 * @desc account 账户评论
 * @author Wty
 *
 */
class Review_create extends Dcontroller {
	
	//account review_create列表
	public function index(){
		$this->load->model("reviewmodel","review");
		
		$page = $this->input->get("page");
		if(!$page || !is_numeric($page)) $page = 1;
		$pagesize = 8;
		$nums = 0;
		
		if(!$this->customer->checkUserLogin()){
			redirect(genURL('login'));
		}
		//type三种类型 0 1 2 ，0：直接进入展示全部微评论商品 1：单个商品进入 
		$type = $this->input->get('type');
		
		$orderId = $this->input->get('order_id');
		$productSku = $this->input->get('product_sku');
		$productId = $this->input->get('product_id');
		
		$userId = $this->customer->getCurrentUserId();
		
		//获取已经评价的列表
		$reviewList = $this->review->reviewListByUserId($userId);
		//获取满足条件的商品列表
		$orderProductList = $this->review->orderProductListByUserId($userId);
			
		//数组来设置相应的已经存在的商品的唯一key
		$arr = array();
		foreach ($reviewList as $review){
			$arr[$review['order_id'].'_'.$review['product_sku']] = 1;
		}
		
		//相应sku信息
		$skuArr = extractColumn($orderProductList, 'product_sku');
		$skuInfoArr = $this->processSkuInfos($skuArr);
		$this->_view_data['skuInfoArr'] = $skuInfoArr;
		
		
		$sortList = array();
		//预留存储指定orderId和productSku的位置 
		$sortList[0] = array();
		foreach ($orderProductList as $orderProduct){
			//判断已经被评论的 将不会再显示
			if(!isset($arr[$orderProduct['order_id'].'_'.$orderProduct['product_sku']])){
				//如果指定了orderId和productSku将其放入预留位置 如果未指定依次存入数组
				//if(!empty($orderId) && !empty($productSku) && $orderProduct['order_id'] == $orderId && $orderProduct['product_sku'] == $productSku){
				//
				//新的规则有所改变，只要是productId匹配就可以
				if(!empty($productId) && empty($sortList[0]) && $orderProduct['product_id'] == $productId){
					$sortList[0] = $orderProduct;
				}else{
					$sortList[] = $orderProduct;
				}
			}
		}
		//如果没有指定的话 将预留位置从数组中删掉
		if(empty($sortList[0])){
			unset($sortList[0]);
		}
		$orderProductList = $sortList;
		$nums = count($orderProductList);
		
		$orderProductList = array_slice($orderProductList, ($page-1)*$pagesize, $pagesize);
		
		$orderIds = extractColumn($orderProductList, 'order_id');
		$this->load->model("ordermodel","order");
		$orderList = $this->order->getOrderList($userId,array());
		$orderList = reindexArray($orderList, 'order_id');
		
		foreach ($orderProductList as &$orderProduct){
			$order = isset($orderList[$orderProduct['order_id']])?$orderList[$orderProduct['order_id']]:array();
			if(empty($order)){
				$orderProduct['order_currency'] = '';
				$orderProduct['format_price'] = currencyAmount($orderProduct['order_product_price']);
			}else{
				$orderProduct['order_currency'] = $order['order_currency'];
				$orderProduct['format_price'] = currencyAmount($orderProduct['order_product_price'],$order['order_currency']);
			}
		}
		
		$reviewNums = $this->review->reviewNumsWithUserid($userId);
		
		//获取商品的id信息
		$pids = array();
		$pids = extractColumn($orderProductList, 'product_id');
		
		$productInfoList = array();
		if(!empty($pids)){
			$productInfoList = $this->searchGoodsinfoWithPids($pids);
		}
		
		$this->_view_data['orderProductList'] = $orderProductList;
		$this->_view_data['productInfoList'] = $productInfoList; 
		$this->_view_data['nums'] = $nums;
		$this->_view_data['reviewNums'] = $reviewNums;
		//当前页名称 处理account中左侧选中的
		$this->_view_data['currentPage'] = 'review';
		
		//分页处理
		$this->_basepagination("review_create/",$page,$nums,$pagesize);

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
	
	/**
	 * 添加评论
	 * 
	 * @author an
	 */
	public function ajaxAdd(){
		$arr = array('status'=>200,'msg'=>'','data'=>array());
		//判断登陆状态
		if(!$this->customer->checkUserLogin()) {
			$arr['status'] = 1007;
			$this->ajaxReturn($arr);
		}
		
		$this->load->model('reviewmodel','review');
		$this->load->model('ordermodel','order');
		
		$userId = $this->customer->getCurrentUserId();
		
		$productSku = $this->input->post('product_sku');
		$productId = $this->input->post('product_id');
		$orderId = $this->input->post('order_id');
		$title = $this->input->post('title');
		$content = $this->input->post('content');
		$score = $this->input->post('score');
		
		//进行部分参数处理 过滤标签
		$title = htmlspecialchars(trim($title));
		$content = htmlspecialchars(trim($content));
		
		//进行评分的处理
		if(!in_array($score, array(1,2,3,4,5))) {
			$score = 1;
		}
		
		//防止提交空数据
		if(empty($title) || empty($content)){
			redirect(genURL('/review_create'));
		}
		
		//检测order状态 是否属于当前用户 以及订单中是否存在此product_id
		//判断订单状态是否为已支付
		$order = $this->order->getOrderById($orderId);
		if(empty($order) || $order['customer_id'] != $userId || !in_array($order['order_status'], array(3,5,6,7,8,9))){
			$arr['status'] = 2200;
			$arr['msg'] = 'Order not Exist,Or No Paid,Or No Auth!';
			$this->ajaxReturn($arr);
		}
		
		//判断此商品是否在此订单内
		$flag = $this->order->existProductByOrderIdAndProductId($orderId,$productId);
		if(!$flag){
			$arr['status'] = 2200;
			$arr['msg'] = 'Product not Exist,Or No Auth!';
			$this->ajaxReturn($arr);
		}
		
		//获取已经评价的列表
		$reviewList = $this->review->reviewListByUserId($userId);
		foreach ($reviewList as $review){
			if($review['order_id'] == $orderId && $review['product_sku'] == $productSku){
				$arr['status'] = 2200;
				$arr['msg'] = 'Already Review,Please Press F5!';
				$this->ajaxReturn($arr);
			}
		}
		
		//插入评论
		$flag = $this->review->createReview(array(
			'product_sku'=>$productSku,
			'product_id'=>$productId,
			'order_id'=>$orderId,
			'review_title'=>$title,
			'review_content'=>$content,
			'review_score'=>$score,
			'customer_id'=>$userId,
			'language_id'=>currentLanguageId(),
			'review_status'=>STATUS_PENDING,
			'review_time_create'=>date('Y-m-d h:i:s',time()),
			'review_time_lastmodified'=>date('Y-m-d h:i:s',time()),
		));
		
		if($flag){
			$arr['msg'] = 'Add Successfully!';
			$this->ajaxReturn($arr);
		}else{
			$arr['status'] = 2200;
			$arr['msg'] = 'Failed,Please Retry!';
			$this->ajaxReturn($arr);
		}
	}

	private function processSkuInfos($skuArr){
		$result = array();
		if(empty($skuArr))
			return $result;
		
		$languageId = currentLanguageId();
		
		$this->load->model('attributeproductmodel','attributeproduct');
		$attrSkuList = $this->attributeproduct->getAttrAndValueWithSkus($skuArr);
		
		//获取相应id
		$attrIds = array();
		$attrValueIds = array();
		foreach ($attrSkuList as $val){
			foreach ($val as $attrSku){
				$attrIds[] = $attrSku['complexattr_id'];
				$attrValueIds[] = $attrSku['complexattr_value_id'];
			}
		}
		
		$attrList = $this->attributeproduct->complexattrBatchInfo($attrIds,$languageId);
		$attrValueList = $this->attributeproduct->complexattrBatchValueInfo($attrValueIds,$languageId);
		
		$attrList = reindexArray($attrList, 'complexattr_id');
		$attrValueList = reindexArray($attrValueList, 'complexattr_value_id');
		
		foreach ($attrSkuList as $val){
			foreach ($val as $attrSku){
				$result[$attrSku['product_sku']][$attrList[$attrSku['complexattr_id']]['complexattr_lang_title']] = $attrValueList[$attrSku['complexattr_value_id']]['complexattr_value_lang_title'];
			}
		}
		
		return $result;
	}
	
	//根据商品id（数组，批量）获取商品信息
	private function searchGoodsinfoWithPids($goodsIdArray){
		$result = array();
	
		$this->load->model("goodsmodel","ProductModel");
		//****************************************测试时
		$data = $this->ProductModel->getProductList( $goodsIdArray, $status=0,0,currentLanguageCode());
	
		$result = $this->productWithPrice($data);
	
		return $result;
	}
}