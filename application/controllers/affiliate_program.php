<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';

class Affiliate_program extends Dcontroller {

	public function index(){
		//render page
		$this->_view_data['name'] = 'affiliate_program';
		$this->_view_data['title'] = sprintf(lang('title'),lang('affiliate_program'));
		parent::index();
	}
}

/* End of file affiliate_program.php */
/* Location: ./application/controllers/default/affiliate_program.php */