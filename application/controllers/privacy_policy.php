<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';

class Privacy_policy extends Dcontroller {

	public function index(){
		//render page
		$this->_view_data['name'] = 'privacy_policy';
		$this->_view_data['title'] = sprintf(lang('title'),lang('privacy_policy'));
		parent::index();
	}
}

/* End of file privacy_policy.php */
/* Location: ./application/controllers/default/privacy_policy.php */