<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';
/**
 * @desc 该商品所有评论
 * @author Administrator
 *
 */
class Review extends Dcontroller {
	
	//all_review
	public function index($pid=0){
		if(!$pid || !is_numeric($pid)) redirect("/");
		$page = $this->input->get("page");
		if(!$page || !is_numeric($page)) $page = 1;
		
		//获取商品信息
		$this->load->model("goodsmodel","product");
		$language_code = currentLanguageCode();
		$product_info = $this->product-> getinfoNostatus($pid,$language_code);
		$product_infos = array($pid=>$product_info);//包装成数组
		$product_infos = $this->productWithPrice($product_infos);//价格处理
		$product_info = $product_infos[$pid];
// 		$product_info = $this->reviewProductWithPrice($product_info);//价格处理
		
		//商品不存在
		if(empty($product_info)){
			show_404();
			return;
		}
		
		$this->_view_data['product_info'] = $product_info;
		
		//获取review列表
		$this->load->model("reviewmodel","review");
		$this->load->model('reviewhelpfulmodel','reviewhelpful');
		$review_list = $this->review->reviewListWithPid($pid);
		$all_data = $this->reviewWithUserScore($review_list);
		$review_data = $all_data['data'];//新增评论人信息
		$review_user_nums = $all_data['review_nums'];


		//评论数
		$reviewNums = $this->review->reviewNumsByPids($pid);
		$reviewNums = reindexArray($reviewNums,'product_id');
		$this->_view_data['review_user_nums'] = isset($reviewNums[$pid]['num'])?$reviewNums[$pid]['num']:0;

		$review_total_score = $all_data['total_score'];
		
		//处理 星级别信息
		$star_info = $this->reviewWithStar(count($review_data),$review_total_score);
		$star_level = $star_info['star_level'];
		$average_score = $star_info['average_score'];
		$this->_view_data['review_user_nums'] = $review_user_nums;
		$this->_view_data['star_level'] = $star_level;
		$this->_view_data['average_score'] = $average_score;

		//处理 like
        if($this->customer->checkUserLogin()){
            $userId = $this->customer->getCurrentUserId();
            //like信息
            $helpfulInfos = $this->reviewhelpful->getHelpfulInfos($pid,$userId);
        }else{
        	//like信息
        	$helpfulInfos = array();
        }

        //处理like unlike信息
		$review_data = $this->_processReviewWithHelpfulInfo($review_data,$helpfulInfos);
		
		//分页数据及分页链接处理
		$pagesize = 8;
		$start_nums = ($page-1)*$pagesize;
		$data = array_slice($review_data, $start_nums,$pagesize);
		$this->_view_data['reviewdata'] = $data;
		$this->_basepagination("review/".$product_info['product_url'],$page,$this->_view_data['review_user_nums'],$pagesize);
		
		//右侧数据
		//also_like推荐数据（右下）
		$alsolike_data = $this->product->alsolikeProductWithPid($pid,$language_code);
		//also_like价格处理
		$alsolike_data = $this->productWithPrice($alsolike_data);
		
		$this->_view_data['alsolike_data'] = $alsolike_data;
		
		//seo info
		$product_name = isset($product_info['product_description_name'])?$product_info['product_description_name']:$product_info['product_name'];
		$seoPageInfo = $page == 1?'':' - '.lang('page').' - '.$page;
		$this->_view_data['title'] = sprintf(lang('title'),$product_name).$seoPageInfo;
		$this->_view_data['seo_keywords'] = sprintf(lang('keywords'),$product_name).$seoPageInfo;
		$this->_view_data['description'] = sprintf(lang('description'),$product_name).$seoPageInfo;
		
		//面包屑
		$category_id = $product_info['category_id'];
		$category_path = $product_info['product_path'];
		$crumbs_list = $this->getCategoryCrumbs($category_path);
		$this->_view_data['crumbs_list'] = $crumbs_list;
		
		parent::index();
	}

	private function _processReviewWithHelpfulInfo($review_data,$helpfulInfos){
		$helpfulInfos = reindexArray($helpfulInfos,'review_id');
		foreach ($review_data as &$data) {
			$data['like'] = 0;
			$data['unlike'] = 0;

			if(isset($helpfulInfos[$data['review_id']])){
				$helpfulInfo = $helpfulInfos[$data['review_id']];
				//1 有帮助 2没帮助
				if($helpfulInfo['review_helpful_type'] == 1){
					$data['like'] = 1;
                    $data['review_count_helpful']++;
				}else{
					$data['unlike'] = 1;
                    $data['review_count_nothelpful']++;
				}
			}
			
		}
		return $review_data;
	}
	
}
