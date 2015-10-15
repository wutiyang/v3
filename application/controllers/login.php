<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Login extends Dcontroller {

    /**
     * @Desc 登录，注册入口
     * @see Dcontroller::index()
     */
    public function index(){
        //判断用户是否登录
        if($this->customer->checkUserLogin()) {
            redirect(genURL('order_list'));
        }

        //控制跳转路径
        $this->_view_data['refer'] = $this->input->get('refer');

        $this->_view_data['msg_login'] = $this->input->get('msg_login');
        $this->_view_data['msg_register_agreement'] = $this->input->get('msg_register_agreement');
        $this->_view_data['msg_register_username'] = $this->input->get('msg_register_username');
        $this->_view_data['msg_register_password'] = $this->input->get('msg_register_password');
        $this->_view_data['msg_register_email'] = $this->input->get('msg_register_email');
        $this->_view_data['msg_register_confirm_password'] = $this->input->get('msg_register_confirm_password');
        $this->_view_data['msg_register_email'] = $this->input->get('msg_register_email');

        parent::index();
    }

    /**
     * @desc 用户登录
     */
    public function authenticate(){
        $username = trim($this->input->post('user_name'));
        $password = trim($this->input->post('password'));
        $remember = trim($this->input->post('remember'));

        $remember = $remember == 'true'?true:false;

        $refer = $this->input->post('refer');

        $processArr = $this->processAuthenticate($username, $password, $remember);

        if($processArr['status'] == 1){
            if(!empty($refer)){
                redirect($refer);
            }else{
                redirect( genURL('order_list') );
            }
        }elseif($processArr['status'] == 0){
            redirect( genURL('login',false,array('msg_login'=>$processArr['message'])) );
        }
    }

    public function facebook(){
        $facebook_id = $this->input->post('facebook_id');
        $facebook_name = $this->input->post('facebook_name');
        $facebook_email = $this->input->post('facebook_email');
        if($facebook_name === false) $facebook_name = substr($facebook_email,0,strpos($facebook_email,'@'));

        $user = $this->customer->getUserBySSOId(SSO_TYPE_FACEBOOK,$facebook_id);

        $redirect_url = $this->input->post('refer');
        if($redirect_url===false) $redirect_url = genURL('order_list');

        if(empty($user)){
            $user = $this->customer->getUserByEmail( $facebook_email );
            if(empty($user)){
                $userByName = $this->customer->getUserByName($facebook_name);
                if(!empty($userByName)) $facebook_name .= time();

                //插入数据
                $userId = $this->customer->createUser(array(
                    'customer_name' => $facebook_name,
                    'customer_email' => $facebook_email,
                    'customer_password' => '',
                    'customer_time_create' => date('Y-m-d H:i:s',time()),
                    'customer_sso_type' => SSO_TYPE_FACEBOOK,
                    'customer_sso_id' => $facebook_id,
                    'customer_sso_email' => $facebook_email,
                    'customer_source' => $this->input->post('source'),
                    'customer_count_visit' => 1,
                    'customer_ip' => $this->input->ip_address(),
                    'customer_time_lastlogin' => date('Y-m-d H:i:s',time()),
                    'customer_status' => STATUS_ACTIVE ,
                ));
                $user = $this->customer->getUserById($userId);

                //登陆处理
                $this->session->set('user_id', $user['customer_id']);
                $this->session->set('user_name', $user['customer_name']);
                $this->session->set('email', $user['customer_email']);

                $tongjiUserData = array('UserID'=>$user['customer_id'],'HashedEmail'=>md5(strtolower($user['customer_email'])));
                $this->session->set('tongji_userdata', json_encode($tongjiUserData));

                //welcome_register邮件
                $this->load->model('emailmodel','emailmodel');
                $params = array(
                    'SITE_DOMAIN' => trim(genUrl(),'/'),
                    'CS_EMAIL' => 'cs@eachbuyer.com',
                    'SITE_DOMAIN1' => COMMON_DOMAIN,
                    'USER_NAME' => $user['customer_name'],
                    'ITEM_REO' => '',
                );
                $result = $this->emailmodel->subscribe_sendMail(13, currentLanguageId() ,$user['customer_email'],$params);

                //合并购物车
                $this->mergeCart();
            }else{
                $this->session->set('sso_facebook_id', $facebook_id);
                $this->session->set('sso_facebook_name', $facebook_name);
                $this->session->set('sso_facebook_email', $facebook_email);
                $this->session->set('sso_source', $this->input->post('source'));

                $redirect_url = genUrl('login_facebook');
            }
        }else{
            //将相关信息放入session(判断是否登录及获取用户信息)
            $this->session->set('user_id', $user['customer_id']);
            $this->session->set('user_name', $user['customer_name']);
            $this->session->set('email', $user['customer_email']);

            //统计信息 email小写
            $tongjiUserData = array('UserID'=>$user['customer_id'],'HashedEmail'=>md5(strtolower($user['customer_email'])));
            $this->session->set('tongji_userdata', json_encode($tongjiUserData));

            $this->customer->updateUserLoginInfo( $user['customer_id'] );

            //合并购物车
            $this->mergeCart();
        }

        $this->ajaxReturn(array(
            'status'=>200,
            'msg'=>'',
            'data'=>array('url' => $redirect_url)
        ));
    }

    /**
     * ajax形式登陆验证
     *
     */
    public function ajaxAuthenticate(){
        $arr = array('status'=>200,'msg'=>'','data'=>array(),'redirect_url'=>'');

        $username = trim($this->input->post('user_name'));
        $password = trim($this->input->post('password'));
        $remember = trim($this->input->post('remember'));

        $remember = $remember == 'true'?true:false;

        $processArr = $this->processAuthenticate($username, $password, $remember);

        if($processArr['status'] == 1){
            $arr['msg'] = 'login success';
            $controller = explode('?',$this->input->post('loadUrl'));
            $controller = $controller[0];
            if(isset($this->cart) && !$this->cart->getCartMerge() && $controller == 'cart') $arr['redirect_url'] = genURL('place_order');
        }elseif($processArr['status'] == 0){
            $arr['status'] = 2200;
            $arr['msg'] = $processArr['message'];
        }

        $this->ajaxReturn($arr);
    }

    private function processAuthenticate($username,$password,$remember){
        $processArr = array('status' => 0,'message'=>'');
        //根据传入的信息获取用户信息
        $user = array();
        if(is_email($username)){
            $user = $this->customer->getUserByEmail( $username );
        }else{
            $user = $this->customer->getUserByName( $username );
        }

        //校验用户名密码
        if( empty( $user ) || !$this->customer->validatePassword( $user['customer_password'], $password )){
            $processArr['message'] = lang('p_login_failure');
            return $processArr;
        }

        if($user['customer_status'] != 1){
            $processArr['message'] = 'account status wrong!';
            return $processArr;
        }

        //将相关信息放入session(判断是否登录及获取用户信息)
        $this->session->set('user_id', $user['customer_id']);
        $this->session->set('user_name', $user['customer_name']);
        $this->session->set('email', $user['customer_email']);

        //统计信息 email小写
        $tongjiUserData = array('UserID'=>$user['customer_id'],'HashedEmail'=>md5(strtolower($user['customer_email'])));
        $this->session->set('tongji_userdata', json_encode($tongjiUserData));

        $this->customer->updateUserLoginInfo( $user['customer_id'] );

        //是否记住用户名及密码
        if($remember){
            $expire = time() + 3600 * 24 * 15;
            //TODO
            setcookie('ECS[user_id]',$user['customer_id'],$expire,'/',COMMON_DOMAIN);
            setcookie('ECS[user_name]',$user['customer_name'],$expire,'/',COMMON_DOMAIN);
            setcookie('ECS[password]',$user['customer_password'],$expire,'/',COMMON_DOMAIN);

            // set_cookie('ECS[user_id]',$user['customer_id'],$expire);
            // set_cookie('ECS[user_name]',$user['customer_name'],$expire);
            // set_cookie('ECS[password]',$user['customer_password'],$expire);
        }else{

            // unset_cookie('ECS[customer_id]');
            // unset_cookie('ECS[customer_name]');
            // unset_cookie('ECS[customer_password]');
            setcookie('ECS[user_id]','',time()-1,'/',COMMON_DOMAIN);
            setcookie('ECS[user_name]','',time()-1,'/',COMMON_DOMAIN);
            setcookie('ECS[password]','',time()-1,'/',COMMON_DOMAIN);

        }

        //合并购物车
        $this->mergeCart();

        $processArr['status'] = 1;
        return $processArr;
    }

    /**
     * @desc 注册
     */
    public function register(){
        //获取传递参数
        $username = htmlspecialchars(trim($this->input->post('user_name')));
        $password = trim($this->input->post('password'));
        $confirmPassword = trim($this->input->post('confirm_password'));
        $email = trim($this->input->post('email'));
        $agreement = trim($this->input->post('agreement'));
        $subscribe = trim($this->input->post('subscribe'));

        $regFrom = $this->input->post('reg_from');

        $refer = $this->input->post('refer');

        $processArr = $this->processRegister($username,$password,$confirmPassword,$email,$agreement,$subscribe,$regFrom);

        if($processArr['status'] == 1){
            if(!empty($refer)){
                redirect($refer);
            }else{
                redirect( genURL('order_list') );
            }
        }elseif($processArr['status'] == 0){
            redirect( genURL('login',false,array($processArr['message_name']=>$processArr['message'])) );
        }
    }

    /**
     * ajax形式请求注册
     *
     */
    public function ajaxRegister(){
        $arr = array('status'=>200,'msg'=>'','data'=>array());
        //获取传递参数
        $username = htmlspecialchars(trim($this->input->post('user_name')));
        $password = trim($this->input->post('password'));
        $confirmPassword = trim($this->input->post('confirm_password'));
        $email = trim($this->input->post('email'));
        $agreement = trim($this->input->post('agreement'));
        $subscribe = trim($this->input->post('subscribe'));

        $regFrom = $this->input->post('reg_from');

        $processArr = $this->processRegister($username,$password,$confirmPassword,$email,$agreement,$subscribe,$regFrom);

        if($processArr['status'] == 1){
            $arr['msg'] = 'success';
            $this->load->model("cartmodel","cart");
            //注册位置参数处理
            $controller = explode('?',$this->input->post('loadUrl'));
            $controller = $controller[0];
            if(isset($this->cart) && !$this->cart->getCartMerge() && $controller == 'cart') $arr['redirect_url'] = genURL('place_order');
            //var_dump(isset($this->cart),!$this->cart->getCartMerge(),$controller == 'cart');
        }elseif($processArr['status'] == 0){
            $arr['status'] = 2200;
            $arr['msg'] = $processArr['message'];
        }

        $this->ajaxReturn($arr);
    }

    private function processRegister($username,$password,$confirmPassword,$email,$agreement,$subscribe,$regFrom){
        $processArr = array('status' => 0,'message'=>'','message_name'=>'');
        //校验传递数据
        if($agreement === false){
            $processArr['message'] = lang('p_agreement');
            $processArr['message_name'] = 'msg_register_agreement';
            return $processArr;
        }
        if($username === false || strlen($username) < 3){
            $processArr['message'] = lang('p_username_shorter');
            $processArr['message_name'] = 'msg_register_username';
            return $processArr;
        }
        if(preg_match('/\'\/^\\s*$|^c:\\\\con\\\\con$|[%,\\*\\"\\s\\t\\<\\>\\&\'\\\\]/',$username)){
            $processArr['message'] = lang('username_invalid');
            $processArr['message_name'] = 'msg_register_username';
            return $processArr;
        }
        if($email === false || strlen($email) < 3 || !is_email($email)){
            $processArr['message'] = lang('email_invalid');
            $processArr['message_name'] = 'msg_register_email';
            return $processArr;
        }
        if($confirmPassword != $password){
            $processArr['message'] = lang('confirm_password_invalid');
            $processArr['message_name'] = 'msg_register_confirm_password';
            return $processArr;
        }
        if($password === false || strlen($password) < 6){
            $processArr['message'] = lang('p_password_shorter');
            $processArr['message_name'] = 'msg_register_password';
            return $processArr;
        }
        if(strpos($password,' ') > 0){
            $processArr['message'] = lang('p_passwd_blank');
            $processArr['message_name'] = 'msg_register_password';
            return $processArr;
        }
        if($this->customer->checkUserNameUsed($username)){
            $processArr['message'] = lang('username_exist');
            $processArr['message_name'] = 'msg_register_username';
            return $processArr;
        }
        if($this->customer->checkEmailUsed($email)){
            $processArr['message'] = lang('email_exist');
            $processArr['message_name'] = 'msg_register_email';
            return $processArr;
        }
        $regFrom = in_array($regFrom, array(1,2,3,4))?$regFrom:1;

        //插入数据
        $userId = $this->customer->createUser(array(
            'customer_name' => $username,
            'customer_email' => $email,
            'customer_password' => $this->customer->hashPassword($password),
            'customer_time_create' => date('Y-m-d H:i:s',time()),
            'customer_source' => $regFrom,
            'customer_count_visit' => 1,
            'customer_ip' => $this->input->ip_address(),
            'customer_time_lastlogin' => date('Y-m-d H:i:s',time()),
            'customer_status' => 1 ,
        ));
        $user = $this->customer->getUserById($userId);

        //登陆处理
        $this->session->set('user_id', $user['customer_id']);
        $this->session->set('user_name', $user['customer_name']);
        $this->session->set('email', $user['customer_email']);

        $tongjiUserData = array('UserID'=>$user['customer_id'],'HashedEmail'=>md5(strtolower($user['customer_email'])));
        $this->session->set('tongji_userdata', json_encode($tongjiUserData));

        $this->customer->updateUserLoginInfo( $user['customer_id'] );

        global $base_url;//多语言网址数组

        $this->load->model('emailmodel','emailmodel');

        //加入订阅信息
        if($subscribe){
            $this->load->model('subscribemodel','subscribe');
            $subscribeInfo = $this->subscribe->getEmailSubscribeInfo($email);
            if(empty($subscribeInfo)){
                //更新subscribe表
                $data['subscribe_email'] = $email;
                $data['language_id'] = currentLanguageId();
                $data['subscribe_status'] = 0;
                $data['subscribe_coupon'] = substr(md5($_SERVER['REQUEST_TIME']),1,10);
                $data['subscribe_source_add'] = NEWSLETTER_SUBSCRIBE_SOURCE_REGISTER;
                //$data['subscribe_time_add'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
                $data['customer_id'] = $user['customer_id'];
                $data['subscribe_ip'] = $this->input->ip_address();
                $data['subscribe_time_lastmodified'] = NOW;
                $this->subscribe->createEmailSubscribe($data);

                //发送校验邮件
                $this->load->model('emailtemplatemodel','emailtemplate');
                $email_type = 10;
                $email_template_info = $this->emailtemplate->getSystemEmailTemplateInfo( $email_type, currentLanguageId() );


                $type = 14;
                $params = array(
                    'SITE_DOMAIN' => substr($base_url[currentLanguageId()],0,-1),
                    'SITE_DOMAIN1' => COMMON_DOMAIN,
                    'USER_NAME' => $username,
                    'SUBSCRIBE_LINK' => genURL('common/validateSubscribe').'?email='.encrypt($email).'&utm_source=System_Own&utm_medium=Email&utm_campaign=Newsletter-confirm&utm_nooverride=1',
                    'CS_EMAIL' => 'cs@eachbuyer.com',
                    //'ITEM_REO' =>'',
                );
                $result = $this->emailmodel->subscribe_sendMail( $type, currentLanguageId() ,$email,$params);

                $this->session->set('newsletter_msg_email',$email);
            }elseif (!empty($subscribeInfo) && ($subscribeInfo['subscribe_status']!=1 || $subscribeInfo['customer_id'] == 0)){
                //更新表
                $data['subscribe_status'] = 1;
                $data['subscribe_email'] = $email;
                $data['language_id'] = currentLanguageId();
                $data['subscribe_source_add'] = NEWSLETTER_SUBSCRIBE_SOURCE_REGISTER;
                $data['subscribe_time_add'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
                $data['subscribe_ip'] = $this->input->ip_address();
                $data['subscribe_time_lastmodified'] = NOW;
                //已有邮箱不属于本网站的用户时修改订阅用户ID
                if($subscribeInfo['customer_id'] == 0){
                    $data['customer_id'] = $user['customer_id'];
                }

                $this->subscribe->updateEmailSubscribe($email,$data);

                if ($subscribeInfo['subscribe_status_validate'] != 1) {
                    //发送校验邮箱
                    $type = 14;
                    $params = array(
                        'SITE_DOMAIN' => substr($base_url[currentLanguageId()],0,-1),
                        'SITE_DOMAIN1' => COMMON_DOMAIN,
                        'USER_NAME' => $username,
                        'SUBSCRIBE_LINK' => genURL('common/validateSubscribe') . '?email=' . encrypt($email) . '&utm_source=System_Own&utm_medium=Email&utm_campaign=Newsletter-confirm&utm_nooverride=1',
                        'CS_EMAIL' => 'cs@eachbuyer.com',
                        //'ITEM_REO' =>'',
                    );
                    $result = $this->emailmodel->subscribe_sendMail($type, currentLanguageId(), $email, $params);
                } else {
                    $type = 15;
                    $params = array(
                        'SITE_DOMAIN' => substr($base_url[currentLanguageId()],0,-1),
                        'SITE_DOMAIN1' => COMMON_DOMAIN,
                        'USER_NAME' => $username,
                        //'SUBSCRIBE_LINK' => genURL('common/validateSubscribe').'?email='.encrypt($email).'&utm_source=System_Own&utm_medium=Email&utm_campaign=Newsletter-confirm&utm_nooverride=1',
                        'CS_EMAIL' => 'cs@eachbuyer.com',
                        //'ITEM_REO' =>'',
                    );
                    $result = $this->emailmodel->subscribe_sendMail($type, currentLanguageId(), $email, $params);
                }
            }else{

                $type = 15;
                $params = array(
                    'SITE_DOMAIN' => substr($base_url[currentLanguageId()],0,-1),
                    'SITE_DOMAIN1' => COMMON_DOMAIN,
                    'USER_NAME' => $username,
                    //'SUBSCRIBE_LINK' => genURL('common/validateSubscribe').'?email='.encrypt($email).'&utm_source=System_Own&utm_medium=Email&utm_campaign=Newsletter-confirm&utm_nooverride=1',
                    'CS_EMAIL' => 'cs@eachbuyer.com',
                    //'ITEM_REO' =>'',
                );
                $result = $this->emailmodel->subscribe_sendMail($type, currentLanguageId(), $email, $params);
            }
        }else{
            //welcome_register邮件
            $type = 13;
            $params = array(
                'SITE_DOMAIN' => substr($base_url[currentLanguageId()],0,-1),
                'CS_EMAIL' => 'cs@eachbuyer.com',
                'SITE_DOMAIN1' => COMMON_DOMAIN,
                'USER_NAME' => $user['customer_name'],
                'ITEM_REO' => '',
            );
            $result = $this->emailmodel->subscribe_sendMail( $type, currentLanguageId() ,$user['customer_email'],$params);
        }

        //合并购物车
        $this->mergeCart();

        $processArr['status'] = 1;
        return $processArr;
    }

    /**
     * 检查用户名是否可用
     */
    public function checkUserNameAvailable(){
        $arr = array('status'=>200,'msg'=>'','data'=>array());
        $userName = $this->input->post('user_name');
        $userName = trim($userName);

        if(!$this->customer->checkUserNameUsed($userName)){
            $arr['msg'] = '';
        }else{
            $arr['status'] = 2200;
            $arr['msg'] = lang('username_exist');
        }

        $this->ajaxReturn($arr);
    }

    /**
     * 检查Email是否可用
     */
    public function checkEmailAvailable(){
        $arr = array('status'=>200,'msg'=>'','data'=>array());
        $email = $this->input->post('email');
        $email = trim($email);

        if(!is_email( $email ) ){
            $arr['msg'] = lang('email_invalid');
        }elseif(is_email($email) && !$this->customer->checkEmailUsed($email)){
            $arr['msg'] = '';
        }else{
            $arr['status'] = 2200;
            $arr['msg'] = lang('email_exist');
        }

        $this->ajaxReturn($arr);
    }

    /**
     * 检查Email是否可用
     */
    public function checkPasswordAvailable(){
        $arr = array('status'=>200,'msg'=>'','data'=>array());
        $userName = $this->input->post('username');
        $password = $this->input->post('password');
        $userName = trim($userName);
        $password = trim($password);

        $user = array();
        if(is_email($userName)){
            $user = $this->customer->getUserByEmail( $userName );
        }else{
            $user = $this->customer->getUserByName( $userName );
        }

        //校验用户名密码
        if( empty( $user ) || !$this->customer->validatePassword( $user['customer_password'], $password )){
            $arr['status'] = 2200;
            $arr['msg'] = lang('p_login_failure');
        }

        $this->ajaxReturn($arr);
    }

    public function logout(){
        $this->session->delete('user_id');
        $this->session->delete('user_name');
        $this->session->delete('email');
        $this->session->delete('tongji_userdata');

        // unset_cookie('ECS[customer_id]');
        // unset_cookie('ECS[customer_name]');
        // unset_cookie('ECS[customer_password]');

        setcookie('ECS[user_id]','',time()-1,'/',COMMON_DOMAIN);
        setcookie('ECS[user_name]','',time()-1,'/',COMMON_DOMAIN);
        setcookie('ECS[password]','',time()-1,'/',COMMON_DOMAIN);

        redirect( genURL('/') );
    }

    /**
     * @desc 合并收藏
     */
    public function mergeWishlist(){
        $session_wishlist_list = $this->session->get("ebwishlist");
        if(!empty($session_wishlist_list)){

        }

        return true;

    }

    /**
     * @desc 合并购物车
     */
    private function mergeCart(){
        if($this->customer->checkUserLogin()){
            $user_id = $this->session->get("user_id");
            $session_id = $this->session->sessionID();

            $this->load->model("cartmodel","cart");

            $result = $this->cart->mergeCart($user_id,$session_id);
        }
    }
}
