<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';
/**
 * 正常支付处理
 */
class Pay extends Dcontroller {

	public function __construct(){
		parent::__construct();
		$this->load->language('place_order',currentLanguageCode());
		//$this->load->library('payment');
		$this->load->library('Payment/adyen');
		
	}
/*
| -------------------------------------------------------------------
|  Adyen Functions
| -------------------------------------------------------------------
*/
	/**
	 * 支付方式跳转
	 * @param  string $code 支付方式关键词
	 * @param  string $brandCode 具体支付方式
	 * @param  string $issuerId  具体支付方式限定标志
	 */
	public function redirect($code, $brandCode = '') {
		//判断如果支付方式不存跳转至购物车页面
		if(!class_exists($code)) { redirect(genURL('cart')); }
		
		global $payment_list;
		if(!in_array($brandCode, $payment_list) || $brandCode == 'paypalsk'){
			redirect(genURL('cart'));
		}
		//取出订单sn
		$orderSn = $this->session->get('order_code');
		
		//订单号为空的时候返回购物车
		if($orderSn === false) { redirect(genURL('cart')); }
		//检测订单信息是否存在
		$this->load->model('ordermodel','ordermodel');
		$order_info = $this->ordermodel->getOrderWithCode($orderSn);
		if(!$order_info || empty($order_info)) { redirect(genURL('cart')); }
		
		//判断支付id是否合法
		if(!$this->$code->checkPaymentIdAvailable($order_info['payment_id'])) { redirect(genURL('cart')); }
		//判断是否可以支付
		if(!$this->$code->checkPaymentAvailable()) { redirect(genURL('cart')); }
		//返回支付的url
		$this->_view_data['url'] = $this->$code->getPaymentUrl(trim($brandCode));
		//返回支付的方式
		$this->_view_data['params'] = $this->$code->getPaymentParams($order_info, trim($brandCode));
		//$params = $this->$code->getPaymentParams($order_info, trim($brandCode));
		//echo "url===<pre>";print_r($params);die;
		//渲染页面
		$this->load->view(strtolower(get_class($this)),$this->_view_data);
	}

	/**
	 * 支付的返回结果
	 * @param  string $code 支付方式关键词
	 */
	public function result($code) {
		//监测支付的状态
		$payStatus = $this->$code->checkPaymentSuccess();
		$order_code = $this->input->get('merchantReference');
		$paymentMethod = $this->input->get('paymentMethod');

		//读取库，查看是否状态是否已经改写
		$this->load->model('ordermodel','ordermodel');
		$order_info = $this->ordermodel->getOrderWithCode($order_code);

		if(!empty($order_info) && $order_info['order_status']== OD_CREATE){
			global $payment_list;
			$map_payment = array_flip($payment_list);
			$payment_id = id2name($paymentMethod,$map_payment,$order_info['payment_id']);
			
			//订阅邮箱状态
			if($payStatus==OD_PAID || $payStatus==OD_PAYING) $this->orderSubscribe($order_info['order_email']);
			
			if($payStatus==OD_PAID){//支付成功
				$change_order_array=  array(
					'payment_id'=>$payment_id,
					'order_status'=>OD_PAID,
					'order_transnumber'=>$_GET['pspReference'],
					'order_time_pay'=>date('Y-m-d H:i:s' ,time()),
				);
				$this->ordermodel->updateOrderWithCode($order_code,$change_order_array);
				//新增order_action动作
				$order_action_info = json_encode(array('payment_id'=>$payment_id,'transnumber'=>$_GET['pspReference']));
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
				//************************************
			}elseif($payStatus==OD_PAYING){//返回支付paying时，判断服务器对支付结果
				if(!empty($order_info) && $order_info['order_status']==OD_CREATE){
					$this->ordermodel->updateOrderWithCode($order_code,array('order_status'=>OD_PAYING));
					//新增order_action动作
					$this->_writeOrderAction($order_info['order_id'],ORDER_ACTION_TYPE_PAYING);
				}
			}
		}
		//记录日志
		$this->log->write(Log::LOG_TYPE_ADYEN,json_encode(array('order_code'=>$order_code,'order_status'=>$payStatus)));
		//成功跳转
		redirect(gensslURL('success').'?success='.($payStatus == OD_PAID?1:0).'&payment='.$this->$code->code(),'refresh');
		
	}

	public function notifcation($code){
		echo '[accepted]';

		if(!class_exists($code)) die();
		if(!$this->$code->checkEnvi()) die();

		$this->$code->writeLog($_POST);
	}

	/**
	 * adyen支付状态毁掉接口
	 */
	public function notifcation_master($code) {
		echo '[accepted]';

		//判断支付的类库文件是否存在
		if(!class_exists($code)) { die(); }

		//adyen支付环境检查
		if(!$this->$code->checkEnvi()) { die(); }

		//记录log并返回log信息
		$info = $this->$code->createLog();
		
		//回写数据库
		if( $info['success'] == 1 && $info['code'] == 'AUTHORISATION' ){
			$this->load->model('ordermodel','ordermodel');
			$order = $this->ordermodel->getOrderWithCode($info['order_sn']);
			
			global $payment_list;
			$map_payment = array_flip($payment_list);
			$payment_id = id2name($info['payment_method'],$map_payment,$order['payment_id']);
			
			if($order['order_status']== OD_CREATE || $order['order_status']== OD_PAYING){
				$data = array(
						'payment_id'=>$payment_id,
						'order_status'=>OD_PAID,
						'order_transnumber'=>$info['trans_number'],//交易号，成功支付后才有
						'order_time_pay'=>date('Y-m-d H:i:s' ,time())
				);
				$this->load->model('ordermodel','ordermodel');
				$this->ordermodel->updateOrderWithCode($order['order_code'],$data);
				$order_action_info = json_encode(array('payment_id'=>$payment_id,'transnumber'=>$info['trans_number']));
				$this->_writeOrderAction($order['order_id'],ORDER_ACTION_TYPE_PAID,$order_action_info);
				
				//积分（rewards,往customer表中rewards加1%）
				if(isset($order['order_rewards']) && $order['order_rewards']){
					//记录rewards奖励history
					$this->load->model('rewardsmodel','rewardsmodel');
					$rewards_data['customer_id'] = $order['customer_id'];
					$rewards_data['rewards_history_type'] = 1;
					$rewards_data['order_id'] = $order['order_id'];
					$rewards_data['rewards_history_value'] = $order['order_rewards'];
					$rewards_data['rewards_history_time_create'] = date('Y-m-d H:i:s',time());
					$this->rewardsmodel->createRewardsHistory($rewards_data);
					//更新用户rewards
					$this->customer->updateUserRewards($order['customer_id'],$order['order_rewards']);
				}
				
				//GA数据发送
				$this->_processGAInfo($order);
				
				//订阅邮箱状态
				$this->orderSubscribe($order['order_email']);
				
				//支付成功邮件
				$this->SuccessEmail($order);
			}
			
		}
		
		//日志记录
		$this->log->write(Log::LOG_TYPE_ADYEN,json_encode($info));
	}
}

/* End of file pay.php */
/* Location: ./application/controllers/default/pay.php */
