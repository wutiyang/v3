<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Reset_password extends Dcontroller {
	
	public function index(){
		
		$code = $this->input->get('code');
		$user_id = $this->input->get('user_id');
		
		$this->_view_data['codes'] = strval($code);
		$this->_view_data['user_id'] = intval($user_id);
		
		parent::index();
	}
	
	public function process(){
		
		$code = $this->input->post('code');
		$user_id = $this->input->post('user_id');
		$password = trim($this->input->post('password'));
		$password_confirm = trim($this->input->post('password_confirm'));
		
		if($password_confirm != $password){
			$this->session->set('msg_resetpwd',lang('resetpwd_password_need_confirm'));
			redirect(genURL('reset_password')."?user_id={$user_id}&code={$code}");
		}
		if($password === false || strlen($password) < 6){
			$this->session->set('msg_resetpwd',lang('resetpwd_password_short'));
			redirect(genURL('reset_password')."?user_id={$user_id}&code={$code}");
		}
		if(strpos($password,' ') > 0){
			$this->session->set('msg_resetpwd',lang('resetpwd_password_blank'));
			redirect(genURL('reset_password')."?user_id={$user_id}&code={$code}");
		}
		
		$user = $this->customer->getUserById($user_id);
		if(empty($user)){
			$this->session->set('msg_resetpwd','User Not Exist!');
			redirect(genURL('reset_password')."?user_id={$user_id}&code={$code}");
		}
			
		
		$todayDate = date('Ymd', time() );
		$tomorrowDate = date('Ymd', time()+3600*24 );
		$todayCode = md5($user['customer_id'].'eachbuyer'.$user['customer_time_create'].$todayDate);
		$tomorrowCode = md5($user['customer_id'].'eachbuyer'.$user['customer_time_create'].$tomorrowDate);
		
		if( empty($user) || ( $code != $todayCode && $code != $tomorrowCode ) ){
			$this->session->set('msg_resetpwd',lang('resetpwd_error'));
			redirect(genURL('reset_password')."?user_id={$user_id}&code={$code}");
		}
		
		$this->customer->editUser($user['customer_id'],array(
				'customer_password' => $this->customer->hashPassword($password),
		));
		
		$this->session->set('msg_login_flag',true);
		$this->session->set('msg_login',lang('resetpwd_reset_password_success'));
		$this->session->delete('msg_resetpwd');
		
		redirect(genURL('login'));
	}
}