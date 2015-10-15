<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc coupon model
 * @author Wty
 *
 */
class Couponmodel extends CI_Model {
	//private $memcache;
	
	public function __construct(){
		parent::__construct();
	}
	
	//eb_coupon
	public function couponInfoWithCode($coupon_code){
		$result = array();
		if(!$coupon_code || empty($coupon_code)) return $result;

		global $mem_expired_time;
		$mem_key = md5("each_buyer_coupon_".$coupon_code);
		
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_coupon');
			$this->database->slave->where('coupon_code',$coupon_code);
			$this->database->slave->where('coupon_status',COUPON_STATUS_ACTIVE);
			$this->database->slave->limit(1);
			$query = $this->database->slave->get();
			$result = $query->result_array();
			//echo $this->database->slave->last_query();die;
		
			$this->memcache->set($mem_key, $result,$mem_expired_time['coupon_code_info']);
		}
		
		return $result;
	}
	
	//eb_coupon_effect
	public function couponeffectInfoWithCouponid($coupon_id){
		$result = array();
		if(!$coupon_id || !is_numeric($coupon_id)) return $result;
		
		global $mem_expired_time;
		$mem_key = md5("each_buyer_coupon_effect".$coupon_id);
		
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_coupon_effect');
			$this->database->slave->where('coupon_id',$coupon_id);
			$this->database->slave->where('coupon_effect_status',STATUS_ACTIVE);
			$this->database->slave->order_by('coupon_effect_price','desc');
			//$this->database->slave->limit(1);
			$query = $this->database->slave->get();
			$result = $query->result_array();
			//echo $this->database->slave->last_query();die;
		
			$this->memcache->set($mem_key, $result,$mem_expired_time['coupon_code_info']);
		}
		
		return $result;
	}
	
	//eb_coupon_history(次数限制)，未正式使用
	public function couponUseTime($coupon_code){
		$times = 0;
		//**********************
		return $times;
	}
	
	/**
	 * @desc 更新coupon使用次数（未完成）
	 * @param unknown $coupon_code
	 * @return boolean
	 */
	public function updateCouponUseTimes($coupon_code){
		if(empty($coupon_code)) return false;
		//$this->database->master->set('product_sales', 'product_sales+1', FALSE);
		//$this->database->master->where('coupon_code',trim($coupon_code));
		//$result = $this->database->master->update('eb_product');
		return true;
	} 
	//满减(两种)

	//满赠
	
	//满折扣(两种)
	
	//coupon_status==3时可用
	
	//可用范围：全站，某分类，某商品，某语言
	
}