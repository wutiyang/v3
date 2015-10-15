<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 商品详情model
 * @author Administrator
 *
 */
class goodsmodel extends CI_Model {
	//private $memcache;
	private $between_days = 14;//new商品间隔天数
	
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}

    public function getProductList($product_ids = array(),$status = 0,$page_size = 0,$language_code = 'us'){
        if(empty($product_ids) || !is_array($product_ids)) return array();
        
        $mem_key = md5("each_buyer_category_products_".json_encode($product_ids).'_status_'.$status."_pagesize_".$page_size."_language_code_".$language_code);
        global $mem_expired_time;
        
        if(!$data = $this->memcache->get($mem_key)){
        	$this->database->slave->from('eb_product');
        	if(is_numeric($status) && $status){
        		$this->database->slave->where('product_status',$status);
        	}
        	$this->database->slave->where_in('product_id',$product_ids);
        	if(is_numeric($page_size) && $page_size){
        		$this->database->slave->limit($page_size);
        	}
        	$query = $this->database->slave->get();
        	$data = $query->result_array();
        	$data = $this->showProductList($data,$language_code);
        	
        	$this->memcache->set($mem_key, $data,$mem_expired_time['each_buyer_category_products']);
        }
        
        return $data;
    }

    public function showProductList($data,$language_code='us'){
        if(empty($language_code))$language_code = currentLanguageCode();
        if(!is_array($data) || empty($data)) return array();
        $result = array();
        $product_ids = array();
        foreach($data as $k=>$v){
            $product_ids[] = $v['product_id'];
            $product_id = $v['product_id'];
            $result[$product_id] = $v;
            $between_days = howbetweendates($v['product_time_initial_active']);
            if($between_days<=PRODUCT_NEW_DAYS){
                $result[$product_id]['new'] = true;
            }else{
                $result[$product_id]['new'] = false;
            }
            $result[$product_id]['icon'] = false;
        }
        //icon标志
        $is_icons = $this->productWithIconNocache($product_ids);
        if(!empty($is_icons)){
            foreach($is_icons as $icon){
                $result[$icon['product_id']]['icon'] = true;
            }
        }

        //slogan
        $slogans = $this->productWithSloganNocache($product_ids);
        if(!empty($slogans)){
            foreach($slogans as $slogan){
                $result[$slogan['product_id']]['slogan'] = $slogan;
            }
        }
        //多语言
        $descriptions = $this->batchProductDescriptionInfo($product_ids,$language_code);
        if(!empty($descriptions)){
            foreach($descriptions as $description){
                if(isset($result[$description['product_id']]))
                    $result[$description['product_id']]['product_description_name'] = $description['product_description_name'];
                    $result[$description['product_id']]['product_description_content'] = $description['product_description_content'];
            }
        }
        return $result;
    }

    //获取该商品是否存在icon属性标志
    private function productWithIconNocache($product_ids = array()){
        if(!is_array($product_ids))return false;

        $this->database->slave->from('eb_product_icon');
        $this->database->slave->where("product_icon_status",1);
        $this->database->slave->where_in("product_id",$product_ids);
        $query = $this->database->slave->get();
        $data = $query->result_array();

        return $data;
    }

    //获取该商品的slogan标志
    private function productWithSloganNocache($product_ids){
        //$product_id = 370290;
        $result = array();
        if(!is_array($product_ids))return $result;
        $product_ids = implode(',',$product_ids);
        $sql = "select * from eb_product_slogan pslogan left join eb_slogan slogan on slogan.slogan_id=pslogan.slogan_id ";
        $sql .= "where pslogan.product_slogan_status=1 and pslogan.product_id in (".$product_ids.") and slogan.slogan_status=1";

        $query = $this->database->slave->query($sql);
        //echo $this->database->slave->last_query();die;
        $result = $query->result_array();
        return $result;
    }
	/**
	 * @desc 获取货物商品详情
	 * @param unknown $product_id
	 * @param int $status 商品状态值
	 * @return boolean|Ambigous <boolean, string>
	 */
	public function getinfo($product_id,$status = 0,$language_code = "us"){
		if(is_array($product_id) && !empty($product_id)){
			$this->getinfoWithArray($product_id,$status,$language_code);
		}else{
			if(!$product_id || !is_numeric($product_id))return false;
			global $language_range_array;
			if(!trim($language_code)  || !in_array(trim($language_code), $language_range_array)) $language_code = 'us';
			
			global $mem_expired_time;
			$mem_key = md5("each_buyer_product_".$product_id."_status_".$status."_languagecode_".$language_code);
			
			//$this->memcache->delete($mem_key);
			if(!$data = $this->memcache->get($mem_key)){
				$this->database->slave->from('eb_product');
				if(is_numeric($status) && $status){
					$this->database->slave->where('product_status',$status);
				}
				$this->database->slave->where('product_id',$product_id);
				$query = $this->database->slave->get();
				$data = $query->result_array();
				$data = !empty($data)?$data[0]:array();
				
				//商品是否是new新品,两周内
				if(!empty($data)){
					$between_days = howbetweendates($data['product_time_initial_active']);
					if($between_days<=PRODUCT_NEW_DAYS){
						$data['new'] = true;
					}else{
						$data['new'] = false;
					}
					
					//icon
					$product_icon = $this->productWithIconHascache($product_id);
					if(!empty($product_icon)){
						$data['icon'] = true;
					}else{
						$data['icon'] = false;
					}
					//slogan
					$product_slogan = $this->productWithSloganHascache($product_id);
					if(!empty($product_slogan)){
						$data['slogan'] = $product_slogan;
					}else{
						$data['slogan'] = array();
					}
					
					//多语言
					$description_info = $this->productDescriptionInfo($product_id,$language_code);
					if(!empty($description_info)){
						$data['product_description_name'] = isset($description_info['product_description_name'])?$description_info['product_description_name']:$data['product_name'];
						$data['product_description_content'] = isset($description_info['product_description_content'])?$description_info['product_description_content']:'';
					}
					
				}	

				$this->memcache->set($mem_key, $data,$mem_expired_time['product_info']);
			}
		}
			return $data;
	}
	
	/**
	 * @desc 获取sku的信息
	 * @param unknown $sku
	 * @param unknown $product_id
	 * @return boolean|Ambigous <multitype:, unknown>
	 */
	public function getSkuInfo($sku,$is_cache = true){
		if(!$sku || empty($sku))return false;
		global $mem_expired_time;
		$mem_key = md5("each_buyer_goods_sku_".$sku);
			
		//$this->memcache->delete($mem_key);
		if(!$data = $this->memcache->get($mem_key) && !$is_cache){
			$this->database->slave->from('eb_product_sku');
			$this->database->slave->where('product_sku_code',$sku);
			$this->database->slave->where('product_sku_status',STATUS_ACTIVE);
			//$this->database->slave->where('product_id',$product_id);
			$query = $this->database->slave->get();
			$data = $query->result_array();
			//echo $this->database->slave->last_query();die;
			$data = !empty($data)?$data[0]:array();

			$this->memcache->set($mem_key, $data,$mem_expired_time['product_info']);
		}
		return $data;
	}
	
	/**
	 * @desc 获取批量sku信息
	 * @param unknown $sku_array
	 * @return multitype:|Ambigous <multitype:, unknown>
	 */
	public function getBatchSkuInfo($sku_array){
		$result = array();
		if(empty($sku_array))return $result;
		
		$this->database->slave->from('eb_product_sku');
		$this->database->slave->where_in('product_sku_code',$sku_array);
		$this->database->slave->where('product_sku_status',STATUS_ACTIVE);
		//$this->database->slave->where('product_id',$product_id);
		$query = $this->database->slave->get();
		$data = $query->result_array();
		return $data;
	}
	
	/**
	 * @desc 获取货物商品详情
	 * @param unknown $product_id
	 * @param int $status 商品状态值
	 * @return boolean|Ambigous <boolean, string>
	 */
	public function getinfoNostatus($product_id,$language_code = "us"){
		if(is_array($product_id) && !empty($product_id)){
			$this->getinfoWithArray($product_id,0,$language_code);
		}else{
			if(!$product_id || !is_numeric($product_id))return false;
			global $language_range_array;
			if(!trim($language_code)  || !in_array(trim($language_code), $language_range_array)) $language_code = 'us';
				
			global $mem_expired_time;
			$mem_key = md5("each_buyer_goods_".$product_id."_languagecode_".$language_code);
				
			//$this->memcache->delete($mem_key);
			if(!$data = $this->memcache->get($mem_key)){
				$this->database->slave->from('eb_product');
				$this->database->slave->where('product_id',$product_id);
				$query = $this->database->slave->get();
				$data = $query->result_array();
				$data = !empty($data)?$data[0]:array();
	
				//商品是否是new新品,两周内
				if(!empty($data)){
					$between_days = howbetweendates($data['product_time_initial_active']);
					if($between_days<=PRODUCT_NEW_DAYS){
						$data['new'] = true;
					}else{
						$data['new'] = false;
					}
						
					//icon
					$product_icon = $this->productWithIconHascache($product_id);
					if(!empty($product_icon)){
						$data['icon'] = true;
					}else{
						$data['icon'] = false;
					}
					//slogan
					$product_slogan = $this->productWithSloganHascache($product_id);
					if(!empty($product_slogan)){
						$data['slogan'] = $product_slogan;
					}else{
						$data['slogan'] = array();
					}
						
					//多语言
					$description_info = $this->productDescriptionInfo($product_id,$language_code);
					if(!empty($description_info)){
						$data['product_description_name'] = isset($description_info['product_description_name'])?$description_info['product_description_name']:$data['product_name'];
						$data['product_description_content'] = isset($description_info['product_description_content'])?$description_info['product_description_content']:'';
					}
						
				}
	
				$this->memcache->set($mem_key, $data,$mem_expired_time['product_info']);
			}
		}
		return $data;
	}
	
	public function productDescriptionInfo($product_id,$language_code = "us"){
		$result = array();
		
		if(!$product_id || !is_numeric($product_id))return $result;
		global $language_range_array;
		if(!trim($language_code)  || !in_array(trim($language_code), $language_range_array)) $language_code = 'us';
		
		global $mem_expired_time;
		$mem_key = md5("each_buyer_product_desc_".$product_id."_languagecode_".$language_code);
			
		//$this->memcache->delete($mem_key);
		if(!$result = $this->memcache->get($mem_key)){
			$this->database->slave->from('eb_product_description_'.$language_code);
			$this->database->slave->where('product_id',$product_id);
			$query = $this->database->slave->get();
			$data = $query->result_array();
			
			if(!empty($data)) $result = $data[0];
            else {
                $this->database->slave->from('eb_product_description_us');
                $this->database->slave->where('product_id',$product_id);
                $query = $this->database->slave->get();
                $data = $query->result_array();
                if(!empty($data)) $result = $data[0];
            }
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_info']);
		}
		
		return $result;
	}
	//多个商品多语言查询，并逐个对商品多语言缓存
	public function batchProductDescriptionInfo($product_ids,$language_code = "us"){
		$result = array();
		if(empty($product_ids) || !is_array($product_ids))return $result;
		global $language_range_array,$mem_expired_time;
		if(!trim($language_code)  || !in_array(trim($language_code), $language_range_array)) $language_code = 'us';
	
		$this->database->slave->from('eb_product_description_'.$language_code);
		$this->database->slave->where_in('product_id',$product_ids);
		$query = $this->database->slave->get();
		$data = $query->result_array();

		if(!empty($data)){
			foreach ($data as $language_key=>$language_val){
				$pid = $language_val['product_id'];
				$mem_key = md5("each_buyer_product_desc_".$pid."_languagecode_".$language_code);
				$this->memcache->set($mem_key, $language_val,$mem_expired_time['product_info']);
				$result[$pid] = $language_val;
                $key = array_search($pid,$product_ids);
                unset($product_ids[$key]);
			}
		}
        if(!empty($product_ids)){
            $this->database->slave->from('eb_product_description_us');
            $this->database->slave->where_in('product_id',$product_ids);
            $query = $this->database->slave->get();
            $data = $query->result_array();
            if(!empty($data)){
                foreach ($data as $language_key=>$language_val){
                    $pid = $language_val['product_id'];
                    $mem_key = md5("each_buyer_product_desc_".$pid."_languagecode_".$language_code);
                    $this->memcache->set($mem_key, $language_val,$mem_expired_time['product_info']);
                    $result[$pid] = $language_val;
                    unset($product_ids[$pid]);
                }
            }
        }

		return $result;
	}
	
	//获取该商品是否存在icon属性标志
	private function productWithIconHascache($product_id){
		$result = array();
		if(!$product_id || !is_numeric($product_id))return $result;
	
		global $mem_expired_time;
		$mem_key = md5("each_buyer_goods_icon_".$product_id);
		//$this->memcache->delete($mem_key);
		if(!$result = $this->memcache->get($mem_key)){
			$this->database->slave->from('eb_product_icon');
			$this->database->slave->where("product_icon_status",1);
			$this->database->slave->where("product_id",$product_id);
			$query = $this->database->slave->get();
			$result = $query->result_array();
			
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_icon']);
		}
	
		return $result;
	}
	
	//获取该商品是否存在icon属性标志
	private function batchProductWithIconHascache($product_ids){
		$result = array();
		if(empty($product_ids) || !is_array($product_ids))return $result;
	
		$this->database->slave->from('eb_product_icon');
		$this->database->slave->where("product_icon_status",1);
		$this->database->slave->where_in("product_id",$product_ids);
		$query = $this->database->slave->get();
		$data = $query->result_array();
		
		global $mem_expired_time;
		if(!empty($data)){
			foreach ($data as $icon_key=>$icon_val){
				$mem_key = md5("each_buyer_goods_icon_".$icon_val['product_id']);
				$new_icon_val[0] = $icon_val;
				$this->memcache->set($mem_key, $new_icon_val,$mem_expired_time['product_icon']);
				$pid = $icon_val['product_id'];
				$result[$pid] = $icon_val;
			}	
		}

		return empty($result)?array():$result;
	}
	
	//获取该商品的slogan标志
	private function productWithSloganHascache($product_id){
		$result = array();
		if(!$product_id || !is_numeric($product_id))return $result;
		
		global $mem_expired_time;
		$mem_key = md5("each_buyer_product_slogan_".$product_id);
		//$this->memcache->delete($mem_key);
		if(!$result = $this->memcache->get($mem_key)){
			$sql = "select * from eb_product_slogan pslogan left join eb_slogan slogan on slogan.slogan_id=pslogan.slogan_id ";
			$sql .= "where pslogan.product_slogan_status=1 and pslogan.product_id=".intval($product_id)." and slogan.slogan_status=1";
			
			$query = $this->database->slave->query($sql);
			//echo $this->database->slave->last_query();die;
			$result = $query->result_array();
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_slogan']);
		}
		
		return $result;
	}
	
	private function batchPproductWithSloganHascache($product_ids){
		$result = array();
		if(empty($product_ids) || !is_array($product_ids))return $result;
	
		$product_ids_string = implode(',', $product_ids);
		$sql = "select * from eb_product_slogan pslogan left join eb_slogan slogan on slogan.slogan_id=pslogan.slogan_id ";
		$sql .= "where pslogan.product_slogan_status=1 and pslogan.product_id in (".$product_ids_string.") and slogan.slogan_status=1";
		
		$query = $this->database->slave->query($sql);
		//echo $this->database->slave->last_query();die;
		$data = $query->result_array();
		
		global $mem_expired_time;
		if(!empty($data)){
			foreach ($data as $slogan_key=>$slogan_val){
				$pid = $slogan_val['product_id'];
				$mem_key = md5("each_buyer_product_slogan_".$pid);
				$new_slogan_val[] = $slogan_val;
				$this->memcache->set($mem_key, $new_slogan_val,$mem_expired_time['product_slogan']);
				$result[$pid] = $slogan_val;
			}
		}
		
		return $result;
	}
	
	/**
	 * @desc 批量获取商品详情信息
	 * @param unknown $product_array
	 * @return multitype:multitype: unknown
	 */
	public function getinfoWithArray($product_array,$status = 0,$language_code = "us"){
		global $mem_expired_time;
		
		$return_infos = array();
		if(is_array($product_array) && !empty($product_array)){

			foreach ($product_array as $k=>$v){
				$product_id = $v;
				if(!$product_id || !is_numeric($product_id)) {
					$return_infos[$product_id] = array();
					continue;
				}
					
				$data = $this->getinfo($product_id,$status,$language_code);
				if(!empty($data))$return_infos[$product_id] = $data;
			}
		}
		//echo "<pre>bsssdddd";print_r($return_infos);die;
		return $return_infos;
	}
	public function getinfoWithArray_bak($product_array,$status = 0,$language_code = "us"){
		global $mem_expired_time;
	
		$return_infos = array();
		if(is_array($product_array) && !empty($product_array)){
				
			foreach ($product_array as $k=>$v){
				$product_id = $v;
				if(!$product_id || !is_numeric($product_id)) {
					$return_infos[$product_id] = array();
					continue;
				}
					
				$data = $this->getinfo($product_id,$status,$language_code);
				if(!empty($data))$return_infos[$product_id] = $data;
			}
		}
	
		return $return_infos;
	}
	
	//多个没有缓存的商品基本信息获取，并逐个缓存
	public function getBaseInfoWithArray($product_array,$language_code = "us"){
		$result = array();
		if(is_array($product_array) && !empty($product_array)){
			$this->database->slave->from('eb_product');
			$this->database->slave->where('product_status',1);
			$this->database->slave->where_in('product_id',$product_array);
			$query = $this->database->slave->get();
			$data = $query->result_array();
			//echo $this->database->slave->last_query();die;

			global $mem_expired_time;
			//对每个缓存
			if(!empty($data)){
				foreach ($data as $k=>$v){
					$pid = $v['product_id'];
					$mem_key = md5("each_buyer_product_".$v['product_id']."_status_1_languagecode_".$language_code);
					$this->memcache->set($mem_key, $v,$mem_expired_time['product_info']);
					$result[$pid] = $v;
				}	
			}
		}
		
		return $result;
	}
	
	public function getAllInfoWithArray($product_array,$language_code = "us",$status = 1){
		if(!is_array($product_array) || empty($product_array)) return array();
		//基本信息
		$all_base_infos = array();
		$no_cache_product_ids = array();
		$cache_product_infos = $no_cache_product_infos = array();
		foreach ($product_array as $base_key=>$base_val){
			$base_mem_key = md5("each_buyer_product_".$base_val."_status_".$status."_languagecode_".$language_code);
			$data = $this->memcache->get($base_mem_key);
			if(!$data){
				array_push($no_cache_product_ids, $base_val);	
			}else{
				$cache_product_infos[$base_val] = $data;
			}
			
		}
		if(!empty($no_cache_product_ids))$no_cache_product_infos = $this->getBaseInfoWithArray($no_cache_product_ids,$language_code);
		$all_base_infos = $cache_product_infos + $no_cache_product_infos;
		
		//计算商品是否是new新品,两周内
		if(!empty($all_base_infos)){
			foreach($all_base_infos as $new_key=>&$new_val){
				$between_days = howbetweendates($new_val['product_time_initial_active']);
				if($between_days<=PRODUCT_NEW_DAYS){
					$new_val['new'] = true;
				}else{
					$new_val['new'] = false;
				}
			}
		}
		
		//icon
		if(!empty($all_base_infos)){
			$icon_no_cache_infos = $icon_cache_infos = array();
			$icon_no_cache_ids = array();
			foreach ($all_base_infos as $icon_key=>&$icon_val){
				$pid = $icon_val['product_id'];
				$icon_mem_key = md5("each_buyer_product_slogan_".$pid);
				$icon_data = $this->memcache->get($icon_mem_key);
				if(!$icon_data){
					array_push($icon_no_cache_ids, $icon_val['product_id']);
				}else{
					$icon_cache_infos[$pid] = $icon_data; 
				}
			}
			
			if(!empty($icon_no_cache_ids))$icon_no_cache_infos = $this->batchProductWithIconHascache($icon_no_cache_ids);
			$icon_all_infos = $icon_cache_infos+$icon_no_cache_infos;
			foreach ($all_base_infos as $icon_info_key=>&$icon_info_val){
				if(array_key_exists($icon_info_val['product_id'], $icon_all_infos)){
					$icon_info_val['icon'] = true;
				}else{
					$icon_info_val['icon'] = false;
				}
			}
		}
		
		//slogan
		if(!empty($all_base_infos)){
			$slogan_no_cache_infos = $slogan_cache_infos = array();
			$slogan_no_cache_ids = array();
			foreach ($all_base_infos as $slogan_key=>&$slogan_val){
				$slogan_pid = $slogan_val['product_id'];
				$slogan_mem_key = md5("each_buyer_product_slogan_".$slogan_pid);
				$slogan_data = $this->memcache->get($slogan_mem_key);
				$slogan_data = array();
				if(!$slogan_data){
					array_push($slogan_no_cache_ids, $slogan_val['product_id']);
				}else{
					$slogan_cache_infos[$pid] = $icon_data; 
				}
			}
			
			if(!empty($slogan_no_cache_ids))$slogan_no_cache_infos = $this->batchPproductWithSloganHascache($slogan_no_cache_ids);
			$slogan_all_infos = $slogan_cache_infos+$slogan_no_cache_infos;
			
			foreach ($all_base_infos as $slogan_info_key=>&$slogan_info_val){
				$slogan_news_id = $slogan_info_val['product_id'];
				if(array_key_exists($slogan_news_id, $slogan_all_infos)){
					$slogan_info_val['slogan'] = $slogan_all_infos[$slogan_news_id];
				}else{
					$slogan_info_val['slogan'] = array();
				}
			}
		}
		
		//多语言
		if(!empty($all_base_infos)){
			$language_no_cache_infos = $language_cache_infos = array();
			$language_no_cache_ids = array();
			foreach ($all_base_infos as $language_key=>$language_val){
				$language_pid = $language_val['product_id'];
				$language_mem_key = md5("each_buyer_product_desc_".$language_pid."_languagecode_".$language_code);
				$language_data = $this->memcache->get($language_mem_key);
				//$language_data = array();
				if(empty($language_data)){
					array_push($language_no_cache_ids, $language_pid);
				}else{
					$language_cache_infos[$language_pid] = $language_data;
				}
			}
			
			if(!empty($language_no_cache_ids))$language_no_cache_infos = $this->batchProductDescriptionInfo($language_no_cache_ids,$language_code);
			$language_all_infos = $language_no_cache_infos+$language_cache_infos;
			foreach ($all_base_infos as $language_all_key=>&$language_all_val){
				$language_again_pid = $language_all_val['product_id'];
				if(array_key_exists($language_again_pid, $language_all_infos)){
					$language_all_val['product_description_name'] = $language_all_infos[$language_again_pid]['product_description_name'];
					$language_all_val['product_description_content'] = $language_all_infos[$language_again_pid]['product_description_content'];
				}else{
					$language_all_val['product_description_name'] = '';
					$language_all_val['product_description_content'] = '';
				}
			}
			
		}
		//echo "<pre>bbbss";print_r($all_base_infos);die;
		return $all_base_infos;
	}
	
	/**
	 * @desc 判断购买商品是否下架
	 * @param unknown $sku_pid_array
	 * @param string $language_code
	 */
	public function getProductInfoWithStatusAndPrice($sku_pid_array,$language_code = 'us'){
		//对基本数据进行判断
		
		$status = true;
		foreach ($sku_pid_array as $key=>$val){
			$pid = $val['product_id'];
			$sku = $val['product_sku'];
			$status = $this->checkProductStatusAndSku($pid,$sku,$language_code);
		}
		
		return $status;
	}
    //判断商品是否能卖
    public function checkProductStatusAndSku($pid,$sku,$language_code = 'us'){
        $status = true;
        //pid状态
        $info = $this->getinfo($pid, 1 ,$language_code);
        if(!empty($info)){
            //sku状态
            $sku_info = $this->getSkuInfo($sku,false);
            if(!$sku_info || empty($sku_info)) $status = false;
        }else{
            $status = false;
        }
        return $status;
    }
	/**
	 * 计算两个日期之间天数
	 * @param unknown $date1
	 * @param unknown $date2
	 * @return number
	 */
	private function daysbetweendates($date){
	    $start_date = strtotime($date);
	    $days = ceil(abs(time() - $start_date)/86400);
	    return $days;
	}
	
	//根据category_id获取所有该分类的副分类商品id列表
	public function categoryProductList($category_array){
		$result = array();
		
		$this->database->slave->from('eb_category_product');
		$this->database->slave->where('category_product_status',1);
		if(is_array($category_array) && count($category_array)){
			$this->database->slave->where_in('category_id', $category_array);
		}elseif(is_numeric($category_array)){
			$this->database->slave->where('category_id',$category_array);
		}else{
			return $result;
		}
		
		$query = $this->database->slave->get();
		//echo $this->database->slave->last_query();die;
		$result = $query->result_array();

		return $result;
	}
	
	//根据category_id，销量，获取上架商品，倒排
	public function recommendProductList($category_array = array(),$product_ids = array(),$pagesize=18){
		$result = array();
		
		$mem_key = "each_buyer_search_recommend";
		if(!empty($category_array)){
			$category_id_string = implode(",", $category_array);
			$mem_key .=$category_id_string;
		}
		$mem_key = md5($mem_key);
		global $mem_expired_time;
		
		//$this->memcache->delete($mem_key);
		if(!$result = $this->memcache->get($mem_key)){
			$this->database->slave->from('eb_product');
			$this->database->slave->where('product_status',1);
			$this->database->slave->order_by("product_sales","desc");
			if(!empty($category_array))$this->database->slave->where_in("category_id",$category_array);
			if(!empty($product_ids))$this->database->slave->where_not_in("product_id",$product_ids);
			
			$this->database->slave->limit($pagesize);
			$query = $this->database->slave->get();
			//echo $this->database->slave->last_query();die;
			$result = $query->result_array();
			
			//icon，slogan等处理
			if(!empty($result)){
				foreach ($result as $k=>&$data){
					//商品是否是new新品,两周内
					$between_days = howbetweendates($data['product_time_initial_active']);
					if($between_days<=PRODUCT_NEW_DAYS){
						$data['new'] = true;
					}else{
						$data['new'] = false;
					}
						
					//icon
					$product_icon = $this->productWithIconHascache($data['product_id']);
					if(!empty($product_icon)){
						$data['icon'] = true;
					}else{
						$data['icon'] = false;
					}
					//slogan
					$product_slogan = $this->productWithSloganHascache($data['product_id']);
					if(!empty($product_slogan)){
						$data['slogan'] = $product_slogan;
					}else{
						$data['slogan'] = array();
					}
				}
			}
			
			$this->memcache->set($mem_key, $result,$mem_expired_time['search_recommend_product']);
		}
		
		return $result;
		
	}
	
	/**
	 * @desc 返回商品图片信息
	 * @param unknown $product_id
	 * @return multitype:
	 */
	public function productImageList($product_id){
		$result = array();
		if(!$product_id || !is_numeric($product_id))return $result;
		
		$mem_key = md5("each_buyer_product_images_".$product_id);
		
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		if(!$result = $this->memcache->get($mem_key)){
			$this->database->slave->from('eb_product_image');
			$this->database->slave->where('product_image_status',1);
			$this->database->slave->where("product_id",$product_id);
			$this->database->slave->order_by("product_image_sort","desc");
				
			$query = $this->database->slave->get();
			//echo $this->database->slave->last_query();die;
			$result = $query->result_array();
			
			$this->memcache->set($mem_key, $result,$mem_expired_time['search_recommend_product']);
			
		}
		return $result;
		
	}
	
	//返回also_like商品
	public function alsolikeProductWithPid($product_id,$language_code= 'us', $pagesize=3){
		$result = array();
		if(is_numeric($product_id)){
			$mem_key = md5("each_buyer_recommendation_statistics_product_".$product_id.'_'.$language_code);
		}elseif(is_array($product_id)){
			$mem_key = md5("each_buyer_recommendation_statistics_product_".json_encode($product_id).'_'.$language_code);
		}else{
			return $result;
		}
		
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		if(!$result = $this->memcache->get($mem_key)){
			$this->database->slave->from('eb_recommendation_statistics');
			if(is_numeric($product_id)){
				$this->database->slave->where("product_id",$product_id);
			}else{
				$this->database->slave->where_in("product_id",$product_id);
			}
			
			$this->database->slave->limit($pagesize);
			
			$query = $this->database->slave->get();
			//echo $this->database->slave->last_query();die;
			$data = $query->result_array();
			
			//处理个数
			if(!empty($data)){
				$num = 0;
                $product_ids = array();
				foreach ($data as $k=>$v){
					$product_ids_string = $v['recommendation_statistics_content'];
                    $product_ids = array_merge($product_ids,explode(',',$product_ids_string));
//					//拿出单独pid，并去检查商品是否上架
//					if(is_numeric($product_ids_string)){
//						$new_data = $this->getinfo($product_ids_string,1,$language_code);
//						if($new_data){
//							//$result[$product_ids_string] = $new_data;
//							$result[] = $new_data;
//						}else $result = array();
//
//					}else{
//						$product_ids_array = explode(",", $product_ids_string);
//						foreach ($product_ids_array as $key=>$val){
//							if($num<$pagesize){
//								$new_data = $this->getinfo($val,1,$language_code);
//								if($new_data){
//									//$result[$val] = $new_data;
//									$result[] = $new_data;
//									$num++;
//								}
//							}
//						}
//					}
				}
                $result = $this->getProductList($product_ids,1,$pagesize,$language_code);
			}else{
				$result = array();
			} 
			
			$this->memcache->set($mem_key, $result,$mem_expired_time['recommendation_statistics_product']);
			
		}
		//echo "<pre>";print_r($result);die;
		return $result;
	}

	public function getRecentView(&$languageId,&$collectProIds,&$curPid=''){
	
		$userVisitedProsCookie = $this->input->cookie('userVisitedPros');
		$userVisitedProsCookieUnserialize = $userVisitedProsCookie===false?array():unserialize($userVisitedProsCookie);
		if($curPid){
			$userVisitedProsCookieUnserialize = array_diff($userVisitedProsCookieUnserialize,array($curPid));
		}
		$userVisitedProsCookieUnserializeSlice = array_slice($userVisitedProsCookieUnserialize,0,6);
		$proInfoList = $this->getProInfoById($userVisitedProsCookieUnserializeSlice, $languageId);
		$return = array();
		foreach ($userVisitedProsCookieUnserializeSlice as $v) {
			$proInfo = isset($proInfoList[$v])?$proInfoList[$v]:current($this->getProInfoById($v, 1));
			if ($proInfo) {
				$proInfo['isCollected'] = in_array($v, $collectProIds) ? TRUE : FALSE;
				$return[] = $proInfo;
			}
		}
		return $return;
	}
	
	/**
	 * @desc @更新单个商品销售数量
	 * @param unknown $product_id
	 * @param unknown $sales_num
	 */
	public function updatePorductSales($product_id ,$sales_num){
		if(!$product_id || !is_numeric($product_id) || !is_numeric($sales_num)) return false;
		$this->database->master->set('product_sales', 'product_sales+'.$sales_num, FALSE);
		$this->database->master->where('product_id',$product_id);
		$result = $this->database->master->update('eb_product');
		return $result;
		//echo $this->database->slave->last_query();die;
	}
	
	
