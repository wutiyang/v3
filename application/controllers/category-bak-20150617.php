<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';

class category extends Dcontroller {
	
	public function index($categoryId = 0, $page = 1){
		//分类id，不存在时的处理（0，表中不存在，非数字的处理）
		$category_id = intval($categoryId);
		$language_id = currentLanguageId();
		//优先301，302，404处理
		//$this->_urlRedirect301And302($language_id);
		
		//获取当前分类，及footer信息，语言处理
		$this->load->model("categorymodel","category");
		$category_info = $this->category->getCategoryinfo($category_id,$language_id);
		if(empty($category_info)){
			//该分类数据不存在或status值不为1
			echo $category_id."分类不可用";die;	
			//判断这个分类是不是影藏分类，如果是影藏分类就直接跳转至首页
			//*****************************
		}
		
		//获取面包屑信息
		$category_path = $category_info['category_path'];
		$crumbs_list = $this->getCategoryCrumbs($category_path);
		
		//及相关分类，及分类desc名称
		$category_related_lists = $this->category->getRelatedCategoryList($category_id,$language_id);
		
		//只处理一级和二级分类
		$cate_array = explode("/", $category_info['category_path']);
		$category_level = count($cate_array);
		
		$childrenCategoryAndProductList = array();
		if($category_level<3){
			$childrenCategoryAndProductList = $this->childrenCategoryAndRelatedProduct($category_id);
		}
		
		//判断检查url
		//$this->_checkUrl($category_info);
		
		//获取其他参数
		$attrNarrowSearch = $this->input->get('attr');
		$sort = $this->input->get('sort');
		
		$priceRange = trim($this->input->get('price_range')); //区间
		if(empty( $priceRange )) { $priceRange = ''; }
		
		
		//*********************************
		$category_memcache = new CI_Memcache();
		$mem_category_key = md5("category_attribute_product_".$category_id);
		$category_memcache->delete($mem_category_key);
		
		if(!$cache_search_result = $category_memcache->get($mem_category_key)){
			global $mem_expired_time;
			//该分类及其所有子分类下所有商品（主分类，副分类：category表中type）  (***数据量大是个问题**)
			$vice_product_list = $this->viceCategoryProductList($category_id);//副分类商品列表
			$main_products_list = $this->category->maincategoryProductWithCate($category_id);//主分类处理,直接在eb_product表中根据product_path字段进行like
			//合并去重副分类，主分类商品
			$all_product_id_list = array_merge(array_keys($main_products_list),array_keys($vice_product_list));//所有商品id
			$all_product_list = $main_products_list;
			if(!empty($vice_product_list)){
				foreach ($vice_product_list as $vice_k=>$vice_v){
					$all_product_list[$vice_k] = $vice_v;
				}
			}
			//echo "<pre>";print_r($all_product_list);die;
			
			//商品信息，hot，new，属性标签Icon，slogan，排序方式，语言，价格，分页（8行，每列5-6列）
			
			//该分类所有属性及属性对应商品信息
			$attribute_data = $this->categoryAttribute($category_id);
			$price_attr_data = $this->priceAttribute($category_id);
			
			$cache_search_result['vice_product_list'] = $vice_product_list;
			$cache_search_result['main_products_list'] = $main_products_list;
			$cache_search_result['all_product_id_list'] = $all_product_id_list;
			$cache_search_result['all_product_list'] = $all_product_list;
			$cache_search_result['attribute_data'] = $attribute_data;
			$cache_search_result['price_attr_data'] = $price_attr_data;
			
			$category_memcache->set($mem_category_key, $cache_search_result,$mem_expired_time['category_attribute_product_cache']);
		}
		//echo "<pre>";print_r($cache_search_result['all_product_list']);die;
		//*********************************
		
		$attrNarrowSearch = $this->_checkUrlAttr($attrNarrowSearch);
		//echo "<pre>";print_r($attrNarrowSearch);//die;
		if( isset( $attrNarrowSearch['parameter']  ) &&( count( $attrNarrowSearch['parameter'] ) > 0 )){
			$basicParam['attr'] = implode( ',', array_keys( $attrNarrowSearch['parameter'] ) )  ;
		}
		//获取所有的选择的商品属性id
		$attrIdsArray = $attrNarrowSearch['attrGroups'];

		//已选择的对应属性对应属性组值得商品pid列表
		$selected_attribute_product_list = array();
		if(isset($attrIdsArray) && count($attrIdsArray)){
			foreach ($attrIdsArray as $attrId_key=>$attrId_val){
				$insert_assoc_product = array();
				foreach ($attrId_val as $attrValue){
					//获取该属性值id对应的pid列表,对全部的pid进行交集(可用)
					$the_attr_value_product_list = array_keys($cache_search_result['attribute_data'][$attrId_key]['group'][$attrValue]['product_list']);
					$insert_assoc_product[] = array_intersect($cache_search_result['all_product_id_list'],$the_attr_value_product_list);
				}
				
				//获取该属性id的并集
				if(!empty($insert_assoc_product)){
					foreach ($insert_assoc_product as $insert_value){
						$selected_attribute_product_list[$attrId_key] = $insert_value;
					}
				}
				
			}
		}
		//对已选择的属性id的pid列表进行交集( ***最终返回展示的商品列表****)
		$return_product_ids_list = array_intersect_upgrade($selected_attribute_product_list);
		$return_product_ids_num = count($return_product_ids_list);
		
		//////////////////////////////////////////////////////////////////////////////////
		//对所有属性计算每个属性的个数及链接，及是否选中状态值
		$cal_selected_attr_and_pid_list = $selected_attribute_product_list;
		foreach ($cache_search_result['attribute_data'] as $k_attr=>&$v_attr){
			if(isset($v_attr['group']) && count($v_attr['group'])){
				//获取其他选中的属性id的并集的交集
				$other_call_selected_attr_pid_list = array();
				foreach ($cal_selected_attr_and_pid_list as $cal_k_attr=>$cal_v_attr){
					if($cal_k_attr!=$k_attr){
						$other_call_selected_attr_pid_list[$cal_k_attr] = $cal_v_attr;
					}
				}
				$other_call_selected_attr_pid_list = array_intersect_upgrade($other_call_selected_attr_pid_list);
				
				//对属性组值的个数：与其他 已选中的 属性并集的 交集进行交集
				foreach ($v_attr['group'] as $v_attr_key=>&$v_attr_val){
					$attr_value_nums = 0;
					//改属性组对应商品id列表
					if(isset($v_attr_val['product_list']) && count($v_attr_val['product_list'])){
						$theattr_product_list = array_intersect_upgrade(array($other_call_selected_attr_pid_list,array_keys($v_attr_val['product_list'])));
						$attr_value_nums = count($theattr_product_list);
					}
					//隐藏商品列表
					unset($v_attr_val['product_list']);
					
					//个数
					$v_attr_val['p_nums'] = $attr_value_nums;
					//判断已选中的属性及属性组中是否有该属性下的属性组，从而判断是否选中
					if(isset($attrIdsArray[$k_attr][$v_attr_key])) $v_attr_val['selected'] = true;
					//链接
					
				}
					
			}
			
		}
		echo "<pre>";print_r($cache_search_result['attribute_data']);die;
		///////////////////////////////////////////////////////////////////////////////////
		//处理属性对应商品个数
		$total_product_num = 0; 
		foreach ($cache_search_result['attribute_data'] as $k_attr=>&$v_attr){
			if(isset($v_attr['group']) && count($v_attr['group'])){
				foreach ($v_attr['group'] as $k_group=>&$v_group){
					$group_num = 0;
					if(isset($v_group['product_list']) && count($v_group['product_list'])){
						foreach ($v_group['product_list'] as $k_product=>$v_product){
							if(in_array($k_product, $cache_search_result['all_product_id_list'])) $group_num++;
						}
					}
					$v_group['nums'] = $group_num;
					$total_product_num += $group_num;
				}
			}
		}
		echo "<pre>";print_r($cache_search_result['attribute_data']);die;
		
		//计算每个价格区间的个数
		if(!empty($cache_search_result['price_attr_data'])){
			foreach ($cache_search_result['price_attr_data'] as &$price_v){
				$start_price = $price_v['category_narrow_price_start'];
				$end_price = $price_v['category_narrow_price_end'];
				$price_num = 0;
				////遍历商品，计算该价格区间个数
				foreach ($cache_search_result['all_product_list'] as $k_product_price=>$v_product_price){
					if($v_product_price['product_price']>=$start_price && $v_product_price['product_price'] <=$end_price) $price_num++;
				}
				$price_v['nums'] = $price_num;
				$total_product_num += $price_num;
			}
			
			$this->_view_data['price_attr_data'] = $cache_search_result['price_attr_data'];
		}
		$show_product_lists = $cache_search_result['all_product_list'];

		/***********************************************/
		//SEO URL 所需要的参数
		if( !empty( $attrNarrowSearch['parameter'] ) && ( count( $attrNarrowSearch['parameter'] ) > 0 ) ){
			$pageTitleInfoNarrowSearchArr = array(); //$pageTitleInfoNarrowSearch 使用需要
			foreach ( $attrNarrowSearch['attrGroups'] as $attId => $groupIds ){
				foreach ( $groupIds as $groupId ){
					$paramKey = $attId . '_' . $groupId ;
					$groupIdNameTmp = trim($this->_view_data['narrow_search'][ $attId ]['group_info'][ $groupId ]['name'] ) ;
					$groupIdNameSeoTmp = trim($this->_view_data['narrow_search'][ $attId ]['group_info'][ $groupId ]['lang'] );
					$attIdName = isset( $this->_view_data['narrow_search'][ $attId ]['name'] ) ? str_replace( ' ' , '' , HelpUrl::removeXSS( trim( $this->_view_data['narrow_search'][ $attId ]['name'] ) ) ) : FALSE;
					$groupIdName = isset( $this->_view_data['narrow_search'][ $attId ]['group_info'][ $groupId ]['name'] ) ? str_replace( ' ' , '' , $groupIdNameTmp ) : FALSE;
					if( ( $attIdName !== FALSE ) && ( $groupIdName !== FALSE ) ){
						$attrNarrowSearch['parameter'][ $paramKey ] = $attIdName .'-' .$groupIdName ;
						$pageTitleInfoNarrowSearchArr[ $paramKey ]  = $groupIdNameSeoTmp ;
					}
				}
			}
			//有已经选择的narrow search
			if( count( $pageTitleInfoNarrowSearchArr ) > 0 ){
				ksort( $pageTitleInfoNarrowSearchArr );
				$pageTitleInfoNarrowSearch = implode(', ', array_slice( $pageTitleInfoNarrowSearchArr , 0 ,3 ) ) ;
			}
		
		}
		$this->_view_data['attrNarrowSearch'] = $attrNarrowSearch['parameter'] ;
		$this->_view_data['attrGroups'] = $attrNarrowSearch['attrGroups'] ;
		/***********************************************/
		
		//给分类推荐商品
		
		//组合所有商品，并处理是否hot，new，slogan(ex:50%off on orders over $100)，价格,
		
		$this->_view_data['return_product'] = $show_product_lists;
		$this->_view_data['Total_num'] = $total_product_num;
		$this->_view_data['crumbs_list'] = $crumbs_list;
		$this->_view_data['category_info'] = $category_info;
		$this->_view_data['category_related_lists'] = $category_related_lists;
		$this->_view_data['category_level'] = $category_level;
		$this->_view_data['children_category'] = $childrenCategoryAndProductList;
		$this->_view_data['attribute_data'] = $cache_search_result['attribute_data'];
		parent::index();
	}
	
