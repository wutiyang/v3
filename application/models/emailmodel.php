<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Emailmodel extends CI_Model {

	public function __construct(){
		parent::__construct();
	}
/*
| -------------------------------------------------------------------
|  DB Read Functions
| -------------------------------------------------------------------
*/
	public function subscribe_sendMail($type,$language_id,$email,$params = array()){
		$email_id = $this->getEmailTemplateId($type , $language_id);
		if(!$email_id) return false;
		if(empty($params)) return false;
		/*$params['site_url'] = genURL().'?utm_source=System_Own&utm_medium=Email&utm_campaign='.$type.'&utm_nooverride=1';
		$params['account_url'] = genURL('account').'?utm_source=System_Own&utm_medium=Email&utm_campaign='.$type.'&utm_nooverride=1';
		$params['help_url'] = genURL('contact_us.html').'?utm_source=System_Own&utm_medium=Email&utm_campaign='.$type.'&utm_nooverride=1';
		$params['login_url'] = genURL('login').'?utm_source=System_Own&utm_medium=Email&utm_campaign='.$type.'&utm_nooverride=1';
		$params['newsletter_url'] = genURL('newsletter').'?utm_source=System_Own&utm_medium=Email&utm_campaign='.$type.'&utm_nooverride=1';

		foreach($params as $search => $replace){
			$content = str_replace('{$'.$search.'}',$replace,$content);
		}
		if( COMMON_DOMAIN == 'eachbuyer.net' ){
			$content = str_replace('eachbuyer.com', 'eachbuyer.net', $content);
		}*/
		//send_mail($email,$subject,$content,$is_html);
		
		return eb_email($email_id,$email,$params);
	}
	
	/**
	 * @desc 获取邮箱模板信息
	 * @param unknown $type
	 * @param number $languageId
	 * @return multitype:|Ambigous <multitype:, unknown>
	 */
	public function getSystemEmailTemplateInfo( $type, $languageId = 1 ){
		$result = array();
		if(!$type || !is_numeric($type)) return $result;
	
		global $mem_expired_time;
		$mem_key = md5("each_buyer_email_template_".$type."_".$languageId);
	
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->select('*');
			$this->database->slave->from('eb_email_template');
			$this->database->slave->where('email_template_status',1);
			$this->database->slave->where('language_id',$languageId);
			$this->database->slave->where('email_template_type',$type);
			$query = $this->database->slave->get();
			$data = $query->result_array();
	
			if(!empty($data))$result = $data[0];
				
			$this->memcache->set($mem_key, $result,$mem_expired_time['email_template']);
		}
	
		return $result;
	}
	
	/**
	 * @desc 获取email模板id
	 * @param unknown $type
	 * @param number $lanauge_id
	 * @return boolean|Ambigous <>
	 */
	public function getEmailTemplateId($type , $language_id = 1){
		if(!$type || !is_numeric($type)) return false;
	
		$email_template_info = $this->getSystemEmailTemplateInfo( $type, $language_id);
		if(!empty($email_template_info)) return $email_template_info['email_template_reference'];
		else return false;
	}
	
}