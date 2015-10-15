<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc product属性模型
 * @author Administrator
 *
 */
class attributeproductmodel extends CI_Model {

    private $mem_key_sku = 'each_buyer_complexattr_sku_attrname_';
	/**
	 * @desc 返回商品全部基本sku信息
	 * @param unknown $product_id
	 * @return multitype:|Ambigous <multitype:, boolean, string>
	 */
	public function productAllAttrIds($product_id){
		$result = array();
		if(!$product_id || !is_numeric($product_id))return $result;
	
		$mem_key = md5("each_buyer_product_all_attr_".$product_id);
	
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_attribute_product');
			$this->database->slave->where('attribute_product_status',1);
			$this->database->slave->where("product_id",$product_id);
	
			$query = $this->database->slave->get();
			//echo $this->database->slave->last_query();die;
			$result = $query->result_array();
	
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_attr_with_block']);
	
		}
		if(!empty($result))return $result;
		else return array();
	}
	
	/**
	 * @desc 返回商品全部基本sku信息(sku状态为1)
	 * @param unknown $product_id
	 * @return multitype:|Ambigous <multitype:, boolean, string>
	 */
	public function productAllBaseSku($product_id){
		$result = array();
		if(!$product_id || !is_numeric($product_id))return $result;
	
		$mem_key = md5("each_buyer_product_base_sku_".$product_id);
	
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_product_sku');
			$this->database->slave->where('product_sku_status',1);
			$this->database->slave->where("product_id",$product_id);
	
			$query = $this->database->slave->get();
			//echo $this->database->slave->last_query();die;
			$result = $query->result_array();
	
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_sku']);
	
		}
		if(!empty($result))return $result;
		else return array();
	}
	public function productAllBaseSkus($product_ids){
		$result = array();
		if(empty($product_ids))return $result;
	
		$mem_key = md5("each_buyer_product_base_sku_".json_encode($product_ids));
	
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_product_sku');
			$this->database->slave->where('product_sku_status',STATUS_ACTIVE);
			$this->database->slave->where_in("product_id",$product_ids);
	
			$query = $this->database->slave->get();
			//echo $this->database->slave->last_query();die;
			$result = $query->result_array();
	
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_sku']);
	
		}
		if(!empty($result))return $result;
		else return array();
	}
	
	/**
	 * @desc 返回商品全部基本sku信息
	 * @param unknown $product_id
	 * @return multitype:|Ambigous <multitype:, boolean, string>
	 */
	public function productAllBaseAllSku($product_id){
		$result = array();
		if(!$product_id || !is_numeric($product_id))return $result;
	
		$mem_key = md5("each_buyer_product_base_sku_".$product_id);
	
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_product_sku');
			//$this->database->slave->where('product_sku_status',1);
			$this->database->slave->where("product_id",$product_id);
	
			$query = $this->database->slave->get();
			//echo $this->database->slave->last_query();die;
			$result = $query->result_array();
	
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_sku']);
	
		}
		if(!empty($result))return $result;
		else return array();
	}
	
	public function productAllBaseAllSkus($product_ids){
		$result = array();
		if(empty($product_ids))return $result;
	
		$mem_key = md5("each_buyer_product_base_sku_".json_encode($product_ids));
	
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_product_sku');
			//$this->database->slave->where('product_sku_status',1);
			$this->database->slave->where_in("product_id",$product_ids);
	
			$query = $this->database->slave->get();
			//echo $this->database->slave->last_query();die;
			$result = $query->result_array();
	
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_sku']);
	
		}
		if(!empty($result))return $result;
		else return array();
	}
	
	/**
	 * @desc 返回商品基本sku信息
	 * @param unknown $product_id
	 * @return multitype:|Ambigous <multitype:, boolean, string>
	 */
	public function productBaseSku($product_id){
		$result = array();
		if(!$product_id || !is_numeric($product_id))return $result;
		
		$mem_key = md5("each_buyer_product_base_sku_".$product_id);
		
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_product_sku');
			$this->database->slave->where('product_sku_status',1);
			$this->database->slave->where("product_id",$product_id);
		
			$query = $this->database->slave->get();
			//echo $this->database->slave->last_query();die;
			$result = $query->result_array();
				
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_sku']);
				
		}
		if(!empty($result))return $result[0];
		else return array();
	}
	
	/**
	 * @desc 返回商品sku信息(长，宽，高，是否敏感品，仓库地址)
	 * @param unknown $product_id
	 * @return multitype:|Ambigous <multitype:, boolean, string>
	 */
	public function productSkuinfoWithSku($product_sku,$product_id){
		$result = array();
		if(!$product_sku || empty($product_sku) || !$product_id || !is_numeric($product_id))return $result;
	
		$mem_key = md5("each_buyer_product_skuinfo_".$product_sku."_id_".$product_id);
	
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_product_sku');
			$this->database->slave->where("product_id",$product_id);
			$this->database->slave->where('product_sku_status',1);
			$this->database->slave->where("product_sku_code",$product_sku);
			
			$query = $this->database->slave->get();
			//echo $this->database->slave->last_query();die;
			$result = $query->result_array();
	
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_sku']);
	
		}
		if(!empty($result))return $result;
		else return array();
	}
	
	//根据多个sku，获取商品sku信息，并逐个缓存
	public function batchProductSkuInfoWithSkuArray($sku_array = array()){
		$result = array();
		if(!is_array($sku_array) || empty($sku_array)) return $result;
		
		$cache_infos = $no_cache_infos = array();
		$no_cache_ids = array();
		foreach ($sku_array as $key=>$val){
			$mem_key = md5("each_buyer_product_skuinfo_".$val);
			$data = $this->memcache->get($mem_key);
			if(!$data){
				array_push($no_cache_ids, $val);
			}else{
				$cache_infos[$val] = $data;
			}
		}
		
		if(!empty($no_cache_ids))$no_cache_infos = $this->getSkuArray($no_cache_ids);
		$result = $cache_infos + $no_cache_infos;
		
		return $result;
	}
	
	//根据多个sku获取sku信息，并缓存
	public function getSkuArray($sku_array){
		$result = array();
		if(!is_array($sku_array) || empty($sku_array)) return $result;
		
		$this->database->slave->from('eb_product_sku');
		$this->database->slave->where('product_sku_status',1);
		$this->database->slave->where_in("product_sku_code",$sku_array);
		$query = $this->database->slave->get();
		//echo $this->database->slave->last_query();die;
		$data = $query->result_array();
		
		global $mem_expired_time;
		foreach ($data as $k=>$v){
			$sku = $v['product_sku_code'];
			$mem_key = md5("each_buyer_product_skuinfo_".$sku);
			$this->memcache->set($mem_key, $v,$mem_expired_time['product_sku']);
			$result[$sku] = $v;
		}
		
		return $result;
	}
	
	//查询单个sku详情
	public function productSkuinfoWithOnlySku($product_sku){
		$result = array();
		if(!$product_sku || empty($product_sku))return $result;
	
		$mem_key = md5("each_buyer_product_skuinfo_".$product_sku);
	
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_product_sku');
			$this->database->slave->where('product_sku_status',1);
			$this->database->slave->where("product_sku_code",$product_sku);
				
			$query = $this->database->slave->get();
			//echo $this->database->slave->last_query();die;
			$result = $query->row_array();
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_sku']);
	
		}
		if(!empty($result))return $result;
		else return array();
	}
	
	/**
	 * @desc 返回商品sku与属性，属性组之间的关系信息
	 * @param unknown $product_id
	 * @return multitype:
	 */
	public function productAttrWithSku($product_id){
		$result = array();
		if(!$product_id || !is_numeric($product_id))return $result;
		
		$mem_key = md5("each_buyer_product_sku_attr_".$product_id);
		
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_complexattr_sku');
			$this->database->slave->where('complexattr_sku_status',1);
			$this->database->slave->where("product_id",$product_id);
		
			$query = $this->database->slave->get();
			//echo $this->database->slave->last_query();die;
			$result = $query->result_array();
		
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_sku_attr']);
		
		}
		return $result;
	}
	
	//查询多个pid sku信息
	public function productAttrWithBatchSku($product_ids){
		$result = array();
		if(empty($product_ids))return $result;
	
		$mem_key = md5("each_buyer_product_sku_attr_".json_encode($product_ids));
	
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_complexattr_sku');
			$this->database->slave->where('complexattr_sku_status',STATUS_ACTIVE);
			$this->database->slave->where_in("product_id",$product_ids);
	
			$query = $this->database->slave->get();
			//echo $this->database->slave->last_query();die;
			$result = $query->result_array();
	
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_sku_attr']);
	
		}
		return $result;
	}
	
	//多语言商品，属性信息
	public function complexattrInfo($complexattr_id,$language_id = 1){
		$result = array();
		if(!$language_id || !is_numeric($language_id)) $language_id = 1;
		if(!$complexattr_id || !is_numeric($complexattr_id))return $result;

		$mem_key = md5("each_buyer_complexattr_sku_".$complexattr_id."_attr_".$language_id);
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_complexattr_lang');
			$this->database->slave->where("complexattr_id",$complexattr_id);
			$this->database->slave->where("language_id",$language_id);
			
			$query = $this->database->slave->get();
			//echo $this->database->slave->last_query();die;
			$result = $query->result_array();
			
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_complexattr']);
		}
		return $result;
	}
	
	public function complexattrBatchInfo($complexattr_ids,$language_id = 1){
		$result = array();
		if(!$language_id || !is_numeric($language_id)) $language_id = 1;
		if(empty($complexattr_ids))return $result;
	
		$mem_key = md5("each_buyer_complexattr_sku_".json_encode($complexattr_ids)."_attr_".$language_id);
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_complexattr_lang');
			$this->database->slave->where_in("complexattr_id",$complexattr_ids);
			$this->database->slave->where("language_id",$language_id);
				
			$query = $this->database->slave->get();
			//echo $this->database->slave->last_query();die;
			$result = $query->result_array();
				
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_complexattr']);
		}
		return $result;
	}
	
	//多语言，属性组信息
	public function complexattrValueInfo($complexattr_value_id,$language_id = 1){
		$result = array();
		if(!$language_id || !is_numeric($language_id)) $language_id = 1;
		if(!$complexattr_value_id || !is_numeric($complexattr_value_id))return $result;
		
		$mem_key = md5("each_buyer_complexattr_sku_".$complexattr_value_id."_attrvalue_".$language_id);
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_complexattr_value_lang');
			$this->database->slave->where("complexattr_value_id",$complexattr_value_id);
			$this->database->slave->where("language_id",$language_id);
				
			$query = $this->database->slave->get();
			//echo $this->database->slave->last_query();die;
			$result = $query->result_array();
				
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_complexattr']);
		}
		
		return $result;
	}
	
	public function complexattrBatchValueInfo($complexattr_value_ids,$language_id = 1){
		$result = array();
		if(!$language_id || !is_numeric($language_id)) $language_id = 1;
		if(empty($complexattr_value_ids))return $result;
	
		$mem_key = md5("each_buyer_complexattr_sku_".json_encode($complexattr_value_ids)."_attrvalue_".$language_id);
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_complexattr_value_lang');
			$this->database->slave->where_in("complexattr_value_id",$complexattr_value_ids);
			$this->database->slave->where("language_id",$language_id);
	
			$query = $this->database->slave->get();
			//echo $this->database->slave->last_query();die;
			$result = $query->result_array();
	
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_complexattr']);
		}
		
		return $result;
	}
	//通过sku获取sku对应的商品属性，属性组信息
	public function attrAndValueWithSku($sku,$status = 1){
		$result = array();
		if(!$sku) return $result;
		if(!$status || !is_numeric($status)) $status = STATUS_ACTIVE;
		
		$mem_key = md5($this->mem_key_sku.$sku."_status_".$status);
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_complexattr_sku');
			$this->database->slave->where("complexattr_sku_status",$status);
			$this->database->slave->where("product_sku",$sku);
		
			$query = $this->database->slave->get();
			//echo $this->database->slave->last_query();die;
			$result = $query->result_array();
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_complexattr']);
		}
		return $result;
	}
	
	//批量获取不在缓存中的sku信息
	public function batchAttrAndValueWithSkuArray($sku_array,$status = 1){
		$result = array();
		if(empty($sku_array) || !is_array($sku_array)) return $result;
		if(!$status || !is_numeric($status)) $status = STATUS_ACTIVE;
	
		$this->database->slave->from('eb_complexattr_sku');
		$this->database->slave->where("complexattr_sku_status",$status);
		$this->database->slave->where_in("product_sku",$sku_array);
		$query = $this->database->slave->get();
		//echo $this->database->slave->last_query();//die;
		$data = $query->result_array();
		
		if(!empty($data)){
			global $mem_expired_time;
			foreach ($data as $key=>$val){
				$sku = $val['product_sku'];
				$result[$sku][] = $val;
			}
            foreach($result as $sku=>$value){
                $mem_key = md5($this->mem_key_sku.$sku."_status_".$status);
                $this->memcache->set($mem_key, $value,$mem_expired_time['product_complexattr']);
            }
		}
	
		return $result;
	}
	
	//批量获取sku信息
	public function getAttrAndValueWithSkus($sku_array){
		$result = array();
		if(empty($sku_array) || !is_array($sku_array)) return $result;

		$no_cache_infos = $cache_infos = array();
		$no_cache_ids = array();
		foreach ($sku_array as $k=>$v){
			$mem_key = md5($this->mem_key_sku.$v."_status_1");
			$data = $this->memcache->get($mem_key);
			$data = array();
			if(!$data){
				array_push($no_cache_ids, $v);
			}else{
				$cache_infos[$v] = $data;
			}
		}
		$no_cache_infos = $this->batchAttrAndValueWithSkuArray($no_cache_ids);
		$result = $cache_infos + $no_cache_infos;
		return $result;
	}

    public function getSKUInfoByPids($pids=array()){
        $result = array();
        if(!is_array($pids) || empty($pids)) return $result;
        $mem_key = "each_buyer_complexattr_sku_pid_";
        foreach($pids as $k => $pid){
            $ikey = md5($mem_key.$pid);
            $result[$pid] = $this->memcache->get($ikey);
            if($result[$pid] != false){
                unset($pids[$k]);
            }
        }
        if(!empty($pids)){
            global $mem_expired_time;
            $this->database->slave->from('eb_product_sku');
            $this->database->slave->where("product_sku_status",STATUS_ACTIVE);
            $this->database->slave->where_in("product_id",$pids);

            $query = $this->database->slave->get();
            //echo $this->database->slave->last_query();die;
            $list = $query->result_array();
            $product_sku = array();
            foreach($list as $item){
            	$sku = $item['product_sku_code'];
                $product_sku[$item['product_id']][$sku] = $item;
            }
            foreach($product_sku as $pid=>$value){
                $ikey = md5($mem_key.$pid);
                $this->memcache->set($ikey, $value,$mem_expired_time['product_sku']);
                $result[$pid] = $value;
            }
        }

        return $result;
    }

    public function getATTRInfoByPids($pids = array(),$language_id = 1){
        $result = array();
        if(!is_array($pids) || empty($pids)) return $result;
        $mem_key = "each_buyer_complexattr_attr_language_".$language_id."_pid_";
        foreach($pids as $k => $pid){
            $ikey = md5($mem_key.$pid);
            $result[$pid] = $this->memcache->get($ikey);
            if($result[$pid] != false){
                unset($pids[$k]);
            }
        }
        //缓存的取完了直接返回结果；
        if(!empty($pids)){
            global $mem_expired_time;
            $this->database->slave->from('eb_complexattr_sku');
            $this->database->slave->where("complexattr_sku_status",STATUS_ACTIVE);
            $this->database->slave->where_in("product_id",$pids);

            $query = $this->database->slave->get();
            //echo $this->database->slave->last_query();die;
            $list = $query->result_array();
            $complexattr_ids = array();
            $complexattr_value_ids = array();
            if(!empty($list)){
                foreach($list as $item){
                    $product_sku[$item['product_id']][$item['product_sku']][] = $item['complexattr_value_id'];
                    $complexattr_ids[] = $item['complexattr_id'];
                    $complexattr_value_ids[$item['complexattr_value_id']] = $item['complexattr_value_id'];
                }
                $complexattr_ids = array_unique($complexattr_ids);
                $complexattr_list = $this->complexattrBatchInfo($complexattr_ids,$language_id);
                $complexattr_array = array();
                foreach($complexattr_list as $item){
                    $complexattr_array[$item['complexattr_id']] = $item;
                }
                $complexattr_value_list = $this->complexattrBatchValueInfo($complexattr_value_ids,$language_id);
                $complexattr_value_array = array();
                foreach($complexattr_value_list as $item){
                    $complexattr_value_array[$item['complexattr_value_id']] = $item;
                }
//            echo '<pre>';print_r($product_sku);
                foreach($list as $item){
                    if(isset($complexattr_info[$item['product_id']][$item['complexattr_id']])){
                        if(isset( $complexattr_value_array[$item['complexattr_value_id']]))
                            $complexattr_info[$item['product_id']][$item['complexattr_id']]['attr_value'][$item['complexattr_value_id']] = $complexattr_value_array[$item['complexattr_value_id']];
                    } else {
                        $complexattr_info[$item['product_id']][$item['complexattr_id']] = $item;
                        unset($complexattr_info[$item['product_id']][$item['complexattr_id']]['complexattr_value_id']);
                        if(isset( $complexattr_value_array[$item['complexattr_value_id']]))
                            $complexattr_info[$item['product_id']][$item['complexattr_id']]['attr_value'][$item['complexattr_value_id']] = $complexattr_value_array[$item['complexattr_value_id']];
                        if(isset($complexattr_array[$item['complexattr_id']]['complexattr_lang_title']))
                            $complexattr_info[$item['product_id']][$item['complexattr_id']]['complexattr_lang_title'] =  $complexattr_array[$item['complexattr_id']]['complexattr_lang_title'];
                    }
                    //属性&sku信息处理
                }
                foreach($complexattr_info as $product_id => $item){
                    $ikey = md5($mem_key.$product_id);
                    if(isset($product_sku[$product_id]))
                        $result[$product_id] = array('attr_data'=>$item ,'sku_data'=>$product_sku[$product_id]);
                    $this->memcache->set($ikey, $result[$product_id],$mem_expired_time['product_sku']);
                }
            }
        }

//        print_r($result);exit;
        return $result;
    }
}
?>
