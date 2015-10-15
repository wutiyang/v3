<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 问答 model
 * @author Administrator
 *
 */
class Couponhistorymodel extends CI_Model {
	//private $memcache;
	
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}

	/**
	 * @desc 新建coupon使用记录
	 * @param unknown $data
	 * @return boolean|unknown
	 */
	public function createHistory($data){
		if(empty($data)) return false;
		$this->database->master->insert('eb_coupon_history', $data);
		$id = $this->database->master->insert_id();
		return $id;
	}
	
	/**
	 * @desc 查询某coupon使用使用记录
	 * @param unknown $coupon
	 * @return multitype:|unknown
	 */
	public function couponHistoryList($coupon){
		$result = array();
		if(empty($coupon)) return $result;
		$this->database->master->from('eb_coupon_history');
		$this->database->master->where('coupon_code',$coupon);
		//$this->database->master->where('subscribe_status_coupon',0);
		//$this->database->master->limit(1);
		$query = $this->database->master->get();
		$res = $query->row_array();
		return $res;
	}
}