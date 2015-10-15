<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 评论model
 * @author Administrator
 *
 */
class rewardsmodel extends CI_Model {
	//private $memcache;
	//type 0:下单消费- 1:下单获得+ 2:评论获得+ 3:退款退回获得+  4:有退货扣减消费-
// 	1 订单奖励+ 2 消费-  3 客服增加+ 4 客服扣除- 将来加5 评论
// 	0、rewards used  on order 134353566 下单消费
// 	1、rewards earned on order 134567790 下单获得
// 	2、rewards earned on review to order 124667434 评论获得
// 	3、rewards refunded for cancelling order 12345667 订单取消后退回rewards
// 	4、rewards deducted for refund order 124354657 订单退货后扣减rewards
	
	
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}
	
	/**
	 * 获取Rewards列表
	 * @param unknown $userId
	 * @param unknown $params
	 * @return multitype:
	 * @author an
	 */
	public function getRewardsHistoryList($userId,$params=array('page'=>1,'pagesize'=>10)){
		$result = array();
		if(empty($userId))
			return $result;
		$this->database->slave->from('eb_rewards_history');
		$this->database->slave->where('customer_id',$userId);
		$this->database->slave->order_by('rewards_history_time_create','desc');
		if(empty($params)){
			$result =$this->database->slave->get()->result_array();
		}else{
			$result =$this->database->slave->limit($params['pagesize'],($params['page']-1)*$params['pagesize'])->get()->result_array();
		}
		return $result;
	}
	
	/**
	 * 获取Rewards历史记录数量
	 * @param unknown $userId
	 * @return number|unknown
	 * @author an
	 */
	public function getRewardsHistoryCount($userId){
		$result = 0;
		if(empty($userId))
			return $result;
		$result = $this->database->slave->from('eb_rewards_history')->where('customer_id',$userId)->count_all_results();
		return $result;
	}
	
	//创建rewards记录
	public function createRewardsHistory($data){
		//检查判断条件是否存在
		$this->database->master->insert('eb_rewards_history',$data);
		$insert_id = $this->database->master->insert_id();
		return $insert_id;
	}
	
	//根据历史记录，计算用户积分（未开发）
	public function computeCustomerRewards($customer_id){
		return  true;
	}
	
}