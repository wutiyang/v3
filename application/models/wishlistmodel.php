<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc wishlist收藏model
 * @author Administrator
 *
 */
class wishlistmodel extends CI_Model {
	public function __construct(){
		
	}
	
	/**
	 * 收藏单个商品的操作
	 * @param  integer $userId 用户id
	 * @param  integer $productId 产品id
	 * @author qcn
	 */
	public function collectProducts($userId = 0, $productId = 0) {
		if(empty($userId) || empty($productId) || !is_numeric($userId) || !is_numeric($productId)) {
			return false;
		}
		$data = array(
				'customer_id' => $userId,
				'product_id' => $productId,
				'wishlist_status'=>STATUS_ACTIVE,
				'wishlist_time_create' => date("Y-m-d H:i:s",requestTime()),
		);
	
		if($this->database->master->insert('eb_wishlist', $data) ) {
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 获得用户收藏商品列表
	 * @param integer $userId
	 * @return Array 返回收藏商品列表
	 * @author lucas
	 */
	public function getUserCollecteList( $userId ,$status = 1){
		$result = array();
		if(!$userId || !is_numeric($userId)) return $result;
	
		$this->database->master->from('eb_wishlist');
		$this->database->master->where('customer_id', $userId);
		$this->database->master->where('wishlist_status', $status);
		$this->database->master->order_by('wishlist_id','desc');
		$query = $this->database->master->get();
		$result = $query->result_array();
	
		return $result;
	}
	
	/**
	 * 取消收藏商品
	 * @param integer $id
	 * @param integer $userId
	 * @return null
	 * @author lucas
	 */
	public function cancelCollect( $id, $userId ){
		if(empty($userId) || empty($id) || !is_numeric($userId) || !is_numeric($id)) {
			return false;
		}
		$data = array(
			'wishlist_status'=>STATUS_DISABLE,
		);
	
		$this->database->master->where('customer_id', $userId);
		$this->database->master->where("product_id",$id);
        $this->database->master->where("wishlist_status",STATUS_ACTIVE);
	
		if($this->database->master->update('eb_wishlist', $data) ) {
			return true;
		}else{
			return false;
		}
	
	}
	
	/**
	 * @desc 根据cart_id更新状态
	 * @param unknown $cart_id
	 * @return boolean|unknown
	 */
	public function updateStatusWithCid($wishlist_id){
		if(!$wishlist_id || !is_numeric($wishlist_id)) return false;
		
		$data['wishlist_status'] = STATUS_ACTIVE;
		$this->database->master->where("wishlist_id",$wishlist_id);
		$result = $this->database->master->update('eb_wishlist', $data);
		return $result;
	}
	
/**
	 * 检查用户是否收藏此商品
	 * @param integer $userId
	 * @param integer $goodsId
	 * @return boolean
	 * @author lucas
	 */
	public function checkUserGoodsCollected( $productId, $userId){
		$this->database->master->from('eb_wishlist');
		$this->database->master->where('customer_id', $userId);
        $this->database->master->where('product_id', $productId);
        $this->database->master->where('wishlist_status', STATUS_ACTIVE);
		
		$query = $this->database->master->get();
		$result = $query->result_array();
		return $result;
		//$count = $this->database->master->count_all_results();
		//return ($count > 0);
	}
	
	//收藏session中商品(批量，只收藏，没有批量取消收藏)
	public function batchCollectProducts($userId = 0, $data){
		if(!$userId || !is_numeric($userId) || empty($data)) return false;
		foreach ($data as $k=>&$v){
			$v['customer_id'] = $userId;
		}
	
		if($this->database->master->insert_batch('eb_wishlist', $data)){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 根据Id获取收藏的内容
	 * @param unknown $id 收藏夹的id
	 * @return multitype:|unknown
	 * @author an
	 */
	public function getCollectById($id){
		$result = array();
		if(empty($id)){
			return $result;
		}
		$result = $this->database->slave->from('eb_wishlist')->where('wishlist_id',$id)->get()->row_array();
		return $result;
	}
}
