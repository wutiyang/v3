<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 问答 model
 * @author Administrator
 *
 */
class qnamodel extends CI_Model {
	//private $memcache;
	
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}

	//根据商品id返回该商品的qna
	public function qnaListWithPid($product_id,$page = 1,$pagesize = 8){
		$result = array();
		if(!$product_id || !is_numeric($product_id)) return $result;
		
		global $mem_expired_time;
		$mem_key = md5("each_buyer_qna_".$product_id);
		
		//$this->memcache->delete($mem_key);
        $list = $this->memcache->get($mem_key);
		if($list===false){
			$this->database->slave->from('eb_qna');
			$this->database->slave->where('product_id',$product_id);
			$this->database->slave->where('qna_status',1);
			$this->database->slave->order_by('qna_time_lastmodified','desc');
//			$this->database->slave->limit($pagesize,$pagesize*($page-1));
			$query = $this->database->slave->get();
            $list = $query->result_array();
			//echo $this->database->slave->last_query();die;
			$this->memcache->set($mem_key, $list,$mem_expired_time['product_qna']);
		}
        $result['data'] = $list;
        if($pagesize && !empty($list))
            $result['data'] = array_slice($list,$pagesize*($page-1),$pagesize);
        $result['nums'] = count($list);
		
		return $result;
	}

//	public function qnaTotalnumsWithPid($product_id){
//		$nums = 0;
//		if(!$product_id || !is_numeric($product_id)) return $nums;
//
//		$qna_list = $this->qnaListWithPid($product_id,0,0);
//        $nums = count($qna_list);
//
//		return $nums;
//	}
}
