<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 商品model
 * @author Administrator
 *
 */
class widgetimagemodel extends CI_Model {
	//private $memcache;
	
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}
	
	/**
	 * 
	 * @desc 获取店铺数据
	 * @param number $language_id
	 * @param number $verson
	 */
	public function imageList($language_id = 1){
		if(!$language_id || !is_numeric($language_id)) $language_id = 1;
		global $mem_expired_time;
		
		$mem_key = md5("each_buyer_widget_image_".$language_id."_".WIDGET_IMAGE_VERSION);
		//$this->memcache->delete($mem_key);
		$list = $this->memcache->get($mem_key);
		if($list === false){
			$this->database->slave->from('eb_widget_image');
			$this->database->slave->where('widget_image_version',WIDGET_IMAGE_VERSION);
			$this->database->slave->order_by('widget_image_id','asc');
			$query = $this->database->slave->get();
			$data = $query->result_array();
			
			$list = array();
			foreach ($data as $k=>$v){
				$key = $v['widget_image_position'];
				$list[$key] = $v;
				
				$lan_title_array = json_decode($v['widget_image_title'],true);
				$lan_url_array = json_decode($v['widget_image_url'],true);
				$lan_image_array = json_decode($v['widget_image_image'],true);
				$default_title = isset($lan_title_array[1])?$lan_title_array[1]:null;
				$default_url = isset($lan_url_array[1])?$lan_url_array[1]:null;
				$default_image = isset($lan_image_array[1])?$lan_image_array[1]:null;
				
				$list[$key]['lan_title'] = isset($lan_title_array[$language_id])?$lan_title_array[$language_id]:$default_title;
				$list[$key]['lan_url'] = isset($lan_url_array[$language_id])?$lan_url_array[$language_id]:$default_url;
				$list[$key]['lan_image'] = isset($lan_image_array[$language_id])?$lan_image_array[$language_id]:$default_image;
			}
			
			$this->memcache->set($mem_key, $list,$mem_expired_time['widget_image']);
		}
		
		return $list;
	}
}