<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc Deals model
 * @author Administrator
 *
 */
class dealmodel extends CI_Model {
	private $all_deals = null;
	
	public function __construct(){
		parent::__construct();
	}

    /**
     * 根据type获取Deal列表
     * @param $type deal的类型
     * @return array
     */
    public function getDealList($type){
        $result = array();
        if(!is_numeric($type)) return $result;
        
        //判断如无数据进行获取
        if($this->all_deals === null) $this->allDealsList();
        
        //获取相应type的deals集合
        foreach ($this->all_deals as $val){
        	if($val['deal_type'] == $type){
        		array_push($result, $val);
        	}
        }
        
        return $result;
    }
    
    /**
     * 获取deals全部列表
     */
    public function allDealsList(){
    	$mem_key = md5("each_buyer_deal_all_list");
    	global $mem_expired_time;
    	$result = $this->memcache->get($mem_key);
    	
    	if($result === false){
    		$slave = $this->database->slave;
    		$slave->from('eb_deal');
    		 
    		$slave->where('deal_status',STATUS_ACTIVE);
    		 
    		$slave->order_by('deal_sort','desc');
    		$slave->order_by('deal_id','asc');
    		 
    		$result = $slave->get()->result_array();
    		
    		$this->memcache->set($mem_key, $result,$mem_expired_time['deal_list']);
    	}
    	
    	$this->all_deals = $result;
    }
    
    /**
     * 根据type获取Deal列表
     * @param $id deal的ID
     * @return array
     */
    public function getDealById($id){
    	$result = array();
    	if(!is_numeric($id)) return $result;
    	$mem_key = md5("each_buyer_deal_".$id);
    	global $mem_expired_time;
    
    	$result = $this->memcache->get($mem_key);
    	if($result === false){
    		$slave = $this->database->slave;
    		$slave->from('eb_deal');
    		
    		$slave->where('deal_status',STATUS_ACTIVE);
    		$slave->where('deal_id',$id);
    
    		$result = $slave->get()->row_array();
    
    		$this->memcache->set($mem_key, $result,$mem_expired_time['deal']);
    	}
    	return $result;
    }

}
