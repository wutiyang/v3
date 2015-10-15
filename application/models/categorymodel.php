<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc category分类模型
 * @author Administrator
 *
 */
class Categorymodel extends CI_Model {
	public $top_category;
	public $other_level_category;
	public $end_category;
	/*private $memcache;
	
	public function __construct(){
		$this->memcache = new CI_Memcache();
	}*/
	
	/**
	 * @desc 获取某category_id的基本信息
	 * @param number $category_id
	 * @return multitype:|Ambigous <unknown>
	 */
	public function getCategoryWithId($category_id = 0){
		$result = array();
		if(!$category_id || !is_numeric($category_id)) return $result;
		
		//从全部的category缓存中读取
		$all_category_lists = $this->getBaseCateList();
		if(array_key_exists($category_id, $all_category_lists)){
			return $all_category_lists[$category_id];
		}
		return $result;
	}
	
	/**
	 * @desc 获取某分类id的某语言详情信息
	 * @param number $category_id
	 * @param number $language_id
	 * @return multitype:|Ambigous <unknown>
	 */
	public function getCategoryDescWithIdAndLanguageid($category_id =0,$language_id = 0){
		$result = array();
		if(!$category_id || !$language_id || !is_numeric($category_id) || !is_numeric($language_id)) return $result;

		//从缓存中获取全部desc详情
		$all_category_desc_lists = $this->getCateDescriptionList($language_id);
		if(array_key_exists($category_id, $all_category_desc_lists)){
			return $all_category_desc_lists[$category_id];
		}
		return $result;
	}
	
	/**
	 * @desc 获取某分类id的全部信息
	 * @param unknown $category_id
	 * @param unknown $language_id
	 * @return boolean|multitype:
	 */
	public function getCategoryinfo($category_id,$language_id){
		$result = array();
		if(!$category_id || !$language_id || !is_numeric($category_id) || !is_numeric($language_id)) return false;
		$base_category_info = $this->getCategoryWithId($category_id);
		if(empty($base_category_info)) return $result;
		$desc_category_info = $this->getCategoryDescWithIdAndLanguageid($category_id,$language_id);
		$result = array_merge($base_category_info,$desc_category_info);
		return $result;
	}

    /**
     * @desc 获取某分类id的全部信息
     * @param unknown $category_id
     * @param unknown $language_id
     * @return boolean|multitype:
     */
    public function getCategoryinfos($category_id_str,$language_id){
        $result = array();
        if(!is_numeric($category_id_str) && !is_string($category_id_str)) return false;
        global $mem_expired_time;
        $mem_key = md5('eb_eachbuyer_category_info_'.$category_id_str.'_language_'.$language_id);
        $result = $this->memcache->get($mem_key);
        if($result === false){
            if(strstr($category_id_str,'/'))
                $category_id_str = explode('/',$category_id_str);
            else
                $category_id_str = array($category_id_str);
            $slave = $this->database->slave;
            $slave->from('eb_category as c');
            $slave->join('eb_category_description as c_desc','c.category_id = c_desc.category_id and c_desc.language_id='.$language_id);
            $slave->where('c.category_status',STATUS_ACTIVE);
            $slave->where_in('c.category_id',$category_id_str);
            $list = $slave->get()->result_array();
            $result = array();
            foreach($category_id_str as $category_id){
                foreach($list as $item){
                    if($item['category_id'] == $category_id)
                        $result[$category_id] = $item;
                }
            }
            $this->memcache->set($mem_key, $result,$mem_expired_time['base_category']);
        }
//        print_r($category_id_str);print_r($result);exit;
        return $result;
    }
	
	/**
	 * @desc 批量获取分类全部信息
	 * @param unknown $cid_array
	 * @param number $language_id
	 * @return multitype:|multitype:Ambigous <boolean, multitype:, multitype:>
	 */
	public function categoryinfoWithCids($cid_array,$language_id=1){
		$result = array();
		if(!is_array($cid_array) || empty($cid_array)) return $result;
	
		foreach ($cid_array as $k=>$v){
			$result[$v] = $this->getCategoryinfo($v,$language_id);
		}
		return $result;
	}
	
