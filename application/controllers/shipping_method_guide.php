<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';

class Shipping_method_guide extends Dcontroller {

	public function index(){

		/*
		 * head banner display
		 */
		//$this->_view_data['headBannerDisabled'] = true;
		$this->_view_data['name'] = 'shipping_method_guide';
		$this->_view_data['title'] = sprintf(lang('title'),lang('shipping_method_guide'));
		//render page
		parent::index();
	}
}

/* End of file shipping_method_guide.php */
/* Location: ./application/controllers/default/shipping_method_guide.php */