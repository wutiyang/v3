<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 商品图片 model
 * @author Administrator
 *
 */
class imageproductmodel extends CI_Model {
	//private $memcache;
	
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}
	
	//返回商品图片，根据商品id
	public function imageListWithPid($product_id){
		$result = array();
		if(!$product_id || !is_numeric($product_id)) return $result;
		
		global $mem_expired_time;
		$mem_key = md5("each_buyer_product_gallery_".$product_id);
		
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_product_gallery');
			$this->database->slave->where('product_gallery_status',1);
			$this->database->slave->where('product_id',$product_id);
			$this->database->slave->order_by('product_gallery_sort','desc');
			
			$query = $this->database->slave->get();
			$result = $query->result_array();
				
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_vice_image']);
		}
		
		return $result;
	}
	//返回商品图片，根据商品id
	public function imageListWithPid_bak($product_id){
		$result = array();
		if(!$product_id || !is_numeric($product_id)) return $result;
	
		global $mem_expired_time;
		$mem_key = md5("each_buyer_product_image_".$product_id);
	
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_product_image');
			$this->database->slave->where('product_image_status',1);
			$this->database->slave->where('product_id',$product_id);
			$this->database->slave->order_by('product_image_sort','desc');
				
			$query = $this->database->slave->get();
			$result = $query->result_array();
	
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_vice_image']);
		}
	
		return $result;
	}
	
}