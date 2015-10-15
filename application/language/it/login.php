<?php
/**
* 个人中心语言包.整理后的键值定义规则，页面title以t_开头，菜单以m_开头，标签以l_开头，js信息提示以j_开头，php信息提示以p_开头
* language: it
* @Author : yuandd
* @Last Modify : 2013-07-04
* ps:请翻译带有 “//translate”注释的语言内容，并将此注释删除掉
*/

//杂项
/* fujia 2013-07-09 */
$lang['require_login'] = 'Entrata rifiutata.<br/> Non si può continuare l\'operazione fino a accedersi.'; //非法访问路径//
$lang['require_tips'] = 'Campo obbligatorio';
$lang['l_back'] = 'Indietro';
$lang['l_save'] = 'Salva';
$lang['l_edit'] = 'Modifica';

$lang['warning']['remove_item']='Sei sicuro di rimuoverlo?';
$lang['confirm']['yes']='Si\'';
$lang['confirm']['no']='No';

//登录注册页面
$lang['t_login_register'] = 'Accedi o crea un nuovo conto';
$lang['l_login'] = 'Clienti registrati';
$lang['l_login_username'] = 'Indirizzo email/ Nickname';
$lang['l_login_psw'] = 'Password';
$lang['l_login_forgotpsw'] = 'Password dimenticata?';

$lang['l_register'] = 'Informazioni personali';
$lang['l_register_nickname'] = 'Nickname';
$lang['l_register_email'] = 'Indirizzo email';
$lang['l_register_psw'] = 'Password';
$lang['l_register_confim'] = 'Conferma password';
$lang['l_verification_code'] = 'Codice di verifica';
$lang['l_register_agree'] = 'Accetti a ' . ucfirst( COMMON_DOMAIN );
$lang['l_register_agree_sub'] = 'Abbonarsi per risparmiare i soldi con i couponsu Newsletter di ogni settimana';
$lang['l_register_terms'] = 'Termini e condizioni';
$lang['l_register_tips'] = 'Dopo aver iscritto, riceverà le newsletter riguardo alle informazioni delle offerte, coupon e promozioni speciali. Può anche cancellarlo nel mio account.';

$lang['p_captcha_invalid'] = 'Ingresso valido, si prega di riprovare.';
$lang['p_login_failure'] = 'Invalida login o password.'; //登录失败
$lang['p_register_fail'] = 'Registrato fallito, riprovi per favore'; //注册失败
$lang['p_agreement'] = 'Non si accetta il contratto'; //用户协议未勾选
$lang['p_username_shorter'] = 'Il soprannome deve avere almeno 3 carateristiche.';
$lang['p_password_shorter'] = 'Il password deve avere almeno 6 carateristiche';
$lang['p_passwd_blank'] = 'Il password inserito non può essere vuoto.';
$lang['p_reset_password'] = 'Riceverà una mail con un link per reimpostare il password.'; //邮箱找回密码信息发送提示
$lang['p_fail_send_password'] = 'Fallimento, si prega di contattare l\'amministratore'; //发送重设密码邮件失败
$lang['p_parm_error'] = 'Errore, ritornare per favore!'; //重设密码时，错误的用户名或code
$lang['p_reset_psw_success'] = 'Il password è risettato.'; //重设密码成功

//注册页面和会员中心js语言包
$lang['username_exist'] = 'Questo soprannome di utente esiste già';
$lang['email_exist'] = 'C\'è già un account con questo indirizzo di email.';

$lang['required']='Questo è un campo obbligatorio.';
$lang['username_shorter'] = 'Si prega di inserire almeno 3 carateristiche. Spazi iniziali o finali vengono ignorati.';
$lang['username_invalid'] = 'Nickname solo può essere composta da lettere , figure e sottolinea.';
$lang['password_shorter'] = 'Si prega di inserire almeno 6 carateristiche. Spazi iniziali o finali vengono ignorati.';
$lang['email_invalid'] = 'Inserisca un indirizzo valido. Per esempio johndoe@domain.com.';
$lang['confirm_password_invalid'] = 'Si assicuri che il password corrisponde per favore.';
$lang['confirm_del_wl']='E\' sicuro di voler rimuovere il prodotto dalla tua lista dei desideri ?';
//退出登录页面
$lang['t_logout'] = 'Logout';
$lang['l_logout_h1'] = 'Sei ora scollegato';
$lang['l_logout_tips'] = 'Già logout e tornerai alla pagina iniziale in <span id="timer">3</span> secondi.';