	/**
	 * @desc 获取所有基本category表数据
	 * @return Ambigous <multitype:unknown , boolean, string>
	 */
	public function getBaseCateList(){
		$mem_key = md5("each_buyer_category_base");
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		
		$list = $this->memcache->get($mem_key);
		if($list === false){
			//$this->database->slave->select('category_id,parent_id,category_name,category_url,category_pid_count,category_path');
			$this->database->slave->from('eb_category');
			$this->database->slave->where('category_status',STATUS_ACTIVE);
			$this->database->slave->order_by('category_sort','desc');
			$this->database->slave->order_by('category_id','asc');
			$query = $this->database->slave->get();
			$data = $query->result_array();
			
			$list = array();
			foreach ($data as $val){
				$cate_id = $val['category_id'];
				$list[$cate_id] = $val;
			}
			
			$this->memcache->set($mem_key, $list,$mem_expired_time['base_category']);
		}
		
		return $list;
	}
	
	/**
	 * @desc 获取所有语言下的category的description表数据
	 * @param unknown $lanauge_id
	 * @return Ambigous <multitype:unknown , boolean, string>
	 */
	public function getCateDescriptionList($lanauge_id){
		global $mem_expired_time;
		if(!$lanauge_id || !is_numeric($lanauge_id)) $lanauge_id = 1;
		$mem_key = md5("each_buyer_category_desc_".$lanauge_id);
		//$this->memcache->delete($mem_key);
		
		$list = $this->memcache->get($mem_key);
		if($list === false){
			$this->database->slave->from('eb_category_description');
			$this->database->slave->where('language_id',$lanauge_id);
			$this->database->slave->order_by('category_description_id','asc');
			$query = $this->database->slave->get();
			$data = $query->result_array();
			
			$list = array();
			foreach ($data as $val){
				$cate_id = $val['category_id'];
				$list[$cate_id] = $val;
			}
			
			$this->memcache->set($mem_key, $list,$mem_expired_time['base_category']);
		}
		
		return $list;
	}
	
	/**
	 * @desc 合并category表及description表的数据
	 * @param unknown $lanauge_id
	 * @return Ambigous <Ambigous, multitype:unknown , boolean, string>
	 */
	public function getAllCateinfos($lanauge_id){
		$base_cate_list = $this->getBaseCateList();
		$description_cate_list = $this->getCateDescriptionList($lanauge_id);

		foreach ($base_cate_list as $k=>&$v){
			$v = array_merge($v,$description_cate_list[$k]);
		}
		return $base_cate_list;
	}
	
	/**
	 * @desc 获取该category_id下的相关分类及相关商品(相关分类与相关商品区别开)
	 * @desc 只返回分类id及商品id，具体分类及商品是否可以仍需继续判断
	 * @param unknown $category_id
	 * @return Ambigous <multitype:, unknown, boolean, string>
	 */
	public function getCategoryRelatedInfos($category_id){
        if(empty($category_id)) return array();
        if(is_array($category_id) && !empty($category_id))
		    $mem_key = md5("each_buyer_category_related_".implode('_',$category_id));
        else
            $mem_key = md5("each_buyer_category_related_".$category_id);
		global $mem_expired_time;
		
		$list = $this->memcache->get($mem_key);
		if($list===false){
			$this->database->slave->from('eb_category_related');
			$this->database->slave->where('category_related_status',1);
            if(is_array($category_id) && !empty($category_id))
                $this->database->slave->where_in('category_id',$category_id);
            else if(is_numeric($category_id))
			    $this->database->slave->where('category_id',$category_id);
			$this->database->slave->order_by('category_related_sort','desc');
			$query = $this->database->slave->get();
			$data = $query->result_array();

			$list = array();
			foreach ($data as $val){
				$key = $val['category_related_content'];
                $category_id = $val['category_id'];
				if($val['category_related_type']==CATEGORY_RELATED_TYPE_CATEGORY){
					$list[$category_id]['category_list'][$key] = $val;
				}elseif($val['category_related_type']==CATEGORY_RELATED_TYPE_PRODUCT){
					$list[$category_id]['product_list'][$key] = $val;
				}
				
			}
				
			$this->memcache->set($mem_key, $list,$mem_expired_time['category_related']);
		}

		return $list;
	}
	
