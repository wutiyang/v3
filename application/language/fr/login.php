<?php
/**
* 个人中心语言包.整理后的键值定义规则，页面title以t_开头，菜单以m_开头，标签以l_开头，js信息提示以j_开头，php信息提示以p_开头
* language: fr
* @Author : yuandd
* @Last Modify : 2013-07-04
* ps:请翻译带有 “//translate”注释的语言内容，并将此注释删除掉
*/
//杂项
/* fujia 2013-07-09 */
$lang['require_login'] = 'L\'entrée illégale. Vous ne pouvez pas finir les opérations avant la connexion.'; //非法访问路径
$lang['require_tips'] = 'Champs obligatoires';
$lang['l_back'] = 'Retour';
$lang['l_save'] = 'Sauvegarder';
$lang['l_edit'] = 'Éditer';

$lang['warning']['remove_item']='Are you sure you want to remove it?';
$lang['confirm']['yes']='Oui';
$lang['confirm']['no']='Non';

//登录注册页面
$lang['t_login_register'] = 'Connexion ou créer un nouveau compte';
$lang['l_login'] = 'Clients enregistrés';
$lang['l_login_username'] = 'Adresse mail/ Pseudo';
$lang['l_login_psw'] = 'Mot de passe';
$lang['l_login_forgotpsw'] = 'Mot de passe oublié ?';

$lang['l_register'] = 'Informations personnelles';
$lang['l_register_nickname'] = 'Pseudo';
$lang['l_register_email'] = 'Adresse mail';
$lang['l_register_psw'] = 'Mot de passe';
$lang['l_register_confim'] = 'Confirmer le mot de passe';
$lang['l_verification_code'] = 'Code de vérification';
$lang['l_register_agree'] = 'Je suis d\'accord pour ' . ucfirst( COMMON_DOMAIN );
$lang['l_register_agree_sub'] = "Abonnez-vous à économiser de l'argent avec les coupons sur la newsletter chaque semaine";
$lang['l_register_terms'] = 'Termes et Conditions.';
$lang['l_register_tips'] = 'Après votre inscription, vous recevrez notre newsletters avec les informations sur des ventes,coupons et promotions spéciales. Vous pouvez vous désinscrire à Mon Compte.';
$lang['p_username_please'] = 'Entrez le nom d\'utilisateur, s\'il vous plaît ';

$lang['p_captcha_invalid'] = 'Entrée non valide, veuillez essayer de nouveau.';
$lang['p_login_failure'] = 'Invalide connexion ou mot de passe.'; //登录失败
$lang['p_register_fail'] = 'L\'inscription échouée, essayer à nouveau s\'il vous plaît.'; //注册失败
$lang['p_agreement'] = 'Vous n\'êtes pas d\'accord avec l\'accord.'; //用户协议未勾选
$lang['p_username_shorter'] = 'Le pseudo doit avoir au moins 3 caractères.';
$lang['p_password_shorter'] = 'Le mot de passe doit avoir au moins 6 caractères.';
$lang['p_passwd_blank'] = 'Le mot de passe entré ne doit pas être vide.';
$lang['p_reset_password'] = 'Vous recevrez un email avec un lien pour rétablir votre mot de passe.'; //邮箱找回密码信息发送提示
$lang['p_fail_send_password'] = 'L\'échec, contactez l\'administrateur s\'il vous plaît !'; //发送重设密码邮件失败
$lang['p_parm_error'] = 'Erreur, veuillez retourner !'; //重设密码时，错误的用户名或code
$lang['p_reset_psw_success'] = 'Le succès à rétablir le mot de passe.'; //重设密码成功

//注册页面和会员中心js语言包
$lang['username_exist'] = 'Ce pseudo est déjà appliqué.=';
$lang['email_exist'] = 'Il existe déjà un compte avec cette adresse email.';

$lang['required']='Ce champ est obligatoire.';
$lang['username_shorter'] = 'Entrez 3 ou plus caractères, s\'il vous plaît. Des espaces avant ou après seront ignorés.';
$lang['username_invalid'] = 'Un pseudo ne peut être composé de lettres, figure et soulignement.';
$lang['password_shorter'] = 'Entrez 6 ou plus caractères, s\'il vous plaît. Des espaces avant ou après seront ignorés.';
$lang['email_invalid'] = 'Entrez une adresse email valide, s\'il vous plaît. Par exemple johndoe@domain.com.';
$lang['confirm_password_invalid']	='Assurez-vous que vos mots de passe correspondent, s\'il vous plaît.';
$lang['confirm_password_invalid'] = 'Etes-vous sûr de supprimer ce produit de votre liste de souhaits ?';

//退出登录页面
$lang['t_logout'] = 'Déconnexion';
$lang['l_logout_h1'] = 'Vous êtes maintenant déconnecté.';
$lang['l_logout_tips'] = 'Vous êtes déconnecté et vous serez redirigé vers notre page d\'accueil dans <span id="timer">3</span> secondes.';
