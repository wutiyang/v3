<?php
/**
* 个人中心语言包.整理后的键值定义规则，页面title以t_开头，菜单以m_开头，标签以l_开头，js信息提示以j_开头，php信息提示以p_开头
* language: de
* @Author : yuandd
* @Last Modify : 2013-07-04
* ps:请翻译带有 “//translate”注释的语言内容,并删除此标记
*/

//杂项
/* fujia 2013-07-05 */
$lang['require_login'] = 'Ungültige Zugriff. <br/>Sie können diese Operation nicht erledigen.Sie müssen einloggen. '; //非法访问路径
$lang['require_tips'] = 'Pflichtfelder';
$lang['l_back'] = 'Zurück';
$lang['l_save'] = 'Speichern';
$lang['l_edit'] = 'Bearbeiten';

$lang['warning']['remove_item']='Wollen Sie es sicher entfernen?';
$lang['confirm']['yes']='Ja';
$lang['confirm']['no']='Nicht';

//登录注册页面
$lang['t_login_register'] = 'Kunde anmelden oder neues Kundenkonto erstellen ';
$lang['l_login'] = 'Registrierte Kunden';
$lang['l_login_username'] = 'eMail-Adresse/ Nickname';
$lang['l_login_psw'] = 'Passwort';
$lang['l_login_forgotpsw'] = 'Passwort vergessen?';

$lang['l_register'] = 'Persönliche Informationen';
$lang['l_register_nickname'] = 'Nickname';
$lang['l_register_email'] = 'eMail-Adresse';
$lang['l_register_psw'] = 'Passwort';
$lang['l_register_confim'] = 'Passwort bestätigen';
$lang['l_verification_code'] = 'Prüfungskode';
$lang['l_register_agree'] = 'Ich stimme zu ' . ucfirst( COMMON_DOMAIN );
$lang['l_register_agree_sub'] = 'Abonnieren Sie auf unsere Newsletter um jede Wochen mit unseren Coupons Geld zu sparen';
$lang['l_register_terms'] = 'Allgemeine Geschäftsbedingungen';
$lang['l_register_tips'] = 'Nach der Registrierung werden Sie unsere Newsletter mit Informationen über Sales, Gutscheins und Sonderaktionen erhalten. Sie können in Mein Konto zurück abonnieren.';

$lang['p_captcha_invalid'] = 'Ungültige Eingabe, versuchen Sie noch bitte.';
$lang['p_username_please']='Geben bitte den Benutzername ein';
$lang['p_login_failure'] = 'Ungültiger Benutzername oder Passwort.'; //登录失败
$lang['p_register_fail'] = 'Registrieren fehlgeschlagen, bitte versuchen erneut.'; //注册失败
$lang['p_agreement'] = 'Sie sind mit der Zustimmung nicht einverstanden.'; //用户协议未勾选
$lang['p_username_shorter'] = 'Der Spitzname muss mindestens 3 Zeichen enthalten.';
$lang['p_password_shorter'] = 'Das Passwort muss mindestens 6 Zeichen enthalten.';
$lang['p_passwd_blank'] = 'Das eingegebene Passwort kann nicht leer sein.';
$lang['p_reset_password'] = 'Sie erhalten eine Email mit einem Link, um Ihre Passwort zu zurücksetzen.'; //邮箱找回密码信息发送提示
$lang['p_fail_send_password'] = 'Misserfolg, kontaktieren Sie bitte mit Administrator!'; //发送重设密码邮件失败
$lang['p_parm_error'] = 'Fehler, zurück bitte!'; //重设密码时，错误的用户名或code
$lang['p_reset_psw_success'] = 'Zurücksetzen Passwort erfolgreich.'; //重设密码成功

//注册页面和会员中心js语言包
$lang['username_exist'] = 'Dieser Kunde-Spitzname ist schon vorhanden.';
$lang['email_exist'] = 'Es gibt bereits ein Konto mit dieser Emailadresse.';

$lang['required']='Dies ist ein Pflichtfeld.';
$lang['username_shorter'] = 'Bitte geben Sie 3 oder mehr Zeichen ein. Führende oder nachfolgende Leerzeichen werden ignoriert.';
$lang['username_invalid'] = 'Spitzname kann nur aus Buchstaben, Figur und Unterstreichen bestehen.';
$lang['password_shorter'] = 'Bitte geben Sie 6 oder mehr Zeichen ein. Führende oder nachfolgende Leerzeichen werden ignoriert.';
$lang['email_invalid'] = 'Bitte geben Sie eine gültige Emailadresse ein. Zum Beispiel johndoe@domain.com.';
$lang['confirm_password_invalid'] = 'Bitte stellen Sie sicher, dass Ihre Passwörter übereinstimmen.';
$lang['confirm_del_wl']='Sind Sie sicher, dass Sie dieses Produkt von Ihrer Wunschliste löschen?';

//退出登录页面
$lang['t_logout'] = 'Kunde abmelden'; //translate
$lang['l_logout_h1'] = 'Sie haben jetzt abgemeldet. ';
$lang['l_logout_tips'] = 'Sie haben sich erfolgreich abgemeldet und werden in <span id="timer">3</span> Sekunden zur Startseite weitergeleitet.';
