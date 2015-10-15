<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';

class Help extends Dcontroller {

	public function index(){
		//render page
		$this->_view_data['name'] = 'help';
		$this->_view_data['title'] = sprintf(lang('title'),lang('help'));
		parent::index();
	}
}

/* End of file faq.php */
/* Location: ./application/controllers/default/faq.php */