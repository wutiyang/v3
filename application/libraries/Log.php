<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Log {

	const LOG_TYPE_ORDER = 'order';
	const LOG_TYPE_PAYPAL = 'paypal';
	const LOG_TYPE_CHECKOUT = 'checkout';
	const LOG_TYPE_SOFORT = 'sofort';
	const LOG_TYPE_SAFETYPAY = 'safetypay';
	const LOG_TYPE_PAYPAL_EC = 'paypal_ec';
	const LOG_TYPE_ADYEN = 'adyen';
	const LOG_TYPE_SYSTEM_EMAIL = 'system_email';

	protected $_log_root = '';
	protected $_log_enabled = true;

	protected $_enabled_log_type = array(
		self::LOG_TYPE_ORDER,
		self::LOG_TYPE_PAYPAL,
		self::LOG_TYPE_CHECKOUT,
		self::LOG_TYPE_SOFORT,
		self::LOG_TYPE_SAFETYPAY,
		self::LOG_TYPE_PAYPAL_EC,
		self::LOG_TYPE_ADYEN,
		self::LOG_TYPE_SYSTEM_EMAIL,
	);

	public function __construct(){
		$this->_log_root = LOG_PATH;
		$this->_log_enabled = true;

		if(!is_dir($this->_log_root) || !is_really_writable($this->_log_root)){
			$this->_log_enabled = false;
		}
	}

	public function write($type, $content, $isScriptTask = false){
		if(!$this->_log_enabled){
			return false;
		}

		if(!in_array($type,$this->_enabled_log_type)){
			return false;
		}

		$path = $this->_log_root . $type . '/';
		if(!is_dir($path)) mkdir($path,0777);
		if(!is_writeable($path)) chmod($path,777);

		if( $isScriptTask === false ){
			$file = $path.$type . '-' . $_SERVER['SERVER_ADDR'] . '-' . date('Y-m-d') . '.log';
		}else{
			$file = $path.$type . '-' . date('Y-m-d') . '.log';
		}

		$message = '';
		if (!file_exists($file)){
			$message .= "<"."?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?".">\n\n";
		}
		if( $isScriptTask === false ){
			$message .= date('Y-m-d H:i:s').' '. $_SERVER['SERVER_ADDR'] . ':'.$content."\n";
		}else{
			$message .= date('Y-m-d H:i:s') . ' :'.$content."\n";
		}

		if ( !$fp = @fopen($file, FOPEN_WRITE_CREATE)){
			return false;
		}

		flock($fp, LOCK_EX);
		fwrite($fp, $message);
		flock($fp, LOCK_UN);
		fclose($fp);

		@chmod($file, FILE_WRITE_MODE);

		return true;
	}
}

/* End of file Log.php */
/* Location: ./application/libraries/Log.php */