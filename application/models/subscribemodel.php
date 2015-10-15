<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc newsletter model
 * @author Administrator
 *
 */
class Subscribemodel extends CI_Model {
	
	public function __construct(){
		parent::__construct();
	}

    /**
     * 获取指定邮件的订阅信息
     * @param  string $email 订阅的邮件
     */
    public function getEmailSubscribeInfo($email) {
        $this->database->master->from('eb_subscribe');
        $this->database->master->where('subscribe_email',$email);
        $this->database->master->limit(1);
        $query = $this->database->master->get();
        $res = $query->row_array();
        return $res;
    }

    /**
     * 根据用户id获取指定邮件的订阅信息
     * @param  string $customer_id 用户ID
     */
    public function getEmailSubscribeInfoByCustomerId($customer_id,$status = 0) {
        $this->database->master->from('eb_subscribe');
        $this->database->master->where('customer_id',$customer_id);
        if($status){
            $this->database->master->where('subscribe_status',1);
        }
        $this->database->master->limit(1);
        $query = $this->database->master->get();
        $res = $query->row_array();
        return $res;
    }

	/**
	 * 创建订阅邮件
	 * @param  array $info 订阅邮件的信息
	 */
	public function createEmailSubscribe($info) {
		$this->database->master->insert('eb_subscribe', $info);
		$id = $this->database->master->insert_id();
		return $id;
	}
	
	/**
	 * 更新订阅邮件
	 * @author qcn
	 */
	public function updateEmailSubscribe($email, $info) {
		$email = strval($email);
		$this->database->master->where('subscribe_email', $email);
		$this->database->master->update('eb_subscribe', $info);
	}
	
	public function getSubscribeCoupon($coupon){
		if(empty($coupon)) return array();
		$this->database->master->from('eb_subscribe');
		$this->database->master->where('subscribe_coupon',$coupon);
		$this->database->master->where('subscribe_status_coupon',0);
		//$this->database->master->limit(1);
		$query = $this->database->master->get();
		$res = $query->row_array();
		return $res;
	}
	
	public function updateSubscribeCouponStatus($coupon){
		if(empty($coupon)) return false;
		$this->database->master->where('subscribe_coupon', $coupon);
		return $this->database->master->update('eb_subscribe', array('subscribe_status_coupon'=>1));
	}
	
	/**
	 * 修改订阅邮件添加日志
	 * @param array $info
	 */
	public function logUpdateEmailSubscribe( $info ) {
		$this->database->master->insert( 'email_list_editlog', $info );
	}
	
}
