<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';
/**
 * 正常支付处理
 */
class Pay extends Dcontroller {

	public function __construct(){
		parent::__construct();
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
		$orderSn = $this->session->get('order_sn');

		//订单号为空的时候返回购物车
		if($orderSn === false) { redirect(genURL('cart')); }

		//取出订单的信息
		$order = $this->OrderModel->getOrderBySn($orderSn);

		//订单为空的时候返回购物车
		if(empty($order) || $order['user_id'] != $this->m_app->getCurrentUserId()) { redirect(eb_gen_url('cart')); }
		
		//判断支付id是否合法
		//if($order['pay_id'] != $this->$code->id()) { redirect(eb_gen_url('cart')); }
		if(!$this->$code->checkPaymentIdAvailable($order['pay_id'])) { redirect(genURL('cart')); }

		//判断是否可以支付
		if(!$this->$code->checkPaymentAvailable()) { redirect(genURL('cart')); }
		
		//返回支付的url
		$this->_view_data['url'] = $this->$code->getPaymentUrl(trim($brandCode));
		//返回支付的方式
		$this->_view_data['params'] = $this->$code->getPaymentParams($order, trim($brandCode));
		//渲染页面
		$this->load->view(strtolower(get_class($this)),$this->_view_data);
	}

	/**
	 * 支付的返回结果
	 * @param  string $code 支付方式关键词
	 */
	public function result($code) {
		//判断如果支付方式不存跳转至购物车页面
		if(!class_exists($code)) { redirect(genURL('cart')); }

		//监测支付的状态
		$payStatus = $this->$code->checkPaymentSuccess();

		//支付状态处理
		if($payStatus == PS_PAID) {
			//获取订单的信息
			$order = $this->OrderModel->getOrderBySn($this->$code->getPaymentOrderSn());

			//判断订单是否为空
			if(!empty($order) && $order['pay_status'] != PS_PAID) {
				$order['billing_address']['consignee'] = id2name('consignee',$order);
				$order['billing_address']['address'] = id2name('address',$order);
				$order['billing_address']['address2'] = id2name('address2',$order);
				$order['billing_address']['city'] = id2name('city',$order);
				$order['billing_address']['province'] = id2name('province',$order);
				$order['billing_address']['zipcode'] = id2name('zipcode',$order);
				$order['billing_address']['country'] = id2name('country',$order);
				$order['billing_address']['mobile'] = id2name('mobile',$order);
				//记录订单支付的信息和状态
				$this->payment->markOrderPaid($order,$this->$code->getPaymentTransNumber(),$order['email']);
			}
			redirect(gensslURL('success').'?payment='.$this->$code->code(),'refresh');
		} elseif($payStatus == PS_PAYING) {
			//获取订单的信息
			$order = $this->OrderModel->getOrderBySn($this->$code->getPaymentOrderSn());
			//判断订单是否为空
			if(!empty($order) && $order['pay_status'] == PS_UNPAID) {
				//标记订单支付
				$this->payment->markOrderPaying($order,$this->$code->getPaymentTransNumber());
			}
		}else{
			//记录返回未支付的订单
			try {
				$this->OrderModel->createPayErrorLog( $code , 1 , $_GET , 255 );
			} catch (Exception $e) {}
		}

		redirect(gensslURL('unpaid').'?payment='.$this->$code->code(),'refresh');
		//redirect(eb_gen_url('success').'?payment='.$this->$code->code(),'refresh');
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
		if( $info['success'] == 1 && $info['code'] == 'AUTHORISATION' ){
			$order = $this->OrderModel->getOrderBySn($info['order_sn']);
			if( !empty( $order ) ){
				if( $order['pay_status'] != PS_PAID ){
					$order['billing_address']['consignee'] = id2name('consignee',$order);
					$order['billing_address']['address'] = id2name('address',$order);
					$order['billing_address']['address2'] = id2name('address2',$order);
					$order['billing_address']['city'] = id2name('city',$order);
					$order['billing_address']['province'] = id2name('province',$order);
					$order['billing_address']['zipcode'] = id2name('zipcode',$order);
					$order['billing_address']['country'] = id2name('country',$order);
					$order['billing_address']['mobile'] = id2name('mobile',$order);
					$this->payment->markOrderPaid($order,$info['trans_number'],$order['email']);
				}
				
				//记录必要日志
				if( $order['pay_status'] == PS_PAYING || $order['pay_status'] == PS_PAID ){
					//记录订单状态是支付成功的订单
					try {
						$this->OrderModel->createPayErrorLog( $code , 5 , $_POST , 255 );
					} catch (Exception $e) {}
				}else{
					//记录订单状态不是支付中 也不是支付成功的订单
					try {
						$this->OrderModel->createPayErrorLog( $code , 2 , $_POST , 9 );
					} catch (Exception $e) {}
				}
				
			}else{
				//记录订单不存在的情况
				try {
					$this->OrderModel->createPayErrorLog( $code , 3 , $_POST , 10 );
				} catch (Exception $e) {}
			}
		}else{
			//记录不合法的订单
			try {
				$this->OrderModel->createPayErrorLog( $code , 4 , $_POST , 254 );
			} catch (Exception $e) {}
		}
	}
}

/* End of file pay.php */
/* Location: ./application/controllers/default/pay.php */
