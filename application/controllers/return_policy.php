<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';

class Return_policy extends Dcontroller {

	public function index(){
		//render page
		$this->_view_data['name'] = 'return_policy';
		$this->_view_data['title'] = sprintf(lang('title'),lang('return_policy'));
		parent::index();
	}
}

/* End of file return_policy.php */
/* Location: ./application/controllers/default/return_policy.php */