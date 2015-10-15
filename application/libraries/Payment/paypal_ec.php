<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//class Paypal_ec extends CI_Module {
class Paypal_ec extends CI_Model {

	const PAY_ID = 1;
	const PAY_CODE = 'paypal_ec';

	const USERNAME_SANDBOX = '727918519-facilitator_api1.qq.com';
	const USERNAME_PRODUCTION = 'support_api1.ebest24.com';
	const PASSWORD_SANDBOX = '1392201103';
	const PASSWORD_PRODUCTION = 'Q9MUN95BUJDUJ25L';
	const SIGNATURE_SANDBOX = 'AFcWxV21C7fd0v3bYYYRCpSSRl31AZV3viuVWOPtwFTi1SJtLwqZpk6W';
	const SIGNATURE_PRODUCTION = 'ApVS1icUpZaY01vRGWLuzO8K87yUA7sxBWtet5-VVaPSP6omb7A.oNHr';
	const API_URL_SANDBOX = 'https://api-3t.sandbox.paypal.com/nvp';
	const API_URL_PRODUCTION = 'https://api-3t.paypal.com/nvp';
	const URL_SANDBOX = 'https://www.sandbox.paypal.com/cgi-bin/webscr&cmd=_express-checkout';
	const URL_PRODUCTION = 'https://www.paypal.com/cgi-bin/webscr&cmd=_express-checkout';

	public function checkPaymentAvailable(){
		global $payment_list;
		if(!in_array(self::PAY_CODE,$payment_list)){
			return false;
		}

		return true;
	}

	public function getPaypalECCurrency(){
		$currency = currentCurrency();
		if(in_array($currency,array('BRL' , 'INR'))){
			return DEFAULT_CURRENCY;
		}else{
			return $currency;
		}
	}

	public function getPaypalECLocaleCode(){
		$language_code = currentLanguageCode();
		
		$lang2localecode = array(
			'us' => 'en_US',
			'de' => 'de_DE',
			'fr' => 'fr_FR',
			'it' => 'it_IT',
			'br' => 'pt_BR',
			'ru' => 'ru_RU',
		);

		return id2name($language_code,$lang2localecode,'en_US');
	}

	public function getPaypalECUserName(){
		if(defined('PAYPAL_EC_SANDBOX_DISABLED') && PAYPAL_EC_SANDBOX_DISABLED === true){
			return self::USERNAME_PRODUCTION;
		}else{
			return self::USERNAME_SANDBOX;
		}
	}

	public function getPaypalECPassword(){
		if(defined('PAYPAL_EC_SANDBOX_DISABLED') && PAYPAL_EC_SANDBOX_DISABLED === true){
			return self::PASSWORD_PRODUCTION;
		}else{
			return self::PASSWORD_SANDBOX;
		}
	}

	public function getPaypalECSignature(){
		if(defined('PAYPAL_EC_SANDBOX_DISABLED') && PAYPAL_EC_SANDBOX_DISABLED === true){
			return self::SIGNATURE_PRODUCTION;
		}else{
			return self::SIGNATURE_SANDBOX;
		}
	}

	public function getPaypalECApiUrl(){
		if(defined('PAYPAL_EC_SANDBOX_DISABLED') && PAYPAL_EC_SANDBOX_DISABLED === true){
			return self::API_URL_PRODUCTION;
		}else{
			return self::API_URL_SANDBOX;
		}
	}

	public function getPaypalECUrl($token){
		if(defined('PAYPAL_EC_SANDBOX_DISABLED') && PAYPAL_EC_SANDBOX_DISABLED === true){
			return self::URL_PRODUCTION . '&token='.$token;
		}else{
			return self::URL_SANDBOX . '&token='.$token;
		}
	}

	public function callPaypalRequest($action,$params = array()){
		$params['USER'] = $this->getPaypalECUserName();
		$params['PWD'] = $this->getPaypalECPassword();
		$params['SIGNATURE'] = $this->getPaypalECSignature();
		$params['version'] = 72.0;
		$params['METHOD'] = $action;

		foreach($params as $key => $value){
			$params[$key] = $key . '=' . urlencode($value);
		}
		$params = implode('&',$params);

		$this->log->write( Log::LOG_TYPE_PAYPAL_EC , $params );

		$request = curl_init();
		curl_setopt($request, CURLOPT_URL, $this->getPaypalECApiUrl());
		curl_setopt($request, CURLOPT_VERBOSE, 1);
		curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($request, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($request, CURLOPT_POST, 1);
		curl_setopt($request, CURLOPT_POSTFIELDS, $params);
		$response = curl_exec($request);
		if (curl_errno($request)) {
			$response = curl_errno($request).':'.curl_error($request);
		}
		curl_close($request);

		$this->log->write( Log::LOG_TYPE_PAYPAL_EC , $response );

		$response_arr = array();
		$response = explode('&',$response);
		foreach($response as $record){
			if(strpos($record,'=') === false) continue;
			list($key,$value) = explode('=',$record);
			$response_arr[urldecode($key)] = urldecode($value);
		}

		return $response_arr;
	}
}

/* End of file paypal_ec.php */
/* Location: ./application/modules/paypal_ec.php */