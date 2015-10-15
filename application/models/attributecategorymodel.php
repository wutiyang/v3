<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc category属性模型
 * @author Administrator
 *
 */
class attributecategorymodel extends CI_Model {
    //通过category_id获取该分类attribute属性
    public function attributeWithCateId($category_id){
        $result = array();
        if(!$category_id || !is_numeric($category_id)) return $result;

        $mem_key = md5("each_buyer_category_attribute_category_".$category_id);
        global $mem_expired_time;
        $list = $this->memcache->get($mem_key);
        if($list === false){
            $this->database->slave->from('eb_attribute_category');
            $this->database->slave->where('attribute_category_status',1);
            $this->database->slave->where('category_id',$category_id);
            $this->database->slave->order_by('attribute_category_sort','desc');
            $this->database->slave->order_by('attribute_category_id','asc');
            $query = $this->database->slave->get();
            //echo $this->database->slave->last_query();die;

            $list = $query->result_array();

            $this->memcache->set($mem_key, $list,$mem_expired_time['attribute_category']);
        }

        return $list;
    }

    //通过block_id获取block信息
    public function attrBlock($block_id,$language_id = 1){
        $result = array();
        if(!$block_id || !is_numeric($block_id)) return $result;
        if(!$language_id || !is_numeric($language_id)) $language_id = 1;

        $mem_key = md5("each_buyer_attribute_block_".$block_id.'_language_id_'.$language_id);
        global $mem_expired_time;
        $result = $this->memcache->get($mem_key);
        if($result === false){
            $sql = "select * from eb_attribute_block block left join eb_attribute_block_lang lang ";
            $sql .="on block.attribute_block_id=lang.attribute_block_id where block.attribute_block_id=".$block_id;
            $sql .= " and block.attribute_block_status=1 and lang.attribute_block_lang_status=1 and lang.language_id=".$language_id;
            $query = $this->database->slave->query($sql);

            //echo $this->database->slave->last_query();die;
            $result = $query->result_array();

            $this->memcache->set($mem_key, $result,$mem_expired_time['product_attr_with_block']);
        }

        return $result;
    }
    //通过多个block_id获取block信息
    public function attrBlocks($block_ids,$language_id = 1){
        $result = array();
        if(!is_array($block_ids)) return $result;
        if(!$language_id || !is_numeric($language_id)) $language_id = 1;

        $block_id_str = implode(',',$block_ids);
        $mem_key = md5("each_buyer_attribute_block_".$block_id_str.'_language_id_'.$language_id);
        global $mem_expired_time;
        $result = $this->memcache->get($mem_key);
        if($result === false){
            $sql = "select * from eb_attribute_block block left join eb_attribute_block_lang lang ";
            $sql .="on block.attribute_block_id=lang.attribute_block_id where block.attribute_block_id in (".$block_id_str;
            $sql .= ") and block.attribute_block_status=1 and lang.attribute_block_lang_status=1 and lang.language_id=".$language_id;
            $query = $this->database->slave->query($sql);

            //echo $this->database->slave->last_query();die;
            $list = $query->result_array();
            foreach ($list as $v) {
                $result[$v['attribute_block_id']] = $v;
            }

            $this->memcache->set($mem_key, $result,$mem_expired_time['product_attr_with_block']);
        }

        return $result;
    }

    //根据属性组id值获取该属性组信息
    public function getAttributeValueWithValueidCache($attribute_value_id,$language_id = 1){
        $result = array();
        if(!$attribute_value_id || !is_numeric($attribute_value_id)) return $result;
        if(!$language_id || !is_numeric($language_id)) $language_id = 1;

        global $mem_expired_time;
        $mem_key = md5("each_buyer_attribute_value_cache_".$attribute_value_id.'_language_id_'.$language_id);

        $result = $this->memcache->get($mem_key);
        if($result === false){
            $sql = "select * from `eb_attribute_value` as attr left join `eb_attribute_value_lang` as lang on attr.attribute_value_id=lang.attribute_value_id where attr.attribute_value_id=";
            $sql .= intval($attribute_value_id)." and lang.attribute_value_lang_status=1 and attr.attribute_value_status=1 and lang.language_id=".intval($language_id);
            $query = $this->database->slave->query($sql);
            $result = $query->result_array();
            //echo "attri-lang<pre>";echo $this->database->slave->last_query();die;

            $this->memcache->set($mem_key, $result,$mem_expired_time['product_attr_value_id']);
        }

        return $result;
    }