	/**
	 * 检查处理属性参数
	 * @param  string $attrNarrowSearch 分类选择的属性
	 * @return array 商品属性
	 */
	protected function _checkUrlAttr($attrNarrowSearch) {
		$resultAttr = array('parameter' => array() , 'attrGroups' => array() );
		if(!empty($attrNarrowSearch)) {
			//根据逗号分隔多个属性
			$paramUrl = explode(',', $attrNarrowSearch);
			if(!empty($paramUrl)) {
				foreach ($paramUrl as $key => $value) {
					$paramUrlArray = explode('_', $value);
					$attrId = isset($paramUrlArray[0] ) ?  (int)$paramUrlArray[0] : 0 ;
					$groupId = isset($paramUrlArray[1] ) ? (int)$paramUrlArray[1] : 0 ;
					if( ( $attrId > 0 ) && ( $groupId > 0 ) ){
						$keyTmp = $attrId . '_' . $groupId ;
						$resultAttr ['parameter'][ $keyTmp ] = FALSE ;
						$resultAttr ['attrGroups'][ $attrId ][ $groupId ] = $groupId ;
					}
				}
			}
		}
		return $resultAttr;
	}
	
	/**
	 * 检查url
	 * @param  array $category 分类信息
	 * @author qcn qianchangnian@hofan.cn
	 */
	protected function _checkUrl($category) {
		if( strpos ( trim( $_SERVER['REQUEST_URI'] ) , trim( $category['category_url'] ) ) === FALSE ){
			redirect( genURL( $category['category_url'], ture), 'location', 301 );
		}
	}
	
