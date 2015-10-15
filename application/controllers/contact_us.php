<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';

class Contact_us extends Dcontroller {

	public function index(){
		//render page
		$this->_view_data['name'] = 'contact_us';
		$this->_view_data['title'] = sprintf(lang('title'),lang('contact_us'));
		parent::index();
	}
}

/* End of file contact_us.php */
/* Location: ./application/controllers/default/contact_us.php */