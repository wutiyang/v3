<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 促销详细
 * @author Administrator
 *
 */
class Promotedetailmodel extends CI_Model {

	public function __construct(){
		parent::__construct();
	}
	
	public function getPromoteDetailByPromoteId($promoteId,$status=1){
		if(empty($promoteId)) return array();
		$this->database->slave->from('eb_promotion_detail');
		$this->database->slave->where('promotion_id',$promoteId);
		$this->database->slave->where('promotion_detail_status',$status);
		$this->database->slave->order_by('promotion_detail_sort','desc');
		$query = $this->database->slave->get();
		$list = $query->result_array();
	
		return $list;
	}
	
	public function getPromoteDetailById($promoteId){
		if(empty($promoteId)) return array();
		$this->database->slave->from('eb_promotion_detail');
		$this->database->slave->where('promotion_detail_id',$promoteId);
		$query = $this->database->slave->get();
		$result = $query->row_array();
	
		return $result;
	}
}