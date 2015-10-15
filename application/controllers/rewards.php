<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Rewards extends Dcontroller {
	
	public function index(){
		//判断用户是否登录
		if(!$this->customer->checkUserLogin()) {
			redirect(genURL('login'));
		}
		$this->load->model("rewardsmodel","rewards");
		
		$page = $this->input->get("page");
		if(!$page || !is_numeric($page)) $page = 1;
		$pagesize = 5;
		$nums = 0;
		
		//获取登陆者信息
		$userId = $this->customer->getCurrentUserId();
		$user = $this->customer->getUserById($userId);
		
		//获取列表信息
		$rewardsHistoryList = $this->rewards->getRewardsHistoryList($userId,array('page'=>$page,'pagesize'=>$pagesize));
		$nums = $this->rewards->getRewardsHistoryCount($userId);

		$orderIds = extractColumn($rewardsHistoryList,'order_id');
		$this->load->model("ordermodel","order");

		$orders = $this->order->getOrderListByOrderIds($orderIds);
		$orders = reindexArray($orders,'order_id');

		foreach ($rewardsHistoryList as &$rewardsHistory) {
			$rewardsHistory['order_code'] = isset($orders[$rewardsHistory['order_id']]['order_code'])?$orders[$rewardsHistory['order_id']]['order_code']:'';
		}

		$this->_view_data['rewardsHistoryList'] = $rewardsHistoryList;
		$this->_view_data['nums'] = $nums;
		$this->_view_data['user'] = $user;
		$this->_view_data['pagesize'] = $pagesize;
		//当前页名称 处理account中左侧选中的
		$this->_view_data['currentPage'] = 'rewards';
		
		//分页处理
		$this->_basepagination("rewards/",$page,$nums,$pagesize);

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
	
	public function ajaxRewardsList(){
		$arr = array('status'=>200,'msg'=>'','data'=>array());
		//判断用户是否登录
		if(!$this->customer->checkUserLogin()) {
			$arr['status'] = 1007;
			$this->ajaxReturn($arr);
		}
		$this->load->model("rewardsmodel","rewards");
		
		$page = $this->input->get("page");
		if(!$page || !is_numeric($page)) $page = 1;
		$pagesize = 5;
		$nums = 0;
		
		//获取当前登陆者信息
		$userId = $this->customer->getCurrentUserId();
		$user = $this->customer->getUserById($userId);
		
		//获取列表信息
		$rewardsHistoryList = $this->rewards->getRewardsHistoryList($userId,array('page'=>$page,'pagesize'=>$pagesize));
		$nums = $this->rewards->getRewardsHistoryCount($userId);

		$orderIds = extractColumn($rewardsHistoryList,'order_id');
		$this->load->model("ordermodel","order");
		
		$orders = $this->order->getOrderListByOrderIds($orderIds);
		$orders = reindexArray($orders,'order_id');
		
		//进行相应处理 方便前端显示
		$allList = array();
		foreach ($rewardsHistoryList as $key=>$val){
			$list = array();
			$order_code = isset($orders[$val['order_id']]['order_code'])?$orders[$val['order_id']]['order_code']:'';
			$list['status'] = in_array($val['rewards_history_type'], array(0,1,3,5))?1:0;
			$list['amount'] = currencyAmount($val['rewards_history_value']);
			$list['date'] = date('M d,Y h:i:s',strtotime($val['rewards_history_time_create']));
			$list['description'] = sprintf(lang('rewards_description'.$val['rewards_history_type']),$order_code);
			$allList[] = $list;
		}
		
		$arr['data'] = $allList;
		//传递页数如果尾页显示-1 防止过度加载
		if(empty($list)){
			$arr['page'] = -1;
		}else {
			$arr['page'] = $page+1;
		}
		
		$this->ajaxReturn($arr);
	}
	
}