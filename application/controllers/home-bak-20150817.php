<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Home extends Dcontroller {
	
	public function index(){
		//获取语言id
		$lauageid = currentLanguageId();

		//焦点图 
		//$this->load->model("imageadmodel","imagead");
		$location_ad_array = array(1,2,3);
		//从库里获取
		$image_ads = $this->imagead->getLocationWithIds($location_ad_array);

		//对焦点图，时间进行判断
		$image_lists = $this->handleLocation($image_ads);
		$this->_view_data['image_ad'] = $image_lists;
		
		//从库里获取widget商品列表
		$this->load->model("widgetproductmodel","product");
		$widget_product_list = $this->product->getIndexDeal($lauageid);
		//获取widget商品列表详情数据
		$product_list = $this->widgetToProductList($widget_product_list);
		//获取widget商品促销活动价格信息（折扣处理）
		//$product_price_list = $this->allProductWithPrice($product_list);
		$this->_view_data['product_list'] = $product_list;
		
		//店铺
		$this->load->model("widgetimagemodel","stores");
		$stores_list = $this->stores->imageList($lauageid);
		$this->_view_data['widget_images'] = $stores_list;
		
		//@GA统计
		$this->ecomm_pagetype = 'home';
		//特殊处理格式
		$all_product_list = $this->formatProductList($product_list);
		//GA统计-展示所有商品（存在商品的地方）
		$this->dataLayerPushImpressions($all_product_list,'Home Page');
		//seo
		$this->_view_data['title'] = lang('title');
		$this->_view_data['seo_keywords'] = lang('keywords');
		$this->_view_data['description'] = lang('description');
		
		parent::index();
	}
	
	//合并且把所有商品转换成一维数组格式
	private function formatProductList($all_data){
		if(empty($all_data)) return array();
		$result = array();
		foreach ($all_data as $key=>&$val){
			if(isset($val['children'])){
				//unset($val['children']['slogan'],$val['children']['product_description_content']);
				$result = array_merge($result,$val['children']);
				unset($val['children']);
			}
			//unset($val['slogan'],$val['product_description_content']);
			$result = array_merge($result,array($val));
		}
		return $result;
	}
	
	//新版商品折扣处理方式（多条折扣查询合并为一条）
	private function allProductWithPrice($data){
		if(!count($data) && !is_array($data)) return false;
		$this->load->model("discountmodel","discount");
		$this->load->model("discountrangemodel","discountrange");
		//$currency_info = $this->getCurrencyNumber();
		
		//所有商品折扣范围信息
		$promote_range_infos = $this->getAllProductRangeInfos($data);
		//处理每个商品折扣情况
		foreach ($data as $p_k=>&$p_v){
			$product_id = $p_v['product_id'];
			$front_price = $p_v['product_price'];
			$market_price = $p_v['product_price_market'];
			
			//判断该商品是否存在折扣范围信息,获取该商品所有折扣id信息
			$product_all_discount_ids = array();
			foreach($promote_range_infos as $promote_k=>$promote_v){
				if($promote_v['promote_range_content']==$product_id) array_push($product_all_discount_ids, $promote_v['promote_discount_id']);
			}
			if(!empty($product_all_discount_ids)){
				//根据所有折扣id，获取最大折扣数
				$discount_num = $this->singleProductBatchDiscount($product_all_discount_ids);
				$discount_price = $front_price;
				if($discount_num!=0){
					$real_discount = (100-$discount_num)/100;
					$discount_price = $market_price*$real_discount;
					$discount_price = round($discount_price,2);
				}
				
				$p_v['product_basediscount_price'] = $p_v['product_discount_price'] = $discount_price;
				$p_v['product_discount_number'] = $discount_num;
			}else{
				$front_price = $p_v['product_price'];
				$market_price = $p_v['product_price_market'];
				$p_v['product_basediscount_price'] = $p_v['product_discount_price'] = $front_price;
				$p_v['product_discount_number'] = 0;
			}
			
			/*$p_v['product_currency'] = "$";
			//汇率转换
			$currency_format = $this->getCurrencyNumber();
			if($currency_format){
				$p_v['product_currency'] = $currency_format['currency_format'];
				$p_v['product_discount_price'] = round($p_v['product_discount_price']*$currency_format['currency_rate'],2);
				$p_v['product_price_market'] = round($p_v['product_price_market']*$currency_format['currency_rate'],2);
			}*/
			
			//次要商品价格处理
			if(isset($p_v['children']) && count($p_v['children'])){
				foreach ($p_v['children'] as $key=>&$val){
					$child_front_price = $val['product_price'];
					$child_market_price = $val['product_price_market'];
					
					$children_all_discount_ids = array();
					//该商品所有折扣
					foreach ($promote_range_infos as $c_p_k=>&$c_p_v){
						if($c_p_v['promote_range_content']==$val['product_id']){//有折扣
							array_push($children_all_discount_ids, $c_p_v['promote_discount_id']);
						}
					}
					if(!empty($children_all_discount_ids)){
						//根据所有折扣id，获取最大折扣数
						$child_discount_num = $this->singleProductBatchDiscount($children_all_discount_ids);
						$child_discount_price = $child_front_price;
						if($child_discount_num!=0){
							$child_real_discount = (100-$child_discount_num)/100;
							$child_discount_price = $child_market_price*$child_real_discount;
							$child_discount_price = round($child_discount_price,2);
						}
					
						$val['product_basediscount_price'] = $val['product_discount_price'] = $child_discount_price;
						$val['product_discount_number'] = $child_discount_num;
					}else{
						$val['product_basediscount_price'] = $val['product_discount_price'] = $child_front_price;
						$val['product_discount_number'] = 0;
					}
					/*$val['product_currency'] = '$';
					//汇率转换
					if($currency_format){
						//echo $val['product_discount_price'].'==='.$currency_format['currency_rate'].'==========';
						$val['product_currency'] = $currency_format['currency_format'];
						$val['product_discount_price'] = round($val['product_discount_price']*$currency_format['currency_rate'],2);
						//echo $val['product_discount_price'];die;
						$val['product_price_market'] = round($val['product_price_market']*$currency_format['currency_rate'],2);
					}*/
					
				}
			}
			
		}
		return $data;
	}
	
	
	/**
	 * @desc 批量获取商品列表的折扣范围信息
	 * @param unknown $data
	 * @return unknown
	 */
	private function getAllProductRangeInfos($data){
		$all_product_ids = array();
		//获取所有商品id
		foreach ($data as $k=>&$v){
			if(isset($v['product_id']) && $v['product_id'] && is_numeric($v['product_id'])){
				array_push($all_product_ids, $v['product_id']);
			}
			if(isset($v['children']) && count($v['children'])){
				foreach ($v['children'] as $c_k=>$c_v){
					array_push($all_product_ids, $c_v['product_id']);
				}
			}
		}
		
		//所有商品折扣范围信息
		$promote_range_infos = $this->discountrange->getRangeExistsWithArray($all_product_ids);
		return $promote_range_infos;
		
	}
	
	/**
	 * @desc widget商品详情信息
	 * @param unknown $data
	 * @return boolean|Ambigous <multitype:multitype: Ambigous <NULL, unknown> , unknown>
	 */
	private function widgetToProductList($data){
		if(!count($data) && !is_array($data)) return false;
	
		//商品详情信息及价格信息
		$detail_body = array();
		$lan_id = currentLanguageId();
        $lan_code = currentLanguageCode();
		foreach ($data as $k=>$v){
			$product_title_array = json_decode($v['widget_product_title'],true);
			$product_main_array = json_decode($v['widget_product_mainproduct_title'],true);
	
			$key = $product_title_array[$lan_id];
			$main_key = $product_main_array[$lan_id];
			$content_product_ids = explode(",", $v['widget_product_content']);
	
			//主商品及次要商品
			$this->load->model("goodsmodel","goods");
			//获取所有的商品id
			$all_product_ids = array_merge(array($v['widget_product_mainproduct_id']),$content_product_ids);

			//根据商品id获取商品详情
			//$infos = $this->goods->getinfoWithArray($all_product_ids,$status=1,currentLanguageCode());
            $infos = $this->goods->getProductList($all_product_ids,1,0,$lan_code);
            $infos = $this->productWithPrice($infos);
            foreach($all_product_ids as $sort=>$value){
                if(isset($infos[$value]))
                    $infos[$value]['sort'] = $sort;
            }
          
			$detail_body[$key] = array();
			$main_id = $v['widget_product_mainproduct_id'];
			$detail_body[$key] = isset($infos[$main_id])?$infos[$main_id]:null;
			/*if(!isset($infos[$main_id])){
				echo $main_id.'====';
				echo "<pre>vvvvvvvvvvvvvv";print_r($infos);
				echo $main_id;die;
			}*/
			unset($infos[$v['widget_product_mainproduct_id']]);
			$detail_body[$key]['product_id'] = $main_id;
			$detail_body[$key]['title'] = $main_key;
            $infos = array_sort($infos,'sort');
			$detail_body[$key]['children'] = $infos;
		}

		//echo '<pre>';print_r($detail_body);exit;
		return $detail_body;
	}
	
	/**
	 * @desc 处理焦点图（是否过期）
	 * @param unknown $image_ads
	 * @return Ambigous <multitype:, mixed>
	 */
	public function handleLocation ($image_ads){
		$image_lists = array();
		$lauageid = currentLanguageId();
		//echo "<pre>image";print_r($image_ads);die;
		if(isset($image_ads[1])){//是否存在焦点轮播图
			foreach($image_ads[1] as $kone=>$vone){
				$one_start_time = strtotime($vone['ad_time_start']);
				$one_end_time = strtotime($vone['ad_time_end']);
		
				if($one_start_time<=time() && $one_end_time>=time()){
					$content = json_decode($vone['ad_content'],true);
					$vone['lan_content'] = $content[$lauageid];
					$image_lists[1][]= $vone;
				}
			}
		}
		if(isset($image_ads[2])){//是否存在焦点图-右上
			foreach($image_ads[2] as $ktwo=>$vtwo){
				$two_start_time = strtotime($vtwo['ad_time_start']);
				$two_end_time = strtotime($vtwo['ad_time_end']);
					
				if($two_start_time<=time() && $two_end_time>=time()){
					$two_content = json_decode($vtwo['ad_content'],true);
					$vtwo['lan_content'] = $two_content[$lauageid];
					$image_lists[2][]= $vtwo;
				}
			}
		}
		if (isset($image_ads[3])){//是否存在焦点图-右下
			foreach($image_ads[3] as $kt=>$vt){
				$three_start_time = strtotime($vt['ad_time_start']);
				$three_end_time = strtotime($vt['ad_time_end']);
					
				if($three_start_time<=time() && $three_end_time>=time()){
					$tcontent = json_decode($vt['ad_content'],true);
					$vt['lan_content'] = $tcontent[$lauageid];
					$image_lists[3][]= $vt;
				}
			}
		}
		
		return $image_lists;
	}
	
}
/* End of file home.php */
/* Location: ./application/controllers/home.php */
