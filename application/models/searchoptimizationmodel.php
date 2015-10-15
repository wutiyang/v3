<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 配置特定关键词跳转
 * @author Administrator
 *
 */
class searchoptimizationmodel extends CI_Model {
	
	public function __construct(){
		parent::__construct();
	}

	//获取当前语言下，所有关键词跳转信息并缓存
	public function optimizatonWithLanguageid($language_id = 1){
		if(!is_numeric($language_id) || $language_id <0) $language_id = 1;
		
		global $mem_expired_time;
		$mem_key = md5("each_buyer_search_optimization_language_id_".$language_id);
		
		$data = $this->memcache->get($mem_key);
		if($data === false){
			$this->database->slave->from('eb_search_optimization');
			$this->database->slave->where('search_optimization_status',STATUS_ACTIVE);
			$this->database->slave->where('language_id',$language_id);
			$query = $this->database->slave->get();
			$data = $query->result_array();
			
			$this->memcache->set($mem_key, $data,$mem_expired_time['search_optimization']);
		}
		
		return $data;
	}	
	
	//遍历，并返回记录
	public function getSearchOptimization($keywords = null, $language_id = 1){
		$result = array();
		//检测
		if(empty($keywords)) return $result; 

		$keywords = strtolower($keywords);
		//获取该语言下，all记录
		$all_language_data = $this->optimizatonWithLanguageid($language_id);
		//遍历，判断（时间，关键词）
		foreach ($all_language_data as $k=>$v){
			if(!isset($v['search_optimization_content'])) continue;
			if(strtotime($v['search_optimization_time_start']) > time() ||  strtotime($v['search_optimization_time_end']) < time()) continue;
			if(stripos($v['search_optimization_content'], ',')!==false){
				$content_array = explode(',', $v['search_optimization_content']);
				foreach ($content_array as $val){
					$key = trim($val);
					if(strtolower($val)==trim($keywords)){
						$result = $v;
					}
				}	
			}else{
				$content = trim($v['search_optimization_content']);
				if(strtolower($content) == trim($keywords)){
					$result = $v;
				}
			}
			 
		}
		
		//返回记录
		return $result;
	}
	
}