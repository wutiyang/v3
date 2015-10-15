<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc widget商品model
 * @author Administrator
 *
 */
class widgetproductmodel extends CI_Model {
	//private $memcache;
	
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}
	
	public function getIndexDeal($lan_id){
		if(!$lan_id || !is_numeric($lan_id)) $lan_id = 1;
		$mem_key = md5("each_buyer_index_deal_specal_".$lan_id);
		global $mem_expired_time;
		
		//widget_product商品表数据
		$data = $this->memcache->get($mem_key);
		if($data === false){
			$this->database->slave->from('eb_widget_product');
			$this->database->slave->where('widget_product_status',1);
			$this->database->slave->order_by('widget_product_sort','desc');
			$query = $this->database->slave->get();
			$data = $query->result_array();
			
			$this->memcache->set($mem_key, $data,$mem_expired_time['index_detal']);
		}
		
		return $data;
	}
	
}