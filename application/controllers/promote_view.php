<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';
/**
 * pc版促销模板页面controller
 * @author wty
 */
class Promote_view extends DController {
	
	public function index( $promoteId = 0 ){
		$all_param = array();
		
		$this->load->model('promotedetailmodel','promotedetail');
		$this->load->model('promotemodel','promote');
		$this->load->model('categorymodel','category');
		
		$sort = $this->input->get('sort');
		//对价格范围的搜索进行处理
		$priceRangeSearch = trim($this->input->get('search_price_range'));
		$basicParam = array();
		$basicParam = $this->_setParam($priceRangeSearch, $sort);
		
		$page = $this->input->get("page");
		if(!$page || !is_numeric($page)) $page = 1;
		$pagesize = 48;
		$nums = 0;
		
		$language_id = currentLanguageId();
		
		$promoteDetail = $this->promotedetail->getPromoteDetailById($promoteId);
		if(empty($promoteDetail)){
			show_404();
			return;
		}else{
			//invalid url 301 redirect
			list($curUri,) = explode('?',trim($_SERVER['REQUEST_URI'],'/'));
			if($curUri != $promoteDetail['promotion_detail_url']) {
	            redirect(genURL($promoteDetail['promotion_detail_url']),'',301);
			}
			
			$promote = $this->promote->getPromoteById($promoteDetail['promotion_id']);
			$title = json_decode($promote['promotion_title'],true);
			$promote['title'] = $title[currentLanguageId()];
		}
		if($promoteDetail['promotion_detail_status'] <= 0){
			redirect( genURL('/') );
		}
		if(empty($promoteDetail['category_id'])){
			redirect( genURL('/') );
		}
		
		$category_id = $promoteDetail['category_id'];

		$category_info = $this->category->getCategoryinfo($category_id,$language_id);
		if(empty($category_info)){
			redirect( genURL('/') );
		}else{
			$categoryName = $category_info['category_name'];
		}
		
		//该分类及其所有子分类下所有商品（主分类，副分类：category表中type）  (***数据量大是个问题**)
		$vice_product_list = $this->viceCategoryProductList($category_id);//副分类商品列表
		$main_products_list = $this->category->maincategoryProductWithCate($category_id);//主分类处理,直接在eb_product表中根据product_path字段进行like
		//合并去重副分类，主分类商品
		$all_product_id_list = array_merge(array_keys($main_products_list),array_keys($vice_product_list));//所有商品id
		
		$all_product_list = $this->searchGoodsinfoWithPids($all_product_id_list);
		
		$currency_format = $this->getCurrencyNumber();
		$this->_view_data['new_currency'] = "$";
		if($currency_format){
			$this->_view_data['new_currency'] = $currency_format['currency_format'];
		}else{
			$currency_format['currency_rate'] = 1;
			$currency_format['currency_format'] = '$';
		}
		
		if($priceRangeSearch){//价格区间以“，”分割
			$all_param['search_price_range'] = $priceRangeSearch;
			if(stripos($priceRangeSearch,",")!==false){
				$range_array = explode(",", $priceRangeSearch);
				$start_price = $range_array[0]?$range_array[0]:0;
				$end_price = $range_array[1]?$range_array[1]:0;
				$start_price= round($start_price/$currency_format['currency_rate'],2, PHP_ROUND_HALF_DOWN);
				$end_price= round($end_price/$currency_format['currency_rate'],2, PHP_ROUND_HALF_DOWN);
			}else{
				$start_price = 0;
				$end_price = trim($priceRangeSearch);
				$end_price= round($end_price/$currency_format['currency_rate'],2, PHP_ROUND_HALF_DOWN);
			}
		
			foreach ($all_product_list as $p_id=>&$p_info){
				$market_price = $p_info['product_price'];
				if($market_price < min($start_price,$end_price) || $market_price > max($start_price,$end_price)){
					unset($all_product_list[$p_id]);
				}
			}
// 			$return_product_ids_num = count($all_product_list);
		}
		
		//对返回的数据"排序"处理
		switch ($sort){
			case 2://时间排序
				$all_product_list = array_sort($all_product_list,"product_time_initial_active","desc");
				$this->_view_data['sort'] = 2;
				break;
			case 3://价格排序
				//折扣前排序
				$all_product_list = array_sort($all_product_list,"product_price");
				//折扣后排序
				$this->_view_data['sort'] = 3;
				break;
			case 4://价格降序排序
				$all_product_list = array_sort($all_product_list,"product_price","desc");
				$this->_view_data['sort'] = 4;
				break;
			default://默认排序
				//获取推荐商品
				$recommend_product = $this->getCategoryProductRecommend($category_id);
				$first_recommend_lists = array();
				if(!empty($recommend_product)){
					//交集
					$recommend_product_ids = array_intersect(array_keys($all_product_list), array_keys($recommend_product));
					//取出推荐商品
					if(!empty($recommend_product_ids)){
						foreach ($recommend_product_ids as $kre=>$vre){
							$first_recommend_lists[$recommend_product[$vre]] = $all_product_list[$vre];
							$first_recommend_lists[$recommend_product[$vre]]['sort'] = $recommend_product[$vre];
							unset($all_product_list[$vre]);
						}
						//推荐商品排序
						$first_recommend_lists = array_sort($first_recommend_lists,"sort");
					}
				}
				$all_product_list = array_sort($all_product_list,"product_sales","desc");
				$all_product_list = array_merge($first_recommend_lists,$all_product_list);
				$this->_view_data['sort'] = 1;
		}
		
		$nums = count($all_product_list);
		
		$productList = array_slice($all_product_list, ($page-1)*$pagesize, $pagesize);
        //添加标签处理
//         $productList = $this->category->showProductList($productList);
        
		$this->_view_data['promote'] = $promote; 
		$this->_view_data['categoryName'] = $categoryName; 
		$this->_view_data['productList'] = $productList; 
		$this->_view_data['nums'] = $nums; 
		//分页处理
		list($curUri,) = explode('?',trim($_SERVER['REQUEST_URI'],'/'));
		$this->_basepagination($curUri,$page,$nums,$pagesize,$basicParam);
		
		$this->_view_data['curUri'] = $curUri;
		
		$this->_view_data['all_param'] = $all_param;//所有url参数，除sort排序（单独处理）
		$this->_view_data['basicParam'] = $basicParam;

		//seo info
		$seoPageInfo = $page == 1?'':' - '.lang('page').' - '.$page;
		$this->_view_data['title'] = sprintf(lang('title'),$categoryName,$promote['title']).$seoPageInfo;
		$this->_view_data['seo_keywords'] = sprintf(lang('keywords'),$categoryName,$promote['title']);
		$this->_view_data['description'] = sprintf(lang('description'),$categoryName,$promote['title']).$seoPageInfo;
		
		parent::index();
	}
	
