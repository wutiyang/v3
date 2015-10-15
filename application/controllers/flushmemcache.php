<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Flushmemcache extends Dcontroller {
	public function index(){
		//获取需要清除缓存的key
		$cache_key = trim($this->input->get('tag_key'));
		
		if(stripos($cache_key, "_")){
			$param_array = explode("_", $cache_key);
			switch ($param_array[0]){
				case 'h'://首页
					if($param_array[1]==1){
						//清除首页缓存
						redirect("home/index?flush_cache=".time());
					}
					break;
				case 'c':
					if(is_numeric($param_array[1]) && is_numeric($param_array[2])){
						//清除某分类某page下缓存
						redirect("category/index/".$param_array[1]."/".$param_array[2]."?flush_cache=".time());
					}
					break;
				case 'p':
					
					break;
				default:
					false;
			}
		}else{
			echo "Please enter the correct parameters";die;
		}
	}
	
	//缓存时间段
	public function start(){
		set_cookie('flush_cache',SALT,1800);
		echo '无缓存模式开启';
	}
	
	//清除缓存时间段
	public function stop(){
		unset_cookie('flush_cache');
		echo '无缓存模式关闭';
	}
}