	/**
	 * @desc 获取该分类id下该语言下的相关分类信息
	 * @param unknown $category_id
	 * @param unknown $language_id
	 */
	public function getRelatedCategoryList($category_id ,$language_id = 1){
		$result = array();
		if(!$category_id || !$language_id || !is_numeric($category_id) || !is_numeric($language_id)) return $result;
		//获取该分类id下的所有相关分类id
		$related_category_ids = $this->getCategoryRelatedInfos($category_id);
        if(!empty($related_category_ids))
            $related_category_ids = current($related_category_ids);
		if($related_category_ids && isset($related_category_ids['category_list'])){
			foreach ($related_category_ids['category_list'] as $key=>$val){
				$info = $this->getCategoryinfo($key,$language_id);
				$result[$key] = $info;
			}
		}

		return $result;
	}
	
	/**
	 * @desc 获取该分类下的所有相关商品信息
	 * @param unknown $category_id
	 * @param unknown $language_id
	 * @return multitype:|multitype:unknown
	 */
	public function getCategoryRelatedProductList($category_ids = array() ,$language_id = 1){
		$result = array();
        if(!is_array($category_ids)) $category_ids = explode(',',$category_ids);
        if(empty($category_ids)) return $result;
        //获取该分类id下的所有相关商品id
        $related_category_ids = $this->getCategoryRelatedInfos($category_ids);
        if($related_category_ids && !empty($related_category_ids)){
            $language_code = currentLanguageCode();
           $list = $product_ids = array();
            $this->load->model("goodsmodel","product");
            foreach($related_category_ids as $key=>$value){
                if($value && isset($value['product_list'])){
                    $list[$key] = array_keys($value['product_list']);
                    $product_ids = array_merge($product_ids,$list[$key]);
                }
            }
            $product_list = $this->product->getProductList($product_ids,$status = 1,0,$language_code);
            foreach($list as $key=>$item){
                foreach($item as $key1=>$product_id){
                    if(isset($product_list[$product_id]))$result[$key][$key1] = $product_list[$product_id];
                }
                if(isset($result[$key])) asort($result[$key]);
            }
        }

		
		return $result;
	}
	
	//获取匹配某分类path的所有分类id
	public function categorySearchLists($category_id){
		$result = array();
		if(!$category_id || !is_numeric($category_id)) return $result;
		
		$mem_key = md5("each_buyer_category_search_".$category_id);
		global $mem_expired_time;
		
		$list = $this->memcache->get($mem_key);
		if($list === false){
			$this->database->slave->from('eb_category');
			$this->database->slave->where('category_status',1);
			$this->database->slave->like('category_path',$category_id);
			$this->database->slave->order_by('category_id','desc');
			$query = $this->database->slave->get();
			$list = $query->result_array();
		
			$this->memcache->set($mem_key, $list,$mem_expired_time['category_search_match']);
		}
				
		return $list;
	}
	
