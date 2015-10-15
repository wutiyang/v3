<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 商品Note标语 model
 * @author Administrator
 *
 */
class notemodel extends CI_Model {
	//private $memcache;
	
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}
	
	public function NoteWithProductAndCategory($product_id,$category_path,$language_id = 1){
		$result = array();
		if(!$product_id || !is_numeric($product_id)) return $result;
		
		$category_in_array = array();
		if(is_numeric($category_path)){
			$category_in_array[] = $category_path;
		}else{
			$category_in_array = explode("/", $category_path);
		}
		
		//根据商品id和分类id查询获取所有的note id
		$note_id_infos = $this->NoteIdsWithProductAndCategory($product_id,$category_in_array);
		
		//获取note_ids
		$note_ids = extractColumn($note_id_infos, 'note_id');
		$note_ids = array_unique($note_ids);
		
		//获取note信息
		$note_infos = $this->noteInfos($note_ids,$language_id);
		
		$note_list = array();
		foreach ($note_id_infos as $key=>$val){
			$note_info = isset($note_infos[$val['note_id']])?$note_infos[$val['note_id']]:array();
			if(!empty($note_info) && strtotime($note_info['note_time_start'])<time() && strtotime($note_info['note_time_end'])> time() && $note_info['note_status'] == 1){
				//如果商品有则直接返回，其他分类存入列表
				if($val['product_id'] == $product_id){
					return $note_info;
				}else {
					$note_list[] = $note_info;
				}
			}
		}
		
		//时间倒排
		if(!empty($note_list)){
			if(count($note_list)==1){
				$result = $note_list[0];
			}else{
				$data_list = array_sort($note_list,"note_time_start",$type='desc');
				$result = $data_list[0];
			}
		}
		
		return $result;
	}
	
	/**
	 * @desc 返回note具体信息
	 * @param unknown $note_id
	 * @param number $language_id
	 * @return multitype:|Ambigous <multitype:, mixed, boolean, string>
	 */
	public function noteInfo($note_id,$language_id = 1){
		$result = array();
		if(!$note_id || !is_numeric($note_id)) return $result;
		global $mem_expired_time;
		
		$mem_key = md5("each_buyer_note_info_".$language_id.$note_id);
		//$this->memcache->delete($mem_key);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_note');
			$this->database->slave->where('note_status',1);
			$this->database->slave->where('note_id',$note_id);
			$query = $this->database->slave->get();
			$data = $query->result_array();
			if($data){
				$language_content = json_decode($data[0]['note_content'],true);
				if(!is_numeric($language_id)) $language_id = 1;
				$data[0]['new_note_content'] = $language_content[$language_id];
				$result = $data[0];
			}else{
				$result = array();
			}
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_note']);
			
		}
		return $result;
	}
	
	/**
	 * @desc 返回该商品id的标语id信息
	 * @param unknown $product_id
	 * @return multitype:
	 */
	public function NoteidWithProduct($product_id){
		$result = array();
		if(!$product_id || !is_numeric($product_id)) return $result;
		global $mem_expired_time;
		
		$mem_key = md5("each_buyer_note_product_".$product_id);
		//$this->memcache->delete($mem_key);
		
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_note_range');
			$this->database->slave->where('note_range_status',1);
			$this->database->slave->where('product_id',$product_id);
			$query = $this->database->slave->get();
			$result = $query->result_array();
			
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_note']);
		}
		
		return $result;
	}
	
	/**
	 * @desc 返回在分类内的标语id信息
	 * @param unknown $category_array
	 * @return multitype:|Ambigous <multitype:, boolean, string>
	 */
	public function NoteidWithCategory($category_array){
		$result = array();
		if(!$category_array || empty($category_array)) return $result;
		global $mem_expired_time;
	
		$mem_key = md5("each_buyer_note_category_".implode(",", $category_array));
		//$this->memcache->delete($mem_key);
	
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_note_range');
			$this->database->slave->where('note_range_status',1);
			$this->database->slave->where_in('category_id',$category_array);
			$query = $this->database->slave->get();
			$result = $query->result_array();
				
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_note']);
		}
	
		return $result;
	}
	
	/**
	 * @desc 返回在分类内的标语id信息
	 * @param unknown $product_id
	 * @param unknown $category_array
	 * @return multitype:|Ambigous <multitype:, boolean, string>
	 */
	public function NoteIdsWithProductAndCategory($product_id,$category_array){
		$result = array();
		if(!$category_array || empty($category_array)) return $result;
		global $mem_expired_time;
	
		$mem_key = md5("each_buyer_note_category_".implode(",", $category_array));
		//$this->memcache->delete($mem_key);
	
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_note_range');
			$this->database->slave->where('note_range_status',STATUS_ACTIVE);
			$this->database->slave->where_in('category_id',$category_array);
			$this->database->slave->or_where('product_id = '.$product_id);
            $this->database->slave->where('note_range_status',STATUS_ACTIVE);
			$query = $this->database->slave->get();
			$result = $query->result_array();
	
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_note']);
		}
	
		return $result;
	}
	
	/**
	 * @desc 返回note具体信息
	 * @param unknown $note_id
	 * @param number $language_id
	 * @return multitype:|Ambigous <multitype:, mixed, boolean, string>
	 */
	public function noteInfos($note_ids,$language_id = 1){
		$result = array();
		if(!$note_ids || !is_array($note_ids)) return $result;
		global $mem_expired_time;
	
		$mem_key = md5("each_buyer_note_infos_".$language_id.implode('_', $note_ids));
		//$this->memcache->delete($mem_key);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_note');
			$this->database->slave->where('note_status',1);
			$this->database->slave->where_in('note_id',$note_ids);
			$query = $this->database->slave->get();
			$data = $query->result_array();
			
			if($data){
				foreach ($data as $note){
					$language_content = json_decode($note['note_content'],true);
					if(!is_numeric($language_id)) $language_id = 1;
					$note['new_note_content'] = $language_content[$language_id];
					$result[$note['note_id']] = $note;
				}
			}else{
				$result = array();
			}
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_note']);
		}
		return $result;
	}
	
}
