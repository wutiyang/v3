<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Newsletter_success extends Dcontroller {

	public function __construct(){
		parent::__construct();
	}

	public function index(){
		//render page
        $email = $this->session->get('newsletter_msg_email');
		$newsletter_first = $this->session->get('newsletter_first');
        $this->_view_data['newsletter_first'] = $newsletter_first;
        $this->_view_data['email'] = $email;
		parent::index();
	}

}

/* End of file newsletter_public.php */
/* Location: ./application/controllers/default/newsletter_public.php */
