<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc bundle促销详情model
 * @author Administrator
 *
 */
class bundlemodel extends CI_Model {
	//private $memcache;
	
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}
	
	/**
	 * @desc 返回商品绑定销售商品
	 * @param unknown $product_id
	 * @param number $pagesize
	 */
	public function bundleWithProductId($product_id,$category_path,$language_code = 'us'){
		$result = $category_bundle = array();
		if(!$product_id || !is_numeric($product_id)) return $result;
		$category_id = explode('/', $category_path);

		$params = array('pid'=>$product_id,'category_ids'=>$category_id);
		//从recommendation_manual表中查询绑定商品
		$product_bundle_infos = $this->productWithNewRecommend($params);
		$product_bundle_info_by_pid = reindexArray($product_bundle_infos,'product_id');
		$product_bundle_info_by_cid = reindexArray($product_bundle_infos,'category_id');

		$pids = array();//所有推荐商品id编号
		if(isset($product_bundle_info_by_pid[$product_id])){
			$pids = array_merge($pids,explode(',', $product_bundle_info_by_pid[$product_id]['recommendation_manual_content']));
		}
		$category_id = array_reverse($category_id);
		foreach($category_id as $cid){
			if(isset($product_bundle_info_by_cid[$cid])){
				$pids = array_merge($pids,explode(',', $product_bundle_info_by_cid[$cid]['recommendation_manual_content']));
			}
		}
		$pids = array_unique($pids);
		
		return $pids;
	}
	
	public function bundleWithProductId_bak($product_id,$category_path, $pagesize=3,$language_code = 'us'){
		$result = $category_bundle = array();
		if(!$product_id || !is_numeric($product_id)) return $result;
		$category_id = explode('/', $category_path);
	
		//从recommendation_manual表中查询绑定商品
		$all_pids = $pids = $cate_pids = array();//所有推荐商品id编号
		$product_bundle_infos = $this->productWithRecommend($product_id);
		$size = 0;
		if(!empty($product_bundle_infos)){
			foreach($product_bundle_infos as $product_bundle_info){
				$pids = array_merge($pids,explode(',', $product_bundle_info['recommendation_manual_content']));
			}
			$size += count($pids);
		}
		$category_limit = $pagesize - $size;
		if($category_limit > 0){
			$category_bundle_infos = $this->productWithRecommend($category_id,$type=1);
			if(!empty($category_bundle_infos)){
				foreach($category_bundle_infos as $category_bundle_info){
					$cate_pids = array_merge($cate_pids,explode(',', $category_bundle_info['recommendation_manual_content']));
				}
			}
		}
		if($category_limit > 0){
			$all_pids = array_slice(array_merge($pids,$cate_pids), 0,$pagesize);
		} else if($size == 0) {
			$all_pids = array_slice($cate_pids, 0,$pagesize);
		} else {
			$all_pids = array_slice($pids, 0,$pagesize);
		}
		$result = $this->getProductInfos($all_pids,$language_code);
	
		return $result;
	}
	
	private function getProductInfos($pid_array,$language_code){
        if(!count($pid_array)) return array();
		$result = array();
		$this->load->model("goodsmodel","product");
        $product_info = $this->product->getProductList($pid_array,$status = 1,0,$language_code);
        foreach($pid_array as $pid){
            if(isset($product_info[$pid]))
                $product_list[] = $product_info[$pid];
        }
		foreach ($product_list as $k=>$product_info){
			//查询该商品是否上架
			$product_id = $product_info['product_id'];
			if($product_info ){
				//比较捆绑折扣与该商品本身折扣大小，去折扣最大的
				$last_discount_num = $this->productDiscount($product_id);
					
				//折扣后价格
                if($last_discount_num){
                    $real_discount = (100-$last_discount_num)/100;
                    $discount_price = $product_info['product_price_market']*$real_discount;
                    $discount_price = round($discount_price,2);
                    $product_info['product_discount_price'] = $discount_price;
                }else{
                    $product_info['product_discount_price'] = $product_info['product_price'];
                }

				//币种
				$product_info['product_currency'] = "$";
				//折扣
				$product_info['product_discount_number'] = $last_discount_num;

				$result[] = $product_info;
			}
		}
		return $result;
	}
	/**
	 * @desc 获取推荐商品
	 * @param unknown $product_id（当type=0时为商品id；当type=1时是分类id）
	 * @param number $type
	 * @return multitype:|unknown
	 */
	public function productWithRecommend($product_ids,$type = 0){
		$result = array();
		if(!is_array($product_ids)) $product_ids = explode(',',$product_ids);
	    if(!count($product_ids)) return false;
		global $mem_expired_time;
		$mem_key = md5("each_buyer_promote_recommend_product_".implode('_',$product_ids)."_".$type);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_recommendation_manual');
			$this->database->slave->where('recommendation_manual_status',STATUS_ACTIVE);
			if($type==0){//根据pid查询
				$this->database->slave->where_in('product_id',$product_ids);
			}else{
				$this->database->slave->where_in('category_id',$product_ids);
			}
			$query = $this->database->slave->get();
			$result = $query->result_array();
			//echo $this->database->slave->last_query();die;
			$this->memcache->set($mem_key, $result,$mem_expired_time['bundle_product']);
		}
		if(!empty($result)) return $result;
		return array();
	}
	
	public function productWithNewRecommend($params = array()){
		$result = array();
		if(empty($params)) return false;
		if(isset($params['pid']) && isset($params['category_ids'])){
			$product_id =  $params['pid'];
			$category_ids = $params['category_ids'];
		}else{
			return false;
		}
		global $mem_expired_time;
		$mem_key = md5("each_buyer_promote_recommend_product_".json_encode($params) );
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_recommendation_manual');
			$this->database->slave->where('recommendation_manual_status',STATUS_ACTIVE);
			$this->database->slave->where_in('product_id',$product_id);
			$this->database->slave->or_where_in('category_id',$category_ids);
            $this->database->slave->where('recommendation_manual_status',STATUS_ACTIVE);
			$query = $this->database->slave->get();
			$result = $query->result_array();
			//echo $this->database->slave->last_query();die;
			$this->memcache->set($mem_key, $result,$mem_expired_time['bundle_product']);
		}
		
		if(!empty($result)) return $result;
		return array();
	}
	
	//bought together 推荐商品
	public function recommendBatchProduct($product_ids,$type = 0){
		$result = array();
		if(!is_array($product_ids) || empty($product_ids)) return $result;
	
		global $mem_expired_time;
		$mem_key = md5("each_buyer_promote_recommend_product_".json_encode($product_ids)."_".$type);
		//$this->memcache->delete($mem_key);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_recommendation_manual');
			$this->database->slave->where('recommendation_manual_status',STATUS_ACTIVE);
			if($type==0){//根据pid查询
				$this->database->slave->where_in('product_id',$product_ids);
			}else{
				$this->database->slave->where_in('category_id',$product_ids);
			}
			$query = $this->database->slave->get();
			$result = $query->result_array();
			//echo $this->database->slave->last_query();die;
			$this->memcache->set($mem_key, $result,$mem_expired_time['bundle_product']);
		}
		//if(!empty($result)) return $result[0];
		if(!empty($result)) return $result;
		return array();
	}
	
	/**
	 * @desc 返回该商品本身最大的折扣（非捆绑促销）
	 * @param unknown $product_id
	 * @return number
	 */
	private function productDiscount($product_id){
		$max_discount_num = 0;
		if(!$product_id || !is_numeric($product_id)) return $max_discount_num;
		//Test***************
		//$product_id = 3159;
		
		//是否有折扣
		$this->load->model("discountrangemodel","range");
		$this->load->model("discountmodel","discount");
		$discunt_range_list = $this->range->getRangeExistsWithId($product_id);
		$discount_ids_array = array();
		$max_discount_id = 0;
		$discountinfos = "";
		
		if($discunt_range_list && !empty($discunt_range_list)){
			//找出最大折扣*********************************************
			foreach ($discunt_range_list as $k=>$info){
                if(!isset( $info['promote_discount_id']))continue;
				$discount_id = $info['promote_discount_id'];
				$discount_ids_array[$discount_id] =(int) $this->discount->getProductDiscount($discount_id);
				$discount_info = $this->discount->getDiscountWithId($discount_id);
				if(isset($discount_info[0]) && !empty($discount_info[0])){
					$start_time = strtotime($discount_info[0]['promote_discount_time_start']);
					$end_time = strtotime($discount_info[0]['promote_discount_time_end']);
					if($discount_ids_array[$discount_id] > $max_discount_num && $start_time<time() && $end_time > time()){
						$max_discount_num = $discount_ids_array[$discount_id];
						$max_discount_id = $discount_id;
						$discountinfos = $discount_info[0];
					}
				}
				
			}
			$result['discount_id'] = $max_discount_id;//最大折扣的discount_id
			$result['all_discount_ids'] = $discount_ids_array;//所有折扣的discount_id
			$result['discount_info'] =$discountinfos;//最大折扣的discount信息
			$discount_number = $max_discount_num;
		}
			
		return $max_discount_num;
		
	}
}
