<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('get_cookie')){
	function get_cookie($key){
		$ci = & get_instance();

		return $ci->input->cookie($key);
	}
}

if ( ! function_exists('set_cookie')){
	function set_cookie($key,$value,$expire = 0){
		$ci = & get_instance();
		$ci->input->set_cookie(array(
			'name' => $key,
			'value' => $value,
			'expire' => $expire,
			'domain' => COMMON_DOMAIN,
			'path' => id2name('cookie_path',$GLOBALS,'/'),
			'secure' => id2name('cookie_secure',$GLOBALS,false),
		));
	}
}

if ( ! function_exists('unset_cookie')){
	function unset_cookie($key){
		$CI = & get_instance();
		$CI->input->set_cookie(array(
				'name' => $key,
				'value' => '',
				'expire' => $_SERVER['REQUEST_TIME'] - 3600,
				'domain' => COMMON_DOMAIN,
				'path' => id2name('cookie_path',$GLOBALS,'/'),
				'secure' => id2name('cookie_secure',$GLOBALS,false),
		));
		return true;
	}
}