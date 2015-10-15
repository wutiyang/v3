<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['crontab_import/(:num)'] = "crontab_import/index/$1";
$route['console/(:any)'] = "console/index/$1";
$route['qna\/(.*)-p([0-9]+).html'] = "qna/index/$2";
$route['review\/(.*)-p([0-9]+).html'] = "review/index/$2";
$route['(.*)-p([0-9]+).html'] = "product/index/$2";

//atoz
$route['buy-(.*).html'] = "atoz_view/index/$1" ;
$route['([A-Z]).html'] = "atoz/index/$1";
$route['0_9.html'] = "atoz/index/0-9";
$route['0-9.html'] = "atoz/index/0-9";
$route['([A-Z])_([0-9]+).html'] = "atoz/index/$1/$2";
$route['0_9_([0-9]+).html'] = "atoz/index/0-9/$1";
$route['0-9_([0-9]+).html'] = "atoz/index/0-9/$1";


//ns 分类处理
$route['ns/(.*)-c([0-9]+)'] = "category/index/$2";
$route['ns/(.*)-c([0-9]+)\/(.*)\/([0-9]+).html'] = "category/index/$2/$4";
$route['ns/(.*)-c([0-9]+)\/(.*)'] = "category/index/$2";

$route['(.*)-c([0-9]+)'] = "category/index/$2";
$route['(.*)_c([0-9]+)'] = "category/index/$2";
$route['(.*)-c([0-9]+).html'] = "category/index/$2";
$route['(.*)_c([0-9]+).html'] = "category/index/$2";
$route['(.*)-c([0-9]+)\/([0-9]+).html'] = "category/index/$2/$3";
$route['(.*)_c([0-9]+)\/([0-9]+).html'] = "category/index/$2/$3";

//促销
$route['(.*)-m([0-9]+).html'] = "promote/index/$2";
$route['(.*)-md([0-9]+).html'] = "promote_view/index/$2";

//满减
$route['(.*)-d([0-9]+).html'] = "promote_fullcut/index/$2";
//品牌分类页
$route['(.*)_bc([0-9]+)'] = "brandcategory/index/$2";
$route['(.*)-bc([0-9]+).html'] = "brandcategory/index/$2";
$route['(.*)-bc([0-9]+)\/([0-9]+).html'] = "brandcategory/index/$2/$3";
//品牌页
$route['(.*)_b([0-9]+)'] = "brandzone/index/$2";
$route['(.*)-b([0-9]+).html'] = "brandzone/index/$2";
$route['(.*)-b([0-9]+)\/([0-9]+).html'] = "brandzone/index/$2/$3";

//Deal
$route['(.*)-deal([0-9]+).html'] = "deals/index/$2";

$route['repay/(:num)'] = "repay/index/$1";
$route['order_detail/(:num)'] = "order_detail/index/$1";

$route['about_us.html'] = 'about_us';
$route['terms_and_conditions.html'] = 'terms_and_conditions';
$route['privacy_policy.html'] = 'privacy_policy';

$route['contact_us.html'] = 'contact_us';
$route['faq.html'] = 'faq';
$route['payment_method.html'] = 'payment_method';
$route['shipping_method_guide.html'] = 'shipping_method_guide';
$route['return_policy.html'] = 'return_policy';
$route['help.html'] = 'help';
$route['affiliate_program.html'] = 'affiliate_program';
$route['wholesale.html'] = 'wholesale';
$route['oukitel.html'] = 'oukitel';
$route['shipping_special_note.html'] = 'shipping_special_note';
/* End of file routes.php */
/* Location: ./application/config/routes.php */
