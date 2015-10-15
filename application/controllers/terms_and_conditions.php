<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';

class Terms_and_conditions extends Dcontroller {

	public function index(){
		//render page
		$this->_view_data['name'] = 'terms_and_conditions';
		$this->_view_data['title'] = sprintf(lang('title'),lang('terms_and_conditions'));
		parent::index();
	}
}

/* End of file terms_and_conditions.php */
/* Location: ./application/controllers/default/terms_and_conditions.php */