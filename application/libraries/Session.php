<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CI_Session {

	protected $_CI = NULL;
	protected $_session_id = NULL;
	protected $_memcache = NULL;
	protected $_data = array();

	public function __construct(){
		$this->_CI = & get_instance();

		$this->_memcache = new Memcache;
		$this->_memcache->addServer(SESSION_SERVER,SESSION_PORT);

		$session_token = get_cookie(SESSION_NAME);
		if(!$this->_checkSessionToken($session_token)) $this->_initSession();
		$this->_loadSession();
	}
/*
| -------------------------------------------------------------------
|  Public Functions
| -------------------------------------------------------------------
*/
	public function set($key,$value){
		$this->_data[$key] = $value;
		$this->_memcache->set($this->_session_id,addslashes(serialize($this->_data)),FALSE ,SESSION_LIFE_TIME);
	}

	public function get($key){
		if(isset($this->_data[$key])){
			return $this->_data[$key];
		}else{
			return false;
		}
	}

	public function delete($key){
		if(!isset($this->_data[$key])) return false;

		unset($this->_data[$key]);
		$this->_memcache->set($this->_session_id,addslashes(serialize($this->_data)),FALSE ,SESSION_LIFE_TIME);
	}

	public function destroy(){
		$this->_memcache->delete($this->_session_id);
		set_cookie(SESSION_NAME,'','');
		$this->_data = array();
	}

	public function dump(){
		return $this->_data;
	}
	
	public function sessionID(){
		return $this->_session_id;
	}
/*
| -------------------------------------------------------------------
|  Private Functions
| -------------------------------------------------------------------
*/
	protected function _checkSessionToken($session_token){
		if($session_token === false) return false;

		$session_id = substr($session_token,0,32);
		$session_key = substr($session_token,32);

		if($session_key != $this->_calSessionKey($session_id)){
			return false;
		}

		$this->_session_id = $session_id;
		return true;
	}

	protected function _genSessionID(){
		$this->_session_id = md5(uniqid(mt_rand(),true).mt_rand(0,1000000));
	}

	protected function _calSessionKey($session_id){
		$ip = $this->_CI->input->ip_address();
		$ip = substr($ip,0,strrpos($ip,'.'));
		$key = sprintf('%08x', crc32($ip . $session_id));

		return $key;
	}

	protected function _initSession(){
		$this->_genSessionID();
		$this->_memcache->set($this->_session_id,'a:0:{}',FALSE ,SESSION_LIFE_TIME);
		set_cookie(SESSION_NAME,$this->_session_id.$this->_calSessionKey($this->_session_id));
	}

	protected function _loadSession(){
		$session_data = $this->_memcache->get($this->_session_id);
		if($session_data === false || empty($session_data)){
			$this->_data = array();
		}else{
			$this->_data = unserialize(stripslashes($session_data));
		}
	}
}

/* End of file Session.php */
/* Location: ./application/libraries/Session.php */