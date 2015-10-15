<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc discount促销model
 * @author Administrator
 *
 */
class discountrangemodel extends CI_Model {
	//private $memcache;
	
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}
	
	/**
	 * @desc 获取该商品的所有促销活动
	 * @param unknown $product_id
	 * @return boolean|unknown
	 */
	public function getRangeExistsWithId($product_id){
		if(!is_numeric($product_id) || !$product_id) return false;
		global $mem_expired_time;
		$mem_key = md5("each_buyer_product_range_".$product_id);
		
		//$this->memcache->delete($mem_key);
		if(!$list = $this->memcache->get($mem_key)){
			$this->database->slave->from('eb_promote_range');
			$this->database->slave->where('promote_range_status',1);
			//$this->database->slave->where('promote_discount_id',1);
			$this->database->slave->where('promote_range_type',2);

			$this->database->slave->where('promote_range_content',$product_id);
			$this->database->slave->order_by('promote_range_id','desc');
			$query = $this->database->slave->get();
			$list = $query->result_array();
			
			$this->memcache->set($mem_key, $list,$mem_expired_time['product_discount_range']);
		}

		return $list;
	}

    /**
     * 折扣方案按照单品id分组
     * @param array $discount_info
     * @return array
     */
    public function getRangeByDiscountIds($discount_info=array()){
        $result = array();
        if(!is_array($discount_info))return false;
        $discount_ids = array();
        foreach($discount_info as $discount)
            $discounts[$discount['promote_discount_id']] = $discount;
        $discount_ids = array_keys($discounts);
        $this->database->slave->from('eb_promote_range');
        $this->database->slave->where('promote_range_status',1);
        $this->database->slave->where('promote_range_type',2);
        $this->database->slave->where_in('promote_discount_id',$discount_ids);
        $this->database->slave->order_by('promote_range_id','desc');
        $query = $this->database->slave->get();
        //echo $this->database->slave->last_query();die;
        $list = $query->result_array();
        foreach($list as $value){
            $product_ids = explode(',',$value['promote_range_content']);
            foreach($product_ids as $product_id){
                $result[$product_id][] = array_merge($value,$discounts[$value['promote_discount_id']]);
            }
        }
        return $result;
    }

    public function getRangeByDiscountInfo($discount_info = array(),$param = array()){
        $result = array();
        $discounts = array();
        if(!is_array($discount_info))return false;
        $discount_ids = array();
        foreach($discount_info as $discount)
            $discounts[$discount['discount_id']] = $discount;
        if(empty($discounts)) return array();
        $discount_ids = array_keys($discounts);
        $this->database->slave->from('eb_discount_range');
        $this->database->slave->where('discount_range_status',1);
        if(is_array($param) && !empty($param)){
            foreach($param as $key=>$value){
                $this->database->slave->where($key,$value);
            }
        }
        $this->database->slave->where_in('discount_id',$discount_ids);
        $this->database->slave->order_by('discount_range_id','desc');
        $query = $this->database->slave->get();
        //echo $this->database->slave->last_query();die;
        $list = $query->result_array();
        if(!empty($list)){
            //绑定全站未处理
            foreach($list as $row){
                $discounts[$row['discount_id']]['category_ids'][$row['category_id']] = $row['category_id'];
                $discounts[$row['discount_id']]['product_ids'][$row['product_id']] = $row['product_id'];
                $discounts[$row['discount_id']]['exclude_product_ids'][$row['exclude_product_id']] = $row['exclude_product_id'];
            }
            //去掉多余的数据，以便日后使用
            foreach($discounts as $key=>$row){
                if(isset($discounts[$key]['category_ids'][0]))
                    unset($discounts[$key]['category_ids'][0]);
                if(isset($discounts[$key]['product_ids'][0]))
                    unset($discounts[$key]['product_ids'][0]);
                if(isset($discounts[$key]['exclude_product_ids'][0]))
                    unset($discounts[$key]['exclude_product_ids'][0]);
            }
            $result = $discounts;
        }
        return $result;
    }

	/**
	 * @desc 获取多个商品，无缓存的折扣范围信息，并对多个商品的折扣范围缓存
	 * @param unknown $product_ids
	 * @return multitype:|multitype:unknown
	 */
	public function getRangeExistsWithArray($product_ids){
		$result = array();
		if(empty($product_ids) || !is_array($product_ids)) return $result;
		
		$this->database->slave->from('eb_promote_range');
		$this->database->slave->where('promote_range_status',1);
		$this->database->slave->where('promote_range_type',2);
		$this->database->slave->where_in('promote_range_content',$product_ids);
		$this->database->slave->order_by('promote_range_id','desc');
		$query = $this->database->slave->get();
		//echo $this->database->slave->last_query();die;
		$list = $query->result_array();
		
		global $mem_expired_time;
		//对每个促销缓存
		foreach ($list as $k=>$v){
			$promote_range_id = $v['promote_range_content'];
			$mem_key = md5("each_buyer_product_range_".$promote_range_id);
			$this->memcache->set($mem_key, $v,$mem_expired_time['product_discount']);
			$result[] = $v;
		}
		
		return $result;
	}
	
	
	
	/**
	 * @desc 批量商品是否存在促销活动
	 * @param unknown $product_array
	 * @return multitype:NULL Ambigous <boolean, unknown, string>
	 */
	public function getRangeExistsWithIds($product_array){
		if(is_array($product_array) && count($product_array)){
			$list = array();
			foreach ($product_array as $val){
				$data = $this->getRangeExistsWithId($val);
				if($data){
					$list[$val] = $data;
				}else{
					$list[$val] = null;
				}
			}
			return $list;
		}else{
			$this->getRangeExistsWithId($product_array);
		}
			
	}