    //根据属性组id值获取该属性组信息
    public function getAttributeValueWithValueidsCache($attribute_value_ids,$language_id = 1){
        $result = array();
        if(!is_array($attribute_value_ids)) return $result;
        if(!$language_id || !is_numeric($language_id)) $language_id = 1;

        global $mem_expired_time;
        $attr_value_str = implode(',',$attribute_value_ids);
        $mem_key = md5("each_buyer_attribute_value_cache_".$attr_value_str.'_'.$language_id);

        $result = $this->memcache->get($mem_key);
        if($result === false){
            $sql = "select * from `eb_attribute_value` as attr left join `eb_attribute_value_lang` as lang on attr.attribute_value_id=lang.attribute_value_id where attr.attribute_value_id in (";
            $sql .= $attr_value_str.") and lang.attribute_value_lang_status=1 and attr.attribute_value_status=1 and lang.language_id=".intval($language_id);
            $query = $this->database->slave->query($sql);
            $result = $query->result_array();

            $this->memcache->set($mem_key, $result,$mem_expired_time['product_attr_value_id']);
        }

        return $result;
    }
    //根据属性id及语言id，获取该属性信息及该语言下属性信息(有缓存)
    public function getAttributeAndLangCache($attribute_id,$language_id=1){
        $result = array();
        if(!$attribute_id || !is_numeric($attribute_id)) return $result;
        if(!$language_id || !is_numeric($language_id)) $language_id = 1;

        global $mem_expired_time;
        $mem_key = md5("each_buyer_attribute_cache_".$attribute_id.'_language_id_'.$language_id);
        $result = $this->memcache->get($mem_key);
        if($result === false){
            $sql = "select * from `eb_attribute` as attr left join `eb_attribute_lang` as lang on attr.attribute_id=lang.attribute_id where attr.attribute_id=".intval($attribute_id)." and lang.attribute_lang_status=1 and attr.attribute_status=1 and lang.language_id=".intval($language_id);
            $query = $this->database->slave->query($sql);
            $result = $query->result_array();

            $this->memcache->set($mem_key, $result,$mem_expired_time['product_attr_id']);
        }

        return $result;
    }
    //根据属性id及语言id，获取该属性信息及该语言下属性信息(有缓存)
    public function getAttributesLangCache($attribute_ids,$language_id=1){
        $result = array();
        if(!is_array($attribute_ids)) return $result;
        if(!$language_id || !is_numeric($language_id)) $language_id = 1;

        global $mem_expired_time;
        $attribute_id_str = implode(',',$attribute_ids);
        $mem_key = md5("each_buyer_attribute_cache_".$attribute_id_str.'_'.$language_id);
        $result = $this->memcache->get($mem_key);
        if($result === false){
//            $sql = "select * from `eb_attribute` as attr left join `eb_attribute_lang` as lang on attr.attribute_id=lang.attribute_id where attr.attribute_id in (".$attribute_id_str.") and lang.attribute_lang_status=1 and attr.attribute_status=1 and lang.language_id=".intval($language_id);
//            $query = $this->database->slave->query($sql);
//            $result = $query->result_array();
            $this->database->slave->from('eb_attribute as attr');
            $this->database->slave->join('eb_attribute_lang as lang','attr.attribute_id=lang.attribute_id','left');
            $this->database->slave->where_in('attr.attribute_id',$attribute_ids);
            $this->database->slave->where('lang.attribute_lang_status',1);
            $this->database->slave->where('attr.attribute_status',1);
            $this->database->slave->where('lang.language_id',$language_id);
            $this->database->slave->order_by('attr.attribute_sort','desc');
            $this->database->slave->order_by('attr.attribute_id','asc');
            $query = $this->database->slave->get();
            $result = $query->result_array();

            $this->memcache->set($mem_key, $result,$mem_expired_time['product_attr_id']);
        }

        return $result;
    }

    //根据属性id及语言id，获取该属性信息及该语言下属性信息（无缓存）
    public function getAttributeAndLang($attribute_ids,$language_id=1){
        $result = array();
        if(is_array($attribute_ids)) $attribute_ids = implode(',',$attribute_ids);
        if($attribute_ids == '') return false;

        global $mem_expired_time;
        $mem_key = md5("each_buyer_attribute_cache_lang_".$attribute_ids.'_'.$language_id);
        $result = $this->memcache->get($mem_key);
        if($result === false){
        	$sql = "select * from `eb_attribute` as attr left join `eb_attribute_lang` as lang on attr.attribute_id=lang.attribute_id where attr.attribute_id in (".$attribute_ids.") and lang.attribute_lang_status=1 and attr.attribute_status=1 and lang.language_id=".intval($language_id);
        	$query = $this->database->slave->query($sql);
        	$list = $query->result_array();
        	
        	if(!empty($list)){
        		foreach($list as $value){
        			$result[$value['attribute_id']] = $value;
        		}
        	}
        	
        	$this->memcache->set($mem_key, $result,$mem_expired_time['product_attr_id']);
        }

        return $result;
    }

