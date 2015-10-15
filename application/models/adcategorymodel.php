<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/17
 * Time: 18:36
 */

class Adcategorymodel extends CI_Model {

    public function __construct(){
        parent::__construct();
    }

    public function getBanner($category_array){
        $result = array();
        if(!$category_array || !is_array($category_array)) return $result;

        $mem_key = md5("each_buyer_ad_category_cid_".implode('_',$category_array));
        global $mem_expired_time;

        $list = $this->memcache->get($mem_key);
        if($list ===false) {
            $endTime = date('Y-m-d H:i:s', requestTime()-60*60*24);
            $this->database->slave->from('eb_ad_category');
            $this->database->slave->where_in('category_id', $category_array);
            $this->database->slave->where("ad_category_time_end >",$endTime);
            $this->database->slave->where("ad_category_status",1);
            $this->database->slave->order_by('ad_category_time_start','asc');
            $query = $this->database->slave->get();
            //echo $this->database->slave->last_query();die;

            $list = $query->result_array();

            $this->memcache->set($mem_key, $list,$mem_expired_time['ad_category']);
        }

        $nowTime = date('Y-m-d H:i:s', requestTime());
        foreach($list as $item){
            if($nowTime > $item['ad_category_time_start'] && $nowTime < $item['ad_category_time_end']){
                //有效的slimbanner
                $data[$item['category_id']] = $item;
            }
        }
        foreach($category_array as $category_id){
            if(isset($data[$category_id])) {
                $result = $data[$category_id];
                break;
            }
        }
        return $result;
    }
}
