<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';

class Search extends Dcontroller {
    private $currency_format;

    public function __construct(){
        parent::__construct();
        $this->currency_format = $this->getCurrencyNumber();
        $this->_view_data['new_currency'] = "$";
        $this->_view_data['currency_code'] = $this->current_currency_code = currentCurrency();
        if($this->currency_format){
            $this->_view_data['new_currency'] = $this->current_currency = $this->currency_format['currency_format'];
        }
    }
	
	public function index(){
		$keywords = $this->input->get( 'keywords' );
		$keywords = str_replace('-',' ',strip_tags($keywords));
		$keywords = addslashes( $keywords );
		$keywords = substr($keywords, 0, 30);
		if(empty($keywords)) {
			redirect(genURL());
		}

		//搜索词人工干预
		$this->searchKeywordsRedirect($keywords);
		
		$sort = $this->input->get('sort');
		$sort = ( $sort !== 'sale_count' && $sort !== 'add_time' && $sort !== 'price' && $sort !== 'price_desc' ) ? 'sale_count' : trim( $sort );
		$page = $this->input->get('page');
		$order =  $this->input->get('order');
		$order = ( $order === 'ASC')? 'ASC':'DESC';
		$page = max(1, (int)$page );
		$page_size = 48 ;
		$category = intval( $this->input->get('category'));
		
		//根据keyword进行搜索，注意缓存 
		$goods_ids = $goodsList = $search_category_list = $search_category_ids = array();
		$count = 0;
		$sou_result = $this->goxunsou($keywords, $category);
		//echo "<pre>";print_r($sou_result);die;
		
		if(!empty($sou_result)){
			$goodsList = $sou_result['goods_list'];
			//对价格范围的搜索进行处理
			$priceRangeSearch = trim($this->input->get('search_price_range'));
			if($priceRangeSearch && stripos($priceRangeSearch,",")){//价格区间以“，”分割
				$all_param['search_price_range'] = $priceRangeSearch;
				$range_array = explode(",", $priceRangeSearch);
				$start_price = $range_array[0];
				$end_price = $range_array[1];

				foreach ($goodsList as $p_id=>&$p_info){
					$market_price = $p_info['product_discount_price'];
					if($market_price < $start_price || $market_price > $end_price){
						unset($goodsList[$p_id]);
					}
				}
			}
            $this->_view_data['search_price_range'] = $priceRangeSearch;
			
			$count = count($goodsList);
			$search_category_list = $sou_result['search_category_list'];
			$search_category_ids = $sou_result['search_category_ids'];
			$goods_ids = $sou_result['goods_ids'];
		}		
		//echo "<pre>";print_r($goodsList);die;
		//排序
		switch ($sort){
			case 'add_time'://上架时间
				$goodsList = array_sort($goodsList,"product_time_initial_active","desc");
				$this->_view_data['sort'] = 'add_time';
				break;
            case 'price'://价格
                $goodsList = array_sort($goodsList,"product_price");
                $this->_view_data['sort'] = 'price';
                break;
            case 'price_desc'://价格
                $goodsList = array_sort($goodsList,"product_price",'desc');
                $this->_view_data['sort'] = 'price_desc';
                break;
			default://销量+推荐
				$this->_view_data['sort'] = 'sale_count';
		}

		//推荐商品
		$recommend_list = array();
		if($count < SEARCH_RESULT_NUM){
			$this->load->model("goodsmodel","goods");
			if($count==0){//没有搜索结果时（按照上架商品，销量倒排，数量为18）
				$recommend_list = $this->goods->recommendProductList(array(),array(),18);
			}else{//搜索结果小于12时（在搜索结果分类下，上架商品，销量倒排，数量为18）
				$recommend_list = $this->goods->recommendProductList(array_keys($search_category_ids),$goods_ids,18);
			}
			//促销价格处理
			$recommend_list = $this->productWithPrice($recommend_list);
		}
		
		//分页处理
		$basic_param = array();
		$basic_param['keywords'] = $keywords ;
		if($category && is_numeric($category))$basic_param['category'] = $category ;
		$basic_param['sort'] = $sort ;
		$basic_param['page'] = '%u' ;
		//$basic_param['order'] = $order ;
		$default_param = $basic_param;
		unset($default_param['page']);
		$this->_view_data['pagination'] = array(
				'current_page' => $page ,
				'href' => genURL( 'search' ,false , $basic_param) ,
				'total_page' => $count > 0 ? ceil( $count / $page_size ) : 1 ,
				'default_href' =>  genURL('search' ,false , $default_param) ,
		);
		
		unset($basic_param['page']);
		if($page && is_numeric($page) && $page>1)$basic_param['page'] = $page ;
		//echo "<pre>";print_r($basic_param);die;
		$this->_view_data['basicParam'] = $basic_param;
		$this->_view_data['goodsList'] = $goodsList;
		$this->_view_data['goods_count'] = $count;
		$this->_view_data['keywords'] = $keywords;
		$this->_view_data['search_category_list'] = $search_category_list;
		$this->_view_data['recommend_list'] = $recommend_list;
		$this->_view_data['page'] = $page;
        //添加部分list参数对应表
        $this->dataLayerPushImpressions($this->_view_data['recommend_list'],'Search Result');
        $relate_search = $this->getRelateSearch($keywords);
        $this->_view_data['relate_search'] = $relate_search;
		parent::index();
		
	}
	
