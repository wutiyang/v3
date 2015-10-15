<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc country shipping rule model
 * @author Administrator
 *
 */
class Countryshippmodel extends CI_Model {
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
	public function countryRuleList($country_code){
		$result = array();
		if(empty($country_code)) return $result;
		
		global $mem_expired_time;
		$mem_key = md5("each_buyer_country_shipping_rule_".$country_code);
		
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_country_shipping_rule');
			$this->database->slave->where('country_code',$country_code);
			$query = $this->database->slave->get();
			$result = $query->result_array();
			
			$this->memcache->set($mem_key, $result,$mem_expired_time['country_shipping_rule']);
		}
		
		return $result;
	}
	
	//返回所有国家信息
	public function allCountry(){
		$result = array();
		
		global $mem_expired_time;
		$mem_key = md5("each_buyer_country_all_info");
		
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_country');
			$query = $this->database->slave->get();
			$result = $query->result_array();
				
			$this->memcache->set($mem_key, $result,$mem_expired_time['all_country']);
		}
		
		return $result;
	}
}