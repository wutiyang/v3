<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Wishlist extends Dcontroller {
	
	//wishlist列表
	public function index(){
		//检测是否登录
		if($this->customer->checkUserLogin()){
			$page = $this->input->get('page');
			if(empty($page)) {
				$page = 1;
			}
			$pagesize = 10;
			$user_id = $this->session->get('user_id');
			$this->load->model("wishlistmodel","wishlist");
			
			//获取wish列表
			$this->load->model("goodsmodel","product");
			$language_code = currentLanguageCode();
			$wishlist_list = $this->wishlist->getUserCollecteList($user_id);
			
			//获取收藏商品基本信息
			if(!empty($wishlist_list)){
				foreach ($wishlist_list as $k=>&$v){
					$product_id = $v['product_id'];
					$product_info = $this->product->getinfoNostatus($product_id,$language_code);
					$v['product_info'] = $product_info;
				}
			}

			$this->_view_data['wishlist'] = array_slice($wishlist_list,$pagesize*($page-1),$pagesize);
			//当前页名称 处理account中左侧选中的
			$this->_view_data['currentPage'] = 'wishlist';
			
			//分页
			$this->_basepagination("wishlist",$page,count($wishlist_list),$pagesize);

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
			
		}else {
			redirect(genURL('login'));
		}
		
	}
	
	/**
	 * 删除收藏表中的内容
	 * 
	 * @author an
	 */
	public function ajaxCancel(){
		$arr = array('status'=>200,'msg'=>'','data'=>array());
		if($this->customer->checkUserLogin()){
			$id = $this->input->post('id');
			
			$userId = $this->customer->getCurrentUserId();
			
			//获取收藏的详细信息
			$this->load->model('wishlistmodel','wishlist');
			$info = $this->wishlist->checkUserGoodsCollected($id,$userId);
			
			//判断是否存在 是否是本人
			if(empty($info)){
				$arr['stNot Exist Or No Authenticationatus'] = 2200;
				$arr['msg'] = 'Not Exist Or No Authentication';
				$this->ajaxReturn($arr);
			}
			//取消收藏
			$flag = $this->wishlist->cancelCollect($id,$userId);
			if(!$flag){
				$arr['status'] = 2200;
				$arr['msg'] = 'Cancel Failed';
			}
		}else{
			$arr['status'] = 1007;
			$arr['msg'] = 'No Login';
		}
		$this->ajaxReturn($arr);
	} 
	
}
