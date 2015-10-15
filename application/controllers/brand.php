<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';
/**
 * @desc brand home
 * @author Wty
 *
 */
class Brand extends Dcontroller {
	
	//brand home
	public function index(){
		$languageId = currentLanguageId();
		//品牌分类
		$this->load->model('brandcategorymodel','brandcategory');
		$brandCategoryList = $this->brandcategory->getBrandCategoryInfo();
		$brandCategoryList = reindexArray($brandCategoryList, 'brand_category_id');
		
		//处理品牌分类name问题
		foreach ($brandCategoryList as $key=>$brandCategory){
			$categoryName = json_decode($brandCategory['brand_category_title'],true);
			$brandCategoryList[$key]['name'] = isset($categoryName[$languageId])?$categoryName[$languageId]:'';
			$brandCategoryList[$key]['brands'] = array();
		}
		
		//品牌
		$this->load->model('brandmodel','brand');
		$brands = $this->brand->getBrandListByBcids(array_keys($brandCategoryList));
		
		//将相应品牌放入品牌分类里面
		foreach ($brands as $brand){
			if(isset($brandCategoryList[$brand['brand_category_id']])){
				$brandCategoryList[$brand['brand_category_id']]['brands'][] = $brand;
			}
		}
		
		//热门商品
		$this->load->model('brandwidgetmodel','brandwidget');
		$branWidgetList = $this->brandwidget->getBrandWidgetList();
		
		$branWidgetList = reindexArray($branWidgetList, 'product_id');
		$productIds = array_keys($branWidgetList);
		
		$hotProductList = $this->searchGoodsinfoWithPids($productIds);
		if(count($hotProductList) > 18){
			$hotProductList = array_slice($hotProductList, 0,18);
		}
		
		$this->_view_data['brandCategoryList'] = $brandCategoryList;
		$this->_view_data['hotProductList'] = $hotProductList;
		
		$this->load->model("imageadmodel","imagead");
		$location_ad_array = array(7);
		//从库里获取
		$image_ads = $this->imagead->getLocationWithIds($location_ad_array);
		
		$imageAdList = array();
		
		//对焦点图，时间进行判断
		if(!empty($image_ads[7])){
			foreach ($image_ads[7] as $ad) {
				if(strtotime($ad['ad_time_start']) < time() && strtotime($ad['ad_time_end']) > time()){
					$ad['ad_content'] = json_decode($ad['ad_content'],true);
					$imageAdList[] = $ad['ad_content'][currentLanguageId()];
				}
			}
		}
		
		$this->_view_data['imageAdList'] = $imageAdList;
		
		$this->dataLayerPushImpressions($hotProductList,'Brand Home');
		
		$this->_view_data['seo_keywords'] = lang('shop_keywords');
		$this->_view_data['description'] = lang('shop_description');
		parent::index();
	}
	
	//根据商品id（数组，批量）获取商品信息
	private function searchGoodsinfoWithPids($goodsIdArray){
		$result = array();
	
		$this->load->model("goodsmodel","ProductModel");
		//****************************************测试时
		$data = $this->ProductModel->getProductList( $goodsIdArray, $status=1,0,currentLanguageCode());
	
		$result = $this->productWithPrice($data);
	
		return $result;
	}
	
}