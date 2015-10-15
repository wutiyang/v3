<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 汇率模型
 * @author Administrator
 *
 */
class currencymodel extends CI_Model {
	private $nocny_currencyList = array();
	private $all_currencyList = array();
	private $countryList = array();
	
	public function __construct(){
		parent::__construct();
		
	}
	//除人民币外所有货币
	public function get_nocny_currencyList(){
		if(empty($this->nocny_currencyList)){
			$all_currencyList = $this->get_all_currencyList();
			foreach ($all_currencyList as $k=>$v){
				if($v['currency_code']=='CNY') unset($all_currencyList[$k]);
			}
			$this->nocny_currencyList = $all_currencyList;
		}
		return $this->nocny_currencyList;
	}
	
	//全部货币
	public function get_all_currencyList(){
		if(empty($this->all_currencyList)){
			$mem_key = md5("eb_eachbuyer_currency_1");
			global $mem_expired_time;
				
			$list = $this->memcache->get($mem_key);
			if($list === false){
				$this->database->slave->from('eb_currency');
				//$this->database->slave->where_not_in('currency_code',"CNY");
				$this->database->slave->order_by('currency_id',"asc");
				$query = $this->database->slave->get();
				$list = $query->result_array();
					
				$this->memcache->set($mem_key, $list,$mem_expired_time['base_currency']);
			}
				
			$this->all_currencyList = $list;
		}
		return $this->all_currencyList;
	}
	
	/**
	 * @desc 获取所有汇率信息
	 * @return unknown
	 */
	public function currencyList($cn=false){
		if($cn===false){
			$list = $this->get_nocny_currencyList();
		}else{
			$list = $this->get_all_currencyList();
		}

		return $list;
	}
	public function currencyList_bak($cn=false){
		$mem_key = md5("eb_eachbuyer_currency_".(int) $cn);
		global $mem_expired_time;
	
		$list = $this->memcache->get($mem_key);
		if($list === false){
			$this->database->slave->from('eb_currency');
			if($cn===false){
				$this->database->slave->where_not_in('currency_code',"CNY");
			}
			$this->database->slave->order_by('currency_id',"asc");
			$query = $this->database->slave->get();
			$list = $query->result_array();
	
			$this->memcache->set($mem_key, $list,$mem_expired_time['base_currency']);
		}
	
		//$this->currencyList = $list;
		return $list;
	}
	/**
	 * @desc获取当前汇率
	 * @return Ambigous <multitype:, unknown, boolean, string>
	 */
	public function todayCurrency(){
		$return = array();
		$code = currentCurrency();
		$currency_list = $this->currencyList();
		foreach ($currency_list as $val){
			if($val['currency_code']==$code){
				$return = $val;
			}	
		}
		return $return;
	}
	
	public function allCountry($cn=false){
		if(empty($this->countryList)){
			$mem_key = md5("eb_eachbuyer_country");
			global $mem_expired_time;
			
			$list = $this->memcache->get($mem_key);
			if($list === false){
				$this->database->slave->from('eb_country');
				$this->database->slave->order_by('country_name',"asc");
				$query = $this->database->slave->get();
				$list = $query->result_array();
			
				$this->memcache->set($mem_key, $list,$mem_expired_time['place_order_country']);
			}
			
			$this->countryList = $list;
		}
		
		return $this->countryList;
		
	}
	
	/**
	 * 获取货币的配置（汇率和货币格式）
	 * @param  string $key     货币标示
	 * @param  string $default 默认返回
	 */
	public function getConfigCurrency($key = 'USD',$default = '') {
		$result = $this->currencyList();
		$resultMc = array();
		if( !empty( $result ) && is_array( $result ) ){
			foreach ( $result as $v ){
				$resultMc[ trim( $v['currency_code'] ) ] = $v ;
			}
			$result = $resultMc ;
		}
		
		$return = array( 'currency_code' => $key , 'currency_rate' => $default, 'currency_format' => '$%s' );
		if( isset( $result[ trim( $key ) ] ) ){
			$return = $result[ trim( $key ) ] ;
		}
		return $return;
	}
	
	public function currentCurrency(){
		$currency = $this->session->get('currency');
		if($currency === false) $currency = DEFAULT_CURRENCY;
	
		return $currency;
	}
}