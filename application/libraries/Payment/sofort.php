<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sofort extends CI_Module {

	const PAY_ID = 5;
	const PAY_CODE = 'sofort';

	const ALLOW_CURRENCY = 'EUR/CHF/GBP';
	const URL_SANDBOX = 'http://preprod.checkout.com/lpi/localpayments/localpayments.aspx';
	const URL_PRODUCTION = 'https://lpi.checkout.com/localpayments/localpayments.aspx';
	const MERCHANT_ID_SANDBOX = 'EachBuyerSofortP';
	const MERCHANT_ID_PRODUCTION = 'SB201401030013';
	const PASSWORD_SANDBOX = 'Password1!';
	const PASSWORD_PRODUCTION = 'L5351xn9O832mK3';

	public function checkPaymentAvailable(){
		global $payment_list;
		if(!in_array(self::PAY_CODE,$payment_list)){
			return false;
		}

		if(strpos(self::ALLOW_CURRENCY,$this->m_app->currentCurrency()) === false){
			return false;
		}

		return true;
	}

	public function getUrl(){
		if(defined('SOFORT_SANDBOX_DISABLED') && SOFORT_SANDBOX_DISABLED === true){
			return self::URL_PRODUCTION;
		}else{
			return self::URL_SANDBOX;
		}
	}

	public function getMerchantId(){
		if(defined('SOFORT_SANDBOX_DISABLED') && SOFORT_SANDBOX_DISABLED === true){
			return self::MERCHANT_ID_PRODUCTION;
		}else{
			return self::MERCHANT_ID_SANDBOX;
		}
	}

	public function getPassword(){
		if(defined('SOFORT_SANDBOX_DISABLED') && SOFORT_SANDBOX_DISABLED === true){
			return self::PASSWORD_PRODUCTION;
		}else{
			return self::PASSWORD_SANDBOX;
		}
	}

	public function checkBankCode(){
		if(defined('SOFORT_SANDBOX_DISABLED') && SOFORT_SANDBOX_DISABLED === true){
			return false;
		}else{
			return true;
		}
	}

	public function callRequest($order){
		$xml = $this->_generateRequestXml($order);

		$this->log->write( Log::LOG_TYPE_SOFORT , $xml );

		$request = curl_init();
		curl_setopt($request, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($request, CURLOPT_URL, $this->getUrl());
		curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($request, CURLOPT_TIMEOUT, 60);
		curl_setopt($request, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($request, CURLOPT_HTTPHEADER, array('Connection: close'));
		curl_setopt($request, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($request, CURLOPT_SSL_VERIFYHOST, false);
		$response = curl_exec ($request);
		if (curl_errno($request)) {
			$response = curl_errno($request).':'.curl_error($request);
		}
		curl_close($request);

		$this->log->write( Log::LOG_TYPE_SOFORT , $response );

		return $response;
	}

	protected function _generateRequestXml($order){
		$bankcode = $this->checkBankCode()?'<bankcode>00000</bankcode>':'';
		$xml = '<request>
			<transactiondetails>
				<merchantcode>' . $this->getMerchantId() . '</merchantcode>
				<merchantpwd>' . $this->getPassword() . '</merchantpwd>
				<trackid>'. $order['order_sn'] .'</trackid>
			</transactiondetails>
			<paymentdetails>
				<paysource>sofort</paysource>
				<actioncode>1</actioncode>
				<amount>'.$order['order_amount'].'</amount>
				<currency>' . $order['currency_code'] . '</currency>
				<countrycode>' . strtoupper($this->m_app->currentLanguageCode()) . '</countrycode>
				' . $bankcode . '
				<interfaceversion>1.0</interfaceversion>
				<languagecode>EN</languagecode>
			</paymentdetails>
			<notificationurls>
				<successurl>' . eb_gen_ssl_url('sofort_payment/success') . '</successurl>
				<failurl>' . eb_gen_ssl_url('sofort_payment/success') . '</failurl>
				<notifyurl>' . eb_gen_url('sofort_payment/notify').'?cid='.generateGACid() . '</notifyurl>
			</notificationurls>
			<lpidetails>
				<transactionsource>api</transactionsource>
			</lpidetails>
			</request>';

		$xml = str_replace("\n",'',$xml);
		$xml = str_replace("\t",'',$xml);
		return $xml;
	}
}

/* End of file sofort.php */
/* Location: ./application/modules/sofort.php */