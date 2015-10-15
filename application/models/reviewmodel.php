<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 评论model
 * @author Administrator
 *
 */
class reviewmodel extends CI_Model {
	//private $memcache;
	
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}
	
	//获取商品的评论(全部评论)
	public function reviewListWithPid($product_id,$page = 0,$pagesize = 8){
		$result = array();
		if(!is_numeric($product_id) || !$product_id) return $result;
		global $mem_expired_time;
		$mem_key = md5("each_buyer_product_review_".$product_id);

        $list = $this->memcache->get($mem_key);
        if($list===false){
			$this->database->slave->from('eb_review');
			$this->database->slave->where('review_status',1);
			$this->database->slave->where('product_id',$product_id);
			$this->database->slave->order_by('review_id',"desc");
//            if($page != 0){
//                $this->database->slave->limit($pagesize,$pagesize*($page-1));
//            }
			
			$query = $this->database->slave->get();
            $list = $query->result_array();
			//echo $this->database->slave->last_query();die;
			$this->memcache->set($mem_key, $list,$mem_expired_time['product_discount']);
		}
        $result['data'] = $list;
        if($page)
            $result['data'] = array_slice($list,$pagesize*($page-1),$pagesize);
        $result['nums'] = count($list);

		return $result;
	}
	
	/**
	 * 根据pids获取相应的有效评论数量
	 * @param unknown $pids
	 * @return multitype:|unknown
	 * @author an
	 */
	public function reviewNumsByPids($pids){
		$result = array();
		if(empty($pids)) {
			return $result;
		}elseif(!is_array($pids)){
			$pids = array($pids);
		}
		$this->database->slave->select('product_id,count(product_id) as num');
		$this->database->slave->from('eb_review');
		$this->database->slave->where('review_status',1);
		$this->database->slave->where_in('product_id',$pids);
		$this->database->slave->group_by('product_id');
		
		$query = $this->database->slave->get();
		$result = $query->result_array();
		
		return $result;
	}
	
	//用户对某商品的评论
	public function customerReviewWithPid($userId,$productId){
		$result = array();
		if(!$userId || !$productId || !is_numeric($userId) || !is_numeric($productId)) return $result;
		
		$mem_key = md5("eb_eachbuyer_customer_product_".$productId."_review_".$userId);
		global $mem_expired_time;
		
		//$this->memcache->delete($mem_key);
		if(!$result = $this->memcache->get($mem_key)){
			$this->database->slave->from('eb_review');
			$this->database->slave->where("review_status",STATUS_ACTIVE);
			$this->database->slave->where("customer_id",$userId);
			$this->database->slave->where("product_id",$productId);
			$this->database->slave->order_by('review_time_lastmodified',"desc");
			$query = $this->database->slave->get();
			$result = $query->result_array();
		
			$this->memcache->set($mem_key, $result,$mem_expired_time['customer_product_review']);
		}
		
		return $result;
	}
	
	//用户所有评论
	public function allReviewWithCustomer($userId,$page = 1,$pagesize = 8){
		$result = array();
		if(!$userId || !is_numeric($userId)) return $result;
		
		$mem_key = md5("eb_eachbuyer_customer_all_review_".$userId."_page_".$page."_size_".$pagesize);
		global $mem_expired_time;
		
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_review');
			$this->database->slave->where("review_status",STATUS_ACTIVE);
			$this->database->slave->where("customer_id",$userId);
			$this->database->slave->order_by('review_time_lastmodified',"desc");
			$this->database->slave->limit($pagesize,($page-1)*$pagesize);
			$query = $this->database->slave->get();
			$result = $query->result_array();
		
			$this->memcache->set($mem_key, $result,$mem_expired_time['customer_all_review']);
		}
		
		return $result;
		
	}
	
	/**
	 * 用户的总评论数
	 * @param unknown $user_id 用户id
	 * @param string $status 状态可选 默认是不设置状态过滤条件的
	 * @return number
	 * @author an
	 */
	public function reviewNumsWithUserid($user_id,$status=''){
		$nums = 0;
		if(!$user_id || !is_numeric($user_id)) return $nums;
		
		global $mem_expired_time;
		$mem_key = md5("eb_eachbuyer_customer_all_review_".$user_id);
		$nums = $this->memcache->get($mem_key);
		if($nums === false){
			$this->database->slave->from('eb_review');
			$this->database->slave->where('customer_id',$user_id);
			if(!empty($status)){
				$this->database->slave->where('review_status',$status);
			}
			$nums = $this->database->slave->count_all_results();
		
			$this->memcache->set($mem_key, $nums,$mem_expired_time['customer_all_review']);
		}
		
		return $nums;
	}
	
	/**
	 * 根据用户Id获取评论列表
	 * @param unknown $userId 用户id
	 * @param unknown $params 参数 一般可选 包括select page pagesize status这几种
	 * @return multitype:|unknown
	 * @author an
	 */
	public function reviewListByUserId($userId,$params=array()){
		$result = array();
		if(empty($userId)) return $result;
		
		if(!empty($params['select'])){
			$this->database->slave->select('review_id,product_id,order_id');
		}
		
		if(!empty($params['page']) && !empty($params['pagesize'])){
			$this->database->slave->limit($params['pagesize'],($params['page']-1)*$params['pagesize']);
		}
		
		if(!empty($params['status']))
		{
			$this->database->slave->where('review_status',$params['status']);
		}

		if(!empty($params['product_id']))
		{
			$this->database->slave->where('product_id',$params['product_id']);
		}
		
		$this->database->slave->order_by('review_time_create',"desc");
		
		$result = $this->database->slave->from('eb_review')->where('customer_id',$userId)->get()->result_array();
		return $result;
	}
	
	//temp
	public function orderProductListByUserId($userId,$product_id=''){
		$result = array();
		if(empty($userId)) return $result;
		
		//获取符合条件的订单
		$orders = $this->database->slave->from('eb_order')->where('customer_id',$userId)->where_in('order_status',array(3,5,6,7,8,9))->get()->result_array();
		
		$orderIds = extractColumn($orders, 'order_id');
		
		//获取相应的商品
		if(empty($orderIds)){
			return $result;
		}
		
		if(empty($product_id)){
			$result = $this->database->slave->from('eb_order_product')->where('order_product_price_subtotal > 0')->where_in('order_id',$orderIds)->order_by('order_product_time_create',"desc")->get()->result_array();
		}else{
			$result = $this->database->slave->from('eb_order_product')->where('order_product_price_subtotal > 0')->where_in('order_id',$orderIds)->where('product_id',$product_id)->order_by('order_product_time_create',"desc")->get()->result_array();
		}
		
		return $result;
	}
	
	//temp
	public function attrSkuList($skuArr){
		$result = array();
		if(empty($skuArr)) 
			return $result;
		$result = $this->database->slave->from('eb_complexattr_sku')->where_in('product_sku',$skuArr)->where('complexattr_sku_status',STATUS_ACTIVE)->get()->result_array();
		return $result;
	}
	
	//temp
	public function attrList($attrIds,$language_id=1){
		$result = array();
		if(empty($attrIds))
			return $result;
		$result = $this->database->slave->from('eb_complexattr_lang')->where_in('complexattr_id',$attrIds)->where('language_id',$language_id)->get()->result_array();
		return $result;
	}
	
	//temp
	public function attrValueList($attrValueIds,$language_id=1){
		$result = array();
		if(empty($attrValueIds))
			return $result;
		$result = $this->database->slave->from('eb_complexattr_value_lang')->where_in('complexattr_value_id',$attrValueIds)->where('language_id',$language_id)->get()->result_array();
		return $result;
	}
	
	/**
	 * 创建评论
	 * @param unknown $info
	 * @return boolean
	 * @author an
	 */
	public function createReview($info){
		if(empty($info))
			return false;
		$this->database->master->insert('eb_review', $info);
		return $this->database->master->insert_id();
	}

	/**
	 * 根据评论id获取评论
	 * @param unknown $reviewId
	 * @return array
	 * @author an
	 */
	public function getReviewById($reviewId){
		$result = array();
		if(empty($reviewId))
			return $result;

		$mem_key = md5("eb_eachbuyer_single_review_".$reviewId);
		global $mem_expired_time;
		
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_review');
			$this->database->slave->where("review_id",$reviewId);
			$query = $this->database->slave->get();
			$result = $query->row_array();
			
			$this->memcache->set($mem_key, $result,$mem_expired_time['customer_all_review']);
		}

		return $result;
	}


	/**
	 * 处理点赞和点踩的数量
	 * @param unknown $reviewId 评论id
	 * @param unknown $type 判断增还是减 incr增 decr减
	 * @param unknown $helpful 判断是赞还是踩 1赞2踩
	 * @return boolean
	 * @author an
	 */
	public function processReviewLikeUnlikeCount($reviewId,$type,$helpful){
		
		if($type == 'incr'){
			if($helpful == 1){
				$this->database->master->set('review_count_helpful', 'review_count_helpful+1', false);
			}else{
				$this->database->master->set('review_count_nothelpful', 'review_count_nothelpful+1', false);
			}
		}elseif($type == 'decr'){
			if($helpful == 1){
				$this->database->master->set('review_count_helpful', 'review_count_helpful-1', false);
			}else{
				$this->database->master->set('review_count_nothelpful', 'review_count_nothelpful-1', false);
			}
		}else{
			return false;
		}
		$this->database->master->where('review_id', $reviewId);
		$result = $this->database->master->update('eb_review');
		
		return $result;
	}

}
