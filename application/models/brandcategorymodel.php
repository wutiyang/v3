<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc brandcategory品牌分类model
 * @author Administrator
 *
 */
class brandcategorymodel extends CI_Model {
	
	public function __construct(){
		parent::__construct();
	}

    /**
     * 获取所有品牌分类的信息列表
     * @return array
     */
    public function getBrandCategoryInfo(){
        $result = array();
        $mem_key = md5("each_buyer_brand_category");
        global $mem_expired_time;

        $result = $this->memcache->get($mem_key);
        if($result === false){
            $slave = $this->database->slave;
            $slave->from('eb_brand_category');
            $slave->where('brand_category_status',STATUS_ACTIVE);
            $slave->order_by('brand_category_sort','desc');
            $slave->order_by('brand_category_id','asc');
            $result = $slave->get()->result_array();

            $this->memcache->set($mem_key, $result,$mem_expired_time['brand_category']);
        }
        return $result;
    }

    /**
     * 获取单一品牌分类的信息
     * @param $bcid 品牌分类id
     * @param int $language_id 语言id
     * @return array
     */
    public function getBrandCategoryByBcid($bcid,$language_id = 1){
        $result = array();
        if(!is_numeric($bcid) && $bcid) return $result;
        $mem_key = md5("each_buyer_brand_category_by_bcid_".$bcid);
        global $mem_expired_time;

        $result = $this->memcache->get($mem_key);
        if($result === false){
            $slave = $this->database->slave;
            $slave->from('eb_brand_category');
            $slave->where('brand_category_status',STATUS_ACTIVE);
            $slave->where('brand_category_id',$bcid);
            $slave->order_by('brand_category_sort','desc');
            $slave->order_by('brand_category_id','asc');
            $list = $slave->get()->result_array();
            if(isset($list[0])){
                $result = $list[0];
                $this->memcache->set($mem_key, $result,$mem_expired_time['brand_category']);
            }
        }
        $result['brand_category_title'] = json_decode($result['brand_category_title'],true);
        if(isset($result['brand_category_title'][$language_id])){
            $result['brand_category_title'] = $result['brand_category_title'][$language_id];
        } else {
            $result['brand_category_title'] = $result['brand_category_title'][1];
        }
        return $result;
    }
}
