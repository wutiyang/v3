<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';

class Faq extends Dcontroller {

	public function index(){
		//render page
		$this->_view_data['name'] = 'faq';
		$this->_view_data['title'] = sprintf(lang('title'),lang('faq'));
		parent::index();
	}
}

/* End of file faq.php */
/* Location: ./application/controllers/default/faq.php */