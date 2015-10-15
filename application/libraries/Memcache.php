<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CI_Memcache {

	protected $_CI = NULL;
	protected $_memcache = NULL;
	protected $_flg_disable = false;
    protected $_buffer = array();

	public function __construct(){
		$this->_CI = & get_instance();
		global $memcache_list;

		$this->_memcache = new Memcache;
		foreach($memcache_list as $server){
			$this->_memcache->addServer($server['host'],$server['port']);
			$this->_memcache->setCompressThreshold(2000,0.2);
		}
		
		//采用 单独页面 种cookie
		$flush_cache = get_cookie('flush_cache');
		$this->_flg_disable = ($flush_cache==SALT);
	}
/*
| -------------------------------------------------------------------
|  Public Functions
| -------------------------------------------------------------------
*/
	public function set($key,$value,$expire = 60){
		if(defined("DISABLE_CACHE") && DISABLE_CACHE === true) return true;

		$this->_memcache->set($key,$value,false,$expire);
        $this->_buffer[$key] = $value;
	}

	public function get($key){
		if(defined("DISABLE_CACHE") && DISABLE_CACHE === true) return false;
		//判断是否需要读取缓存
		if($this->_flg_disable) return false;

        if(!isset($this->_buffer[$key])) {
            $this->_buffer[$key] = $this->_memcache->get($key);
        }
        return  $this->_buffer[$key];
	}

	public function delete($key){
		if(defined("DISABLE_CACHE") && DISABLE_CACHE === true) return true;

		$this->_memcache->delete($key);
	}
}

/* End of file Memcache.php */
/* Location: ./application/libraries/Memcache.php */
