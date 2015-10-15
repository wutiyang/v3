<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc brandcategory品牌model
 * @author Administrator
 *
 */
class brandmodel extends CI_Model {
	
	public function __construct(){
		parent::__construct();
	}

    /**
     * 根据单个品牌分类id来获取品牌列表信息的接口
     * @param $brand_category_id 单个品牌分类id
     * @return array
     */
    public function getBrandListByBcid($brand_category_id){
        $result = array();
        if(!is_numeric($brand_category_id)) return $result;
        $mem_key = md5("each_buyer_brand_by_bcid_".$brand_category_id);
        global $mem_expired_time;

        $result = $this->memcache->get($mem_key);
        if($result === false){
            $slave = $this->database->slave;
            $slave->from('eb_brand');
            $slave->where('brand_status',STATUS_ACTIVE);
            $slave->where('brand_category_id',$brand_category_id);
            $slave->order_by('brand_sort','desc');
            $slave->order_by('brand_id','desc');
            $result = $slave->get()->result_array();

            $this->memcache->set($mem_key, $result,$mem_expired_time['brand_category']);
        }
        return $result;
    }

    /**
     * 根据多个品牌分类id来获取品牌列表信息的接口
     * @param $brand_category_ids 多个品牌分类id的数组
     * @return array
     */
    public function getBrandListByBcids($brand_category_ids = array()){
        $result = array();
        if(!is_array($brand_category_ids)) return $result;
        $mem_key = md5("each_buyer_brand_by_bcid_".json_encode($brand_category_ids));
        global $mem_expired_time;

        $result = $this->memcache->get($mem_key);
        if($result === false){
            $slave = $this->database->slave;
            $slave->from('eb_brand');
            $slave->where('brand_status',STATUS_ACTIVE);
            if(!empty($brand_category_ids))
                $slave->where_in('brand_category_id',$brand_category_ids);
            $slave->order_by('brand_sort','desc');
            $slave->order_by('brand_id','desc');
            $result = $slave->get()->result_array();

            $this->memcache->set($mem_key, $result,$mem_expired_time['brand_category']);
        }
        return $result;
    }

    /**
     * 根据品牌ID获取品牌信息
     * @param $brand_id 品牌id
     * @param int $language_id 语言id
     * @return array
     */
    public function getBrandInfoByBid($brand_id,$language_id = 1){
        $result = array();
        if(!is_numeric($brand_id) && !$brand_id) return $result;
        $mem_key = md5("each_buyer_brand_by_bid_".$brand_id);
        global $mem_expired_time;

        $result = $this->memcache->get($mem_key);
        if($result === false){
            $slave = $this->database->slave;
            $slave->from('eb_brand');
            $slave->where('brand_status',STATUS_ACTIVE);
            $slave->where_in('brand_id',$brand_id);
            $list = $slave->get()->result_array();
            if(!empty($list))
                $result = $list[0];
            $this->memcache->set($mem_key, $result,$mem_expired_time['brand_category']);
        }
        if(isset($result['brand_description'])){
            $result['brand_description'] = json_decode($result['brand_description'],true);
            if(isset($result['brand_description'][$language_id])){
                $result['brand_description'] = $result['brand_description'][$language_id];
            } else {
                $result['brand_description'] = $result['brand_description'][1];
            }
        }
        return $result;
    }

    /**
     * 获取剔除商品id列表
     * @param array $brand_ids 品牌id的数组
     * @return array
     */
    public function getExcludeProductInfo($brand_ids = array()){
        $result = array();
        $mem_key = md5("each_buyer_brand_exclude_product");
        global $mem_expired_time;

        $data = $this->memcache->get($mem_key);
        if($data === false){
            $slave = $this->database->slave;
            $slave->from('eb_brand_exclude');
            $slave->where('brand_exclude_status',STATUS_ACTIVE);
            $list = $slave->get()->result_array();
            $data = array();
            foreach($list as $item){
                $data[$item['brand_id']] = $item;
            }
            $this->memcache->set($mem_key, $data,$mem_expired_time['brand_category']);
        }
        if(is_array($brand_ids) && !empty($brand_ids)){
            foreach($brand_ids as $brand_id)
                if(isset($data[$brand_id]))
                    array_push($result,$data[$brand_id]);
        }
        return $result;
    }
}
