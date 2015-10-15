<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';
/**
 * @desc Deals page
 * @author Wty
 *
 */
class Deals extends Dcontroller {
	
	//deals
	public function index($dealId=0){
		$languageId = currentLanguageId();
		
		$sort = $this->input->get('sort');
		$page = $this->input->get('page');
		if(!$page || !is_numeric($page)) $page = 1;
		//对价格范围的搜索进行处理
		$priceRangeSearch = trim($this->input->get('search_price_range'));
		
		list($curUri,) = explode('?',trim($_SERVER['REQUEST_URI'],'/'));
		
		$this->load->model('dealmodel','deal');
		
		//获取底层全部deals
		$deals = $this->deal->getDealList($type=2);
		$deals = reindexArray($deals, 'deal_id');
		
		$dealsProductIds = array();
		if($dealId == 0){
			//处理dealtitle 以及deal的商品信息
			foreach ($deals as &$deal){
				$title = json_decode($deal['deal_title'],true);
				$deal['title'] = isset($title[$languageId])?$title[$languageId]:'';
				$dealsProductIds = array_merge($dealsProductIds,explode(',', $deal['deal_content']));
			}
		}else{
			$currentDeal = array();
			
			//根据id获取deal信息
			if(isset($deals[$dealId])){
				$currentDeal = $deals[$dealId];
				$dealsProductIds = explode(',', $currentDeal['deal_content']);
			}
			
			//如果当前dealId不存在调回首页
			if(empty($currentDeal)){
				redirect(genURL('/'));
			}
			
			//针对不规则url进行301跳转
			if($curUri != $currentDeal['deal_url']) {
				redirect(genURL($currentDeal['deal_url']),'',301);
			}			
			//处理deal_title 以及deal的商品信息
			foreach ($deals as &$deal){
				$title = json_decode($deal['deal_title'],true);
				$deal['title'] = isset($title[$languageId])?$title[$languageId]:'';
			}
			
		}
		
		//获取热门deal
		$hotDeals = $this->deal->getDealList($type=1);
		
		//热门deal的productIds集合
		$hotDealProductIds = extractColumn($hotDeals, 'deal_content');
		
		$productIds =  array_merge($hotDealProductIds,$dealsProductIds);
		
		$productList = $this->searchGoodsinfoWithPids($productIds);
		
		//获取有效的hotDeals
		$hotDeals = $this->getValidHotDeals($hotDeals,$productList,$languageId);
		
		//按照排序和条件筛选后的商品列表
		$dealsProducts = $this->formatProductList($productList,$sort,$priceRangeSearch);
		
		//统计商品个数
		$product_ids_num = count($dealsProducts);
		
 		$pagesize = 50;
 		
		$this->_pagination($curUri, $page, $product_ids_num, $this->_view_data['all_param'] ,$pagesize);
		//分页截取数据
		$dealsProducts = array_slice($dealsProducts,$pagesize*($page-1),$pagesize);
		
		$this->dataLayerPushImpressions($productList,'Deals');
		
		$this->_view_data['hotDeals'] = $hotDeals;
		$this->_view_data['dealsProducts'] = $dealsProducts;
		
		$this->_view_data['deals'] = $deals;
		$this->_view_data['curUri'] = $curUri;
		$this->_view_data['dealId'] = $dealId;
		
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
	
	/**
	 * 根据商品列表和热门deal 处理掉热门deal中失效的商品 同时删除掉productList中与热门deal重复的product
	 * @param unknown $hotDeals 热门deal
	 * @param unknown $productList 商品列表
	 * @param unknown $languageId 语言id
	 * @return multitype:
	 */
	private function getValidHotDeals($hotDeals,&$productList,$languageId){
		//获取有效的hotDeal信息
		foreach ($hotDeals as $key=>&$hotDeal){
			if(isset($productList[$hotDeal['deal_content']])){
				$hotDeal['product'] = $productList[$hotDeal['deal_content']];
				$title = json_decode($hotDeal['deal_title'],true);
				$hotDeal['title'] = isset($title[$languageId])?$title[$languageId]:'';
				unset($productList[$hotDeal['deal_content']]);
			}else{
				unset($hotDeals[$key]);
			}
		}
		
		//取前4个
		if(count($hotDeals) > 4){
			$hotDeals = array_slice($hotDeals, 0,4);
		}
		return $hotDeals;
	}
	
	/**
	 * 对商品进行排序和筛选处理
	 * @param $product_list 商品列表
	 * @param $sort 排序方式
	 * @param $priceRangeSearch 价格区间
	 * @return array
	 */
	public function formatProductList($product_list,$sort,$priceRangeSearch){
		$currency_format = $this->getCurrencyNumber();
		if(!$currency_format['currency_rate']){
			$currency_format['currency_rate'] = 1;
			$currency_format['currency_format'] = '$';
		}
		//价格筛选
		//        if(strstr($priceRangeSearch,',') !== false){
		$priceRangeSearch = explode(',',$priceRangeSearch);
	
		$start_price = $priceRangeSearch[0]?$priceRangeSearch[0]:'';
		$end_price = isset($priceRangeSearch[1])?$priceRangeSearch[1]:'';
		$start_price= $end_price == '' ?'':round($start_price/$currency_format['currency_rate'],2, PHP_ROUND_HALF_DOWN);
		$end_price= $end_price == '' ?'':round($end_price/$currency_format['currency_rate'],2, PHP_ROUND_HALF_DOWN);
		if($start_price <> '' && $end_price <> ''){
			foreach($product_list as $id=>&$product){
				$price = $product['product_price'];
				if(($start_price <> '' && $price < $start_price) || ($end_price <> '' && $price > $end_price))
					unset($product_list[$id]);
			}
			$all_param['search_price_range'] = $start_price.','.$end_price;
		}
		//        }
	
		//对返回的数据"排序"处理
		switch ($sort){
			case 2://时间排序
				$product_list = array_sort($product_list,"product_time_initial_active","desc");
				$this->_view_data['sort'] = 2;
				break;
			case 3://价格排序
				//折扣前排序
				$product_list = array_sort($product_list,"product_price");
				//折扣后排序
				//                $product_list = array_sort($product_list,"product_discount_price");
				$this->_view_data['sort'] = 3;
				break;
			case 4://价格降序排序
				$product_list = array_sort($product_list,"product_price","desc");
				$this->_view_data['sort'] = 4;
				break;
			default://默认排序
				$product_list = array_sort($product_list,"product_sales","desc");
				$this->_view_data['sort'] = 1;
		}
		$all_param['sort'] = $sort?$sort:1;
		$this->_view_data['all_param'] = $all_param;
		$this->_view_data['new_currency'] = $currency_format['currency_format'];
		return $product_list;
	}
	
	/**
	 * 分页处理
	 * @param  array $deal_url 地址
	 * @param  integer $page 页码
	 * @param  integer $count 商品总数
	 * @param  array $basicParam URL参数
	 */
	protected function _pagination($deal_url, $page = 1, $count = 0, $basicParam = array(),$pagesize = 48) {
		$this->_view_data['pagination']['current_page'] = $page;
		$this->_view_data['pagination']['total_page'] = $count > 0 ? ceil( $count / $pagesize ) : 1;
		$urlTmp = trim( $deal_url ) ;
		$this->_view_data['pagination']['default_href'] = genURL( $urlTmp , false, $basicParam );
		$basicParam['page'] = '%u';
		$this->_view_data['pagination']['href'] = genURL( $urlTmp,false,$basicParam);
	}
	
}