	/**
	 * 判断这个分类是不是影藏分类，如果是影藏分类就直接跳转至首页
	 * @param  array $category 分类信息
	 * @author qcn qianchangnian@hofan.cn
	 */
	protected function _checkCategoryDisplay($category) {
		if($category['status'] == 0) {
			redirect( genURL(''));
		}
	}
	
	//获取分类价格区间
	public function priceAttribute($category_id){
		$result = array();
		if(!$category_id || !is_numeric($category_id)) return $result;
		$this->load->model("attributecategorymodel","attribute_category");
		
		$result = $this->attribute_category->categoryPriceAttribute($category_id);
		
		return $result;
	}
	
	//副分类商品列表
	private function viceCategoryProductList($category_id){
		$result = array();
		if(!$category_id || !is_numeric($category_id)) return $result;
		
		$product_list = $this->viceCategory($category_id);
		$this->load->model("goodsmodel","goods");
		if(!empty($product_list)){
			foreach ($product_list as $k=>$v){
				$product_id = $v['product_id'];
				$info = $this->goods->getinfo($product_id,1);
				if(!empty($info))$result[$product_id] = $info;
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
	
	//单个分类下attribute属性处理(属性名称)
	public function categoryAttribute($category_id){
		$result = array();
		if(!$category_id || !is_numeric($category_id)) return $result;
		
		//
		$this->load->model("attributecategorymodel","attribute_category");
		$data = $this->attribute_category->attributeWithCateId($category_id);
		//每个属性对应名称及lang语言及值
		$language_id = currentLanguageId();
		foreach ($data as $k=>$v){
			$name_list = $this->attribute_category->getAttributeAndLang($v['attribute_id'],$language_id);
			$attribute_id = $name_list['attribute_id'];
			$result[$attribute_id] = $name_list;
			//$result['name'][] = $name_list;
			
			//每个属性对应可用值
			$value_list = $this->attribute_category->groupWithAttributeCategoryId($v['attribute_category_id']);
			$result[$attribute_id]['group'] = $value_list;
		}
		
		//echo "<pre>attribute_value";print_r($result);die;
		return $result;
	}
	
	
	//获取某分类id下的所有子分类信息及子分类相关商品，及子分类的子分类信息
	public function childrenCategoryAndRelatedProduct($category_id){
		$result = array();
		$language_id = currentLanguageId();
		
		//所有分类信息
		$all_category = $this->_view_data['all_category'];
		
		//获取一级子分类信息
		$children_category = isset($all_category[$category_id])?$all_category[$category_id]:array();
		if(empty($children_category)) return $result;
		
		$result['children'] = $children_category;
		$this->load->model("categorymodel","cate_model");
		
		//获取二级子分类，及一级子分类的相关商品信息
		foreach ($children_category as $key=>$val){
			$cate_id = $val['category_id'];
			$type = $val['category_type'];
			$cate_id = 15294;//测试数据
			
			//echo $cate_id;die;//16290
			$product_list = $this->cate_model->getCategoryRelatedProductList($cate_id,$language_id);
			//*****商品价格没有做处理***
			$result['children_children'][$key]["son_product"] = $product_list;
			
			//获取子分类信息
			$children_children_category = isset($all_category[$cate_id])?$all_category[$cate_id]:array();
			$result['children_children'][$key]['son_category'] = $children_children_category;
		}
		
		return $result;
	}
	
	/**
	 * @desc 获取该分类path的面包屑分类信息
	 * @param unknown $category_path(某分类下的path值)
	 * @return multitype:|multitype:unknown
	 */
	public function getCategoryCrumbs($category_path){
		$result = array();
		if(!$category_path) return $result;
		
		$language_id = currentLanguageId();
		$this->load->model("categorymodel","category");
		if(is_numeric($category_path) && $category_path){
			$category_info = $this->category->getCategoryinfo($category_path,$language_id);
			$result[$category_info['category_id']] = $category_info;
		}else{
			if(strpos($category_path,"/")){
				$category_ids = explode("/", $category_path);
				foreach ($category_ids as $category_id){
					$category_info = $this->category->getCategoryinfo($category_id,$language_id);
					$result[$category_info['category_id']] = $category_info;
				}
			}
		}
		
		return $result;
	}

	//对所有的二维数组取交集
	public function mergeAllArrayTo($data){
		
	}
	protected function _urlRedirect301And302($categoryId = 0) {
		//分类id不存在时404页面跳转
		if ( $categoryId <= 0 ) {
			eb_show_404();
			return;
		}
		// 实力化制定跳转类库
		$this->CategoryredirectModel = new CategoryredirectModel();
	
		// 取出分类页面id的跳转信息（包括时间和类型）
		$categoryRedirectInfo = $this->CategoryredirectModel->getRedirectInfoByCategoryId($categoryId);
	
		// 执行跳转操作
		HelpUrl::url301And302Redirect($categoryRedirectInfo, '-c{$targetCategoryId}/');
	}
	
	
}
?>