<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc country shipping rule model
 * @author Administrator
 *
 */
class Countryprovincemodel extends CI_Model {
	//private $memcache;
	
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}
	

	/**
	 * @desc 根据国家code，返回国家配送规则
	 * @param unknown $country_code
	 * @return multitype:
	 */
	public function countryProvinceList(){
		global $mem_expired_time;
		$mem_key = md5("each_buye_all_country_province_list");
		
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_country_province');
			$this->database->slave->order_by('country_code','asc');
			$query = $this->database->slave->get();
			$data = $query->result_array();
			
			$result = array();
			foreach ($data as $val){
				$result[$val['country_code']][] = $val['country_province_name']; 
			}
			$this->memcache->set($mem_key, $result,$mem_expired_time['country_shipping_rule']);
		}
		
		return $result;
	}
	
	/**
	 * @desc 获取所有国家列表（按照iso2正向排序）
	 * @return array
	 */
	public function countryList(){
		global $mem_expired_time;
		$mem_key = md5("each_buye_all_country_list");
		
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_country');
			$this->database->slave->order_by('country_iso2','asc');
			$query = $this->database->slave->get();
			$result = $query->result_array();
			
			//$this->memcache->set($mem_key, $result,$mem_expired_time['country_shipping_rule']);
		}
		
		return $result;
	}
	
}