	//副分类商品列表
	private function viceCategoryProductList($category_id){
		$result = array();
		if(!$category_id || !is_numeric($category_id)) return $result;
	
		$product_list = $this->viceCategory($category_id);
		$this->load->model("goodsmodel","goods");
		if(!empty($product_list)){
			foreach ($product_list as $k=>$v){
				$product_ids[] = $v['product_id'];
			}
            $info = $this->goods->getProductList($product_ids,1,0,currentLanguageCode());
            foreach($info as $product){
                $product_id = $product['product_id'];
                $result[$product_id] = $product;
            }
		}
	
		return $result;
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
	
	//获取分类下所有可用推荐商品
	private function getCategoryProductRecommend($category_id){
		$result = array();
		if(!$category_id || !is_numeric($category_id))return $result;
	
		$this->load->model("recommendproductmodel","recommend");
		$result = $this->recommend->getinfo($category_id);
		return $result;
	}
	
	/**
	 * 设置展示的参数
	 * @param boolean $priceMax 最大价格
	 * @param boolean $priceMin 最小价格
	 * @param boolean $display 展示类型
	 * @param boolean $sort 排序
	 */
	protected function _setParam( $priceRange = '', $sort = false) {
		$basicParam = array(); //初始化数组
		//网盟的参数 带上
		if( isset( $_GET ) && is_array( $_GET ) && (count( $_GET )> 0 ) ){
			foreach ( $_GET as $name => $value ){
				$name = removeXSS( $name );
				$value = removeXSS( $value );
				if( !empty( $name ) && !empty( $value ) ){
					$basicParam[ $name ] = $value ;
				}
			}
		}
		if( !empty( $priceRange ) ) {
			$basicParam['search_price_range'] = trim( $priceRange );
		}
	
		if($sort !== false) {
			// xss 过滤
			$specialCharacters = array( '%', '^', '[', ']', '{', '}', '€', '¥', '£', '<', '>', '=', '+', '*', '\\', "\n", "\n\r", "&nbsp", "\r", "\t", "'", '"', ",");
			$sort = str_replace( $specialCharacters, '', $sort );
			$basicParam['sort'] = trim($sort);
		}
	
		return $basicParam;
	}
	
}
