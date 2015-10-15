<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';

class Found extends Dcontroller {

    function index(){
        //获取language_code
        $language_code = currentLanguageCode();
        $language_id = currentLanguageId();
        $this->load->model("goodsmodel",'good');
        $product_list = $this->good->recommendProductList($category_array = array(),$product_ids = array(),$pagesize=18);
        $time = date('Y-m-d H:i:s',requestTime());
        //汇率转换
        $currency_format = $this->getCurrencyNumber();
        foreach($product_list as $key=>&$sales_v){
            //多语言
            $description_info = $this->good->productDescriptionInfo($sales_v['product_id'],$language_code);
            if(!empty($description_info)){
                $sales_v['product_description_name'] = isset($description_info['product_description_name'])?$description_info['product_description_name']:'';
                $sales_v['product_description_content'] = isset($description_info['product_description_content'])?$description_info['product_description_content']:'';
            }
            $product_id = $sales_v['product_id'];

            $front_price = $sales_v['product_price'];
            $market_price = $sales_v['product_price_market'];
            $discount_infos = $this->singleProductDiscount($product_id,$market_price);
            $sales_v['product_basediscount_price'] = $sales_v['product_discount_price'] = isset($discount_infos["discount_price"])?$discount_infos["discount_price"]:$front_price;
            $sales_v['product_discount_number'] = isset($discount_infos["discount_number"])?$discount_infos["discount_number"]:0;
            $sales_v['product_currency'] = "$";
            if($currency_format){
                $sales_v['product_currency'] = $currency_format['currency_format'];
                $sales_v['product_discount_price'] = round($sales_v['product_discount_price']*$currency_format['currency_rate'],2, PHP_ROUND_HALF_DOWN);
                $sales_v['product_price_market'] = round($sales_v['product_price_market']*$currency_format['currency_rate'],2, PHP_ROUND_HALF_DOWN);
            }
            if(isset($sales_v['slogan'][0]['slogan_time_start']) && $time > $sales_v['slogan'][0]['slogan_time_start'] && $time < $sales_v['slogan'][0]['slogan_time_end']){
                $text = json_decode($sales_v['slogan'][0]['slogan_content'],true);
                $sales_v['slogan'] = $text[$language_id];
            } else {
                unset($sales_v['slogan']);
            }
        }
        $this->_view_data['product_list'] = $product_list;
        $this->_view_data['name'] = 'found';
        parent::index();
    }

    /**
     * @desc 获取币种及汇率值；默认为“$” 时返回false
     * @return multitype:string unknown |boolean
     */
    public function getCurrencyNumber($flush_cache = false){
        $this->load->model("currencymodel","currencymodel");
        $info = $this->currencymodel->todayCurrency($flush_cache);

        $result = $this->floatcmp($info['currency_rate'], 1);
        if(!$result){
            $format_string = trim(str_replace("%s", "", $info['currency_format']));

            return array("currency_rate"=>$info['currency_rate'],"currency_format"=>$format_string);
        }
        return false;
    }
} 
