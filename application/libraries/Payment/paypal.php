<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Paypal extends CI_Model {

	const PAY_ID = 2;
	const PAY_CODE = 'paypal';

	//TODO 修改为配置 Kim
	const PAY_ACCOUNT = 'pay@eachbuyer.com';
	const URL_SANDBOX = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
	const URL_PRODUCTION = 'https://www.paypal.com/cgi-bin/webscr';


	public function checkPaymentAvailable(){
		global $payment_list;

		return in_array(self::PAY_CODE,$payment_list);
	}

	public function getPaypalUrl(){
		if(defined('PAYPAL_SANDBOX_DISABLED') && PAYPAL_SANDBOX_DISABLED === true){
			return self::URL_PRODUCTION;
		}else{
			return self::URL_SANDBOX;
		}
	}

	public function getInfoFromPaypalRequest(){
		$result = array();

		$order_sn = $this->input->post('invoice');
		if($order_sn !== false){
			$result['order_sn'] = $order_sn;
		}

		$txn_id = $this->input->post('txn_id');
		if($txn_id !== false){
			$result['txn_id'] = $txn_id;
		}

		$payer_email = $this->input->post('payer_email');
		if($payer_email !== false){
			$result['payer_email'] = $payer_email;
		}

		return $result;
	}

	public function confirmPayment(){
		$params = 'cmd=_notify-validate';
		foreach ($_POST as $key => $value){
			$value = urlencode(stripslashes($value));
			$params .= "&$key=$value";
		}

		$request = curl_init();
		curl_setopt_array($request, array(
			CURLOPT_URL => $this->getPaypalUrl(),
			CURLOPT_POST => TRUE,
			CURLOPT_POSTFIELDS => $params,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HEADER => FALSE,
			CURLOPT_TIMEOUT => 30,
		));
		$response = curl_exec($request);
		if (curl_errno($request)) {
			$response = curl_errno($request).':'.curl_error($request);
		}

		curl_close($request);

		$this->log->write( Log::LOG_TYPE_PAYPAL , json_encode(array('response' => $response,'request' => $params)) );

		return ($response == 'VERIFIED');
	}

}

/* End of file paypal.php */
/* Location: ./application/modules/paypal.php */