    //根据attribute_category_id获取属性值
    public function groupWithAttributeCategoryId($attribute_category_id = array(),$language_id = 1){
        $result = $data = array();
        if(is_array($attribute_category_id) && !empty($attribute_category_id)) $attribute_category_id = implode(',',$attribute_category_id);
        if($attribute_category_id == '' || empty($attribute_category_id)) return array();
        
        global $mem_expired_time;
        $mem_key = md5("each_buyer_attribute_cache_group_category_id".$attribute_category_id.'_'.$language_id);
        $data = $this->memcache->get($mem_key);
        if($data === false){
        	$sql = "SELECT * FROM `eb_attribute_value_group` groups left join `eb_attribute_value` attr on  groups.attribute_value_group_content=attr.attribute_value_id ";
        	$sql .="where groups.attribute_category_id in (".$attribute_category_id.") and groups.attribute_value_group_status=1 and attr.attribute_value_status=1";
        	$sql .=' order by attribute_value_group_sort desc,attribute_value_group_id asc;';
        	$query = $this->database->slave->query($sql);
        	$data = $query->result_array();
        	//echo "attri-lang<pre>";echo $this->database->slave->last_query();die;
        	
        	$this->memcache->set($mem_key, $data,$mem_expired_time['product_attr_id']);
        }	
        $attr_list = array();
        $i = 1;
        foreach ($data as $a_v){
        	$a_v['sort'] = $i++;
        	$attr_list[$a_v['attribute_id']][$a_v['attribute_value_group_id']] = $a_v;
        }
        foreach($attr_list as $key=>$list){
        	if(!empty($list)){
        		$value_array = array();
        		//语言处理
        		foreach ($list as $k=>&$v){
        			$json_array_name = json_decode($v['attribute_value_group_lang'],true);
        			$v['new_group_lang'] = isset($json_array_name[$language_id])?$json_array_name[$language_id]:$json_array_name[1];
        	
       				//商品个数
       				$attribute_product_list = array();
       				//************echo "attributecategorymodel-有多个属性值,请注意检查";die;
       				$i_array = explode(",", $v['attribute_value_group_content']);
       				$attr_list[$v['attribute_value_group_id']] = $i_array;
       				$value_array = array_merge($value_array,$i_array);
       	
       				$v['product_list'] = $attribute_product_list;
       			}
       			$attribute_product_list = $this->attributeWithProductNum($value_array);
       			foreach($attribute_product_list as $product){
       				foreach($attr_list as $attr_group_id => $attr_value_id){
       					if(in_array($product['attribute_value_id'],$attr_value_id))
       						$list[$attr_group_id]['product_list'][$product['product_id']] = $product['product_id'];
       				}
       			}
       			$list = array_sort($list,'sort');
       			$result[$key] = $list;
       		}
        }
        	
//        echo "<pre>ccc";print_r($result);die;
        return $result;
    }

    //属性值在attribute_productb表中商品个数，且判断该商品是否上架，是否在该分类下
    public function attributeWithProductNum($attribute_value_ids){
        $result = array();
        if(!$attribute_value_ids || !is_array($attribute_value_ids))$attribute_value_ids = explode(',',$attribute_value_ids);
        if(!count($attribute_value_ids)) return false;
        //attribute_product表
        global $mem_expired_time;
        $mem_key = md5("each_buyer_attribute_product_attrid_".implode('_',$attribute_value_ids));
        $result = $this->memcache->get($mem_key);
        if($result === false){
            $this->database->slave->from('eb_attribute_product');
            $this->database->slave->where('attribute_product_status',1);
            $this->database->slave->where_in('attribute_value_id',$attribute_value_ids);
            $query = $this->database->slave->get();
            //echo $this->database->slave->last_query();die;

            $result = array();
            $attribute_product_list = $query->result_array();
            if(!empty($attribute_product_list)){
                foreach ($attribute_product_list as $k=>$v){
                    $result[$v['product_id']] = $v;
                }
                $this->memcache->set($mem_key, $result,$mem_expired_time['attribute_product']);
            }
        }
        return $result;
    }

    //获取某分类价格区间信息
    public function categoryPriceAttribute($category_id){
        $result = array();
        if(!$category_id || !is_numeric($category_id)) return $result;

        global $mem_expired_time;
        $mem_key = md5("each_buyer_attribute_product_price_".$category_id);
        
        $result = $this->memcache->get($mem_key);
        if($result===false){
        	$this->database->slave->from('eb_category_narrow_price');
        	$this->database->slave->where('category_narrow_price_status',1);
        	$this->database->slave->where('category_id',$category_id);
        	$this->database->slave->order_by("category_narrow_price_sort","desc");
        	$this->database->slave->order_by("category_narrow_price_id","asc");
        	$query = $this->database->slave->get();
        	//echo $this->database->slave->last_query();die;
        	
        	$result = $query->result_array();
        	
        	$this->memcache->set($mem_key, $result,$mem_expired_time['attribute_product']);
        }
        
        return $result;
    }

}
?>
