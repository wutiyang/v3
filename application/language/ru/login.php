<?php
/**
* 个人中心语言包.整理后的键值定义规则，页面title以t_开头，菜单以m_开头，标签以l_开头，js信息提示以j_开头，php信息提示以p_开头
* language: ru
* @Author : yuandd
* @Last Modify : 2013-07-04
* ps:请翻译带有 “//translate”注释的语言内容，并将此注释删除掉
*/

//杂项
/* fujia 2013-07-09 */
$lang['require_login'] = 'Ошибка входа. <br/> Вы не можете продолжить без регистрации'; //非法访问路径
$lang['require_tips'] = 'Обязательные поля';
$lang['l_back'] = 'Вернуться';
$lang['l_save'] = 'Сохранить';
$lang['l_edit'] = 'Редактировать';

$lang['warning']['remove_item']='Вы уверены, что хотите удалить?';
$lang['confirm']['yes']='Да';
$lang['confirm']['no']='нет';

//登录注册页面
$lang['t_login_register'] = 'Войти или зарегистрироваться';
$lang['l_login'] = 'Зарегистрированный пользователь';
$lang['l_login_username'] = 'Email адрес/ Псевдоним пользователя';
$lang['l_login_psw'] = 'Пароль';
$lang['l_login_forgotpsw'] = 'Забыли пароль?';

$lang['l_register'] = 'Личная информация';
$lang['l_register_nickname'] = 'Псевдоним пользователя';
$lang['l_register_email'] = 'Email адрес';
$lang['l_register_psw'] = 'Пароль';
$lang['l_register_confim'] = 'Подтвердите пароль';
$lang['l_verification_code'] = 'Проверочный код';
$lang['l_register_agree'] = 'Я принимаю Общие положения и условия ' . ucfirst( COMMON_DOMAIN );
$lang['l_register_agree_sub'] = 'Подпишитесь, чтобы сэкономить с помощью купонов каждую неделю';
$lang['l_register_terms'] = 'Условия пользования.';
$lang['l_register_tips'] = 'После регистрации, Вы будете получать рассылку с информацией о продажах, купонах и специальных акциях. Отменить подписку можно в Личном кабинете.';

$lang['p_captcha_invalid']='Неправильный ввод, пожалуйста, попробуйте еще раз';
$lang['p_login_failure'] = 'Неверный Логин или пароль.'; //登录失败
$lang['p_register_fail'] = 'Register failed,please try again.'; //注册失败
$lang['p_agreement'] = 'You do not agree with the agreement'; //用户协议未勾选
$lang['p_username_shorter'] = 'Прозвище должно быть не менее 3 символов.';
$lang['p_password_shorter'] = 'Пароль должен содержать не менее 6 символов.';
$lang['p_passwd_blank'] = 'The password entered can`t have blank.';
$lang['p_reset_password'] = 'You will receive an email with a link to reset your password.'; //邮箱找回密码信息发送提示
$lang['p_fail_send_password'] = 'Failure, please contact with administrator!'; //发送重设密码邮件失败
$lang['p_parm_error'] = 'Error, Please return!'; //重设密码时，错误的用户名或code
$lang['p_reset_psw_success'] = 'Reset password success.'; //重设密码成功

//注册页面和会员中心js语言包
$lang['username_exist'] = 'This customer Nickname already exists';
$lang['email_exist'] = 'Существует уже учетная запись с таким адресом электронной почты.';

$lang['required']='This is a required field.';
$lang['username_shorter'] = 'Please enter 3 or more characters. Leading or trailing spaces will be ignored.';
$lang['username_invalid'] = 'Nickname only can be composed of letters, figure and underline.';
$lang['password_shorter'] = 'Please enter 6 or more characters. Leading or trailing spaces will be ignored.';
$lang['email_invalid'] = 'Пожалуйста, введите верный адрес. Например johndoe@domain.com.';
$lang['confirm_password_invalid'] = 'Пожалуйста, убедитесь, что ваши пароли совпадают.';

//退出登录页面
$lang['t_logout'] = 'Выйти';
$lang['l_logout_h1'] = 'Вы вышли';
$lang['l_logout_tips'] = 'Вы вышли из системы и будете перенаправлены на главную страницу в течение <span id="timer">3</span> секунд.';
