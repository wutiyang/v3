<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';
/**
 * @desc 首页入口  newsletter
 * @author wty
 *
 */
class Common extends Dcontroller {
	//订阅
	public function subscribe(){
		//$this->load->language('newsletter',currentLanguageCode());
		
		$subscribe_point = 10;
		$email = $this->input->post('email');
		
		$this->load->model('Emailmodel','m_email');
		if(!is_email($email) || empty($email)){
			$return_data['status'] = 1009;
			$return_data['msg'] = 'Please enter a valid email address or try different one.';
			$return_data['data'] = null;
			$this->ajaxReturn($return_data);
		}
		
		//获取订阅信息
		$this->load->model('subscribemodel','subscribe');
		$subscribeInfo = $this->subscribe->getEmailSubscribeInfo($email);
        //获取当前用户id
        $user_id = $this->session->get('user_id');
        //自动校验功能
        $this->load->model('ordermodel','order');
        $paid_order = $this->order->getPaidOrderByEmail($email);
		if(empty($subscribeInfo)){
			//更新subscribe表
			$data['subscribe_email'] = $email;
            $data['customer_id'] = 0;
			$data['language_id'] = currentLanguageId();
            //添加自动校验的订阅功能
            if(!empty($paid_order)){
                $data['subscribe_status'] = 1;
                $data['subscribe_status_validate'] = 1;
                $data['subscribe_time_add'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
                $data['subscribe_time_coupon'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']+SUBSCRIBE_COUPON_TIME);
            } else {
                $data['subscribe_status'] = 0;
            }
			$data['subscribe_coupon'] = substr(md5($_SERVER['REQUEST_TIME']),1,10);
			$data['subscribe_source_add'] = NEWSLETTER_SUBSCRIBE_SOURCE_FOOTER; 
//			$data['subscribe_time_add'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
			$data['subscribe_ip'] = $this->input->ip_address();
			$data['subscribe_time_lastmodified'] = NOW;
			$this->subscribe->createEmailSubscribe($data);			
			
			//发送校验邮件
			$this->load->model('emailtemplatemodel','emailtemplate');
			$email_type = 10;
			$email_template_info = $this->emailtemplate->getSystemEmailTemplateInfo( $email_type, currentLanguageId() );
			
			$this->load->model('emailmodel','emailmodel');
            //添加自动校验的订阅功能
            if(!empty($paid_order)){
                $type = 11;
                global $base_url;
                $params = array(
                    'SITE_DOMAIN' => substr($base_url[currentLanguageId()],0,-1),
                    'SITE_DOMAIN1' => COMMON_DOMAIN,
                    'USER_NAME' => 'Subscriber',
                    //'SUBSCRIBE_LINK' => genURL('common/validateSubscribe').'?email='.encrypt($email).'&utm_source=System_Own&utm_medium=Email&utm_campaign=Newsletter-confirm&utm_nooverride=1',
                    'CS_EMAIL' => 'cs@eachbuyer.com',
                );
                $coupon = $subscribeInfo['subscribe_coupon'];
                $params['COUPON_EDM'] = $coupon;
                $result = $this->emailmodel->subscribe_sendMail( $type, currentLanguageId() ,$email,$params);
            } else {
                $type = 10;
                global $base_url;
                $params = array(
                    'SITE_DOMAIN' => substr($base_url[currentLanguageId()],0,-1),
                    'SITE_DOMAIN1' => COMMON_DOMAIN,
                    'USER_NAME' => 'Subscriber',
                    'SUBSCRIBE_LINK' => genURL('common/validateSubscribe').'?email='.encrypt($email).'&utm_source=System_Own&utm_medium=Email&utm_campaign=Newsletter-confirm&utm_nooverride=1',
                    'CS_EMAIL' => 'cs@eachbuyer.com',
                    //'ITEM_REO' =>'',
                );
                $result = $this->emailmodel->subscribe_sendMail( $type, currentLanguageId() ,$email,$params);
            }
			
			$this->session->set('newsletter_msg_email',$email);
			$return_data['status'] = 200;
			$return_data['msg'] = 'OK';
            //添加自动校验的订阅功能
            if(!empty($paid_order)){
                $this->session->set('newsletter_first',false);
                $return_data['data'] = array('url'=>genURL('newsletter_success'));
            } else {
                $return_data['data'] = array('url'=>genURL('newsletter_result'));
            }
			$this->ajaxReturn($return_data);
		}elseif (!empty($subscribeInfo) &&  $subscribeInfo['subscribe_status']!=1){
			//更新表
			$data['subscribe_status'] = 1;
			$data['subscribe_email'] = $email;
			$data['language_id'] = currentLanguageId();
            //已有邮箱不属于本网站的用户时修改订阅用户ID
            if($subscribeInfo['customer_id'] == 0) {
                $data['customer_id'] =  0;
            }
			$data['subscribe_source_add'] = NEWSLETTER_SUBSCRIBE_SOURCE_FOOTER;
			$data['subscribe_ip'] = $this->input->ip_address();
			$data['subscribe_time_lastmodified'] = NOW;
//			$data['subscribe_time_add'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);

            //添加自动校验的订阅功能
            if(!empty($paid_order)){
                $subscribeInfo['subscribe_status_validate'] = 1;
                $data['subscribe_status_validate'] = 1;
                $data['subscribe_time_add'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
                $data['subscribe_time_coupon'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']+SUBSCRIBE_COUPON_TIME);
            }

			$this->subscribe->updateEmailSubscribe($email,$data);
			$this->load->model('emailmodel','emailmodel');
            if($subscribeInfo['subscribe_status']!=1){
                if($subscribeInfo['subscribe_status_validate']!=1){
                    //发送校验邮箱
                    $type = 10;
                    global $base_url;
                    $params = array(
                        'SITE_DOMAIN' => substr($base_url[currentLanguageId()],0,-1),
                        'SITE_DOMAIN1' => COMMON_DOMAIN,
                        'USER_NAME' => 'Subscriber',
                        'SUBSCRIBE_LINK' => genURL('common/validateSubscribe').'?email='.encrypt($email).'&utm_source=System_Own&utm_medium=Email&utm_campaign=Newsletter-confirm&utm_nooverride=1',
                        'CS_EMAIL' => 'cs@eachbuyer.com',
                        //'ITEM_REO' =>'',
                    );
                    $result = $this->emailmodel->subscribe_sendMail( $type, currentLanguageId() ,$email,$params);
                }else{
                    $type = 12;
                    global $base_url;
                    $params = array(
                        'SITE_DOMAIN' => substr($base_url[currentLanguageId()],0,-1),
                        'SITE_DOMAIN1' => COMMON_DOMAIN,
                        'USER_NAME' => 'Subscriber',
                        //'SUBSCRIBE_LINK' => genURL('common/validateSubscribe').'?email='.encrypt($email).'&utm_source=System_Own&utm_medium=Email&utm_campaign=Newsletter-confirm&utm_nooverride=1',
                        'CS_EMAIL' => 'cs@eachbuyer.com',
                        //'ITEM_REO' =>'',
                    );
                    $result = $this->emailmodel->subscribe_sendMail( $type, currentLanguageId() ,$email,$params);
                }
            }
			
			//直接跳转到二次成功页
			$return_data['status'] = 200;
			$return_data['msg'] = 'OK';
            $this->session->set('newsletter_msg_email',$email);
            if($subscribeInfo['subscribe_status_validate']==1){
                $this->session->set('newsletter_first',false);
                $return_data['data'] = array('url'=>genURL('newsletter_success'));
            } else {
                $return_data['data'] = array('url'=>genURL('newsletter_result'));
            }

			$this->ajaxReturn($return_data);
		}elseif(!empty($subscribeInfo) && $subscribeInfo['subscribe_status']==1){
//            if($subscribeInfo['customer_id'] == 0) {
//                $data['customer_id'] = $user_id ? $user_id : 0;
//                $this->subscribe->updateEmailSubscribe($email,$data);
//            }
			//已经订阅,提示已经订阅
			$return_data['status'] = 1008;
			$return_data['msg'] = lang('newsletter_msg');
			$return_data['data'] = null;
			$this->ajaxReturn($return_data);
		}else{
			$return_data['status'] = 1009;
			$return_data['msg'] = 'Error';
			$return_data['data'] = null;
			$this->ajaxReturn($return_data);
		}

	}

	//校验订阅（from邮件中）
	public function validateSubscribe(){
		$this->load->language('newsletter',currentLanguageCode());
		
		$email = $this->input->get('email');
		//ZWFjaGJ1eWVyX2JXRjBkQT09OnRpbV93dXRpeWFuZ0AxNjMuY29t
		$email = decrypt($email);
		if(!is_email($email) || empty($email)){
			redirect(genURL('/'));
		}

		$this->load->model('subscribemodel','subscribe');
		$this->load->model('Emailmodel','m_email');
		$subscribeInfo = $this->subscribe->getEmailSubscribeInfo($email);
		if(empty($subscribeInfo)){
			redirect(genURL('/'));
		}

		//跳转页面
		if($subscribeInfo['subscribe_source_add'] == NEWSLETTER_SUBSCRIBE_SOURCE_ACCOUNT){
			$redirect_target = 'newsletter';
		}else{
			$redirect_target = 'newsletter_success';
		}
		
		$this->load->model('emailmodel','emailmodel');
		$type = 11;
        global $base_url;
        $params = array(
            'SITE_DOMAIN' => substr($base_url[currentLanguageId()],0,-1),
            'SITE_DOMAIN1' => COMMON_DOMAIN,
				'USER_NAME' => 'Subscriber',
				//'SUBSCRIBE_LINK' => genURL('common/validateSubscribe').'?email='.encrypt($email).'&utm_source=System_Own&utm_medium=Email&utm_campaign=Newsletter-confirm&utm_nooverride=1',
				'CS_EMAIL' => 'cs@eachbuyer.com',
		);
		
		//校验，说明该邮件肯定是第一次订阅：则发送coupon)
		if($subscribeInfo['subscribe_status']!=1 || $subscribeInfo['subscribe_status_validate'] != 1){
            $data['subscribe_status_validate'] = 1;
            $data['subscribe_status'] = 1;
            $data['subscribe_time_add'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
            $data['subscribe_time_coupon'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']+SUBSCRIBE_COUPON_TIME);
			$data['subscribe_ip'] = $this->input->ip_address();
			$data['subscribe_time_lastmodified'] = NOW;
			$this->subscribe->updateEmailSubscribe($email,$data);
			$coupon = $subscribeInfo['subscribe_coupon'];
			$params['COUPON_EDM'] = $coupon;
			
		}
        if($subscribeInfo['subscribe_status_validate'] == 0){
            $this->session->set('newsletter_first',true);
            $result = $this->emailmodel->subscribe_sendMail( $type, currentLanguageId() ,$email,$params);
        } else {
            $this->session->set('newsletter_first',false);
        }
        $this->session->set('newsletter_msg_email',$email);

		redirect(genURL($redirect_target));
	}

	//取消订阅
	/*public function unsubscribe(){
		//$this->load->language('newsletter',currentLanguageCode());
		$email = $this->input->get('email');
		$hash = $this->input->get('hash');

		$this->load->model('subscribemodel','subscribe');
		$subscribeInfo = $this->subscribe->getEmailSubscribeInfo($email);

		//存在该订阅
		if(!empty($subscribeInfo) && $subscribeInfo['subscribe_status'] == 1){
			//更新表
			$data['subscribe_status'] = 0;
			$data['subscribe_source_cancel'] = NEWSLETTER_SUBSCRIBE_SOURCE_FOOTER;///???????
			$data['subscribe_time_cancel'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
			$this->subscribe->updateEmailSubscribe($email,$data);
			
			//跳转取消成功页
			//?????
		}else{
			redirect(genURL('/'));
		}
		
		//redirect(genURL('newsletter_result').'?type=unsub_s');
	}*/

}

/* End of file common.php */
/* Location: ./application/controllers/default/common.php */
