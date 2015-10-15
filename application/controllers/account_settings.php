<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Account_settings extends Dcontroller {
	public function index(){
		//判断登陆状态
		if(!$this->customer->checkUserLogin()) {
			redirect(genURL('login'));
		}
		
		$this->_view_data['userid'] = $this->customer->getCurrentUserId();
		$this->_view_data['username'] = $this->customer->getCurrentUserName();
		$this->_view_data['email'] = $this->customer->getCurrentUserEmail();
		
		//当前页名称 处理account中左侧选中的
		$this->_view_data['currentPage'] = 'settings';

		//个人中心广告位
		$this->load->model("imageadmodel","imagead");
		$image_ads = $this->imagead->getLocationWithId(5);

		$image_ad = '';
		foreach ($image_ads as $ad) {
			if(strtotime($ad['ad_time_start']) < time() && strtotime($ad['ad_time_end']) > time()){
				$ad['ad_content'] = json_decode($ad['ad_content'],true);
				$image_ad = $ad['ad_content'][currentLanguageId()];
				break;
			}
		}
		$this->_view_data['image_ad'] = $image_ad;


		parent::index();
	}
	
	/**
	 * ajax形式修改用户昵称
	 * 
	 */
	public function ajaxModifyUsername(){
		$arr = array('status'=>200,'msg'=>'','data'=>array());
		//判断登陆状态
		if(!$this->customer->checkUserLogin()) {
			$arr['status'] = 1007;
			$this->ajaxReturn($arr);
		}
		
		$username = trim($this->input->post('user_name'));
		$currentUsername = $this->customer->getCurrentUserName();
		$currentUserId = $this->customer->getCurrentUserId();
		
		$username = htmlspecialchars($username);
		
		//判断是否为空
		if(empty($username)){
			$arr['status'] = 2200;
			$arr['msg'] = sprintf(lang('field_required_tips',lang('nickname')));
			$this->ajaxReturn($arr);
		}

		//username长度不要超过34
		if(strlen($username) > 34){
			$arr['status'] = 2200;
			$arr['msg'] = lang('nickname_length_tips');
			$this->ajaxReturn($arr);
		}
		
		//是否未做修改
		if($username == $currentUsername){
			$arr['status'] = 2200;
			$arr['msg'] = 'No Modify!';
			$this->ajaxReturn($arr);
		}
		
		//是否已经存在
		if($this->customer->checkUserNameUsed($username)){
			$arr['status'] = 2200;
			$arr['msg'] = lang('nickname_exists_tips');
			$this->ajaxReturn($arr);
		}
		
		//操作数据库
		$flag = $this->customer->editUser($currentUserId,array(
			'customer_name' => $username,
		));
		
		if($flag){
			$this->session->set('user_name', $username);

			//取消以前的记住密码登陆
			// unset_cookie('ECS[customer_id]');
			// unset_cookie('ECS[customer_name]');
			// unset_cookie('ECS[customer_password]');

			setcookie('ECS[user_id]','',time()-1,'/',COMMON_DOMAIN);
			setcookie('ECS[user_name]','',time()-1,'/',COMMON_DOMAIN);
			setcookie('ECS[password]','',time()-1,'/',COMMON_DOMAIN);

			$arr['msg'] = 'Modify Successfully!';
			$this->ajaxReturn($arr);
		}else{
			$arr['status'] = 2200;
			$arr['msg'] = 'Failed,Please Retry!';
			$this->ajaxReturn($arr);
		}
	}
	
	/**
	 * ajax形式修改邮箱
	 * 
	 */
	public function ajaxModifyEmail(){
		$arr = array('status'=>200,'msg'=>'','data'=>array());
		//判断登陆状态
		if(!$this->customer->checkUserLogin()) {
			$arr['status'] = 1007;
			$this->ajaxReturn($arr);
		}
		
		$email = trim($this->input->post('email'));
		$currentEmail = $this->customer->getCurrentUserEmail();
		$currentUserId = $this->customer->getCurrentUserId();
		
		//判断是否为空
		if(empty($email)){
			$arr['status'] = 2200;
			$arr['msg'] = sprintf(lang('field_required_tips',lang('email')));
			$this->ajaxReturn($arr);
		}
	
		//是否未做修改
		if($email == $currentEmail){
			$arr['status'] = 2200;
			$arr['msg'] = 'No Modify!';
			$this->ajaxReturn($arr);
		}
	
		//是否已经存在
		if($this->customer->checkEmailUsed($email)){
			$arr['status'] = 2200;
			$arr['msg'] = 'Email has Used!';
			$this->ajaxReturn($arr);
		}
	
		//操作数据库
		$flag = $this->customer->editUser($currentUserId,array(
			'customer_email' => $email,
		));
		
		if($flag){
			$this->session->set('email', $email);
			//TODO 是否重新发送邮箱激活
			//取消以前的记住密码登陆
			// unset_cookie('ECS[customer_id]');
			// unset_cookie('ECS[customer_name]');
			// unset_cookie('ECS[customer_password]');

			setcookie('ECS[user_id]','',time()-1,'/',COMMON_DOMAIN);
			setcookie('ECS[user_name]','',time()-1,'/',COMMON_DOMAIN);
			setcookie('ECS[password]','',time()-1,'/',COMMON_DOMAIN);

			$arr['msg'] = 'Modify Successfully!';
			$this->ajaxReturn($arr);
		}else{
			$arr['status'] = 2200;
			$arr['msg'] = 'Failed,Please Retry!';
			$this->ajaxReturn($arr);
		}
	}
	
	/**
	 * ajax形式修改密码
	 * 
	 */
	public function ajaxModifyPassword(){
		$arr = array('status'=>200,'msg'=>'','data'=>array());
		//判断登陆状态
		if(!$this->customer->checkUserLogin()) {
			$arr['status'] = 1007;
			$this->ajaxReturn($arr);
		}
		
		$password = trim($this->input->post('password'));
		$current_password = trim($this->input->post('current_password'));
		$confirm_password = trim($this->input->post('confirm_password'));
		$currentUserId = $this->customer->getCurrentUserId();
		
		//判断密码输入是否为空
		if(strpos($password,' ') > 0){
			$arr['status'] = 2200;
			$arr['msg'] = lang('confirm_password_blank');
			$this->ajaxReturn($arr);
		}
		
		//判断是否两次密码输入一致
		if($confirm_password != $password){
			$arr['status'] = 2200;
			$arr['msg'] = lang('confirm_password_invalid');
			$this->ajaxReturn($arr);
		}
		
		//判断密码长度是否符合要求
		if($password === false || strlen($password) < 6){
			$arr['status'] = 2200;
			$arr['msg'] = lang('confirm_password_shorter');
			$this->ajaxReturn($arr);
		}
		
		$user = $this->customer->getUserById($currentUserId);
		
		//校验密码与当前库密码是否一致
		if(!$this->customer->validatePassword($user['customer_password'],$current_password)){
			$arr['status'] = 2200;
			$arr['msg'] = lang('confirm_password_current_invalid');
			$this->ajaxReturn($arr);
		}
		
		//进行修改
		$flag = $this->customer->editUser($currentUserId,array(
			'customer_password' => $this->customer->hashPassword($password),
		));
		
		if($flag){
			//取消以前的记住密码登陆
			// unset_cookie('ECS[customer_id]');
			// unset_cookie('ECS[customer_name]');
			// unset_cookie('ECS[customer_password]');

			setcookie('ECS[user_id]','',time()-1,'/',COMMON_DOMAIN);
			setcookie('ECS[user_name]','',time()-1,'/',COMMON_DOMAIN);
			setcookie('ECS[password]','',time()-1,'/',COMMON_DOMAIN);
			
			$arr['msg'] = 'Modify Successfully!';
			$this->ajaxReturn($arr);
		}else{
			$arr['status'] = 2200;
			$arr['msg'] = 'Failed,Please Retry!';
			$this->ajaxReturn($arr);
		}
	}
}