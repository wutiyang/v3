<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc address model
 * @author Administrator
 *
 */
class addressmodel extends CI_Model {
	//新建完是1 删除是-2 只显示状态为1的
	
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 添加address
	 * @param unknown $info 创建的信息
	 * @return boolean 返回创建是否成功
	 * @author an
	 */
	public function createAddress($info) {
		if(empty($info)) return false;
		$this->database->master->insert('eb_address', $info);
		return $this->database->master->insert_id();
	}
	
	/**
	 * 
	 * @param unknown $id address id
	 * @param unknown $info 修改的信息
	 * @return boolean 返回修改是否成功
	 * @author an
	 */
	public function editAddress($id, $info) {
		if(empty($id) || empty($info)) return false;
		$this->database->master->where('address_id', $id);
		return $this->database->master->update('eb_address', $info);
	}
	
	/**
	 * @desc 取消该用户默认地址
	 * @param unknown $customer_id
	 * @return boolean
	 */
	public function cancleCustomerDefaultAddressid($customer_id){
		if(empty($customer_id) || !is_numeric($customer_id)) return false;
		$this->database->master->where('customer_id', $customer_id);
		$info['address_default'] = 0;
		return $this->database->master->update('eb_address', $info);
	}
	
	/**
	 * 删除address(暂时废弃目前删除是修改状态为-2)
	 * @param unknown $id address id
	 * @return boolean 返回删除是否成功
	 * @author an
	 */
	public function removeAddress($id) {
		if(empty($id)) return false;
		return $this->database->master->where('address_id', $id)->delete('eb_address');
	}
	
	/**
	 * 
	 * 返回用户地址列表（根据user_id）
	 * @param unknown $userId 用户id
	 * @return multitype:集合列表
	 * @author an
	 */
	public function getAddressList($userId,$status=1){
		$result = array();
		if(empty($userId)) return $result;
		$result = $this->database->master->from('eb_address')->where('customer_id',$userId)->where('address_status',$status)->order_by('address_default','desc')->order_by('address_time_update','desc')->get()->result_array();
		return $result;
	}
	
	/**
	 * 返回用户地址数量
	 * @param unknown $userId 用户id
	 * @return number|unknown 地址数量
	 * @author an
	 */
	public function getAddressListCount($userId,$status=1){
		$result = 0;
		if(empty($userId)) return $result;
		$result = $this->database->master->from('eb_address')->where('customer_id',$userId)->where('address_status',$status)->count_all_results();
		return $result;
	}
	
	/**
	 * 获取用户默认地址
	 * @param unknown $userId 用户id
	 * @return multitype:|unknown 默认地址如无，返回空数组
	 * @author an
	 */
	public function getDefaultAddress($userId){
		$result = array();
		if(empty($userId)) return $result;
		$result = $this->database->master->from('eb_address')->where('customer_id',$userId)->where('address_default',1)->limit(1)->get()->row_array();
		return $result;
	}
	
	/**
	 * @desc 根据地址 address_id 返回地址信息
	 * @param unknown $id 地址id
	 * @return boolean 根据地址id获取地址信息
	 * @author an
	 */
	public function getAddressById($id){
		if(empty($id)) return false;
		return $this->database->master->from('eb_address')->where('address_id',$id)->get()->row_array();
	}
	
	/**
	 * 获取国家集合
	 * @return 返回集合列表
	 */
	public function getCountryList(){
		return $this->database->slave->from('eb_country')->get()->result_array();
	}
	
	/**
	 * 获取国家-省份集合
	 * @param string $isFormat 是否将coutry_code作为key格式化集合形式 array('AR'=>array('Beijing','Nanjing'))
	 * @return unknown 省份集合列表
	 */
	public function getCountryProvinceList($isFormat = true){
		$province_list = $this->database->slave->from('eb_country_province')->get()->result_array();
		if($isFormat){
			$list = array();
			foreach ($province_list as $province){
				$list[$province['country_code']][] = $province['country_province_name'];
			}
			return $list;
		} else {
			return $province_list;
		}
	}
}