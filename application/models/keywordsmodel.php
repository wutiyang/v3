<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Keywordsmodel extends CI_Model {

	public function getKeywordsList($keywords_type,$language_id){
		$this->database->slave->select('keywords_title,keywords_url,keywords_highlight');
		$this->database->slave->from('eb_keywords');
		$this->database->slave->where('language_id',$language_id);
		$this->database->slave->where('keywords_type',$keywords_type);
		$this->database->slave->where('keywords_status',STATUS_ACTIVE);
		$this->database->slave->order_by('keywords_sort','desc');
		$this->database->slave->order_by('keywords_id','asc');
		$query = $this->database->slave->get();
		$list = $query->result_array();

		return $list;
	}
	
	public function getKeywordsListWithTypeArray($keywords_type_array,$language_id){
		$result = array();
		if(!is_array($keywords_type_array) || empty($keywords_type_array)) return $result;
		
		global $mem_expired_time;
		$mem_key = md5("each_buyer_index_keywords_".json_encode($keywords_type_array).$language_id);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->select('keywords_type,keywords_title,keywords_url,keywords_highlight');
			$this->database->slave->from('eb_keywords');
			$this->database->slave->where('language_id',$language_id);
			$this->database->slave->where_in('keywords_type',$keywords_type_array);
			$this->database->slave->where('keywords_status',STATUS_ACTIVE);
			$this->database->slave->order_by('keywords_sort','desc');
			$this->database->slave->order_by('keywords_id','asc');
			$query = $this->database->slave->get();
			$result = $query->result_array();
			
			$this->memcache->set($mem_key, $result,$mem_expired_time['index_keywords']);
		}
		
		return $result;
	}
}