<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';
/**
 * paypal支付
 */
class Paypal_payment extends Dcontroller {

	public function __construct(){
		parent::__construct();
		//多语言的获取
		//$this->load->language('place_order',$this->m_app->currentLanguageCode());
	}

	/**
	 * paypal正常支付入口
	 */
	public function index() {
		//加载支付扩展
		$this->load->library('Payment/paypal');
		
		//判断如果不能支付就跳转到购物车
		if(!$this->paypal->checkPaymentAvailable()) { redirect(genURL('cart')); }

		//获取订单号
		$orderSn = $this->session->get('order_code');

		//订单号为空的时候返回购物车
		if($orderSn === false) { redirect(genURL('cart')); }
		//检测订单信息是否存在
		$this->load->model('ordermodel','ordermodel');
		$order_info = $this->ordermodel->getOrderWithCode($orderSn);
		if(!$order_info || empty($order_info)) { redirect(genURL('cart')); }

		$this->_view_data['order'] = $order_info;
		$this->_view_data['pay_account'] = paypal::PAY_ACCOUNT;
		$this->_view_data['url'] = $this->paypal->getPaypalUrl();
		$this->_view_data['url_return'] = genURL('paypal_payment/finish');
		$this->_view_data['url_notify'] = genURL('paypal_payment/notify').'?cid='.generateGACid();
		$this->_view_data['url_cancel'] = genURL('paypal_payment/finish');

		$this->load->view(strtolower(get_class($this)),$this->_view_data);
	}

	/**
	 * 支付完成
	 */
	public function finish() {
		$this->log->write( Log::LOG_TYPE_PAYPAL , json_encode($_POST) );
		$order_code = $this->input->post('invoice');
		$auth = $this->input->post('auth');
		$verify_sign = $this->input->post('verify_sign');
		$order_transnumber = $this->input->post('txn_id');//交易号
		
		$status = $this->input->post('payment_status');   // Pending;
		$payStatus = OD_CREATE;
		switch ($status){
			case 'Pending':
				$payStatus = OD_PAYING;
				break;
			case 'Completed':
				$payStatus = OD_PAID;
				break;
			default:$payStatus = OD_CREATE;break;
		
		}
		//读取库，查看是否状态是否已经改写
		$this->load->model('ordermodel','ordermodel');
		$order_info = $this->ordermodel->getOrderWithCode($order_code);
		if(!empty($order_info) && ($order_info['order_status']==OD_CREATE || $order_info['order_status']==OD_PAYING)){
			//订阅邮箱状态
			if($payStatus==OD_PAID || $order_info['order_status']==OD_PAYING) $this->orderSubscribe($order_info['order_email']);
			
			if($payStatus==OD_PAID){//支付成功
				$change_order_array=  array(
						'payment_id'=>2,
						'order_status'=>OD_PAID,
						'order_transnumber'=>$order_transnumber,
						'order_time_pay'=>date('Y-m-d H:i:s' ,time()),
				);
				$this->ordermodel->updateOrderWithCode($order_code,$change_order_array);
				
				//新增order_action动作
				$order_action_info = json_encode(array('payment_id'=>2,'transnumber'=>$order_transnumber));
				$this->_writeOrderAction($order_info['order_id'],ORDER_ACTION_TYPE_PAID,$order_action_info);
				
				//积分（rewards,往customer表中rewards加1%）
				if(isset($order_info['order_rewards']) && $order_info['order_rewards']){
				 	//记录rewards奖励history
				 	$this->load->model('rewardsmodel','rewardsmodel');
				 	$rewards_data['customer_id'] = $order_info['customer_id'];
				 	$rewards_data['rewards_history_type'] = 1;
				 	$rewards_data['order_id'] = $order_info['order_id'];
				 	$rewards_data['rewards_history_value'] = $order_info['order_rewards'];
				 	$rewards_data['rewards_history_time_create'] = date('Y-m-d H:i:s',time());
				 	$this->rewardsmodel->createRewardsHistory($rewards_data);
					//更新用户rewards
					$this->customer->updateUserRewards($order_info['customer_id'],$order_info['order_rewards']);
				}

				//GA订单数据
				$this->_processGAInfo($order_info);
				
				//支付成功邮件
				$this->SuccessEmail($order_info);
			}elseif($payStatus==OD_PAYING){//返回支付paying时，判断服务器对支付结果
				if(!empty($order_info) && $order_info['order_status']==OD_CREATE){
					$this->ordermodel->updateOrderWithCode($order_code,array('order_status'=>OD_PAYING));
					//新增order_action动作
					$this->_writeOrderAction($order_info['order_id'],ORDER_ACTION_TYPE_PAYING);
				}
			}

		}
		
		//记录日志
		$this->log->write(Log::LOG_TYPE_PAYPAL,json_encode(array('order_code'=>$order_code,'order_status'=>$payStatus)));
		//成功跳转
		redirect(gensslURL('success').'?success='.($payStatus == OD_PAID?1:0).'&payment=paypalsk','refresh');		
	}

	/**
	 * 支付通知
	 */
	public function notify() {
		$this->log->write( Log::LOG_TYPE_PAYPAL , json_encode($_POST) );
		//加载支付扩展
		$this->load->library('Payment/paypal');
		
		//获取支付请求信息
		$info = $this->paypal->getInfoFromPaypalRequest();
		if(empty($info)) { return false; }
		
		//读取库，查看是否状态是否已经改写(读取订单)
		$this->load->model('ordermodel','ordermodel');
		$order_info = $this->ordermodel->getOrderWithCode($info['order_sn']);
		if(empty($order_info)) { return false; }
		
		$result = $this->paypal->confirmPayment();
		
		if($order_info['order_status']==OD_CREATE || $order_info['order_status']==OD_PAYING){
			//订阅邮箱状态
			if($order_info['order_status']==OD_PAID || $result) $this->orderSubscribe($order_info['order_email']);
			
			//解析请求处理
			if($result){
				$data = array(
						'payment_id'=>2,
						'order_status'=>OD_PAID,
						'order_transnumber'=>$info['txn_id'],//交易号，成功支付后才有
						'order_time_pay'=>date('Y-m-d H:i:s' ,time())
				);
				$this->load->model('ordermodel','ordermodel');
				$this->ordermodel->updateOrderWithCode($order_info['order_code'],$data);
					
				//新增order_action动作
				$order_action_info = json_encode(array('payment_id'=>2,'transnumber'=>$info['txn_id']));
				$this->_writeOrderAction($order_info['order_id'],ORDER_ACTION_TYPE_PAID,$order_action_info);
				
				//积分（rewards,往customer表中rewards加1%）
				if(isset($order_info['order_rewards']) && $order_info['order_rewards']){
					//记录rewards奖励history
					$this->load->model('rewardsmodel','rewardsmodel');
					$rewards_data['customer_id'] = $order_info['customer_id'];
					$rewards_data['rewards_history_type'] = 1;
					$rewards_data['order_id'] = $order_info['order_id'];
					$rewards_data['rewards_history_value'] = $order_info['order_rewards'];
					$rewards_data['rewards_history_time_create'] = date('Y-m-d H:i:s',time());
					$this->rewardsmodel->createRewardsHistory($rewards_data);
					//更新用户rewards
					$this->customer->updateUserRewards($order_info['customer_id'],$order_info['order_rewards']);
				}
				
				//GA订单数据
				$this->_processGAInfo($order_info);
				
				//支付成功邮件
				$this->SuccessEmail($order_info);
			}
			
		}

	}
	
}

/* End of file paypal_payment.php */
/* Location: ./application/controllers/default/paypal_payment.php */