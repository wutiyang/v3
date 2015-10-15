<?php
/**
 * User: WTY
 * Date: 15-8-10
 * Time: 16:16
 */

/**
 * 系统配置(运维需要更改的配置)
 */
//开启错误信息
define( "DEBUG_OFF" , TRUE );
if( DEBUG_OFF === TRUE ){
	error_reporting( 2047 );
	ini_set( 'display_errors', 1 );
}else{
	error_reporting( 0 );
	ini_set( 'display_errors', 0 );
}

define( "SITE_HOME_URL" , "http://www.v3.eachbuyer.com/" );

return $system_config = array(
	//DB配置文件
	'eb_db_config' => array (
		'eb_pc_site' => array( 'host' => '172.16.0.231' , 'port' => 3306 , 'user' => 'root' , 'passwd' => 'db01123456' , 'name' => 'eachbuyer_v3' ) ,
	),

	//memcache 配置文件
	'memcache_config' => array(
		'memcache_web' => array(
			'host' => '172.16.0.230' ,
			'port' => 11211 ,
		),
		'memcache_page' => array(
			'host' => '172.16.0.230' ,
			'port' => 11311 ,
		),
		'memcache_session' => array(
			'host' => '172.16.0.230' ,
			'port' => 11411 ,
		),
	),

	//语言ID对应的语言
	'language_arr' => array(
		1 => 'us' ,
		2 => 'de' ,
		3 => 'es' ,
		4 => 'it' ,
		5 => 'fr' ,
		6 => 'br' ,
		7 => 'ru'
	),
);
