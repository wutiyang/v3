<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 焦点图片
 * @author Administrator
 *
 */
class imageadmodel extends CI_Model {
	//private $memcache;
	
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}

	public function getlocation($postion=array(1,2,3)){
		global $mem_expired_time;
		$mem_key = md5("each_buyer_index_image_postion");
		
		//$this->memcache->delete($mem_key);
		$list = $this->memcache->get($mem_key);
		if($list === false){
			$this->database->slave->from('eb_ad');
			$this->database->slave->where('ad_status',1);
			$this->database->slave->where_in('ad_location',$postion);
			//$this->database->slave->order_by('ad_position','asc');
			$query = $this->database->slave->get();
			$data = $query->result_array();
			
			$list = array();
			foreach ($data as $val){
				$location = $val['ad_location'];
				$list[$location][] = $val;
			}
			//echo "<pre>kkkkk";print_r($list);die;
			$this->memcache->set($mem_key, $list,$mem_expired_time['index_image']);
		}
		
		return $list;
	}

	/**
	 * @desc 单独获取对应location焦点图
	 * @param unknown $location_id
	 * @return boolean|Ambigous <boolean, string>
	 */
	public function getLocationWithId($location_id){
		if(!$location_id || !is_numeric($location_id)) return false;
		global $mem_expired_time;
		$mem_key = md5("each_buyer_index_images_location_".$location_id);
		
		//$this->memcache->delete($mem_key);
		$list = $this->memcache->get($mem_key);
		if($list === false){
			$this->database->slave->from('eb_ad');
			$this->database->slave->where('ad_status',1);
			$this->database->slave->where_in('ad_location',$location_id);
			$this->database->slave->order_by('ad_position','asc');
			$query = $this->database->slave->get();
			$list = $query->result_array();
			
			$this->memcache->set($mem_key, $list,$mem_expired_time['index_image']);
		}

		return $list;
	}
	
	public function getLocationWithIdArray($location_ids){
		$result = array();
		if(!is_array($location_ids) || empty($location_ids)) return $result;
		
		$this->database->slave->from('eb_ad');
		$this->database->slave->where('ad_status',1);
		$this->database->slave->where_in('ad_location',$location_ids);
		$this->database->slave->order_by('ad_position','asc');
		$query = $this->database->slave->get();
		$list = $query->result_array();
		
		global $mem_expired_time;
		if(!empty($list)){
			foreach ($list as $key=>$val){
				$id = $val['ad_location'];
				$result[$id][] = $val;
			}

			//写入缓存location_id为key
			foreach($result as $rKey=>$rVal){
				$mem_key = md5("each_buyer_index_images_location_".$rKey);
				$this->memcache->set($mem_key, $rVal,$mem_expired_time['index_image']);
			}
		}
		
		foreach ($location_ids as $k=>$v){
			$mem_key = md5("each_buyer_indexwithimage_location_".$v);
			$data = isset($result[$v])?$result[$v]:null;
			$this->memcache->set($mem_key, $data,$mem_expired_time['index_image']);
		}
		return $result;
	}
	
	/**
	 * @desc 批量获取location数据
	 * @param unknown $location_array
	 * @return multitype:NULL
	 */
	public function getLocationWithIds($location_array){
		$result = array();
		if(is_array($location_array) && count($location_array)){
			global $mem_expired_time;
			$mem_key = md5('each_buyer_indexwithimage_location_'.json_encode($location_array));
			$result = $this->memcache->get($mem_key);
			if($result === false){
				$this->database->slave->from('eb_ad');
				$this->database->slave->where('ad_status',1);
				$this->database->slave->where_in('ad_location',$location_array);
				$this->database->slave->order_by('ad_position','asc');
				$query = $this->database->slave->get();
				$list = $query->result_array();
				if(!empty($list)){
					foreach ($list as $k=>$v){
						$ad_id = $v['ad_location'];
						$result[$ad_id][] = $v;
					}
				}
				$this->memcache->set($mem_key, $result,$mem_expired_time['index_image']);
			}
			
			
		}else{
			$result[$location_array] = $this->getLocationWithId($location_array);
		}

		return $result;
	}
	public function getLocationWithIds_bak($location_array){
		$result = array();
		if(is_array($location_array) && count($location_array)){
			$no_cache_id = array();
			$no_cache_infos = $cache_infos = array();
				
			foreach ($location_array as $val){
				$mem_key = md5("each_buyer_indexwithimage_location_".$val);//广告位id，一个广告位有多个广告
				$data = $this->memcache->get($mem_key);
				if(!$data){
					array_push($no_cache_id, $val);
				}else{
					$cache_infos[$val] = $data;
				}
			}
			$no_cache_infos = $this->getLocationWithIdArray($no_cache_id);
			$result = $no_cache_infos + $cache_infos;
				
		}else{
			$result[$location_array] = $this->getLocationWithId($location_array);
		}
	
		return $result;
	}
}