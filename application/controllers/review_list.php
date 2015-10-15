<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';
/**
 * @desc account 账户评论
 * @author Wty
 *
 */
class Review_list extends Dcontroller {
	
	//account review列表
	public function index(){
		$page = $this->input->get("page");
		if(!$page || !is_numeric($page)) $page = 1;
		$pagesize = 8;
		$nums = 0;
		
		//检测是否登录
		if(!$this->customer->checkUserLogin()){
			redirect(genURL('login'));
		}
		
		$userId = $this->customer->getCurrentUserId();
		$this->load->model("reviewmodel","review");
		
		//获取review列表
		$this->load->model("goodsmodel","product");
		$language_code = currentLanguageCode();
		
		$reviewlist_list = $this->review->reviewListByUserId($userId,array('page'=>$page,'pagesize'=>$pagesize));
		
		$pids = extractColumn($reviewlist_list, 'product_id');
		
		//获取商品基本信息
		if(!empty($reviewlist_list)){
			$productInfoList = array();
			if(!empty($pids)){
				$productInfoList = $this->searchGoodsinfoWithPids($pids);
			}
			
			foreach ($reviewlist_list as $k=>&$v){
				$product_id = $v['product_id'];
				if(isset($productInfoList[$v['product_id']])){
					$product_info = $productInfoList[$v['product_id']];
					$v['product_info'] = $product_info;
				}else{
					//此处为了容错，应该存入日志
					unset($reviewlist_list[$k]);
				}
			}
		}
		
		//相应sku信息
		$skuArr = extractColumn($reviewlist_list, 'product_sku');
		$skuInfoArr = $this->processSkuInfos($skuArr);
		$this->_view_data['skuInfoArr'] = $skuInfoArr;
		
		$reviewNums = $this->review->reviewNumsWithUserid($userId);
		
		$this->_view_data['review_list'] = $reviewlist_list;
		$this->_view_data['nums'] = $reviewNums;
		
		//当前页名称 处理account中左侧选中的
		$this->_view_data['currentPage'] = 'review';
		
		//分页
		$this->_basepagination("review_list",$page,$reviewNums,$pagesize);

		//个人中心广告位
		$this->load->model("imageadmodel","imagead");
		$image_ads = $this->imagead->getLocationWithId(5);

		$image_ad = '';
		foreach ($image_ads as $ad) {
			if(strtotime($ad['ad_time_start']) < time() && strtotime($ad['ad_time_end']) > time()){
				$ad['ad_content'] = json_decode($ad['ad_content'],true);
				$image_ad = $ad['ad_content'][currentLanguageId()];
				break;
			}
		}
		$this->_view_data['image_ad'] = $image_ad;
		
		parent::index();
	}
	
	private function processSkuInfos($skuArr){
		$result = array();
		if(empty($skuArr))
			return $result;
		
		$languageId = currentLanguageId();
		
		$this->load->model('attributeproductmodel','attributeproduct');
		$attrSkuList = $this->attributeproduct->getAttrAndValueWithSkus($skuArr);
		
		//获取相应id
		$attrIds = array();
		$attrValueIds = array();
		foreach ($attrSkuList as $val){
			foreach ($val as $attrSku){
				$attrIds[] = $attrSku['complexattr_id'];
				$attrValueIds[] = $attrSku['complexattr_value_id'];
			}
		}
		
		$attrList = $this->attributeproduct->complexattrBatchInfo($attrIds,$languageId);
		$attrValueList = $this->attributeproduct->complexattrBatchValueInfo($attrValueIds,$languageId);
		
		$attrList = reindexArray($attrList, 'complexattr_id');
		$attrValueList = reindexArray($attrValueList, 'complexattr_value_id');
		
		foreach ($attrSkuList as $val){
			foreach ($val as $attrSku){
				$result[$attrSku['product_sku']][$attrList[$attrSku['complexattr_id']]['complexattr_lang_title']] = $attrValueList[$attrSku['complexattr_value_id']]['complexattr_value_lang_title'];
			}
		}
		
		return $result;
	}
	
	//根据商品id（数组，批量）获取商品信息
	private function searchGoodsinfoWithPids($goodsIdArray){
		$result = array();
	
		$this->load->model("goodsmodel","ProductModel");
		//****************************************测试时
		$data = $this->ProductModel->getProductList( $goodsIdArray, $status=0,0,currentLanguageCode());
	
		$result = $this->productWithPrice($data);
	
		return $result;
	}
	
}
