<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Manage_address_book extends Dcontroller {

	/**
	 * 地址薄首页(non-PHPdoc)
	 * @see Dcontroller::index()
	 */
	public function index($page = 1){
		//判断登陆状态
		if(!$this->customer->checkUserLogin()) {
			redirect(genURL('login'));
		}
		
		$this->load->model("addressmodel","address");
		
		$userId = $this->customer->getCurrentUserId();
		$tips = $this->input->get('tips');
		
		//获取列表
		$address_list = $this->address->getAddressList($userId);
		//国家列表
		$country_list = $this->address->getCountryList();
		//省份列表
		$province_list = $this->address->getCountryProvinceList();
		
		$this->_view_data['address_list'] = $address_list;
		$this->_view_data['country_list'] = $country_list;
		$this->_view_data['province_list'] = json_encode($province_list);
		$this->_view_data['tips'] = $tips;
		
		//当前页名称 处理account中左侧选中的
		$this->_view_data['currentPage'] = 'address';

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
	 * 编辑address
	 * 
	 * @author an
	 */
	public function edit(){
		//判断登陆状态
		if(!$this->customer->checkUserLogin()) {
			redirect(genURL('login'));
		}
		
		$this->load->model("addressmodel","address");
		
		$userId = $this->customer->getCurrentUserId();
		
		$id = $this->input->post('address_id');
		$firstName = htmlspecialchars(trim($this->input->post('first_name')));
		$lastName = htmlspecialchars(trim($this->input->post('last_name')));
		$country = $this->input->post('country');
		$province = htmlspecialchars(trim($this->input->post('province')));
		$city = htmlspecialchars(trim($this->input->post('city')));
		$zipCode = htmlspecialchars(trim($this->input->post('zipcode')));
		$phone = htmlspecialchars(trim($this->input->post('phone')));
		$address = htmlspecialchars(trim($this->input->post('address')));
		$cpfcnpj = trim($this->input->post('cpfcnpj'));
		$default = trim($this->input->post('defaultValue'))==1?1:0;
		
		//validate
		//判断要求非空
		if(empty($firstName)){
			redirect(genURL('manage_address_book',false,array('tips'=>sprintf(lang('field_required_tips'),lang('first_name')))));
		}
		if(empty($lastName)){
			redirect(genURL('manage_address_book',false,array('tips'=>sprintf(lang('field_required_tips'),lang('last_name')))));
		}
		if(empty($country)){
			redirect(genURL('manage_address_book',false,array('tips'=>sprintf(lang('field_required_tips'),lang('country')))));
		}
		if(empty($province)){
			redirect(genURL('manage_address_book',false,array('tips'=>sprintf(lang('field_required_tips'),lang('state_province_region')))));
		}
		if(empty($city)){
			redirect(genURL('manage_address_book',false,array('tips'=>sprintf(lang('field_required_tips'),lang('city')))));
		}
		if(empty($zipCode)){
			redirect(genURL('manage_address_book',false,array('tips'=>sprintf(lang('field_required_tips'),lang('zip_code')))));
		}
		
		//地址不能包含undefined none关键词
		if(empty($address) || preg_match('/^undefined|none$/',$address)){
			redirect(genURL('manage_address_book',false,array('tips'=>lang('invalid_address_tips'))));
		}
		
		//地址长度4-100
		if(strlen($address) < 4 || strlen($address) > 100){
			redirect(genURL('manage_address_book',false,array('tips'=>lang('address_length_tips'))));
		}
		
		//城市名称字长不能大于50
		if(strlen($city) > 50){
			redirect(genURL('manage_address_book',false,array('tips'=>lang('city_length_tips'))));
		}
		
		//电话号码不能小于5位,可以包含字母数字+-()（）字样
		if(strlen($phone) < 5 || !preg_match('/^[A-Za-z0-9\s\+\-\(\)\（\）]+$/',$phone)){
			redirect(genURL('manage_address_book',false,array('tips'=>lang('invalid_phone_tips'))));
		}
		
		//如果国家为巴西 需要输入CPF CPF不能小于11位
		if($country == 'BR'){
			if(empty($cpfcnpj)){
				redirect(genURL('manage_address_book',false,array('tips'=>lang('cpf_required_tips'))));
			}
			if(strlen($cpfcnpj) < 11){
				redirect(genURL('manage_address_book',false,array('tips'=>lang('cpf_length_tips'))));
			}
		}
		
		//获取相应地址信息
		$addr = $this->address->getAddressById($id);
		
		//判断地址是否存在 或者是否由当前用户拥有
		if(empty($addr) || $addr['customer_id'] != $userId){
			redirect(genURL('manage_address_book',false,array('tips'=>'This address is not exist.')));
		}
		
		//处理默认地址
		//如果用户选择设置为默认地址，那么获取默认地址判断是否是同一个，如果不是那么取消原有默认地址，同时重新更新customer表中默认地址id
		if($default){
			//获取原有default_address
			$defaultAddress = $this->address->getDefaultAddress($userId);
			
			if(!empty($defaultAddress) && isset($defaultAddress['address_id']) && $defaultAddress['address_id'] != $id){
				//取消原有default_address
				$this->address->editAddress($defaultAddress['address_id'],array(
					'address_default' => 0,
				));
			}
			
		}else if($addr['address_default'] == 0){
			//判断当前用户的地址总数，如果为0,则此次添加必须是默认地址
			$addressCount = $this->address->getAddressListCount($userId);
			
			if(empty($addressCount)){
				$default = 1;
			}
		}
		
		//插入 注意default问题
		$flag = $this->address->editAddress($id,array(
			'address_firstname' => $firstName,
			'address_lastname' => $lastName,
			'address_country' => $country,
			'address_province' => $province,
			'address_city' => $city,
			'address_address' => $address,
			'address_phone' => $phone,
			'address_zipcode' => $zipCode,
			'address_cpfcnpj' => $cpfcnpj,
			'address_default' => $default,
			'address_time_update' => date('Y-m-d H:i:s',time()),
		));
		
		if($flag){
			//如果用户选择此为默认地址
			if($default){
				//更新cusmtomer表中address_id
				$this->customer->editUser($userId,array(
						'address_id'=>$id,
				));
			}
			redirect(genURL('manage_address_book',false,array('tips'=>lang('changed_successfully_tips'))));
		}else{
			redirect(genURL('manage_address_book',false,array('tips'=>'Failed,Please Retry.')));
		}
	}
	
	/**
	 * 添加address
	 * 
	 * @author an
	 */
	public function add(){
		$this->load->model("addressmodel","address");
		
		$userId = $this->customer->getCurrentUserId();
		
		$firstName = htmlspecialchars(trim($this->input->post('first_name')));
		$lastName = htmlspecialchars(trim($this->input->post('last_name')));
		$country = $this->input->post('country');
		$province = htmlspecialchars(trim($this->input->post('province')));
		$city = htmlspecialchars(trim($this->input->post('city')));
		$zipCode = htmlspecialchars(trim($this->input->post('zipcode')));
		$phone = htmlspecialchars(trim($this->input->post('phone')));
		$address = htmlspecialchars(trim($this->input->post('address')));
		$cpfcnpj = trim($this->input->post('cpfcnpj'));
		$default = trim($this->input->post('defaultValue'))==1?1:0;
		
		//validate
		//判断要求非空
		if(empty($firstName)){
			redirect(genURL('manage_address_book',false,array('tips'=>sprintf(lang('field_required_tips'),lang('first_name')))));
		}
		if(empty($lastName)){
			redirect(genURL('manage_address_book',false,array('tips'=>sprintf(lang('field_required_tips'),lang('last_name')))));
		}
		if(empty($country)){
			redirect(genURL('manage_address_book',false,array('tips'=>sprintf(lang('field_required_tips'),lang('country')))));
		}
		if(empty($province)){
			redirect(genURL('manage_address_book',false,array('tips'=>sprintf(lang('field_required_tips'),lang('state_province_region')))));
		}
		if(empty($city)){
			redirect(genURL('manage_address_book',false,array('tips'=>sprintf(lang('field_required_tips'),lang('city')))));
		}
		if(empty($zipCode)){
			redirect(genURL('manage_address_book',false,array('tips'=>sprintf(lang('field_required_tips'),lang('zip_code')))));
		}
		
		//地址不能包含undefined none关键词
		if(preg_match('/^undefined|none$/',$address)){
			redirect(genURL('manage_address_book',false,array('tips'=>lang('invalid_address_tips'))));
		}
		
		//地址长度4-100
		if(empty($address) || strlen($address) < 4 || strlen($address) > 100){
			redirect(genURL('manage_address_book',false,array('tips'=>lang('address_length_tips'))));
		}
		
		//城市名称字长不能大于50
		if(strlen($city) > 50){
			redirect(genURL('manage_address_book',false,array('tips'=>lang('city_length_tips'))));
		}
		
		//电话号码不能小于5位,可以包含字母数字+-()（）字样
		if(strlen($phone) < 5 || !preg_match('/^[A-Za-z0-9\s\+\-\(\)\（\）]+$/',$phone)){
			redirect(genURL('manage_address_book',false,array('tips'=>lang('invalid_phone_tips'))));
		}
		
		//如果国家为巴西 需要输入CPF CPF不能小于11位
		if($country == 'BR'){
			if(empty($cpfcnpj)){
				redirect(genURL('manage_address_book',false,array('tips'=>lang('cpf_required_tips'))));
			}
			if(strlen($cpfcnpj) < 11){
				redirect(genURL('manage_address_book',false,array('tips'=>lang('cpf_length_tips'))));
			}
		}
		
		//处理默认地址
		if($default){
			//获取原有default_address
			$defaultAddress = $this->address->getDefaultAddress($userId);
			if(!empty($defaultAddress)){
				//取消原有default_address
				$this->address->editAddress($defaultAddress['address_id'],array(
					'address_default' => 0,
				));
			}
		}else{
			//判断当前用户的地址总数，如果为0,则此次添加必须是默认地址
			$addressCount = $this->address->getAddressListCount($userId);
			if(empty($addressCount)){
				$default = 1;
			}
		}
		
		//插入 注意default问题
		$addrId = $this->address->createAddress(array(
			'customer_id' => $userId,
			'address_firstname' => $firstName,
			'address_lastname' => $lastName,
			'address_country' => $country,
			'address_province' => $province,
			'address_city' => $city,
			'address_address' => $address,
			'address_phone' => $phone,
			'address_zipcode' => $zipCode,
			'address_cpfcnpj' => $cpfcnpj,
			'address_default' => $default,
			'address_status' => 1,
			'address_time_update' => date('Y-m-d H:i:s',time()),
		));
		
		if($addrId){
			//更新cusmtomer表中address_id
			$this->customer->editUser($userId,array(
					'address_id'=>$addrId,
			));
			
			redirect(genURL('manage_address_book',false,array('tips'=>lang('add_successfully_tips'))));
		}else{
			redirect(genURL('manage_address_book',false,array('tips'=>'Failed,Please Retry.')));
		}
	}
	
	/**
	 * 删除address
	 * 
	 * @author an
	 */
	public function ajaxDel(){
		$arr = array('status'=>200,'msg'=>'','data'=>array());
		$this->load->model("addressmodel","address");
		
		//判断登陆状态
		if(!$this->customer->checkUserLogin()) {
			$arr['status'] = 1007;
			$this->ajaxReturn($arr);
		}
		
		$id = $this->input->post('id');
		$userId = $this->customer->getCurrentUserId();
		
		//获取地址信息
		$addr = $this->address->getAddressById($id);
		
		//判断是否存在并且判断是否为当前用户数据
		if(!empty($addr) && $addr['customer_id'] == $userId){
			//判断是否为默认地址
			if($addr['address_default'] == 1){
				$arr['status'] = 2200;
				$arr['msg'] = lang('preferred_address_delete_tips');
				$this->ajaxReturn($arr);
			} else {
				$this->address->editAddress($id,array(
					'address_status' => -2,
					'address_time_update' => date('Y-m-d H:i:s',time()),
				));
				$arr['msg'] = 'Removed Successfully!';
				$this->ajaxReturn($arr);
			}
		}else{
			$arr['status'] = 2200;
			$arr['msg'] = 'Not Exist!';
			$this->ajaxReturn($arr);
		}
		
	}
	
	/**
	 * 获取某一address信息
	 * 
	 * @author an
	 */
	public function ajaxAddrInfo(){
		$arr = array('status'=>200,'msg'=>'','data'=>array());
		$this->load->model("addressmodel","address");
		
		//判断登陆状态
		if(!$this->customer->checkUserLogin()) {
			$arr['status'] = 1007;
			$this->ajaxReturn($arr);
		}
		
		$id = $this->input->post('id');
		$userId = $this->customer->getCurrentUserId();
		
		$addr = $this->address->getAddressById($id);
		
		//判断是否存在并且判断是否为当前用户数据
		if(!empty($addr) && $addr['customer_id'] == $userId){
			$arr['data'] = array($id=>$addr);
			$this->ajaxReturn($arr);
		}else{
			$arr['status'] = 2200;
			$arr['msg'] = 'Not Exist!';
			$this->ajaxReturn($arr);
		}
	}
}