<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Wholesale extends Dcontroller {
	
	public function index(){
		$this->_view_data['title'] = sprintf(lang('title'),lang('wholesale'));
		parent::index();
	}
}