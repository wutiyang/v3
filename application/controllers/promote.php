<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';
/**
 * pc版促销模板页面controller
 * @author wty
 */
class Promote extends Dcontroller {
	public function index( $promoteId = 0 ){
		$this->load->model('promotedetailmodel','promotedetail');
		$this->load->model('promotemodel','promote');
		
		$languageId = currentLanguageId();
		
		$promote = $this->promote->getPromoteById($promoteId);
		if(empty($promote)){
			show_404();
			return;
		}
		
		//invalid url 301 redirect
		list($curUri,) = explode('?',trim($_SERVER['REQUEST_URI'],'/'));
		if($curUri != $promote['promotion_url']) {
            redirect(genURL($promote['promotion_url']),'',301);
		}

		if($promote['promotion_status'] <= 0){
			redirect( genURL('/') );
			//echo 'invalid promotion!';
			//exit;
		}		
		
		$template = $promote['promotion_template'];
		
		$titleArr = json_decode($promote['promotion_title'],true);
		$promote['promotion_title'] = isset($titleArr[$languageId])?$titleArr[$languageId]:'';
		
		$bannerArr = json_decode($promote['promotion_banner'],true);
		$promote['promotion_banner'] = isset($bannerArr[$languageId])?$bannerArr[$languageId]:'';
		
		$configArr = json_decode($promote['promotion_config'],true);
		foreach ($configArr as $key=>$val){
			$this->_view_data[$key] = $val;
		}
		
		$this->_view_data['promote'] = $promote;
		
		$promoteDetailList = $this->promotedetail->getPromoteDetailByPromoteId($promoteId);
		
		$arr = array();
		foreach ($promoteDetailList as $promoteDetail){
			$detailTitleArr = json_decode($promoteDetail['promotion_detail_title'],true);
			$promoteDetail['promotion_detail_title'] = isset($detailTitleArr[$languageId])?$detailTitleArr[$languageId]:'';
			
			$promoteDetail['promotion_detail_config'] = json_decode($promoteDetail['promotion_detail_config'],true);
			if($template == 1){
				$promoteDetail['icon_type'] = isset($promoteDetail['promotion_detail_config']['icon_type'])?$promoteDetail['promotion_detail_config']['icon_type']:'';
			}elseif($template == 2){
				$promoteDetail['config_img'] = isset($promoteDetail['promotion_detail_config'][$languageId])?$promoteDetail['promotion_detail_config'][$languageId]:'';
			}elseif($template == 4){
				if(isset($promoteDetail['promotion_detail_config']['type'])){
					$promoteDetail['type'] = isset($promoteDetail['promotion_detail_config']['type'])?$promoteDetail['promotion_detail_config']['type']:'';
				}else{
					continue;
				}
			}
			$productIds =  explode(',',$promoteDetail['promotion_detail_content']);
			$promoteDetail['productList'] = $this->searchGoodsinfoWithPids($productIds);
// 			print_r($promoteDetail['productList']);
// 			exit;
			
			$arr[] = $promoteDetail;
		}
		//exit;
		
		$this->_view_data['promoteDetailList'] = $arr;
		
		$data_end = strtotime($promote['promotion_time_end'])-time();
		$this->_view_data['data_end'] = $data_end;
		
		$this->dataLayerPushImpressions($promoteDetail['productList'],'Promotion');

		//seo info
		$this->_view_data['title'] = sprintf(lang('title'),$promote['promotion_title']);
		$this->_view_data['seo_keywords'] = sprintf(lang('keywords'),$promote['promotion_title']);
		$this->_view_data['description'] = sprintf(lang('description'),$promote['promotion_title']);
		
		$this->_view_data['tongji_promote']  = json_encode(array(array('id'=>$promote['promotion_id'],'name'=>addslashes($promote['promotion_title']))));
		$this->_view_data['top_banner'] = false;//该大促页面不显示顶通banner
		parent::index2('promote'.$template);
	}
	
	//根据商品id（数组，批量）获取商品信息
	private function searchGoodsinfoWithPids($goodsIdArray){
		$result = array();
	
		$this->load->model("goodsmodel","ProductModel");
		//****************************************测试时
		$data = $this->ProductModel->getProductList( $goodsIdArray, $status=1,0,currentLanguageCode());
		$data = $this->productWithPrice($data);
		
		foreach ($goodsIdArray as $productId){
			if(isset($data[$productId])){
				$result[$productId] = $data[$productId];
			}
		}
		
		return $result;
	}
	
	

	
}