/**
	 * 获得发送系统邮件推荐商品列表
	 * @param int $languageId 语言
	 * @param array $productIds 当前购买这个订单的商品
	 * @param int $isScriptTask FALSE为网站触发 TRUE为定时任务触发
	 * @param array $cartProductId 用户购物车商品分类ID
	 * @param int $userId 用户ID
	 * @param string $currency 货币
	 * @param sting $languageCode 语言code
	 * @param string $orderFrom 订单来源
	 * @param string $source 来源
	 * @return array recommendProductList
	 * @author WTY
	 */
	public function getEmailRecommendProduct($productIds = array(), $limit = 4, $languageId = 1){
		if(!empty($productIds)){
			$nums = 0;
			global $language_range_array;
			$language_code = $language_range_array[$languageId];
			$this->load->model("bundlemodel","bundle");
			$num = 0;
			$return_data = $recommend_ids = $bought_together_products = array();
			
			//先推荐bought together
			$bought_together_info = $this->bundle->recommendBatchProduct($productIds);
			
			if(!empty($bought_together_info)){
				$bought_together_ids = array();
				foreach ($bought_together_info as $bundle_key=>$bundle_val){
					$bought_together_ids = array_merge($bought_together_ids,explode(',', $bundle_val['recommendation_manual_content']));
				}
				$recommend_ids = $bought_together_ids;
				//商品详情
				$bought_together_products = $this->getinfoWithArray($recommend_ids,1);
				$num = count($bought_together_ids);
			}
			
			//再推荐who bought this item also bought
			if($num < $limit){
				$also_like_info = $this->alsolikeProductWithPid($productIds,$language_code);
				
				//去重
				foreach ($also_like_info as $like_k=>&$like_v){
					if(in_array($like_v['product_id'], $recommend_ids)) unset($like_v);
					$recommend_ids[] = $like_v['product_id'];
					if(!isset($like_v['product_description_name'])) $like_v['product_description_name'] = '';
					if(!isset($like_v['product_description_content'])) $like_v['product_description_content'] = '';
				}
				$return_data = array_merge($bought_together_products,$also_like_info);
				$num = count($return_data);
				
				if($num < $limit){
					//还不够再推荐这个商品所在分类的销量排名前10的商品（去重）
					$product_sales = $this->getProductOrderbySales();
					//去重
					$product_desc_ids = array();
					foreach ($product_sales as $sales_k=>&$sales_v){
						if(in_array($sales_v['product_id'], $recommend_ids)) {
							unset($sales_v);
							continue;
						}
						array_push($product_desc_ids, $sales_v['product_id']);
					}
					//多语言
					$description_info = array();
					if(!empty($product_desc_ids))$description_info = $this->batchProductDescriptionInfo($product_desc_ids,$language_code);
					//映射多语言选项
					foreach ($product_sales as $desc_key=>&$desc_val){
						$desc_pid = $desc_val['product_id'];
						if(array_key_exists($desc_pid, $description_info)){
							$desc_val['product_description_name'] = $description_info[$desc_pid]['product_description_name'];
							$desc_val['product_description_content'] = $description_info[$desc_pid]['product_description_content'];
						}else{
							$desc_val['product_description_name'] = '';
							$desc_val['product_description_content'] = '';
						}
					}
					$return_data = array_merge($return_data,array_slice($product_sales,0,$limit-$num));
					return $return_data;
				}else{
					return $return_data = array_slice($return_data, 0,$limit);
				}
			}else{
				return $return_data = array_slice($bought_together_products, 0,$limit);
			}
			
		}

	}

	public function getProductOrderbySales($limit = 10){
		$mem_key = md5("each_buyer_product_recommend_email");
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		if(!$result = $this->memcache->get($mem_key)){
			$this->database->slave->from('eb_product');
			$this->database->slave->where('product_status',1);
			$this->database->slave->order_by("product_sales","desc");
			$this->database->slave->limit($limit);
			$query = $this->database->slave->get();
			//echo $this->database->slave->last_query();die;
			$result = $query->result_array();
				
			$this->memcache->set($mem_key, $result,$mem_expired_time['search_recommend_product']);
				
		}
		return $result;
	}
	
	public function getUncachedProductById($product_ids){
		if(!is_array($product_ids)) $product_ids = array($product_ids);
		if(empty($product_ids)) return array();

		$this->database->slave->from('eb_product');
		$this->database->slave->where_in('product_id',$product_ids);
		$query = $this->database->slave->get();
		$list = $query->result_array();

		return $list;
	}

	public function getUncachedProductSKUByCode($product_skus){
		if(!is_array($product_skus)) $product_skus = array($product_skus);
		if(empty($product_skus)) return array();

		$this->database->slave->from('eb_product_sku');
		$this->database->slave->where_in('product_sku_code',$product_skus);
		$query = $this->database->slave->get();
		$list = $query->result_array();

		return $list;
	}	
}
