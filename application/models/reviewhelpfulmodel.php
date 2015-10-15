<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 评论model
 * @author Administrator
 *
 */
class reviewhelpfulmodel extends CI_Model {
	//private $memcache;
	
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}
	
	/**
	 * 获取商品的所有顶踩信息
	 * @param unknown $productId 评论id
	 * @param unknown $userId 判断增还是减 incr增 decr减
	 * @return array
	 * @author an
	 */
	public function getHelpfulInfos($productId,$userId){
		$result = array();
		if(empty($productId) || empty($userId)){
			return $result;
		}
		$this->database->slave->from('eb_review_helpful');
		$this->database->slave->where("product_id",$productId);
		$this->database->slave->where("customer_id",$userId);
		$this->database->slave->order_by('review_helpful_time_create',"desc");
		$result = $this->database->slave->get()->result_array();
		return $result;
	}
	
	public function getHelpfulInfosByReviewIds($reviewIds,$userId){
		$result = array();
		if(empty($reviewIds) || empty($userId)){
			return $result;
		}
		$this->database->slave->from('eb_review_helpful');
		$this->database->slave->where_in("review_id",$reviewIds);
		$this->database->slave->where("customer_id",$userId);
		$this->database->slave->order_by('review_helpful_time_create',"desc");
		$result = $this->database->slave->get()->result_array();
		return $result;
	}

	/**
	 * 根据评论id获取相应的顶踩信息
	 * @param unknown $reviewId 评论id
	 * @param unknown $userId 用户id
	 * @return array
	 * @author an
	 */
	public function getHelpInfoByReviewId($reviewId,$userId){
		$result = array();
		if(empty($reviewId) || empty($userId))
			return $result;

		$this->database->slave->from('eb_review_helpful');
		$this->database->slave->where("review_id",$reviewId);
		$this->database->slave->where("customer_id",$userId);
		$query = $this->database->slave->get();
		$result = $query->row_array();

		return $result;
	}

	/**
	 * 删除一条顶踩信息
	 * @param unknown $id 顶踩信息的id
	 * @return boolean
	 * @author an
	 */
	public function deleteHelpful($id){
		if(empty($id))
			return false;
		$this->database->master->where('review_helpful_id', $id);
		$this->database->master->from('eb_review_helpful');
		return $this->database->master->delete();
	}

	/**
	 * 创建一条顶踩信息
	 * @param array $info 顶踩信息的组合
	 * @return boolean
	 * @author an
	 */
	public function createHelpful($info) {
		if(empty($info))
			return false;
		$this->database->master->insert('eb_review_helpful', $info);
		return $this->database->master->insert_id();
	}
}
