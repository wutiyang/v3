<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Checkout extends CI_Module {

	const PAY_ID = 4;
	const PAY_CODE = 'credit_card';

	const MERCHANT_ID_SANDBOX = 'CHINAMADETEST';
	const MERCHANT_ID_PRODUCTION = 'CHINAMADE';
	const PASSWORD_SANDBOX = 'CHINAMADETEST987';
	const PASSWORD_PRODUCTION = 'CHINAMADE15';
	const URL = 'https://api.checkout.com/process/gateway.aspx';

	public function __construct(){
		//load models
		$this->OrderModel = new OrderModel();
		$this->load->model('Paymentmodel','m_payment');
	}

	public function checkPaymentAvailable($country_code){
		global $payment_list;
		if(!in_array(self::PAY_CODE,$payment_list)){
			return false;
		}

		if(!$this->checkCountryAvailable($country_code)){
			return false;
		}

		$config = $this->m_payment->getCreditCardLimitConfig();
		if(isset($config['pay_amount']) && !$this->checkOrderAmountLimit($config['pay_amount'])){
			return false;
		}

		if(isset($config['pay_num']) && !$this->checkOrderCountLimit($config['pay_num'])){
			return false;
		}

		return true;
	}

	public function checkCountryAvailable($country_code){
		if($this->m_payment->checkCountryCreditCardAllowed($country_code)){
			return true;
		}else{
			return false;
		}
	}

	public function checkOrderAmountLimit($limit_info){
		if($this->OrderModel->getUserPaymentOrderAmount($this->m_app->getCurrentUserId(),self::PAY_ID,strtotime('-'.$limit_info['day'].' day')) >= $limit_info['amount']){
			return false;
		}else{
			return true;
		}
	}

	public function checkOrderCountLimit($limit_info){
		if($this->OrderModel->getUserPaymentOrderCount($this->m_app->getCurrentUserId(),self::PAY_ID,strtotime('-'.$limit_info['day'].' day')) >= $limit_info['number']){
			return false;
		}else{
			return true;
		}
	}

	public function getMerchantId(){
		if(defined('CHECKOUT_SANDBOX_DISABLED') && CHECKOUT_SANDBOX_DISABLED === true){
			return self::MERCHANT_ID_PRODUCTION;
		}else{
			return self::MERCHANT_ID_SANDBOX;
		}
	}

	public function getPassword(){
		if(defined('CHECKOUT_SANDBOX_DISABLED') && CHECKOUT_SANDBOX_DISABLED === true){
			return self::PASSWORD_PRODUCTION;
		}else{
			return self::PASSWORD_SANDBOX;
		}
	}

	public function callRequest($order,$address){
		$xml = $this->_generateRequestXml($order,$address);

		$this->log->write( Log::LOG_TYPE_CHECKOUT , $xml );

		$request = curl_init();
		curl_setopt($request, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($request, CURLOPT_URL, self::URL);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($request, CURLOPT_TIMEOUT, 60);
		curl_setopt($request, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($request, CURLOPT_HTTPHEADER, array('Connection: close'));
		curl_setopt($request, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($request, CURLOPT_SSL_VERIFYHOST, false);
		// curl_setopt($request, CURLOPT_SSL_CIPHER_LIST, "RC4-SHA"); //test
		curl_setopt($request, CURLOPT_SSL_CIPHER_LIST, "rsa_rc4_128_sha"); //online
		$response = curl_exec ($request);
		if (curl_errno($request)) {
			$response = curl_errno($request).':'.curl_error($request);
		}
		curl_close($request);

		$this->log->write( Log::LOG_TYPE_CHECKOUT , $response );

		return $response;
	}

	protected function _generateRequestXml($order,$address){
		$credit_card_info = $this->session->get('credit_card_info');

		$xml = '<request>
			<account_identifier></account_identifier>
			<merchantid>' . $this->getMerchantId() . '</merchantid>
			<password>' . $this->getPassword() . '</password>
			<action>1</action>
			<trackid>' . $order['order_sn'] . '</trackid>
			<bill_currencycode>' . $order['currency_code'] . '</bill_currencycode>
			<bill_cardholder>'.$address['first_name'].' '.$address['last_name'].'</bill_cardholder>
			<bill_cc>'.id2name('CardNumber',$credit_card_info).'</bill_cc>
			<bill_expmonth>'.str_pad(id2name('ExpirationMonth',$credit_card_info),2,'0',STR_PAD_LEFT).'</bill_expmonth>
			<bill_expyear>20'.id2name('ExpirationYear',$credit_card_info).'</bill_expyear>
			<bill_cvv2>'.id2name('CVVCode',$credit_card_info).'</bill_cvv2>
			<bill_amount>'.$order['order_amount'].'</bill_amount>
			<bill_address>' . $address['address'] . '</bill_address>
			<bill_address2></bill_address2>
			<bill_postal>' . $address['zipcode'] . '</bill_postal>
			<bill_city>' . $address['city'] . '</bill_city>
			<bill_state>' . $address['province'] . '</bill_state>
			<bill_email>' . $order['email'] . '</bill_email>
			<bill_country>' . $address['country'] . '</bill_country>
			<bill_phone>' . $address['mobile'] . '</bill_phone>
			<bill_fax></bill_fax></request>';

		$xml = str_replace("\n",'',$xml);
		$xml = str_replace("\t",'',$xml);
		return $xml;
	}
}

/* End of file checkout.php */
/* Location: ./application/modules/checkout.php */