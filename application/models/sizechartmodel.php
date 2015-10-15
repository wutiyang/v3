<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 商品sizechart model
 * @author Administrator
 *
 */
class sizechartmodel extends CI_Model {
	//private $memcache;
	
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}
	
	public function sizechartListWithPid($product_id,$language_id = 1){
		$result = array();
		if(!$product_id || !is_numeric($product_id)) return $result;
		if(!$language_id || !is_numeric($language_id)) $language_id = 1;
		
		global $mem_expired_time;
		$mem_key = md5("each_buyer_product_sizechart_".$product_id."_language_".$language_id);
	
		//$this->memcache->delete($mem_key);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_sizechart_product');
			$this->database->slave->where('sizechart_product_status',1);
			$this->database->slave->where('product_id',$product_id);
				
			$query = $this->database->slave->get();
			$result = $query->result_array();
	
			foreach ($result as $k=>$v){
				$lan_size = json_decode($v['sizechart_product_title'],true);
				//if(isset($lan_size[$language_id])) $v['new_product_title'] = $lan_size[$language_id];
				if(isset($lan_size[0][$language_id])) $v['new_product_title'] = $lan_size[0][$language_id];
			}
			
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_sizechart']);
		}
	
		return $result;
	}
	
}