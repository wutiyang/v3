<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';

class About_us extends Dcontroller {

	public function index(){
		//render page
		$this->_view_data['name'] = 'about_us';
		$this->_view_data['title'] = sprintf(lang('title'),lang('about_us'));
		parent::index();
	}


}

/* End of file about_us.php */
/* Location: ./application/controllers/default/about_us.php */