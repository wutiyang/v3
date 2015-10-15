<?php
/**
* 个人中心语言包.整理后的键值定义规则，页面title以t_开头，菜单以m_开头，标签以l_开头，js信息提示以j_开头，php信息提示以p_开头
* language: br
* @Author : yuandd
* @Last Modify : 2013-07-04
* ps:请翻译带有 “//translate”注释的语言内容，并将此注释删除掉
*/

//杂项
/* fujia 2013-07-09 */
$lang['require_login'] = 'Entrada ilegal.<br/> Você não pode concluir a operação até fazer o registro.'; //非法访问路径//translate
$lang['require_tips'] = 'Campos obrigatórios';
$lang['l_back'] = 'Voltar';
$lang['l_save'] = 'Salvar';
$lang['l_edit'] = 'Editar';

$lang['warning']['remove_item']='Are you sure you want to remove it?';
$lang['confirm']['yes']='Sim';
$lang['confirm']['no']='Não';

//登录注册页面
$lang['t_login_register'] = 'Cliente, faça o login ou crie uma nova conta de cliente';
$lang['l_login'] = 'clientes cadastrados';
$lang['l_login_username'] = 'Endereço de e-mail/ Apelido';
$lang['l_login_psw'] = 'Senha';
$lang['l_login_forgotpsw'] = 'Esqueceu sua senha?';

$lang['l_register'] = 'Informações pessoais';
$lang['l_register_nickname'] = 'Apelido';
$lang['l_register_email'] = 'Endereço de e-mail';
$lang['l_register_psw'] = 'Senha';
$lang['l_register_confim'] = 'Confirmar senha';
$lang['l_verification_code'] = 'Código de verificação';
$lang['l_register_agree'] = 'Concordo com os termos da ' . ucfirst( COMMON_DOMAIN );
$lang['l_register_agree_sub'] = 'Subscreva para poupar dinheiro com os cupons da Newsletter semanal';
$lang['l_register_terms'] = 'Termos e Condições.';
$lang['l_register_tips'] = 'Após o cadastro, você receberá as nossas newsletters com informações sobre produtos, cupons e promoções especiais. Você pode cancelar sua inscrição em minha conta.
';

$lang['p_captcha_invalid'] = 'Entrada inválida, por favor tente novamente.';
$lang['p_login_failure'] = 'login ou senha são inválidos.'; //登录失败
$lang['p_register_fail'] = 'O registre falhou, por favor, tente novamente.'; //注册失败
$lang['p_agreement'] = 'Você não concorda com as condições de serviço.'; //用户协议未勾选
$lang['p_username_shorter'] = 'O apelido deve conter pelo menos 3 caracteres.';
$lang['p_password_shorter'] = 'A senha deve ter pelo menos 6 caracteres.';
$lang['p_passwd_blank'] = 'A senha digitada não pode possuir espaço em branco.';
$lang['p_reset_password'] = 'Você receberá um e-mail com um link para redefinir sua senha.'; //邮箱找回密码信息发送提示
$lang['p_fail_send_password'] = 'Falha, por favor, entre em contato com o administrador!'; //发送重设密码邮件失败
$lang['p_parm_error'] = 'Erro, Por favor, retorne!'; //重设密码时，错误的用户名或code
$lang['p_reset_psw_success'] = 'Senha alterada com sucesso.'; //重设密码成功

//注册页面和会员中心js语言包
$lang['username_exist'] = 'Esse apelido de cliente já existe.';
$lang['email_exist'] = 'Já existe uma conta com este endereço de e-mail.';

$lang['required']='Ops, este é um campo obrigatório.';
$lang['username_shorter'] = 'Por favor, digite três ou mais caracteres. Espaços antes ou depois serão ignorados.';
$lang['username_invalid'] = 'O apelido só pode ser composto de letras, simbolos e sublinhado.';
$lang['password_shorter'] = 'Por favor insira seis ou mais caracteres. Espaços antes ou depois serão ignorados.';
$lang['email_invalid'] = 'Por favor insira um endereço de e-mail válido. Por exemplo johndoe@domain.com.';
$lang['confirm_password_invalid'] = 'Por favor, certifique-se de que suas senhas coincidem.';
$lang['confirm_del_wl'] ='Você tem certeza que deseja remover o produto da sua lista de desejos?';

//退出登录页面
$lang['t_logout'] = 'Sair ';
$lang['l_logout_h1'] = 'Você agora está desconectado';
$lang['l_logout_tips'] = 'Você saiu e será redirecionado para nossa página inicial em  <span id="timer">3</span> segundos.';
