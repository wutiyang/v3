<?php
/**
* 个人中心语言包.整理后的键值定义规则，页面title以t_开头，菜单以m_开头，标签以l_开头，js信息提示以j_开头，php信息提示以p_开头
* language: en
* @Author : yuandd
* @Last Modify : 2013-07-04
*/

//杂项
$lang['require_login'] = 'Illegal entry.<br />You can\'t finish the operation until login.'; //非法访问路径
$lang['require_tips'] = 'Required Fields';
$lang['l_back'] = 'Back';
$lang['l_save'] = 'Save';
$lang['l_edit'] = 'Edit';

$lang['warning']['remove_item']='Are you sure you want to remove it?';
$lang['confirm']['yes']='Yes';
$lang['confirm']['no']='No';

//登录注册页面
$lang['t_login_register'] = 'Customer login or create new customer account';
$lang['l_login'] = 'Registered Customers';
$lang['l_login_username'] = 'Email Address/Nickname';
$lang['l_login_psw'] = 'Password';
$lang['l_login_forgotpsw'] = 'Forgot Your Password?';

$lang['l_register'] = 'Personal Information';
$lang['l_register_nickname'] = 'Nickname';
$lang['l_register_email'] = 'Email Address';
$lang['l_register_psw'] = 'Password';
$lang['l_register_confim'] = 'Confirm Password';
$lang['l_verification_code'] = 'Verification code';
$lang['l_register_agree'] = 'I agree to ' . ucfirst( COMMON_DOMAIN );
$lang['l_register_agree_sub'] = 'Subscribe to save money with coupons on newsletter every week';
$lang['l_register_terms'] = 'Terms and Conditions.';
$lang['l_register_tips'] = 'After registering, you will receive our newsletters with information about sales, coupons, and special promotions. You can unsubscribe in My Account.';

$lang['p_username_please'] = 'please input the user name';
$lang['p_captcha_invalid'] = 'Invalid entry, please try again.';
$lang['p_login_failure'] = 'Invalid login or password.'; //登录失败
$lang['p_register_fail'] = 'Register failed,please try again.'; //注册失败
$lang['p_agreement'] = 'You do not agree with the agreement'; //用户协议未勾选
$lang['p_username_shorter'] = 'The nickname must have at least 3 characters.';
$lang['p_password_shorter'] = 'The password must have at least 6 characters.';
$lang['p_passwd_blank'] = 'The password entered can`t have blank.';
$lang['p_reset_password'] = 'You will receive an email with a link to reset your password.'; //邮箱找回密码信息发送提示
$lang['p_fail_send_password'] = 'Failure, please contact with administrator!'; //发送重设密码邮件失败
$lang['p_parm_error'] = 'Error, Please return!'; //重设密码时，错误的用户名或code
$lang['p_reset_psw_success'] = 'Reset password success.'; //重设密码成功

//注册页面和会员中心js语言包
$lang['username_exist'] = 'This customer Nickname already exists';
$lang['email_exist'] = 'There is already an account with this email address.';

$lang['required']='This is a required field.';
$lang['username_shorter'] = 'Please enter 3 or more characters. Leading or trailing spaces will be ignored.';
$lang['username_invalid'] = 'Nickname only can be composed of letters, figure and underline.';
$lang['password_shorter'] = 'Please enter 6 or more characters. Leading or trailing spaces will be ignored.';
$lang['email_invalid'] = 'Please enter a valid email address. For example johndoe@domain.com.';
$lang['confirm_password_invalid'] = 'Please make sure your passwords match.';
$lang['confirm_del_wl']='Are you sure you want to remove this product from your wishlist?';

//退出登录页面
$lang['t_logout'] = 'Customer logout';
$lang['l_logout_h1'] = 'You are now logged out';
$lang['l_logout_tips'] = 'You have logged out and will be redirected to our homepage in <span id="timer">3</span> seconds.';
