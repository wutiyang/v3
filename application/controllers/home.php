<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Home extends Dcontroller {
	
	public function index(){
		//echo "<pre>index";print_r($this->load);die;
		/*$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
		
		if ( ! $foo = $this->cache->get('foo'))
		{
			echo 'Saving to the cache!<br />';
			$foo = 'foobarbaz!';
		
			// Save into the cache for 5 minutes
			$this->cache->save('foo', $foo, 300);
		}
		
		echo $foo;die;*/
		
		//获取语言id
		$lauageid = currentLanguageId();

		//焦点图 
		//$this->load->model("imageadmodel","imagead");
		$location_ad_array = array(1,2,3);
		//从库里获取
		$image_ads = $this->imagead->getLocationWithIds($location_ad_array);
		//$image_ads = array();
		
		//对焦点图，时间进行判断
		$image_lists = $this->handleLocation($image_ads);
		$this->_view_data['image_ad'] = $image_lists;
		
		//从库里获取widget商品列表
		$this->load->model("widgetproductmodel","product");
		$widget_product_list = $this->product->getIndexDeal($lauageid);
		//获取widget商品列表详情数据
		$product_list = $this->widgetToProductList($widget_product_list);
		//获取widget商品促销活动价格信息（折扣处理）
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
//            echo '<pre>';print_r($infos);exit;
			$detail_body[$k] = array();
			$main_id = $v['widget_product_mainproduct_id'];
			$detail_body[$k] = isset($infos[$main_id])?$infos[$main_id]:null;
			unset($infos[$v['widget_product_mainproduct_id']]);
            $detail_body[$k]['title'] = $main_key;
            $detail_body[$k]['key'] = $key;
            $infos = array_sort($infos,'sort');
			$detail_body[$k]['children'] = $infos;
		}

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