//******************捆绑促销 *****************************
	
	//该商品所在分类，绑定促销的商品-绑定单号(最终的结果是绑定单号)
	public function categoryBundle($category_path){
		$result = array();
		if(!$category_path) return $result;
		//对分类path分析
		if(stripos($category_path, "/")){//非顶级分类
			$category_array = explode("/", $category_path);
		}elseif(is_numeric($category_path)){//顶级分类
			$category_array = $category_path;			
		}else{
			return $result;
		}
		
		global $mem_expired_time;
		$mem_key = md5("each_buyer_bundlecategory_bundle_".$category_path);
		//$this->memcache->delete($mem_key);
		if(!$result = $this->memcache->get($mem_key)){
			$this->database->slave->from('eb_promote_range');
			$this->database->slave->where('promote_range_status',1);
			//$this->database->slave->where('promote_discount_id',0);
			$this->database->slave->where('promote_range_type',1);//分类
			$this->database->slave->where_in('promote_range_content',$category_array);//分类
			$query = $this->database->slave->get();
			
			$data = $query->result_array();
			if(!empty($data)){
				foreach ($data as $k=>$v){
					$bundle_id = $v['promote_bundle_id'];
					$result[$bundle_id] = $bundle_id;
				}
			}else{
				$result = array();
			}
			//echo $this->database->slave->last_query();die;
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_category_bundle']);
		}
		
		return $result;
	}
	
	//该商品直接绑定的商品-绑定单号(最终的结果是绑定单号)
	public function productBundle($product_id){
		$result = array();
		if(!is_numeric($product_id) || !$product_id) return $result;
		global $mem_expired_time;
		$mem_key = md5("each_buyer_bundleproduct_bundle_".$product_id);
		//$this->memcache->delete($mem_key);
		if(!$result = $this->memcache->get($mem_key)){
			$this->database->slave->from('eb_promote_range');
			$this->database->slave->where('promote_range_status',1);
			//$this->database->slave->where('promote_discount_id',0);
			$this->database->slave->where('promote_range_type',2);//分类
			$this->database->slave->where('promote_range_content',$product_id);//分类
			$query = $this->database->slave->get();
			
			$data = $query->result_array();
			if(!empty($data)){
				foreach ($data as $k=>$v){
					$bundle_id = $v['promote_bundle_id'];
					$result[$bundle_id] = $bundle_id;
				}
			}else{
				$result = array();
			}
			
			//echo $this->database->slave->last_query();die;
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_p_bundle']);
		}
		
		return $result;
	}
	
	//该商品不能绑定的商品-绑定单号(最终的结果是绑定单号)
	public function noproductBundle($product_id){
		$result = array();
		if(!is_numeric($product_id) || !$product_id) return $result;
		global $mem_expired_time;
		$mem_key = md5("each_buyer_nobundleproduct_bundle_".$product_id);
		//$this->memcache->delete($mem_key);
		if(!$result = $this->memcache->get($mem_key)){
			$this->database->slave->from('eb_promote_range');
			$this->database->slave->where('promote_range_status',1);
			//$this->database->slave->where('promote_discount_id',0);
			$this->database->slave->where('promote_range_type',3);//分类
			$this->database->slave->where('promote_range_content',$product_id);//分类
			$query = $this->database->slave->get();
				
			$data = $query->result_array();
			if(!empty($data)){
				foreach ($data as $k=>$v){
					$bundle_id = $v['promote_bundle_id'];
					$result[$bundle_id] = $bundle_id;
				}
			}else{
				$result = array();
			}
			//echo $this->database->slave->last_query();die;
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_p_bundle']);
		}
		
		return $result;
	}
	
	/**
	 * 获取商品对应的满减信息
	 * @param unknown $productId
	 * @param unknown $discountIds
	 * @param unknown $categoryIds
	 * @return NULL|multitype:
	 */
	public function getPidFullcutDiscounts($productId,$discountIds,$categoryIds){
		if(empty($discountIds)){
			return null;
		}
		
		//获取所有符合条件的discount_ids
		$this->database->slave->from('eb_discount_range');
		$this->database->slave->where('discount_range_status',1);
		$this->database->slave->where_in('discount_id',$discountIds);
		
		$this->database->slave->where('(product_id='.$productId.' or category_id in ('.implode(',', $categoryIds).'))');
// 		$this->database->slave->or_where_in('category_id',$categoryIds);//分类
		
		$this->database->slave->group_by('discount_id');
		$query = $this->database->slave->get();
		
		$discounts = $query->result_array();
		$discounts = reindexArray($discounts, 'discount_id');
		
		//获取product在exclude_product_id上的discount_ids
		$this->database->slave->from('eb_discount_range');
		$this->database->slave->where('discount_range_status',1);
		$this->database->slave->where_in('discount_id',$discountIds);
		$this->database->slave->where('exclude_product_id',$productId);
		$this->database->slave->group_by('discount_id');
		$query = $this->database->slave->get();
		
		$exDiscounts = $query->result_array();
		$exDiscounts = reindexArray($exDiscounts, 'discount_id');
		
		foreach ($exDiscounts as $key=>$val){
			if(isset($discounts[$key])){
				unset($discounts[$key]);
			}
		}
		
		return array_keys($discounts);
	}
	
}
