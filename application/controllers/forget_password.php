<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Forget_password extends Dcontroller {
	
	public function index(){
		$this->_view_data['tips'] = $this->input->get('tips');
		parent::index();
	}
	
	/**
	 * 发送找回密码邮件
	 * 
	 * @author an
	 */
	public function findPassword(){
		$this->load->model('Emailmodel','m_email');
		$email = $this->input->post('email');
		$email = trim($email);

		$user = $this->customer->getUserByEmail($email);

		//校验邮件是否存在
		if(!is_email($email) || empty($user)){
			redirect(genURL('forget_password',false,array('tips'=>'The Email address was not found in our records,please try again')));
		}

		$todayDate = date('Ymd', $_SERVER['REQUEST_TIME'] );
		$code = md5($user['customer_id'] . 'eachbuyer' . $user['customer_time_create'] . $todayDate );

		$email_reset_url = genURL('reset_password').'?user_id=' . $user['customer_id'] . '&code=' . $code;

		//发送邮件
		$this->load->model('emailmodel','emailmodel');
		global $base_url;//多语言网址数组
		$type = 16;
		$params = array(
				'SITE_DOMAIN' => substr($base_url[currentLanguageId()],0,-1),
				'CS_EMAIL' => 'cs@eachbuyer.com',
				'SITE_DOMAIN1' => COMMON_DOMAIN,
				'USER_NAME' => $user['customer_name'],
				'NEW_PASSWORD' => $email_reset_url,
				'ITEM_REO' => '',
		);

		$result = $this->emailmodel->subscribe_sendMail( $type, currentLanguageId() ,$email,$params);
		if($result){
			redirect(genURL('forget_password',false,array('tips'=>'An email has been sent to your email address')));
		}else{
			redirect(genURL('forget_password',false,array('tips'=>'Email Send Failed.')));
		}
	}
	
	public function checkEmail(){
		$arr = array('status'=>200,'msg'=>'','data'=>array());
		$email = $this->input->post('email');
		$email = trim($email);
		
		if(!is_email( $email ) ){
			$arr['status'] = 2200;
			$arr['msg'] = lang('bbs_error_email_address2');
		}elseif(is_email($email) && !$this->customer->checkEmailUsed($email)){
			$arr['status'] = 2200;
			$arr['msg'] = lang('email_not_exist');
		}else{
			$arr['msg'] = lang('email_exist');
		}
		
		$this->ajaxReturn($arr);
	}
	
}
