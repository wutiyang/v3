<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 促销
 * @author Administrator
 *
 */
class Promotemodel extends CI_Model {

	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 
	 * @param unknown $promoteId
	 * @return multitype:|unknown
	 * @author an
	 */
	public function getPromoteById($promoteId){
		$result = array();
		if(empty($promoteId)) return $result;
		
		$this->database->slave->from('eb_promotion');
		$this->database->slave->where('promotion_id',$promoteId);
		$this->database->slave->limit(1);
		$query = $this->database->slave->get();
		$result = $query->row_array();

		return $result;
	}
}