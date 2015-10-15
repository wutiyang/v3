<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CI_Database {

	protected $_CI = NULL;
	protected $master = null;
	protected $slave = null;

	public function __construct(){
		$this->_CI = & get_instance();
	}

	public function __get($name){
		if($name == 'master'){
			if($this->master === null) $this->master = $this->_CI->load->database('master',true);
			return $this->master; 
		}elseif($name == 'slave'){
			if($this->slave === null) $this->slave = $this->_CI->load->database('slave',true);
			return $this->slave; 
		}else{
			return null;
		}
	}

	public function close(){
		if($this->master !== null) {
			$this->master->close();
			$this->master = null;
		}
		if($this->slave !== null){
			$this->slave->close();
			$this->slave = null;
		}
	}

	public function dumpSQL($name){
		$queries = $this->$name->queries;
		foreach($queries as $key => $query){
			echo $key.': '.str_replace("\n",' ',$query).'<br />';
		}
	}
}

/* End of file Database.php */
/* Location: ./application/libraries/Database.php */