	//根据单个category_id获取该分类下的所属商品id
	public function maincategoryProductWithCate($category_id){
		$result = array();
		if(!$category_id || !is_numeric($category_id)) return $result;
		$mem_key = md5("each_buyer_category_product_search_".$category_id."_type_main");
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$result = array();
			$this->database->slave->from('eb_product');
			$this->database->slave->where('product_status',1);
			$this->database->slave->like('product_path',$category_id);
			$this->database->slave->order_by('product_price','desc');
			$query = $this->database->slave->get();
			$data = $query->result_array();

	        foreach($data as $v){
	            $result[$v['product_id']] = $v;
	        }
			$this->memcache->set($mem_key, $result,$mem_expired_time['category_product_type']);
		}
		return $result;
	}
	
	public function newMaincategoryProduct($category_id){
		$result = array();
		if(!$category_id || !is_numeric($category_id)) return $result;
		
		//获取该分类及子分类下所有非隐藏可用分类id
		$able_category_ids = $this->categoryAbleUseChildrenId($category_id);
		//获取所有可用非隐藏，可用分类对应商品
		$result = $this->productListWithCategoryIds($able_category_ids);
		return $result;
	}
	
	//获取该分类列表下，可用商品
	public function productListWithCategoryIds($category_list){
		$result = array();
		if(empty($category_list) || !is_array($category_list)) return $result;
		$mem_key = md5("each_buyer_category_product_search_list_".json_encode($category_list)."_type_main");
		global $mem_expired_time;
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$result = array();
			$this->database->slave->from('eb_product');
            $this->database->slave->where_in('category_id',$category_list);
			$this->database->slave->where('product_status',STATUS_ACTIVE);
			$query = $this->database->slave->get();
			$data = $query->result_array();
			//echo $this->database->slave->last_query();die;
			foreach($data as $v){
				$result[$v['product_id']] = $v;
			}
			$this->memcache->set($mem_key, $result,$mem_expired_time['category_product_type']);
		}
		return $result;
	}
	
	//获取某分类id对应全部子分类，可用，非隐藏分类id
	public function categoryAbleUseChildrenId($category_id){
		$disable_pids = $able_category_ids = array();
		if(empty($category_id) || !is_numeric($category_id)) return $able_category_ids;
        //获取所有有效分类---该分类内容在初始化菜单时已有缓存
		$all_catgory_list = $this->getBaseCateList();

        if(isset($all_catgory_list[$category_id])){
            //本分类有效
            $able_category_ids[] = $category_id;
            $tree = spreadArray($all_catgory_list,'parent_id');
            $able_category_ids = $this->getAbleCategoryId($tree,$able_category_ids);
        }
		return 	$able_category_ids;
	} 

    public function getAbleCategoryId($tree = array(),$able_cids = array(),$i=0){
        if(!is_array($tree) && empty($tree) && !is_array($able_cids) && empty($able_cids)) return array();
        $cate_list = array();
        //遍历分类树查出所有节点下的子节点
        foreach($able_cids as $cid){
            if(isset($tree[$cid])) $cate_list = array_merge($cate_list,$tree[$cid]);
        }
        //提取cid
        $cate_list = reindexArray($cate_list,'category_id');
        $cate_list = array_keys($cate_list);
        $new_able_cids = array();
        //合并结果集
        if(is_array($cate_list) || !empty($cate_list)){
            $new_able_cids = array_merge($able_cids,$cate_list);
            $new_able_cids = array_unique($new_able_cids);
        } else {
            $new_able_cids = $able_cids;
        }

        $i++;
        //条件一：如果查询结果和本次结果一样，那么下次查询也将不会有新增的子节点
        //第二个条件是为了限制递归循环的层次，控制其不至于变成死循环所致，最多循环5次
        if(count($new_able_cids) != count($able_cids) || $i < 5){
            $new_able_cids = $this->getAbleCategoryId($tree,$new_able_cids,$i);
        }
        return $new_able_cids;
    }

    public function showProductList($data){
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
	 * @desc 相同父分类下，推荐商品 
	 * @param unknown $category_array
	 * @param number $pagesize
	 */
	public function incategoryProductRecommend($category_array , $pagesize = 8){
		$result = array();
		if(!is_array($category_array) || empty($category_array)) return $result;
		$mem_key = md5("each_buyer_category_recommend_product_".implode(",", $category_array))."_".$pagesize;
		global $mem_expired_time;
		if(!$pagesize || !is_numeric($pagesize)) $pagesize = 8;
		
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_product');
			$this->database->slave->where('product_status',1);
			$this->database->slave->where_in('category_id',$category_array);
			$this->database->slave->order_by('product_price_market','desc');
			$this->database->slave->limit($pagesize);
			$query = $this->database->slave->get();
			$result = $query->result_array();
			//echo $this->database->slave->last_query();die;	
			$this->memcache->set($mem_key, $result,$mem_expired_time['product_category_recommend']);
		}
		
		return $result;
	}
	
	//获取匹配某分类path的所有分类id,有缓存
	public function getSubCatIdbyCatId( $category_id ){
		$result = array();
		if(!$category_id || !is_numeric($category_id)) return $result;
		
		$mem_key = md5("each_buyer_category_search_".$category_id);
		global $mem_expired_time;
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_category');
			$this->database->slave->where('category_status',1);
			$this->database->slave->like('category_path',$category_id);
			$this->database->slave->order_by('category_id','desc');
			$query = $this->database->slave->get();
			$result = $query->result_array();
			
			$this->memcache->set($mem_key, $result,$mem_expired_time['category_search_match']);
		}

		return $result;
	}
	
	//获取匹配某分类path的所有分类id,有缓存（不判断状态）
	public function getAllSubCatIdbyCatId( $category_id ){
		$result = array();
		if(!$category_id || !is_numeric($category_id)) return $result;
	
		$mem_key = md5("each_buyer_category_search_all_".$category_id);
		global $mem_expired_time;
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_category');
			$this->database->slave->like('category_path',$category_id);
			$this->database->slave->order_by('category_id','desc');
			$query = $this->database->slave->get();
			$result = $query->result_array();
				
			$this->memcache->set($mem_key, $result,$mem_expired_time['category_search_match']);
		}
	
		return $result;
	}
	
	public function buildTree($data,$root = 0){
		$res = array();
	
		if(isset($data[$root])){
			$res = $data[$root];
		}
		foreach($res as $key => $record){
			$res[$key]['children'] = $this->buildTree($data,$record['category_id']);
		}
	
		return $res;
	}
	
	//
	public function getCateList(){
		$mem_key = md5("each_buyer_category_base");
		global $mem_expired_time;
		//$this->memcache->delete($mem_key);
		
		$list = $this->memcache->get($mem_key);
		if($list === false){
			/*$sql = "SELECT * FROM (`eb_category`) WHERE `category_status` = 1 ORDER BY `category_sort` desc, `category_id` asc";
			 $query  = $this->database->slave->query($sql);
			$list = $query->result_array();
			*/
			$this->database->slave->select('category_id,parent_id,category_name,category_url,category_pid_count,category_path');
			$this->database->slave->from('eb_category');
			$this->database->slave->where('category_status',STATUS_ACTIVE);
			$this->database->slave->order_by('category_sort','desc');
			$this->database->slave->order_by('category_id','asc');
			$query = $this->database->slave->get();
			$list = $query->result_array();
			
			$this->memcache->set($mem_key, $list,$mem_expired_time['base_category']);
		}
		
		//数据格式处理
		foreach ($list as $val){
			$pid = $val['parent_id'];
			if($pid==0){
				$this->top_category[] = $val;
			}else{
				$cate_path = $val['category_path'];
				$path_info = explode("/", $cate_path);
				$path_level = count($path_info);
				//$this->other_level_category[$path_level][$pid][] = $val;
				$this->other_level_category[$path_level][$pid] = $val;
			}
		
		}
		//echo "<pre>";print_r($this->other_level_category);die;
		return $list;
		
	}
	
	/**
	 * @desc 获取顶级分类下的description表数据
	 * @param unknown $lanauge_id
	 */
	public function getTopCate($lanauge_id){
		$this->getCateList();
		
		global $mem_expired_time;
		if(!$lanauge_id || !is_numeric($lanauge_id)) $lanauge_id = 1;
		$mem_key = md5("each_buyer_category_desc_top_".$lanauge_id);
		
		$return_cate_top_info = $this->memcache->get($mem_key);
		if($return_cate_top_info === false){
			//获取顶级分类信息
			$return_cate_top_info = array();
			foreach ($this->top_category as $key=>$val){
				$top_cate_id_array[] = $val['category_id'];
				$return_cate_top_info[$val['category_id']] = $val;
			}
			
			//返回对应语言下顶级分类信息
			$this->database->slave->select('*');
			$this->database->slave->from('eb_category_description');
			$this->database->slave->where_in('category_id',$top_cate_id_array);
			$this->database->slave->where_in('language_id',$lanauge_id);
			$this->database->slave->group_by('category_id');
			$this->database->slave->order_by('category_description_id','asc');
			$query = $this->database->slave->get();
			$list = $query->result_array();
			
			foreach ($list as $k=>$v){
				$return_cate_top_info[$v['category_id']]['category_description_id'] = $v["category_description_id"];
				$return_cate_top_info[$v['category_id']]['language_id'] = $v["language_id"];
				$return_cate_top_info[$v['category_id']]['category_description_name'] = $v["category_description_name"];
				$return_cate_top_info[$v['category_id']]['category_description_title'] = $v["category_description_title"];
				$return_cate_top_info[$v['category_id']]['category_description_footer'] = $v["category_description_footer"];
				$return_cate_top_info[$v['category_id']]['category_description_keyword'] = $v["category_description_keyword"];
				$return_cate_top_info[$v['category_id']]['category_description_meta'] = $v["category_description_meta"];
			}
			
			$this->memcache->set($mem_key, $return_cate_top_info,$mem_expired_time['top_category']);
		}
		
		
		return $return_cate_top_info;
	}
	
	/**
	 * @desc 获取除顶级分类信息外的description表分类数据
	 */
	public function getOtherCate($lanauge_id){
		$this->getCateList();
		global $mem_expired_time;
		if(!$lanauge_id || !is_numeric($lanauge_id)) $lanauge_id = 1;
		$mem_key = md5("each_buyer_category_desc_other_".$lanauge_id);
		
		$return_cate_other_info = $this->memcache->get($mem_key);
		if($return_cate_other_info === false){
			foreach ($this->other_level_category as $key=>$val){
				//echo "<pre>";print_r($val);die;
				$other_cate_id_array[$key]= array_keys($val);
				$return_cate_other_info[$key] = $val;
			}
			
			$other_cate_infos = array();
			foreach ($other_cate_id_array as $level=>$ids){
				$this->database->slave->from('eb_category_description');
				$this->database->slave->where_in('category_id',$ids);
				$this->database->slave->where_in('language_id',$lanauge_id);
				$this->database->slave->group_by('category_id');
				$this->database->slave->order_by('category_description_id','asc');
				$query = $this->database->slave->get();
				$list = $query->result_array();
					
				$other_cate_infos[$level] = $list;
			}
			
			foreach ($other_cate_infos as $kl=>$vl){
				foreach ($vl as $kn=>$vcate){
					$return_cate_other_info[$kl][$vcate['category_id']]['category_description_id'] = $vcate["category_description_id"];
					$return_cate_other_info[$kl][$vcate['category_id']]['language_id'] = $vcate["language_id"];
					$return_cate_other_info[$kl][$vcate['category_id']]['category_description_name'] = $vcate["category_description_name"];
					$return_cate_other_info[$kl][$vcate['category_id']]['category_description_title'] = $vcate["category_description_title"];
					$return_cate_other_info[$kl][$vcate['category_id']]['category_description_footer'] = $vcate["category_description_footer"];
					$return_cate_other_info[$kl][$vcate['category_id']]['category_description_keyword'] = $vcate["category_description_keyword"];
					$return_cate_other_info[$kl][$vcate['category_id']]['category_description_meta'] = $vcate["category_description_meta"];
				}
					
			}
			
			$this->memcache->set($mem_key, $return_cate_other_info, $mem_expired_time['other_category']);
		}
		
		return $return_cate_other_info;
		
	}
	
	/**
	 * @desc 获取category_description表，分类信息
	 * @return unknown
	 */
	public function getAllCateDesc(){
		//一次性获取太多
		$cate_list = $this->getAllList();
		echo "<pre>";print_r($cate_list);die;
		//top category类
		
		$this->database->slave->select('*');
		$this->database->slave->from('eb_category_description');
		//$this->database->slave->where('language_id',$language_id);
		//$this->database->slave->where('keywords_type',$keywords_type);
		$this->database->slave->where('category_status',STATUS_ACTIVE);
		$this->database->slave->order_by('category_sort','desc');
		$this->database->slave->order_by('category_id','asc');
		$query = $this->database->slave->get();
		$list = $query->result_array();
	
		//****需要做缓存处理*****//
		
		return $list;
	}

	public function getCategoryTemplateByCategory($category_id,$language_id){
		if(!is_array($category_id)) $category_id = array($category_id);
		if(empty($category_id)) return array();
		
		$result = array();
		$mem_key = md5("get_category_template_by_category".implode(',', $category_id)).'_'.$language_id;
		global $mem_expired_time;
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->from('eb_category_template');
			$this->database->slave->where_in('category_id',$category_id);
			$this->database->slave->where('language_id',$language_id);
			$this->database->slave->where('category_template_status',STATUS_ACTIVE);
			$query = $this->database->slave->get();
			$result = $query->result_array();
			$this->memcache->set($mem_key, $result,$mem_expired_time['category_template']);
		}
		
		return $result;
	}
}
