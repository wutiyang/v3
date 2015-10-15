<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 用户model
 * @author Administrator
 *
 */
class customermodel extends CI_Model {
	//private $memcache;
	
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}
	
	public function checkUserLogin(){
		$user_id = $this->session->get('user_id');
		
		if($user_id === false || $user_id <= 0){
			return false;
		}else{
			return true;
		}
	}
	
	/**
	 * 获取当前登陆用户的ID
	 * @return number 用户ID
	 */
	public function getCurrentUserId(){
		$user_id = 0;
		if($this->checkUserLogin()){
			$user_id = $this->session->get('user_id');
			$user_id = intval($user_id);
		}
	
		return $user_id;
	}
	
	/**
	 * 获取当前登陆用户的用户名
	 * @return string 用户名
	 */
	public function getCurrentUserName(){
		$user_name = '';
		if($this->checkUserLogin()){
			$user_name = $this->session->get('user_name');
			$user_name = strval($user_name);
		}
	
		return $user_name;
	}
	
	/**
	 * 获取当前登陆用户的邮箱
	 * @return string 邮箱
	 */
	public function getCurrentUserEmail(){
		$email = '';
		if($this->checkUserLogin()){
			$email = $this->session->get('email');
			$email = strval($email);
		}
	
		return $email;
	}
	
	/**
	 * 通过邮箱获取用户信息
	 * @param unknown $email 邮箱
	 * @return unknown 用户信息
	 */
	public function getUserByEmail($email){
		if(empty($email)) return array();
		
		$user = $this->database->master->from('eb_customer')->where('customer_email',$email)->get()->row_array();
		return $user;
	}
	
	/**
	 * 通过用户名获取用户信息
	 * @param unknown $username 用户名
	 * @return unknown 用户信息
	 */
	public function getUserByName($username){
		if(empty($username)) return array();
		
		$user = $this->database->master->from('eb_customer')->where('customer_name',$username)->get()->row_array();
		return $user;
	}
	/**
	 * 通过用户id获取用户信息
	 * @param unknown $userid 用户id
	 * @return unknown 用户信息
	 */
	public function getUserById($userId){
		if(empty($userId)) return array();
		
		$user = $this->database->master->from('eb_customer')->where('customer_id',$userId)->get()->row_array();
		return $user;
	}
	
	/**
	 * 通过用户id获取用户信息
	 * @param unknown $userid 用户id
	 * @return unknown 用户信息
	 */
	public function getUserBySSOId($customer_sso_type,$customer_sso_id){
		$this->database->slave->from('eb_customer');
		$this->database->slave->where('customer_sso_id',$customer_sso_id);
		$this->database->slave->where('customer_sso_type',$customer_sso_type);
		$this->database->slave->where('customer_status',STATUS_ACTIVE);
		$this->database->slave->limit(1);
		$query = $this->database->slave->get();
		$res = $query->row_array();

		return $res;
	}
	
	/**
	 * 验证数据库密码与用户输入密码
	 * @param unknown $dbPassword 数据库存储密码
	 * @param unknown $inputPassword 用户输入密码
	 * @return boolean 如果一致返回true
	 */
	public function validatePassword($dbPassword,$inputPassword){
		$hashArr = explode(':',$dbPassword);
		if(count($hashArr) == 1){
			if(md5($inputPassword) !== $dbPassword) return false;
		}elseif(count($hashArr) == 2){
			if(md5($hashArr['1'].$inputPassword) !== $hashArr['0']) return false;
		}
		return true;
	}
	
	/**
	 * 创建密码 规则：salt.password 的md5
	 * @param unknown $password 用户输入密码
	 * @return string 处理后的密码
	 */
	public function hashPassword($password){
		$salt = '';
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$lc = strlen($chars)-1;
		mt_srand(10000000*(double)microtime());
		for($i=0;$i<2;$i++) {
			$salt .= $chars[mt_rand(0,$lc)];
		}
		return md5($salt.$password).':'.$salt;
	}
	
	/**
	 * 创建用户
	 * @param unknown $info 用户相关信息
	 */
	public function createUser($info) {
		$this->database->master->insert('eb_customer', $info);
		return $this->database->master->insert_id();
	}
	
	/**
	 * 检测用户名是否注册
	 * @param unknown $username 用户名
	 * @return boolean 如果已经注册返回true
	 */
	public function checkUserNameUsed($username){
		if(empty($username)) return true;
		$user = $this->database->master->from('eb_customer')->where('customer_name',$username)->get()->row_array();
		
		if($user){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 检测用户邮箱是否注册
	 * @param unknown $email 邮箱
	 * @return boolean 如过已经注册返回true
	 * @author Diaoqp
	 */
	public function checkEmailUsed($email){
		if(empty($email)) return true;
		$user = $this->database->master->from('eb_customer')->where('customer_email',$email)->get()->row_array();
		
		if($user){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 更新用户登陆信息
	 * @param unknown $userId
	 */
	public function updateUserLoginInfo($userId){
		$this->database->master->set('customer_count_visit', 'customer_count_visit+1', false);
		$this->database->master->set('customer_ip', $this->input->ip_address());
		$this->database->master->set('customer_time_lastlogin', date('y-m-d h:i:s',time()));
		$this->database->master->where('customer_id', $userId);
		return $this->database->master->update('eb_customer');
	}
	
	public function updateUserInfo($userId,$info){
		$this->database->master->where('customer_id', $userId);
		$this->database->master->update('eb_customer',$info);
	}
	/**
	 * @desc 根据用户id，返回用户name
	 * @param unknown $user_id
	 * @return string
	 * @author Wty
	 */
	public function nameWithUid($user_id){
		$name = "";
		if(!$user_id || !is_numeric($user_id)) return $name;
		
		$user_info = $this->database->master->from('eb_customer')->where('customer_id',$user_id)->get()->row_array();
		if($user_info){
			return $user_info['customer_name'];
		}else{
			return $name;
		}
	}

	public function updateUserEmail($userId,$email){
		$this->database->master->set('customer_email', $email);
		$this->database->master->where('customer_id', $userId);
		return $this->database->master->update('eb_customer');
	}
	
	public function updateUserName($userId,$username){
		$this->database->master->set('customer_name', $username);
		$this->database->master->where('customer_id', $userId);
		return $this->database->master->update('eb_customer');
	}
	
	/**
	 * 更新用户信息
	 * @param  integer $userId 用户id
	 * @param  array $info 用户信息
	 */
	public function editUser($userId, $info) {
		//校验传入数据
		if(empty($userId) || empty($info)) return false;
		
		//返回此次修改的信息
		$this->database->master->where('customer_id', $userId);
		return $this->database->master->update('eb_customer', $info);
	}
	
	/**
	 * @desc 返回用户rewords级别
	 */
	public function getRewordsRate($customer_id){
		$rate = 1;
		if(!$customer_id || !is_numeric($customer_id)) return $rate;
		
		$this->database->master->from("eb_customer");
		$this->database->master->where("customer_id",$customer_id);
		$query = $this->database->master->get();
		$result = $query->result_array();
		if($result){
			$rate = (int) $result[0]['customer_rewards_rate'];
		}
		return $rate;
	}
	
	/**
	 * @desc 更新用户rewards
	 * @param unknown $user_id
	 * @param unknown $rewards_num
	 * @return boolean|unknown
	 */
	public function updateUserRewards($user_id,$rewards_num,$type = '+'){
		//未开发
		/*$this->load->model('rewardsmodel','rewardsmodel');
		$this->rewwardsmodel->computeCustomerRewards($customer_id);
		*/
		if(!$user_id || !is_numeric($user_id) || !is_numeric($rewards_num)) return false;
		$this->database->master->set('customer_rewards', 'customer_rewards'.$type.$rewards_num, FALSE);
		$this->database->master->where('customer_id',$user_id);
		$result = $this->database->master->update('eb_customer');
		return $result;
	}
}