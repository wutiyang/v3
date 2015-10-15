<?php
/**
* 个人中心语言包.整理后的键值定义规则，页面title以t_开头，菜单以m_开头，标签以l_开头，js信息提示以j_开头，php信息提示以p_开头
* language: es
* @Author : yuandd
* @Last Modify : 2013-07-04
* ps:请翻译带有 “//translate”注释的语言内容，并将此注释删除掉
*/

//杂项
/* fujia 2013-07-09 */
$lang['require_login'] = 'L\'entrée illégale. <br/>Vous ne pouvez pas finir les opérations avant la connexion.'; //非法访问路径
$lang['require_tips'] = 'Campos requeridos';
$lang['l_back'] = 'Volver atrás';
$lang['l_save'] = 'Guardar';
$lang['l_edit'] = 'Editar';

$lang['warning']['remove_item']='¿Seguro que deseas eliminarlo?';
$lang['confirm']['yes']='sí';
$lang['confirm']['no']='No';

//登录注册页面
$lang['t_login_register'] = 'Connexion ou créer un nouveau compte';
$lang['l_login'] = 'Clientes Registrados';
$lang['l_login_username'] = 'Dirección de correo electrónico/ Apodo';
$lang['l_login_psw'] = 'Contraseña';
$lang['l_login_forgotpsw'] = 'Olvidó su contraseña?';

$lang['l_register'] = 'Información personal';
$lang['l_register_nickname'] = 'Apodo';
$lang['l_register_email'] = 'Dirección de correo electrónico';
$lang['l_register_psw'] = 'Contraseña';
$lang['l_register_confim'] = 'Confirmar la contraseña';
$lang['l_verification_code'] = 'Código de verificación:';
$lang['l_register_agree'] = 'Je suis d\'accord pour ' . ucfirst( COMMON_DOMAIN );
$lang['l_register_agree_sub'] = 'Suscríbete a ahorrar dinero con cupones en boletín de noticias cada semana';
$lang['l_register_terms'] = 'Termes et Conditions.';
$lang['l_register_tips'] = 'Après votre inscription, vous recevrez notre newsletters avec les informations sur des ventes,coupons et promotions spéciales. Vous pouvez vous désinscrire à Mon Compte.';

$lang['p_captcha_invalid'] = 'Código incorrecto, por favor inténtalo de nuevo.';
$lang['p_login_failure'] = 'usuario o contraseña inválido'; //登录失败
$lang['p_register_fail'] = 'No inscribirse, por favor intente de nuevo.'; //注册失败
$lang['p_agreement'] = 'Usted no está de acuerdo con el acuerdo'; //用户协议未勾选
$lang['p_username_shorter'] = 'El apodo debe tener al menos 3 caracteres.';
$lang['p_password_shorter'] = 'La contraseña debe tener al menos 6 caracteres.';
$lang['p_passwd_blank'] = 'La contraseña introducida no puede tener en blanco.';
$lang['p_reset_password'] = 'Usted recibirá un correo electrónico con un enlace para restablecer tu contraseña.'; //邮箱找回密码信息发送提示
$lang['p_fail_send_password'] = 'Si no, por favor póngase en contacto con el administrador!'; //发送重设密码邮件失败
$lang['p_parm_error'] = 'Error, vuelva por favor!'; //重设密码时，错误的用户名或code
$lang['p_reset_psw_success'] = 'Restablecer contraseña de éxito.'; //重设密码成功

//注册页面和会员中心js语言包
$lang['username_exist'] = 'Ya existe este apodo al cliente';
$lang['email_exist'] = 'Ya existe una cuenta con esta dirección de correo electrónico.';

$lang['required']='Este es un campo obligatorio.';
$lang['username_shorter'] = 'Por favor, introduzca 3 o más caracteres. Espacios iniciales o finales serán ignoradas.';
$lang['username_invalid'] = 'Apodo sólo puede estar compuesta de letras, calcular y subrayado.';
$lang['password_shorter'] = 'Por favor, introduzca 6 o más caracteres. Espacios iniciales o finales serán ignoradas.';
$lang['email_invalid'] = 'Introduzca una dirección de correo electrónico válida. Por ejemplo johndoe@domain.com.';
$lang['confirm_password_invalid'] = 'Por favor, asegúrese de que sus contraseñas coinciden.';
$lang['confirm_del_wl'] ='¿Está seguro que desea eliminar este producto de la lista?';

//退出登录页面
$lang['t_logout'] = 'Déconnexion';
$lang['l_logout_h1'] = 'Vous êtes maintenant déconnecté';
$lang['l_logout_tips'] = 'Vous êtes déconnecté et vous serez redirigé vers notre page d\'accueil dans <span id="timer">3</span>  secondes.';
