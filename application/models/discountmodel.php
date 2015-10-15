<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc discount促销详情model
 * @author Administrator
 *
 */
class discountmodel extends CI_Model {
	//private $memcache;
	
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}
	
	/**
	 * @desc 获取对应促销活动信息
	 * @param unknown $discount_id
	 * @return boolean|Ambigous <boolean, string>
	 */
	public function getDiscountWithId($discount_id){
		if(!is_numeric($discount_id) || !$discount_id) return false;
		global $mem_expired_time;
		$mem_key = md5("each_buyer_product_newdiscount_".$discount_id);
		
		//$this->memcache->delete($mem_key);
		if(!$list = $this->memcache->get($mem_key)){
			$this->database->slave->from('eb_promote_discount');
			$this->database->slave->where('promote_discount_status',1);
			$this->database->slave->where('promote_discount_id',$discount_id);
			$query = $this->database->slave->get();
			$list = $query->result_array();
		
			//echo $this->database->slave->last_query();die;
			$this->memcache->set($mem_key, $list,$mem_expired_time['product_discount']);
		}
		
		return $list;
	}
	
	/**
	 * @desc 对没有缓存的多个促销活动信息获取并缓存（对多个促销活动，单个做缓存）
	 * @param unknown $discount_id
	 * @return boolean|Ambigous <boolean, string>
	 */
	public function getDiscountWithIds($discount_ids){
		$result = array();
		if(empty($discount_ids) || !is_array($discount_ids)) return $result;

        global $mem_expired_time;
        $mem_key = md5("each_buyer_product_newdiscount_by_dids_".json_encode($discount_ids));

        //$this->memcache->delete($mem_key);
        if(!$list = $this->memcache->get($mem_key)){
            $this->database->slave->from('eb_promote_discount');
            $this->database->slave->where('promote_discount_status',1);
            $this->database->slave->where_in('promote_discount_id',$discount_ids);
            $query = $this->database->slave->get();
            //echo $this->database->slave->last_query();die;
            $list = $query->result_array();
            $this->memcache->set($mem_key, $list,$mem_expired_time['product_discount']);
        }
		
		global $mem_expired_time;
		//对每个促销缓存
		foreach ($list as $k=>$v){
			//echo "<pre>bbyyyy";print_r($v);die;
			$discount_id = $v['promote_discount_id'];
			$mem_key = md5("each_buyer_product_newdiscount_".$discount_id);
			$new_v = $v;
			$this->memcache->set($mem_key, $v,$mem_expired_time['product_discount']);
			$result[$discount_id] = $v;
		}
	
		return $result;
	}
	
	//多个discount_id查询(有缓存查询缓存，无缓存则通过where_in形式查询并单个缓存起来)
	public function getBatchDiscountWithIds($discount_ids = array()){
		$result = array();
		if(empty($discount_ids) || !is_array($discount_ids)) return $result;
		
		$no_in_cache_discount_ids = array();
		$in_cache_discount_infos = array();
		foreach ($discount_ids as $k=>$v){
			//查询是否有单个缓存
			$mem_key = md5("each_buyer_product_newdiscount_".$v);
			$data = $this->memcache->get($mem_key);
			if(!$data){//无缓存
				array_push($no_in_cache_discount_ids, $v);
			}else{
				$in_cache_discount_infos[$v] = $data;
			}
		}
		
		//获取没有缓存，多个discount_id数据
		$no_cache_discount_infos = $this->getDiscountWithIds($no_in_cache_discount_ids);
		$result = $in_cache_discount_infos+$no_cache_discount_infos;
		return $result;
	}
	
	
	/**
	 * @desc 获取促销活动后的价格，根据促销活动编号及活动前价格（单个）
	 * @param unknown $discount_id
	 * @param unknown $price
	 * @return Ambigous <unknown, unknown, number>
	 */
	public function getDiscountprice ($discount_id,$price){
		$return_price = $price;
		//促销活动详情
		$discount_info = $this->getDiscountWithId($discount_id);
		
		//是否有该促销活动
		if($discount_info && isset($discount_info[0])){
			$info = $discount_info[0];
			//判断状态及有效期时间
			$start_time = strtotime($info['promote_discount_time_start']);
			$end_time = strtotime($info['promote_discount_time_end']);

			if($start_time <=time() && $end_time <time()){
				$type = $info['promote_discount_type'];
				if($type==1){//折扣倒计时
					$return_price = $this->discountTimePrice($price,$info);
				}elseif($type==2){//满减
					$return_price = $this->discountSubtract($price,$info);
				}
			}
			
		}
		return $return_price;
	}
	
	/**
	 * @desc  获取商品折扣
	 * @param unknown $discount_id
	 * @return number
	 */
	public function getProductDiscount($discount_id){
		$discount = 0;
		
		if(!$discount_id || !is_numeric($discount_id)) return $discount;
		
		//促销活动详情
		$discount_info = $this->getDiscountWithId($discount_id);
		//是否有该促销活动
		if($discount_info && isset($discount_info[0])){
			$info = $discount_info[0];
			//判断状态及有效期时间
			$start_time = strtotime($info['promote_discount_time_start']);
			$end_time = strtotime($info['promote_discount_time_end']);
			if($start_time <=time() && $end_time >time()){
				$type = $info['promote_discount_type'];
				if($type==1){
					$discount = $info['promote_discount_effect_value'];	
				}
			}
		}
		return $discount;
		
	}
	
	/**
	 * @desc  折扣倒计时
	 * @param unknown $price
	 * @param unknown $info
	 */
	public function discountTimePrice($price,$info){
		$return_price = $price;
		if(is_array($info) && isset($info['promote_discount_effect_value'])){
			$real_discount = (100-$info['promote_discount_effect_value'])/100;
			$real_price = $price*$real_discount;
			$return_price = round($real_price,2);
		}
		
		return $return_price;
	}

    /**
     * @desc  获取有效的折扣详情
     *
     */
    public function getActiveDiscount($param = array()){
        global $mem_expired_time;
        $list = array();
        $mem_key = 'each_buyer_promote_active_discount';
        if(is_array($param) && !empty($param)){
            foreach($param as $key=>$value)
                $mem_key .= '_'.$key.'_'.$value;
        }
        $mem_key = md5($mem_key);
        if(!$list=$this->memcache->get($mem_key)){
            $this->database->slave->from('eb_promote_discount');
            if(is_array($param) && !empty($param)){
                foreach($param as $key=>$value){
                    $this->database->slave->where($key,$value);
                }
            }
            $endTime = date('Y-m-d H:i:s', requestTime()-60*60*24);
            $this->database->slave->where('promote_discount_time_end >',$endTime);
            $this->database->slave->where('promote_discount_status',1);
            $query = $this->database->slave->get();
            $list = $query->result_array();
            $this->memcache->set($mem_key,$list,$mem_expired_time['product_discount']);
        }

        return $list;
    }
    
    /**
     * @desc  获取新的有效的折扣详情
     *
     */
    public function getActiveDiscountNew($param = array()){
        global $mem_expired_time;
        $list = array();
        $mem_key = 'each_buyer_active_discount';
        if(is_array($param) && !empty($param)){
            foreach($param as $key=>$value)
                $mem_key .= '_'.$key.'_'.$value;
        }
        $mem_key = md5($mem_key);
        if(!$list=$this->memcache->get($mem_key)){
            $this->database->slave->from('eb_discount');
            if(is_array($param) && !empty($param)){
                foreach($param as $key=>$value){
                    $this->database->slave->where($key,$value);
                }
            }
            $endTime = date('Y-m-d H:i:s', requestTime()-60*60*24);
            $this->database->slave->where('discount_time_finish >',$endTime);
            $this->database->slave->where('discount_status',STATUS_ACTIVE);
            $query = $this->database->slave->get();
            $list = $query->result_array();
            $this->memcache->set($mem_key,$list,$mem_expired_time['product_discount']);
        }

        return $list;
    }

	//满减
	public function discountSubtract($price,$info){
		//暂时不做处理
		return $price;
	}
	
	public function getListDiscountPrice(){
			
	}
	
}
