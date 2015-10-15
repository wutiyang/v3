<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Newsletter_result extends Dcontroller {

	public function __construct(){
		parent::__construct();
		$this->load->language('newsletter',currentLanguageCode());
	}

	public function index(){
		/*$message_url = $this->session->get('newsletter_msg_url');
		$message_flg = $this->session->get('newsletter_msg_flag');
		$message_title = $this->session->get('newsletter_msg_title');
		$message_content = $this->session->get('newsletter_msg_content');

		if($message_url === false) $message_url = genURL('newsletter_public');
		if($message_title === false) $message_title = lang('subscribe_is_unsubscribed_title');
		if($message_content === false) $message_content = lang('subscribe_fail');

		$this->_view_data['message_url'] = $message_url;
		$this->_view_data['message_flg'] = $message_flg;
		$this->_view_data['message_title'] = $message_title;
		$this->_view_data['message_content'] = $message_content;

		$this->_view_data['head']['title'] = 'Newsletter Subscribe - ' . COMMON_DOMAIN;
		*/
		$message_email = $this->session->get('newsletter_msg_email');
		
		$this->_view_data['message_email'] = $message_email;
		//render page
		parent::index();
	}
}

/* End of file newsletter_result.php */
/* Location: ./application/controllers/default/newsletter_result.php */