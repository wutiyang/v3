<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 商品推荐model
 * @author Administrator
 *
 */
class recommendproductmodel extends CI_Model {
	//private $memcache;
	
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}
	
	/**
	 * @desc 获取货物商品详情
	 * @param unknown $category_id
	 * @param int $status 商品状态值
	 * @return boolean|Ambigous <boolean, string>
	 */
	public function getinfo($category_id,$status = 1){
		$result = array();
		if(!$category_id || !is_numeric($category_id))return $result;
		global $mem_expired_time;
		$mem_key = md5("each_buyer_product_recommend_".$category_id."_status_".$status);
		
		//$this->memcache->delete($mem_key);
		$result = $this->memcache->get($mem_key);
		if($result===false){
			$this->database->slave->from('eb_product_recommend');
			$this->database->slave->where('product_recommend_status',$status);
			$this->database->slave->where('category_id',$category_id);
            $this->database->slave->order_by("product_recommend_sort","desc");
            $this->database->slave->order_by("product_recommend_id");
			$query = $this->database->slave->get();
			$data = $query->result_array();

			if(!empty($data)){
				foreach ($data as $k=>$v){
					$result[ $v['product_id']] =  $k;
				}
                $this->memcache->set($mem_key, $result,$mem_expired_time['category_product_recommend']);
			}

		}
		
		return $result;
	}
	
	
}
