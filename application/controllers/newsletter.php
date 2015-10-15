<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';

/**
 * @desc account newsletter
 * @author wty
 *
 */
class Newsletter extends Dcontroller {

	public function index(){
		//判断用户是否登录
		if(!$this->customer->checkUserLogin()) {
			redirect(genURL('login'));
		}
        //获取订阅邮箱信息
		$customer_id = $this->session->get('user_id');

		
		//render page
		$this->_view_data['name'] = 'Newsletter Subscriptions';
		$this->load->model('subscribemodel','subscribe');
		$subscribeInfo = $this->subscribe->getEmailSubscribeInfoByCustomerId($customer_id,1);
        //获取订阅邮箱
        $email = isset($subscribeInfo['subscribe_email'])?$subscribeInfo['subscribe_email']:'';
		$subscribe = false;
		$unsubscribe = false;
		//empty时
		if(!empty($subscribeInfo)){
			if($subscribeInfo['subscribe_status']==1){
				//已经订阅(显示已订阅文案)
				$subscribe = true;
			}else{
				$unsubscribe = true;//有取消
			}
		}

		$this->_view_data['email'] = $email;
		$this->_view_data['subscribe'] = $subscribe;
		$this->_view_data['unsubscribe'] = $unsubscribe;
        $this->_view_data['validate'] = isset($subscribeInfo['subscribe_status_validate'])?$subscribeInfo['subscribe_status_validate']:'';
        $this->_view_data['coupon_code'] = isset($subscribeInfo['subscribe_coupon'])?$subscribeInfo['subscribe_coupon']:'';
        $this->_view_data['coupon_time'] = isset($subscribeInfo['subscribe_time_coupon'])?date('d/m/Y',strtotime($subscribeInfo['subscribe_time_coupon'])):'';
        $this->_view_data['subscribe_status_coupon'] = isset($subscribeInfo['subscribe_status_coupon'])?$subscribeInfo['subscribe_status_coupon']:'';
            //当前页名称 处理account中左侧选中的
		$this->_view_data['currentPage'] = 'newsletter';

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
	
	//取消订阅
	public function unsubscribe(){
        $email = '';
        $this->load->model('subscribemodel','subscribe');
        $islogin = $this->customer->checkUserLogin();
		if(!$islogin) {
            $email = $this->input->post('unsubscribe_mail');
            $hash = $this->input->post('hash');
            $subscribeInfo = $this->subscribe->getEmailSubscribeInfo($email);
            if(!isset($subscribeInfo['subscribe_coupon']) || !$hash || $hash != $subscribeInfo['subscribe_coupon'])  $this->ajaxReturn($data=array('status'=>10051,'msg'=>'OK','data'=>'hash error'));
		} else {
            $user_id = $this->session->get('user_id');

            //状态改变
            $subscribeInfo = $this->subscribe->getEmailSubscribeInfoByCustomerId($user_id,1);
            if(isset($subscribeInfo['subscribe_email']))
                $email = $subscribeInfo['subscribe_email'];
        }
		if(!empty($subscribeInfo) && $subscribeInfo['subscribe_status']==1){
            $data['subscribe_status'] = 0;
            $data['customer_id'] = 0;
			$data['subscribe_source_cancel'] = NEWSLETTER_SUBSCRIBE_SOURCE_ACCOUNT;
			$data['subscribe_time_cancel'] = date('Y-m-d H:i:s',time());
			$data['subscribe_time_lastmodified'] = NOW;
			$this->subscribe->updateEmailSubscribe($email,$data);
            $this->ajaxReturn($data=array('status'=>200,'msg'=>'OK','data'=>null));
		} else {
            $this->ajaxReturn($data=array('status'=>10050,'msg'=>'OK','data'=>'mail error'));
        }
		
		//redirect(genURL('newsletter'));
	}
	
	//订阅
	/*public function subscribe(){
		if(!$this->customer->checkUserLogin()) {
			redirect(genURL('login'));
		}
		$email = $this->session->get('email');
		
		$this->load->model('subscribemodel','subscribe');
		$subscribeInfo = $this->subscribe->getEmailSubscribeInfo($email);
		//是否本人邮件
		
		if(empty($subscribeInfo)){//未曾订阅
			
		}
	}*/
	
}

/* End of file faq.php */
/* Location: ./application/controllers/default/faq.php */
