<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

/**
 * @desc 单个商品的qna
 * @author Administrator
 *
 */
class Qna extends Dcontroller {
	
	//qna列表
	public function index($pid = 0){
		if(!$pid || !is_numeric($pid)) redirect("/");
		$page = $this->input->get("page");
		if(!$page || !is_numeric($page)) $page = 1;
		$pagesize = 8;

        $qna_list = array();
        $qna_total_nums = 0;
        $this->load->model("qnamodel","qna");
        $qnas = $this->qna->qnaListWithPid($pid,1,5);
        if($qnas['data'] && !empty($qnas['data'])){
            $qna_list = $qnas['data'];
            //查询qna的用户信息
            $this->load->model("customermodel","user");
            foreach ($qna_list as $k=>&$v){
                $customer_id = $v['customer_id'];
                $user_name = $this->user->nameWithUid($customer_id);
                $v['customer_name'] = $user_name;
            }
            $qna_total_nums = $qnas['nums'];
        }
		//echo "<pre>";print_r($qna_list);die;
		//获取单个商品信息
		$this->load->model("goodsmodel","product");
		$language_code = currentLanguageCode();
		$product_info = $this->product-> getinfoNostatus($pid,$language_code);
		$product_infos = array($pid=>$product_info);//包装成数组
		$product_infos = $this->productWithPrice($product_infos);//价格处理
		$product_info = $product_infos[$pid];
		$this->_view_data['product_info'] = $product_info;
		
		//分页处理
		$this->_basepagination("qna/".$product_info['product_url'],$page,$qna_total_nums,$pagesize);
		
		//also_like推荐数据（右下）
		$alsolike_data = $this->product->alsolikeProductWithPid($pid,$language_code);
		//also_like价格处理
		$alsolike_data = $this->productWithPrice($alsolike_data);
		
		$this->_view_data['alsolike_data'] = $alsolike_data;
		
		$this->_view_data['$product_info'] = $product_info;
		$this->_view_data['qna_list'] = $qna_list;
		parent::index();
	}
	
	
}
