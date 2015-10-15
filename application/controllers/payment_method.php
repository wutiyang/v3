<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';

class Payment_method extends Dcontroller {

	public function index(){
		/*
		 * head banner display
		 */
		//$this->_view_data['headBannerDisabled'] = true;
		$this->_view_data['name'] = 'payment_method';
		$this->_view_data['title'] = sprintf(lang('title'),lang('payment_method'));
		//render page
		parent::index();
	}
}

/* End of file payment_method.php */
/* Location: ./application/controllers/default/payment_method.php */