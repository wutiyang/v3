<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc express 国家  快递方式 模型
 * @author Administrator
 *
 */
class Expresszonemodel extends CI_Model {
	/**
	 * @desc 根据zone_id获取对应zone信息
	 * @return unknown
	 */
	public function expressZoneList($zone_id){
		$result = array();
		if(!$zone_id || !is_numeric($zone_id)) return $result;
		$mem_key = md5("eb_eachbuyer_express_zone_".$zone_id);
		global $mem_expired_time;
		
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_express_zone');
			$this->database->slave->where('express_zone_id',$zone_id);
			$query = $this->database->slave->get();
			$result = $query->result_array();
		
			$this->memcache->set($mem_key, $result,$mem_expired_time['express_zone']);
		}
		
		return $result;
	}
	
	/**
	 * @desc 根据国家code获取zone——id编号
	 * @param unknown $country_code
	 * @return multitype:
	 */
	public function expressCountry2Zone($country_code){
		$result = array();
		if(!$country_code || empty($country_code)) return $result;
		$mem_key = md5("eb_eachbuyer_express_country2zone_".$country_code);
		global $mem_expired_time;
	
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_express_country2zone');
			$this->database->slave->where('express_zone_country',strtoupper($country_code));
			$query = $this->database->slave->get();
			$result = $query->result_array();
	
			$this->memcache->set($mem_key, $result,$mem_expired_time['express_country2zone']);
		}
	
		return $result;
	}
}