<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('validate')){
	function validate($input,$min = 1,$max = -1){

		if(is_array($input)) return true;

		$len = strlen($input);
		$res = true;
		if($min != -1 && $len < $min){
			$res = false;
		}
		if($max != -1 && $len > $max){
			$res = false;
		}

		return $res;
	}
}