<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 订单 model
 * @author Administrator
 *
 */
class ordermodel extends CI_Model {

	public function __construct(){
		parent::__construct();
	}

	//创建订单
	public function createOrder($data){
		//检查判断条件是否存在
		$this->database->master->insert('eb_order',$data);
		$orderId = $this->database->master->insert_id();
		$new_data['order_code'] = ORDER_PREFIX . date('Ymd',requestTime()) . str_pad($orderId,8,'0',STR_PAD_LEFT);
		$this->updateOrder($orderId, $new_data);
		return $orderId;
	}
	
	//更新订单
	public function updateOrder($orderId,$data){
		//判断条件
		
		$this->database->master->where('order_id', $orderId);
		$this->database->master->update('eb_order', $data);
	}
	
	/**
	 * @desc 根据code编号，更新订单
	 * @param unknown $order_code
	 * @param unknown $data
	 */
	public function updateOrderWithCode($order_code,$data){
		if(empty($order_code) || empty($data)) return false;
		
		$this->database->master->where('order_code', $order_code);
		$this->database->master->update('eb_order', $data);
	}
	
	//批量创建订单中商品
	public function createOrderProductbatch($data){
		//判断条件
		
		$this->database->master->insert_batch('eb_order_product',$data);
		
	}
	
	//获取订单详情(根据订单编号)
	public function getOrderWithCode($order_code){
		if(empty($order_code)) return array();
		$this->database->master->from('eb_order');
		$this->database->master->where('order_code',$order_code);
		$this->database->master->limit(1);
		$result =$this->database->master->get()->result_array();
		//echo $this->database->master->last_query();die;
		if($result && !empty($result)) return $result[0];
		else return array();
	}
	
	//查询订单
	
	public function getOrderById($order_id){
		$this->database->slave->from('eb_order');
		$this->database->slave->where('order_id',$order_id);
		$this->database->slave->limit(1);
		$query = $this->database->slave->get();
		$result = $query->row_array();

		return $result;
	}
	
	/**
	 * 根据用户id获取订单列表
	 * @param unknown $userId 用户id
	 * @param unknown $params 分页参数 默认是第一页 如果为空的话 查询全部
	 * @return multitype: array 订单列表
	 * @author an
	 */
	public function getOrderList($userId,$params=array('page'=>1,'pagesize'=>10)){
		$result = array();
		if(empty($userId))
			return $result;
		$this->database->slave->from('eb_order');
		$this->database->slave->where('customer_id',$userId);
		$this->database->slave->order_by('order_id',"desc");
		if(empty($params)){
			$result =$this->database->slave->get()->result_array();
		}else{
			$result =$this->database->slave->limit($params['pagesize'],($params['page']-1)*$params['pagesize'])->get()->result_array();
		}
		return $result;
	}
	
	/**
	 * 根据用户id获取订单数量
	 * @param unknown $userId 用户id
	 * @return number|unknown int 订单数量
	 * @author an
	 */
	public function getOrderCount($userId){
		$result = 0;
		if(empty($userId))
			return $result;
		$result = $this->database->slave->from('eb_order')->where('customer_id',$userId)->count_all_results();
		return $result;
	}
	/**
	 * 根据订单id获取发货package列表
	 * @param unknown $orderId
	 * @return multitype:|unknown
	 * @author an
	 */
	public function getPackageListByOrderId($orderId){
		$result = array();
		if(empty($orderId))
			return $result;
		$result = $this->database->slave->from('eb_order_package')->where('order_id',$orderId)->get()->result_array();
		return $result;
	}
	/**
	 * 根据订单id获取商品列表
	 * @param unknown $orderId
	 * @return multitype:|unknown
	 * @author an
	 */
	public function getProductListByOrderId($orderId){
		if(!is_array($orderId)) $orderId = array($orderId);
		if(empty($orderId)) return array();

		$result = $this->database->slave->from('eb_order_product')->where_in('order_id',$orderId)->get()->result_array();
		return $result;
	}

	/**
	 * @desc 根据订单id批量获取订单中商品信息
	 * @param unknown $order_array
	 * @return multitype:|unknown
	 */
	public function getProductListByOrderIdArray($order_array){
		$result = array();
		if(empty($order_array)) return $result;

		$result = $this->database->slave->from('eb_order_product')->where_in('order_id',$order_array)->get()->result_array();
		return $result;
	}
	
	/**
	 * 获取订单动作列表
	 * @param unknown $orderId
	 * @return multitype:|unknown
	 * @author an
	 */
	public function getActionListByOrderId($orderId){
		$result = array();
		if(empty($orderId))
			return $result;
		$result = $this->database->slave->from('eb_order_action')->where('order_id',$orderId)->get()->result_array();
		return $result;
	}
	
	/**
	 * 根据orderId判断此productId是否存在
	 * @param unknown $orderId
	 * @param unknown $productId
	 * @return boolean
	 * @author an
	 */
	public function existProductByOrderIdAndProductId($orderId,$productId){
		if(empty($orderId) || empty($productId)) return false;
		$result = $this->database->slave->from('eb_order_product')->where('order_id',$orderId)->where('product_id',$productId)->get()->result_array();
		if(empty($result)){
			return false;
		}else{
			return true;
		}
	}
	
	//创建订单action
	public function createOrderAction($data){
		//检查判断条件是否存在
		if(!isset($data['order_action_time_create']))$data['order_action_time_create'] = date('Ymd',requestTime());
		$this->database->master->insert('eb_order_action',$data);
		$order_action_id = $this->database->master->insert_id();
		return $order_action_id;
	}


