<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Adyen extends CI_Model {

	const PAY_ID = 7;
	const PAY_CODE = 'adyen';

	const DISABLE_COUNTRY = '';
	const MERCHANT_ACCOUNT = 'EachBuyerEurope';
	const SKIN_DEFAULT = 'h2zwU6F6';
	const SKIN_MOBILE = 'vIEMVwe1';

	const BASE_URL_SANDBOX = 'https://test.adyen.com/hpp/';
	const BASE_URL_PRODUCTION = 'https://live.adyen.com/hpp/';
	const HMAC_SANDBOX = 'eachbuyer';
	const HMAC_PRODUCTION = 'pbbVBLdOtCb3v5jl';

	const URL_ONE_PAGE = 'pay.shtml';
	const URL_MULTI_PAGE = 'select.shtml';

	const URL_PAYMENT_DETAILS = 'details.shtml';
	const URL_DIRECTORY = 'directory.shtml';

	public function id(){
		return self::PAY_ID; //@todo存在数据库里或用数组管理
	}

	public function code(){
		return self::PAY_CODE;
	}

	public function checkPaymentIdAvailable($payId = ''){
		global $payment_list;
		$arrKeys = array_keys($payment_list);
		if(in_array($payId , $arrKeys)){
			return true;
		}
		return false;
	}

	public function checkPaymentAvailable($payment_country = false){
		global $payment_list;
		$payment_country = strval($payment_country);

		if($payment_country != '' && strpos(self::DISABLE_COUNTRY,$payment_country)!==false) return false;
		//@todo 应对具体的支付方式判断
		if(!in_array(self::PAY_CODE,$payment_list)) return false;

		return true;
	}

	public function getPaymentUrl($brandCode = ''){
		$url = '';
		if(defined('ADYEN_SANDBOX_DISABLED') && ADYEN_SANDBOX_DISABLED === true){
			$url = self::BASE_URL_PRODUCTION;
		}else{
			$url = self::BASE_URL_SANDBOX;
		}
		//兼容老版的重支付
		if($brandCode == 'adyen'){
			if(defined('SITE_CODE') && SITE_CODE === 'mobile'){
				$url .= self::URL_MULTI_PAGE;
			}else{
				$url .= self::URL_ONE_PAGE;
			}
		}else{
			$url .= self::URL_PAYMENT_DETAILS;
		}


		return $url;
	}

	public function getAdyenDirectoryUrl(){
		$url = '';
		if(defined('ADYEN_SANDBOX_DISABLED') && ADYEN_SANDBOX_DISABLED === true){
			$url = self::BASE_URL_PRODUCTION;
		}else{
			$url = self::BASE_URL_SANDBOX;
		}

		$url .= self::URL_DIRECTORY;

		return $url;
	}

	public function getPaymentParams($order, $brandCode = 'paypal', $issuerId = '' ){
		$data = array();
		$data['merchantReference'] = $order['order_code'];
		$data['paymentAmount'] = $order['order_price']*100;
		$data['currencyCode'] = $order['order_currency'];
		$data['shipBeforeDate'] = date('Y-m-d',strtotime('+3 days'));
		$data['skinCode'] = $this->_getSkinCode();
		$data['merchantAccount'] = self::MERCHANT_ACCOUNT;
		global $language_range_array;
		$language_code = $language_range_array[$order['language_id']];
		$data['shopperLocale'] = $this->_getLanguageCode($language_code);
		$data['sessionValidity'] = date('c',strtotime('+30 minutes'));
		if($order['order_country_payment'] != '') $data['countryCode'] = $order['order_country_payment'];
		$data['shopperEmail'] = $order['order_email'];
		$data['shopperReference'] = $order['customer_id'];
		$data['resURL'] = genURL('pay/result/'.self::PAY_CODE);

		if($brandCode != 'adyen'){
			$data['brandCode'] = $brandCode;
		}
		if(!empty($issuerId)){
			$data['issuerId'] = $issuerId;
		}

		$data['merchantSig'] = $this->_genMerchantSig($data);

		return $data;
	}

	public function checkPaymentSuccess(){
		$this->log->write( Log::LOG_TYPE_ADYEN , json_encode($_GET) );

		$result = $this->input->get('authResult');
		$sig = $this->input->get('merchantSig');

		$sig = ($sig === false)?'':$sig;
		$result = ($result === false)?'':$result;

		$calculated_sig = base64_encode(pack("H*",hash_hmac(
			'sha1',
			id2name('authResult',$_GET) . id2name('pspReference',$_GET) . id2name('merchantReference',$_GET) . id2name('skinCode',$_GET) . id2name('merchantReturnData',$_GET),
			$this->_getHMACKey(id2name('skinCode',$_GET))
		)));

		if($calculated_sig != $sig) return OD_CREATE;

		$status = OD_CREATE;//创建
		switch($result){
			case 'PENDING':$status = OD_PAYING;break;
			case 'AUTHORISED':$status = OD_PAID;break;
			default:$status = OD_CREATE;break;
		}

		return $status;
	}

	public function getPaymentOrderSn(){
		return id2name('merchantReference',$_GET);
	}

	public function getPaymentTransNumber(){
		return id2name('pspReference',$_GET);
	}

	public function writeLog($info){
		$this->log->write( Log::LOG_TYPE_ADYEN , 'slave:'.json_encode($info) );
	}

	/**
	 * 记录adyen支付日志
	 */
	public function createLog() {
		//日志记录
		$this->log->write( Log::LOG_TYPE_ADYEN , 'master:'.json_encode($_POST) );

		//支付返回信息
		$info = array(
			'order_sn' => id2name('merchantReference',$_POST),
			'trans_number' => id2name('pspReference',$_POST),
			'payment_amount' => id2name('value',$_POST),
			'payment_amount_extracosts' => id2name('additionalData_extraCostsValue',$_POST),
			'currency' => id2name('currency',$_POST),
			'code' => id2name('eventCode',$_POST),
			'info' => id2name('reason',$_POST),
			'success' => id2name('success',$_POST),
			'payment_method' => id2name('paymentMethod',$_POST),
			'time_event' => id2name('eventDate',$_POST),
			'time_record' => NOW,
			'request_content' => json_encode($_POST),
		);
		$info['payment_amount'] = floatval($info['payment_amount']/100);
		$info['payment_amount_extracosts'] = floatval($info['payment_amount_extracosts']/100);
		$info['success'] = $info['success']=='true'?1:0;

		/*
		$this->load->model('Paymentmodel','m_payment');
		//检查记录支付信息
		if(!$this->m_payment->checkAdyenNotificationExists($info['trans_number'])){
			$this->m_payment->createAdyenNotification($info);
		}
		*/
		return $info;
	}

	/**
	 * adyen支付环境检查
	 */
	public function checkEnvi(){
		if(defined('ADYEN_SANDBOX_DISABLED') && ADYEN_SANDBOX_DISABLED === true){
			$live = id2name('live',$_POST,'false');
			if($live != 'true'){
				return false;
			}
		}
		return true;
	}

	protected function _getSkinCode(){
		if(defined('SITE_CODE') && SITE_CODE === 'mobile'){
			return self::SKIN_MOBILE;
		}else{
			return self::SKIN_DEFAULT;
		}
	}

	protected function _getLanguageCode($language_code){
		$code = 'en_US';
		switch($language_code){
			case 'us':$code = 'en_US';break;
			case 'de':$code = 'de';break;
			case 'es':$code = 'es';break;
			case 'it':$code = 'it';break;
			case 'fr':$code = 'fr';break;
			case 'br':$code = 'pt';break;
			case 'ru':$code = 'ru';break;
		}

		return $code;
	}

	protected function _genMerchantSig($data){
		$sig = base64_encode(pack("H*",hash_hmac(
			'sha1',
			$data['paymentAmount'] . $data['currencyCode'] . $data['shipBeforeDate'] . $data['merchantReference'] . $data['skinCode'] . $data['merchantAccount'] .
			$data['sessionValidity'] . $data['shopperEmail'] . $data['shopperReference'],
			$this->_getHMACKey($data['skinCode'])
		)));
		return $sig;
	}

	protected function _getHMACKey($skinCode){
		if(defined('ADYEN_SANDBOX_DISABLED') && ADYEN_SANDBOX_DISABLED === true){
			return self::HMAC_PRODUCTION;
		}else{
			return self::HMAC_SANDBOX;
		}
	}


	/**
	 * @desc 获取即时adyen列表(有缓存)
	 * @param string $countryCode
	 * @param string $currencyCode
	 * @param number $payAmount
	 * @return Ambigous <string, mixed>
	 */
	public function callDirectoryLookupRequest($countryCode = 'US', $currencyCode = 'EUR', $payAmount = 0 ){
		if(!trim($countryCode )) {
			$countryCode = 'US';
			$currencyCode = 'EUR';
		}
		//$countryCode = "DE";
		//$currencyCode = "EUR";
		//与金额大小没有关系
		global $mem_expired_time;
		$mem_key = md5("each_buyer_adyen_list_".$countryCode."_".$currencyCode);
		//$this->memcache->delete($mem_key);
		if(!$response = $this->memcache->get($mem_key)){
			$request = array(
					"paymentAmount" => $payAmount * 100, //不能有小数 不同支付金额，有些支付方式是不支持的
					"currencyCode" => $currencyCode,
					"merchantReference" => 'Request payment methods',
					"skinCode" => $this->_getSkinCode(),
					"merchantAccount" => self::MERCHANT_ACCOUNT,
					'sessionValidity' => date('c',strtotime('+30 minutes')),
					"countryCode" => $countryCode,
					"merchantSig" => "",
			);
			$request['merchantSig'] = base64_encode(pack("H*",hash_hmac(
					'sha1',
					$request["paymentAmount"] .
					$request["currencyCode"] . $request["merchantReference"] .
					$request["skinCode"] .  $request["merchantAccount"] . $request["sessionValidity"],
					$this->_getHMACKey($request["skinCode"])
			)));
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,  $this->getAdyenDirectoryUrl());
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//@todo发布时最好去掉
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//1 可以将他返回的内容赋值给一个变量, 0 返回bool值
			curl_setopt($ch, CURLOPT_POST,count($request));
			curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($request));
			
			$response = curl_exec($ch);
			
			if (curl_errno($ch)) {
				$response = json_encode(array());
			}else{
				$this->memcache->set($mem_key, $response,$mem_expired_time['adyen_list']);
			}
			curl_close($ch);
			
			
			//$this->memcache->set($mem_key, $response,$mem_expired_time['adyen_list']);
		}
		return $response;
	}

	/**
	 * 获得常用的支付方式
	 * @param type $country  $cart['payment_country']
	 * @param type $email
	 * @return type
	 */
	public function getCommonPayMethods($country = ''){
		if(empty($country) ){
			return array();
		}
		$email =  $this->session->get('email');
		$cacheParams = array( $country,  $email);
		$cache_key = "idx_get_common_pay_method_%s_%s";
		$list = $this->memcache->get($cache_key, $cacheParams);
		if($list === false){
			//这里字段可能不一定就是这个
			$this->db_ebmaster_read->select('`pay_name`, `id`');
			$this->db_ebmaster_read->from('order_info');
			$this->db_ebmaster_read->where('email', $email);
			$this->db_ebmaster_read->where('country', $country);
			$this->db_ebmaster_read->order_by( '`id`', 'desc' );
			$this->db_ebmaster_read->limit(4);
			$query = $this->db_ebmaster_read->get();
			$list = $query->result_array();

			if(!empty($list)){
				$this->memcache->set($cache_key , $list, $cacheParams);
			}
		}
		return $list;
	}
}

/* End of file adyen.php */
/* Location: ./application/modules/adyen.php */