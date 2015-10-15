<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Script extends CI_controller {

	public function __construct(){
		parent::__construct();
		@set_time_limit(0);
		@ini_set('memory_limit', '-1');
		$this->load->helper(array('array','app','other'));
		$this->load->library('database');
	}

	public function index() {}
}
