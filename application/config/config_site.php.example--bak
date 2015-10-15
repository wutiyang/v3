<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| Site Config
| -------------------------------------------------------------------
| This file differ in different environment.
| no version control.
| 
| file: config_site.php.example
| usage: cp config_site.php.example config_site.php
|
*/
error_reporting(E_ALL);
ini_set('display_errors',1);
//error_reporting(0);
//ini_set('display_errors',0);

/*
|--------------------------------------------------------------------------
| GENERAL
|--------------------------------------------------------------------------
*/
define('STATIC_FILE_VERSION','20140228102610');
define('SSL_ENABLED',false);

/*
|--------------------------------------------------------------------------
| URL
|--------------------------------------------------------------------------
*/
define('COMMON_DOMAIN', 'eachbuyer.com');

define('SITE_CODE' , 'default');
define( 'EBPLATEFORM' , 0 );//0 为com,1为移动, 2为net ,用作来源标记
define( 'ORDER_PREFIX' , '' );//订单规则前缀

$base_url = array(
	1 => 'http://www.v3.eachbuyer.com/',
	2 => 'http://de.v3.eachbuyer.com/',
	3 => 'http://es.v3.eachbuyer.com/',
	4 => 'http://it.v3.eachbuyer.com/',
	5 => 'http://fr.v3.eachbuyer.com/',
	6 => 'http://br.v3.eachbuyer.com/',
	7 => 'http://ru.v3.eachbuyer.com/',
);
$base_url_mobile = array(
	1 => 'http://www.i.eachbuyer.com/',
	2 => 'http://de.i.eachbuyer.com/',
	3 => 'http://es.i.eachbuyer.com/',
	4 => 'http://it.i.eachbuyer.com/',
	5 => 'http://fr.i.eachbuyer.com/',
	6 => 'http://br.i.eachbuyer.com/',
	7 => 'http://ru.i.eachbuyer.com/',
);
define('RESOURCE_URL','http://www.v3.eachbuyer.com/resource/default/');
define('COMMON_IMAGE_URL','http://img5.eachbuyer.com/');
define('PRODUCT_IMAGE_URL','http://img6.eachbuyer.com/');

/*
|--------------------------------------------------------------------------
| PATH
|--------------------------------------------------------------------------
*/
define('LOG_PATH',ROOT_PATH.'log/');
define('SYNC_PATH',ROOT_PATH.'sync/');

/*
|--------------------------------------------------------------------------
| DATABASE
|--------------------------------------------------------------------------
*/
define('DATABASE_MASTER_HOST','172.16.0.231');
define('DATABASE_MASTER_NAME','root');
define('DATABASE_MASTER_PASSWORD','db01123456');
define('DATABASE_MASTER_DATABASE','eachbuyer_v3');

define('DATABASE_SLAVE_HOST','172.16.0.231');
define('DATABASE_SLAVE_NAME','root');
define('DATABASE_SLAVE_PASSWORD','db01123456');
define('DATABASE_SLAVE_DATABASE','eachbuyer_v3');
/*
|--------------------------------------------------------------------------
| SESSION
|--------------------------------------------------------------------------
*/
define('SESSION_SERVER','172.16.0.230');
define('SESSION_PORT',11411);
define('SESSION_NAME','EB_ID');
define('SESSION_LIFE_TIME', 7200 );

/*
|--------------------------------------------------------------------------
| MEMCACHE
|--------------------------------------------------------------------------
*/
define('DISABLE_CACHE',false);

$memcache_list = array(
	array('host'=>'172.16.0.230','port'=>11211),
	array('host'=>'172.16.0.230','port'=>11311),
);

/*
|--------------------------------------------------------------------------
| LANGUAGE
|--------------------------------------------------------------------------
*/
$language_list = array(
	1 => 'us',
	2 => 'de',
	3 => 'es',
	4 => 'it',
	5 => 'fr',
	6 => 'br',
	7 => 'ru',
);
define('DEFAULT_LANGUAGE',1);

/*
 |--------------------------------------------------------------------------
| Payment
|--------------------------------------------------------------------------
*/
define('PAYPAL_SANDBOX_DISABLED',false);
define('PAYPAL_EC_SANDBOX_DISABLED',false);
define('CHECKOUT_SANDBOX_DISABLED',false);
define('SOFORT_SANDBOX_DISABLED',false);
define('SAFETYPAY_SANDBOX_DISABLED',false);
define('ADYEN_SANDBOX_DISABLED',false);

/*
|--------------------------------------------------------------------------
| Currency List
|--------------------------------------------------------------------------
*/
$currency_list = array('AUD','BRL','GBP','CAD','EUR','HKD','CHF','USD','RUB','INR','MXN');
define('DEFAULT_CURRENCY','USD');

/* End of file config_site.php */
/* Location: ./application/config/config_site.php */