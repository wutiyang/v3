<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 黑名单
 * @author Administrator
 *
 */
class blacklistmodel extends CI_Model {
	
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * @desc 查询用户是否在黑名单(根据id，及email)
	 * @param unknown $customer_id
	 * @param unknown $email
	 */
	public function genInfo($customer_id = 0,$email = null){
		if(!is_numeric($customer_id) || !$customer_id || empty($email)) return false;
		
		$this->database->master->from("eb_blacklist");
		$this->database->master->where_in("blacklist_content",array($customer_id,$email) );
		$this->database->master->where("blacklist_status",STATUS_ACTIVE);
		$query = $this->database->master->get();
		$num = $query->row_array();
		//echo $this->database->master->last_query();die;
		return $num;
	}
	
	/**
	 * @desc 查询用户是否在黑名单(根据id)
	 * @param number $customer_id
	 */
	public function getInfoById($customer_id = 0){
		if(!is_numeric($customer_id) || !$customer_id) return false;
		
		$this->database->master->from("eb_blacklist");
		$this->database->master->where("blacklist_type",1);
		$this->database->master->where("blacklist_content",$customer_id);
		$this->database->master->where("blacklist_status",STATUS_ACTIVE);
		$query = $this->database->master->get();
		$num = $query->row_array();
		return $num;
	}
	
	/**
	 * @desc 查询用户是否在黑名单(根据email)
	 * @param string $email
	 */
	public function getInfoByEmail($email = null){
		if(empty($email)) return false;
		
		$this->database->master->from("eb_blacklist");
		$this->database->master->where("blacklist_type",2);
		$this->database->master->where("blacklist_content",trim($email));
		$this->database->master->where("blacklist_status",STATUS_ACTIVE);
		$query = $this->database->master->get();
		$num = $query->row_array();
		return $num;
	}
	
}