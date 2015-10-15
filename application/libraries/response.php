<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Response {

	protected $_items = array();
	protected $_totalCount = 0;
	protected $_detail = '';
	protected $_success = FALSE;
	protected $_addon = array();

	public function __construct(){}

	public function setItems($input){
		if(!is_array($input)) $input = array($input);
		$this->_items = $input;
	}

	public function setTotalCount($input = 0){
		$this->_totalCount = intval($input);
	}

	public function setMessage($input){
		$this->_detail = trim($input);
	}

	public function setSuccess(){
		$this->_success = TRUE;
	}

	public function setAddon($input){
		if(!is_array($input)) $input = array($input);
		$this->_addon = $input;
	}

	public function responseExit(){
		if($this->_totalCount == 0 && count($this->_items) > 0){
			$this->_totalCount = count($this->_items);
		}
		$resArray = array(
			'items'      => $this->_items,
			'totalCount' => $this->_totalCount,
			'detail'     => $this->_detail,
			'success'    => $this->_success,
			'addon'      => $this->_addon,
		);

		echo json_encode($resArray);
		exit();
	}
}

/* End of file response.php */
/* Location: ./application/libraries/response.php */