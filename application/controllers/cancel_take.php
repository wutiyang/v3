<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Cancel_take extends Dcontroller {

	public function __construct(){
		parent::__construct();
		$this->load->language('newsletter',currentLanguageCode());
	}

	public function index(){
        $message_email = $this->input->get('email');
        $hash = $this->input->get('hash');
        if(!$message_email || !$hash)
            redirect(genURL(''));
        $this->session->set('unsubscribe_mail',$message_email);
        $this->_view_data['message_email'] = $message_email;
        $this->_view_data['hash'] = $hash;
		//render page
		parent::index();
	}
}

/* End of file newsletter_result.php */
/* Location: ./application/controllers/default/newsletter_result.php */
