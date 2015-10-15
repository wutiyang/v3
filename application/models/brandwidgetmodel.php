<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc brand_widget品牌热门商品model
 * @author Administrator
 *
 */
class brandwidgetmodel extends CI_Model {
	//private $memcache;
	
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}
	
	public function getBrandWidgetList(){
		$list = array();
		
		$mem_key = md5("each_buyer_widget_brand");
		global $mem_expired_time;
		$list = $this->memcache->get($mem_key);
		if($list === false) {
			$this->database->slave->from('eb_widget_brand');
			$this->database->slave->where("widget_brand_status",STATUS_ACTIVE);
			$this->database->slave->order_by('widget_brand_sort','desc');
			$this->database->slave->order_by('product_id','asc');
			$query = $this->database->slave->get();
		
			$list = $query->result_array();
		
			$this->memcache->set($mem_key, $list,$mem_expired_time['widget_brand']);
		}
		
		return $list;
	}
	
}