	/**
	 * 根据orderIds获取order列表
	 * @param  [type] $orderIds [description]
	 * @return [type]           [description]
	 */
	public function getOrderListByOrderIds($orderIds){
		$result = array();
		if(empty($orderIds)){
			return $orderIds;
		}
		if(!is_array($orderIds)){
			$orderIds = array($orderIds);
		}

		$this->database->slave->from('eb_order');
		$this->database->slave->where_in('order_id',$orderIds);
		$this->database->slave->order_by('order_id',"desc");

		$result =$this->database->slave->get()->result_array();
		return $result;
	}
	
	/**
	 * @desc 获取未支付订单总数
	 */
	public function getUnpaidOrderNum($type = 1){
		$this->database->slave->from('eb_order');
		$this->database->slave->where('order_status',OD_CREATE);
		$time = strtotime(date('Y-m-d'));
		
		if($type==2){//48-14天内
			$this->database->slave->where('order_time_create > ',date('Y-m-d H:i:s',$time-14*24*3600) ) ;
			$this->database->slave->where('order_time_create < ',date('Y-m-d H:i:s',$time-48*3600) );
			$this->database->slave->where('order_status_email_pay2',0);
		}else{//48小时内
			$this->database->slave->where('order_time_create > ',date('Y-m-d H:i:s',$time-48*3600) );
			$this->database->slave->where('order_time_create < ',date('Y-m-d H:i:s',$time) );
			$this->database->slave->where('order_status_email_pay1',0);
		}
		
		$this->database->slave->order_by('order_id',"asc");
		return $this->database->slave->count_all_results();
	}
	
	/**
	 * @desc 分页获取未付款订单
	 * @param number $type 1:48小时内; 2:48-72小时内
	 * @param number $page
	 * @param number $pagesize
	 * @return unknown
	 */
	public function getOrderListWithPage($type = 1, $page = 1, $pagesize = 100){
		$result = array();
		$this->database->slave->from('eb_order');
		$this->database->slave->where('order_status',OD_CREATE);
		$time = strtotime(date('Y-m-d'));
		
		if($type==2){//48-14天内
			$this->database->slave->where('order_time_create > ',date('Y-m-d H:i:s',$time-14*24*3600) ) ;
			$this->database->slave->where('order_time_create < ',date('Y-m-d H:i:s',$time-48*3600) );
			$this->database->slave->where('order_status_email_pay2',0);
		}else{//48小时内
			$this->database->slave->where('order_time_create > ',date('Y-m-d H:i:s',$time-48*3600) );
			$this->database->slave->where('order_time_create < ',date('Y-m-d H:i:s',$time) );
			$this->database->slave->where('order_status_email_pay1',0);
		}
		
		$this->database->slave->order_by('order_id',"asc");
		$result = $this->database->slave->limit($pagesize,($page-1)*$pagesize)->get()->result_array();
		//echo $this->database->slave->last_query()."<br>";die;
		return $result;
	}
	
	/**
	 * @desc 判断下单用户是新老用户
	 * @param unknown $customer_id
	 * @return number 返回大于1为老用户，否则为新用户
	 */
	public function getOrderCustomerType($customer_id){
		if(empty($customer_id) || !is_numeric($customer_id)) return false;
		$this->database->master->from('eb_order');
		$this->database->master->where('order_status',OD_PAID);
		$this->database->master->where('customer_id',$customer_id);
		$this->database->master->where('order_time_pay >',date('Y-m-d H:i:s',time()-24*3600));//24小时之内
		
		return $this->database->master->count_all_results();
	}
	
	/**
	 * @desc 创建订单中满减记录
	 * @param unknown $order_id
	 * @param unknown $discount_ids
	 * @return boolean
	 * @author WTY
	 */
	public function createOrderDiscountHistory($order_id,$discount_ids){
		if(!is_numeric($order_id) || !$order_id || empty($discount_ids) || !is_array($discount_ids)) return false;
		$list = array();
		foreach ($discount_ids as $k=>$v){
			$data['order_id'] = $order_id;
			$data['discount_id'] = $v;
			$data['order_discount_time_create'] = date('Y-m-d H:i:s',time());
			array_push($list, $data);
		}

		return $this->database->master->insert_batch('eb_order_discount',$list);
	}
	
	/**
	 * @检测paypal_ec交易号是否重复下单
	 * @param unknown $paypalEcPayerid
	 * @param unknown $customer_id
	 * @param unknown $payment_id
	 * @return number
	 */
	public function checkTransnumberIdOrder($paypalEcPayerid,$customer_id,$payment_id){
		if(empty($paypalEcPayerid) || !is_numeric($customer_id) || !is_numeric($payment_id)) return 0;
		$this->database->master->from('eb_order');
		$this->database->master->where('customer_id',$customer_id);
		$this->database->master->where('order_transnumber',$paypalEcPayerid);//paypal_ec交易号
		$this->database->master->where('payment_id',$payment_id);//支付方式
		
		return $this->database->master->count_all_results();
	}

    public function getPaidOrderByEmail($email){
        $result = array();
        $slave = $this->database->slave;
        $slave->from('eb_order');
        $slave->where('order_email',$email);
        $slave->where('order_status >=',OD_PAID);
        $result = $slave->get()->result_array();
        return $result;
    }
}
