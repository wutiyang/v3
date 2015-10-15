<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Login_facebook extends Dcontroller {
	
	public function index(){
		$facebook_id = $this->session->get('sso_facebook_id');
		$facebook_name = $this->session->get('sso_facebook_name');
		$facebook_email = $this->session->get('sso_facebook_email');

		if($facebook_id === false || $facebook_name === false || $facebook_email === false) redirect(genUrl('login'));
		
		$this->_view_data['facebook_email'] = $facebook_email;
		parent::index();
	}

	public function authenticate(){
		$facebook_id = $this->session->get('sso_facebook_id');
		$facebook_name = $this->session->get('sso_facebook_name');
		$facebook_email = $this->session->get('sso_facebook_email');

		if($facebook_id === false || $facebook_name === false || $facebook_email === false){
			$this->ajaxReturn(array(
				'status'=>200,
				'msg'=>'',
				'data'=>array('url' => genUrl('login'))
			));
		}

		$password = $this->input->post('password');
		$password = strval($password);

		//根据传入的信息获取用户信息
		$user = $this->customer->getUserByEmail( $facebook_email );
		
		//校验用户名密码
		if( empty( $user ) || $user['customer_status'] != STATUS_ACTIVE || !$this->customer->validatePassword( $user['customer_password'], $password )){
			$this->ajaxReturn(array(
				'status'=>0,
				'msg'=>lang('p_login_failure'),
			));
		}
		
		//将相关信息放入session(判断是否登录及获取用户信息)
		$this->session->set('user_id', $user['customer_id']);
		$this->session->set('user_name', $user['customer_name']);
		$this->session->set('email', $user['customer_email']);
		
		//统计信息 email小写
		$tongjiUserData = array('UserID'=>$user['customer_id'],'HashedEmail'=>md5(strtolower($user['customer_email'])));
		$this->session->set('tongji_userdata', json_encode($tongjiUserData));
		
		$this->customer->updateUserLoginInfo( $user['customer_id'] );
		$this->customer->updateUserInfo($user['customer_id'],array(
			'customer_sso_type' => SSO_TYPE_FACEBOOK,
			'customer_sso_id' => $facebook_id,
			'customer_sso_email' => $facebook_email,
		));
		
		//合并购物车
		$this->mergeCart();

		$this->session->delete('sso_facebook_id');
		$this->session->delete('sso_facebook_name');
		$this->session->delete('sso_facebook_email');
		$this->session->delete('sso_source');

		$this->ajaxReturn(array(
			'status'=>200,
			'msg'=>'',
			'data'=>array('url' => genUrl('order_list'))
		));
	}

	public function register(){
		$facebook_id = $this->session->get('sso_facebook_id');
		$facebook_name = $this->session->get('sso_facebook_name');
		$facebook_email = $this->session->get('sso_facebook_email');
		$source = $this->session->get('sso_source');
		if($source===false) $source=1;

		if($facebook_id === false || $facebook_name === false || $facebook_email === false){
			$this->ajaxReturn(array(
				'status'=>200,
				'msg'=>'',
				'data'=>array('url' => genUrl('login'))
			));
		}

		$email = $this->input->post('email');
		$email = strval($email);

		//根据传入的信息获取用户信息
		$user = $this->customer->getUserByEmail( $email );
		
		if( !empty( $user )){
			$this->ajaxReturn(array(
				'status'=>2200,
				'msg'=>lang('email_exist'),
			));
		}

		$userByName = $this->customer->getUserByName($facebook_name);
		if(!empty($userByName)) $facebook_name .= time();

		//插入数据
		$userId = $this->customer->createUser(array(
			'customer_name' => $facebook_name,
			'customer_email' => $email,
			'customer_password' => '',
			'customer_time_create' => date('Y-m-d H:i:s',time()),
			'customer_sso_type' => SSO_TYPE_FACEBOOK,
			'customer_sso_id' => $facebook_id,
			'customer_sso_email' => $facebook_email,
			'customer_source' => $source,
			'customer_count_visit' => 1,
			'customer_ip' => $this->input->ip_address(),
			'customer_time_lastlogin' => date('Y-m-d H:i:s',time()),
			'customer_status' => STATUS_ACTIVE ,
		));
		$user = $this->customer->getUserById($userId);
		
		//登陆处理
		$this->session->set('user_id', $user['customer_id']);
		$this->session->set('user_name', $user['customer_name']);
		$this->session->set('email', $user['customer_email']);
		
		$tongjiUserData = array('UserID'=>$user['customer_id'],'HashedEmail'=>md5(strtolower($user['customer_email'])));
		$this->session->set('tongji_userdata', json_encode($tongjiUserData));
		
		//welcome_register邮件
		$this->load->model('emailmodel','emailmodel');
		$params = array(
			'SITE_DOMAIN' => trim(genUrl(),'/'),
			'CS_EMAIL' => 'cs@eachbuyer.com',
			'SITE_DOMAIN1' => COMMON_DOMAIN,
			'USER_NAME' => $user['customer_name'],
			'ITEM_REO' => '',
		);
		$result = $this->emailmodel->subscribe_sendMail(13, currentLanguageId() ,$user['customer_email'],$params);

		//合并购物车
		$this->mergeCart();

		$this->session->delete('sso_facebook_id');
		$this->session->delete('sso_facebook_name');
		$this->session->delete('sso_facebook_email');
		$this->session->delete('sso_source');

		$this->ajaxReturn(array(
			'status'=>200,
			'msg'=>'',
			'data'=>array('url' => genUrl('order_list'))
		));
	}

	private function mergeCart(){
		$user_id = $this->session->get("user_id");
		$session_id = $this->session->sessionID();
		
		$this->load->model("cartmodel","cart");
		$result = $this->cart->mergeCart($user_id,$session_id);
	}
}
