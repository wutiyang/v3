<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';
/**
 * pc版促销模板页面controller
 * @author wty
 */
class promote_fullcut extends DController {
	
	public function index( $discountId = 0 ){
		$this->load->model('categorymodel','category');
		$this->load->model('discountmodel','discount');
		$this->load->model('discountrangemodel','discountrange');
		
		$page = $this->input->get("page");
		if(!$page || !is_numeric($page)) $page = 1;
		$pagesize = 48;
		$nums = 0;
		
		$language_id = currentLanguageId();

		//根据id获取discount	
		$param = array();
		$param['discount_id'] = $discountId;
		$discount_info = $this->discount->getActiveDiscountNew($param);
		$discount = isset($discount_info[0])?$discount_info[0]:array();
		
		//不存在
		if(empty($discount)){
			redirect('/');
		}else{
			//过期
			if(requestTime() > strtotime($discount['discount_time_finish'])){
				redirect('/');
			}
			
			//invalid url 301 redirect
			list($curUri,) = explode('?',trim($_SERVER['REQUEST_URI'],'/'));
			if($curUri != $discount['discount_url']) {
	            redirect(genURL($discount['discount_url']),'',301);
			}
			
			//notice
			$title = json_decode($discount['discount_title'],true);
			$content = $title[currentLanguageId()];
			
			$productCurrency = '$';
			$discountCondition = $discount['discount_condition'];
			$discountEffect = $discount['discount_effect'];
			
			$currency_format = $this->getCurrencyNumber();
			if($currency_format){
				$productCurrency = $currency_format['currency_format'];
				$discountCondition = round($discountCondition*$currency_format['currency_rate'],2);
				$discountEffect = round($discountEffect*$currency_format['currency_rate'],2);
			}
			
			$content = str_replace('{$condition_price}', '<span>'.$productCurrency.ceil($discountCondition).'</span>', $content);
			//2是满减 1是满折
			if($discount['discount_type_effect'] == 2){
				$content = str_replace('{$effect_price}', '<span>'.$productCurrency.floor($discountEffect).'</span>', $content);
			}else{
				$content = str_replace('{$effect_price}', '<span>'.$discount['discount_effect'].'%'.'</span>', $content);
			}
			
			$this->_view_data['fullcut_note'] = $content;
		}
		
		//获取促销详情
		$discount_range_info = $this->discountrange->getRangeByDiscountInfo($discount_info);
		$discount_range = isset($discount_range_info[$discountId])?$discount_range_info[$discountId]:array();
		
		if(empty($discount_range)){
			show_404();
			return;
		}
		
		$categoryIds = $discount_range['category_ids'];
		$productIds = array();
		foreach ($categoryIds as $categoryId){
			//主分类商品
			$main_products_list = $this->category->maincategoryProductWithCate($categoryId);
			$productIds = array_merge(array_keys($main_products_list),$productIds);
			//子分类商品
			$vice_products_ids_list = $this->viceCategoryProductList($categoryId);
			$productIds = array_merge(array_keys($vice_products_ids_list),$productIds);
		}
		
		$productIds = array_merge(array_keys($discount_range['product_ids']),$productIds);
		$productIds = array_unique($productIds);
		
		//排除productId
		$exProductIds = $discount_range['exclude_product_ids'];
		if(!empty($exProductIds)){
			foreach ($productIds as $key=>$val){
				if(isset($exProductIds[$key])){
					unset($productIds[$key]);
				}
			}
		}
		
		$all_product_list = $this->searchGoodsinfoWithPids($productIds);
		$nums = count($all_product_list);
		
		$productList = array_slice($all_product_list, ($page-1)*$pagesize, $pagesize);
		//添加标签处理
// 		$productList = $this->category->showProductList($productList);
		
		$this->_view_data['productList'] = $productList;
		$this->_view_data['nums'] = $nums;
		
		//分页处理
		list($curUri,) = explode('?',trim($_SERVER['REQUEST_URI'],'/'));
		$this->_basepagination($curUri,$page,$nums,$pagesize);
		
		parent::index();
	}
	
	//副分类商品列表
	private function viceCategoryProductList($category_id){
		$product_ids = array();
		if(!$category_id || !is_numeric($category_id)) return $product_ids;
	
		$product_list = $this->viceCategory($category_id);
		$this->load->model("goodsmodel","goods");
		if(!empty($product_list)){
			foreach ($product_list as $k=>$v){
				$product_ids[$v['product_id']] = $v['product_id'];
			}
//             $info = $this->goods->getProductList($product_ids,1,0,currentLanguageCode());
//             foreach($info as $product){
//                 $product_id = $product['product_id'];
//                 $result[$product_id] = $product;
//             }
		}
	
		return $product_ids;
	}
	
	//获取副分类商品id列表
	private function viceCategory($category_id){
		$result = array();
		if(!$category_id || !is_numeric($category_id)) return $result;
	
		$this->load->model("categorymodel","category");
		//like所有的category_path
		$all_search_category_lists = $this->category->categorySearchLists($category_id);
	
		$all_vice_category_ids = array();
		//副分类处理,获取所有副分类category_id，并且可用的
		foreach ($all_search_category_lists as $k_search=>$v_search){
			if($v_search['category_type']==CATEGORY_TYPE_VICE){
				$all_vice_category_ids[] = $v_search['category_id'];
			}
		}
	
		if(!empty($all_vice_category_ids)){
			//根据副分类商品表，获取所有商品product_id列表
			$this->load->model("goodsmodel","goods");
			$result = $this->goods->categoryProductList($all_vice_category_ids);
		}
	
		return $result;
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