	//去到xunsou搜索
	private function goxunsou($keywords, $category,$page = 1,$pagesize = 48,$sort = 'sale_count',$order = 'DESC'){
		$language_id = currentLanguageId();
		$result = array();

		$this->load->model("goodsmodel","ProductModel");
		$product = $this->ProductModel->getBaseInfoWithArray(array($keywords));
		if(empty($product)){
			$sku = $this->ProductModel->getSkuInfo($keywords);
			if(!empty($sku)) $product = $this->ProductModel->getBaseInfoWithArray(array($sku['product_id']));
		}

		$pidByDirectSearch = false;
		if(!empty($product)){
			$product = current($product);
			$pidByDirectSearch = $product['product_id'];
		}
		
		try {
			$this->load->library( 'xsearch/Xsearch' );
			$xs = new XS( 'product_description_' . $language_id );
			$search = $xs->search;
			$search->setCharset( 'UTF-8' );
			$this->load->model("categorymodel","CategoryModel");
			//对category_id范围的限定	
			if( !empty( $category ) ){
				$subCatIds = $this->CategoryModel->getSubCatIdbyCatId( $category );
                foreach($subCatIds as $v){
                    $cids[] = $v['category_id'];
                }
//				if( !empty( $subCatIds ) && is_array( $subCatIds ) ){
//					foreach ( $subCatIds as $v ){
//						$sc = 'category_id:'.(int)$v['category_id'];
//						$search->addQueryString($sc, CMD_QUERY_OP_OR);
//					}
//				}
			}
			//关键词及商品id的搜索
			$query_string = $search->setQuery( $keywords );
			//排序规则
			if( $order === 'ASC' ){
				$search->setSort( $sort , TRUE );
			}else{
				$search->setSort( $sort );
			}
			
			$search->setFacets( 'category_id' );
			$search->setLimit($pagesize, ($page - 1) * $pagesize);
			//搜索结果
			$goodsList = $search->search();
			$goodsList = array_filter($goodsList);
			//搜索结果个数	
			$count = $search->getLastCount();
			$result['count'] = $count;
			
			//提取搜索结果中商品id
			$goodsIdArray = array();
			if($pidByDirectSearch !== false) $goodsIdArray[] = $pidByDirectSearch;
			foreach ( $goodsList as $row ) {
				if ( empty( $row['name'] ) ) {
					continue;
				}
				$goodsIdArray[] = $row['product_id'];
			}
			
			$result['goods_ids'] = $goodsIdArray;
			//根据搜索pid，获取pid对应的商品信息(及促销价格)
			$goodsList = $this->searchGoodsinfoWithPids($goodsIdArray);
            //栏目筛选
            if(isset($cids)){
                foreach($goodsList as $key=>$value){
                    if(!in_array($value['category_id'],$cids)){
                        unset($goodsList[$key]);
                    }
                }
            }
			$result['goods_list'] = $goodsList;

			//搜索结果中，category信息
//			$cid_counts = $search->getFacets('category_id');//按分类id分组统计结果
            $cid_counts = array();
            foreach($goodsList as $k=>$v){
                if(!isset($cid_counts[$v['category_id']])) $cid_counts[$v['category_id']] = 0;
                $cid_counts[$v['category_id']]++;
            }
			$allCatNames = $this->CategoryModel->categoryinfoWithCids( array_keys( $cid_counts ) , $language_id );
			//echo "<pre>search_result:";print_r($allCatNames);//die;
			$search_category_list = array();
			if( is_array( $cid_counts) ){
				foreach( $cid_counts as $cid => $v ){
					if( isset( $allCatNames[ $cid ]['category_name'] ) ){
						$search_category_list[$cid] = array(
								'count' => $v,
								'url' => ( genURL( 'search' ,false , array()) ) . '?keywords=' . urlencode($keywords).'&category='.$cid,
								'category_name' => htmlspecialchars($allCatNames[$cid]['category_name'], ENT_QUOTES),
						);
					}
				}
			}
			
			$result['search_category_ids'] = $cid_counts;
			$result['search_category_list'] = $search_category_list;
			//echo "<pre>search_result:";print_r($allCatNames);die;
			
		} catch ( XSException $e ) {
			$error = strval( $e );
			//echo $error; die;
			//写错误日志
			
			return array();
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
	
	//获取对应促销活动等折扣价格
	public function productWithPrice($data){
		if(!count($data) && !is_array($data)) return false;
		$this->load->model("discountmodel","discount");
		$this->load->model("discountrangemodel","discountrange");
		$currency_info = $this->getCurrencyNumber();
	
		foreach ($data as $k=>&$v){
			if(isset($v['product_id']) && $v['product_id'] && is_numeric($v['product_id'])){
				$product_id = $v['product_id'];
	
				$front_price = $v['product_price'];
				$market_price = $v['product_price_market'];
				$discount_infos = $this->singleProductDiscount($product_id,$market_price);
                $v['product_basediscount_price'] = $v['product_discount_price'] = isset($discount_infos["discount_price"])?$discount_infos["discount_price"]:$front_price;
				$v['product_discount_number'] = isset($discount_infos["discount_number"])?$discount_infos["discount_number"]:0;
				$v['product_currency'] = "$";
				//汇率转换
				$currency_format = $this->getCurrencyNumber();
				if($currency_format){
					$v['product_currency'] = $currency_format['currency_format'];
					$v['product_discount_price'] = round($v['product_discount_price']*$currency_format['currency_rate'],2);
					$v['product_price_market'] = round($v['product_price_market']*$currency_format['currency_rate'],2);
				}
			}
	
		}
		return $data;
	}

    /**
     * 关联搜索推荐
     * @param $keywords
     * @return array
     */
    public function getRelateSearch($keywords){
        $language_id = currentLanguageId();

        $result = array();
        try {
            $this->load->library('xsearch/Xsearch');
            $xs = new XS('product_description_' . $language_id);

            $xs->search->setCharset('UTF-8');
            $xs->search->setQuery($keywords);
            $result = $xs->search->getRelatedQuery();
            $result = array_unique($result);
            foreach($result as $key => &$item){
                $item = strip_tags($item);
                if(preg_match('/[<,>,",\',(,),{,},=,+,-,%]/',$item)){
                    unset($result[$key]);
                }
                if(preg_match('/[\.,\,,:,;,；,：,。]$/',$item)){
                    unset($result[$key]);
                }
            }
        } catch ( XSException $e ) {
            $error = strval( $e );
            //echo $error; die;
            //写错误日志

        }
        return array_unique($result);
    }

    /**
     * 搜索词推荐
     */
    public function getKeyWords(){
        $keywords = $this->input->get( 'keywords' );
        $keywords = str_replace('-',' ',strip_tags($keywords));
        $keywords = addslashes( $keywords );
        $keywords = substr($keywords, 0, 30);

        $result = array();
        $result['data'] = $this->getRelateSearch($keywords);
        $result['number'] = intval($this->input->get( 'number' ));
        echo json_encode($result);
    }

    //搜索关键词跳转
    private function searchKeywordsRedirect($keywords){
    	if(empty($keywords)) return false;
    
    	//$keywords = 'lighting';  //test
    	$language_id = currentLanguageId();
    	$this->load->model('searchoptimizationmodel','searchoptimization');
    	$result = $this->searchoptimization->getSearchOptimization($keywords, $language_id);
    	if(!empty($result)){
    		if(isset($result['search_optimization_url'])) redirect($result['search_optimization_url']);
    	}
    	return false;
    }
    
}
?>
