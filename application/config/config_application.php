<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//public
define('SQL_EXECUTE_RETAIN_CONDITION', false);
define('SALT', '4859789228b007d1f2882476bbb0b1d0');
define('NOW',date('Y-m-d H:i:s'));
define('TODAY',date('Y-m-d'));
define('TIME',time());
define('PAYPALEC_LOGIN_PASSWORD',12345678);

//status
define('STATUS_ACTIVE', 1);
define('STATUS_DISABLE', 0);
define('STATUS_PENDING', -1);
define('STATUS_DELETE', -2);

//product status
define('PRODUCT_STATUS_ACTIVE', 1);
define('PRODUCT_STATUS_DISABLE', 0);
define('PRODUCT_STATUS_INFRINGE', 2);

//order action type
define('ORDER_ACTION_TYPE_CREATE', 1);
define('ORDER_ACTION_TYPE_PAYING', 2);
define('ORDER_ACTION_TYPE_PAID', 3);
define('ORDER_ACTION_TYPE_CANCEL', 4);

//keywords type
define('KEYWORDS_TYPE_HEADER', 1);
define('KEYWORDS_TYPE_FOOTER', 2);
define('KEYWORDS_TYPE_SEARCH', 3);

//coupon status
DEFINE('COUPON_STATUS_DISABLED', 0);
DEFINE('COUPON_STATUS_DRAFT', 1);
DEFINE('COUPON_STATUS_PENDING', 2);
DEFINE('COUPON_STATUS_ACTIVE', 3);

//coupon range type
define('COUPON_RANGE_TYPE_SITE', 1);
define('COUPON_RANGE_TYPE_CATEGORY', 2);
define('COUPON_RANGE_TYPE_PRODUCT', 3);

//coupon type
define('COUPON_TYPE_REDUCE', 1);
define('COUPON_TYPE_DISCOUNT', 2);
define('COUPON_TYPE_REDUCE_LEVEL', 3);
define('COUPON_TYPE_DISCOUNT_LEVEL', 4);
define('COUPON_TYPE_GIFT', 5);
//insurance
define("INSURANCE",1.99);
//base track(track费用)
define('BASE_TRACK',1.69);
//subscribe coupon price
define('SUBSCRIBE_COUPON_PRICE',3);
//coupon type(coupon 类型定义)
define('SUBSCRIBE_COUPON',1);
define('NORMAL_COUPON',2);

//sso type
define('SSO_TYPE_PAYPAL',1);
define('SSO_TYPE_FACEBOOK',2);

//email template type
$email_template_type = array(
	1=>'订单提交成功',2=>'订单支付成功',
	3=>'付款提醒邮件一','4'=>'付款提醒邮件二',
	5=>'订单已发货一','6'=>'订单已发货二',
	7=>'评价提醒',8=>'退款成功邮件',
	9=>'重新下单',10=>'订阅验证',
	11=>'订阅成功',12=>'再次订阅成功',
	13=>'注册成功',14=>'注册成功&订阅验证',
	15=>'注册成功&订阅成功',16=>'重置密码'	
);

//newsletter subscribe source
define('NEWSLETTER_SUBSCRIBE_SOURCE_OTHER',0);
define('NEWSLETTER_SUBSCRIBE_SOURCE_EBAY',1);
define('NEWSLETTER_SUBSCRIBE_SOURCE_SIDEBAR',2);
define('NEWSLETTER_SUBSCRIBE_SOURCE_ACCOUNT',3);
define('NEWSLETTER_SUBSCRIBE_SOURCE_REGISTER',4);
define('NEWSLETTER_SUBSCRIBE_SOURCE_AUCTION',5);
define('NEWSLETTER_SUBSCRIBE_SOURCE_FOOTER',6);
define('NEWSLETTER_SUBSCRIBE_SOURCE_EBAY_ORDER',7);
define('NEWSLETTER_SUBSCRIBE_SOURCE_POPUP',8);
define('NEWSLETTER_SUBSCRIBE_SOURCE_ORDER_SUCCESS',9);
define('NEWSLETTER_SUBSCRIBE_SOURCE_ORDER_SUCCESS_AUTO',11);
//subscribe coupon time
define('SUBSCRIBE_COUPON_TIME', 15);

/* 订单状态 */
define('OD_CREATE',                1); // 新建
define('OD_PAYING',                2); // 支付中
define('OD_PAID',                  3); // 已支付
define('OD_PAIDCONFIRM',           4); // 支付确认
define('OD_AUDIT',                 5); // 已审核
define('OD_PROCESSING',            6);//处理中
define('OD_DELIVER',               7);//发货中
define('OD_DELIVERED',             8);//已发货
define('OD_COMPLETED',             9);//已完成
define('OD_CANCEL',                0); // 取消

//discount type
define('DISCOUNT_TYPE_NORMAL', 1);
define('DISCOUNT_TYPE_GIFT', 2);
define('DISCOUNT_TYPE_PRESALE', 3);
define('DISCOUNT_TYPE_PHASE', 4);
define('DISCOUNT_TYPE_CONDITION', 5);
define('DISCOUNT_TYPE_COUPON', 6);
define('DISCOUNT_TYPE_SHIPPING', 7);

//discount type effect
define('DISCOUNT_TYPE_EFFECT_MULTIPLY', 1);
define('DISCOUNT_TYPE_EFFECT_SUBTRACT', 2);

//Language
$language_info = array(
	1 => array('code'=>'us','title'=>'English','currency'=>'USD','common_code'=>'en'),
	2 => array('code'=>'de','title'=>'Deutsch','currency'=>'EUR','common_code'=>'de'),
	3 => array('code'=>'es','title'=>'Español','currency'=>'EUR','common_code'=>'es'),
	4 => array('code'=>'it','title'=>'Italiano','currency'=>'EUR','common_code'=>'it'),
	5 => array('code'=>'fr','title'=>'Français','currency'=>'EUR','common_code'=>'fr'),
	6 => array('code'=>'br','title'=>'Português','currency'=>'BRL','common_code'=>'pt'),
	7 => array('code'=>'ru','title'=>'Pусский','currency'=>'RUB','common_code'=>'ru'),
);

//sync
$sync_map = array(
	'ad' => array('ad_id','ad_location','ad_position','ad_content','ad_status','ad_time_start','ad_time_end'),
	'attribute' => array('attribute_id','attribute_block_id','attribute_name','attribute_unit','attribute_sort','attribute_status'),
	'attribute_block' => array('attribute_block_id','attribute_block_name','attribute_block_sort','attribute_block_status'),
	'attribute_block_lang' => array('attribute_block_lang_id','attribute_block_id','language_id','attribute_block_lang_title','attribute_block_lang_status'),
	'attribute_category' => array('attribute_category_id','attribute_id','category_id','attribute_category_sort','attribute_category_display','attribute_category_type','attribute_category_status'),
	'attribute_lang' => array('attribute_lang_id','attribute_id','language_id','attribute_lang_title','attribute_lang_status'),
	'attribute_product' => array('attribute_product_id','attribute_block_id','attribute_id','product_id','attribute_value_id','attribute_value','attribute_product_status'),
	'attribute_value' => array('attribute_value_id','attribute_id','attribute_block_id','attribute_value_name','attribute_value_status'),
	'attribute_value_group' => array('attribute_value_group_id','attribute_category_id','attribute_id','category_id','attribute_value_group_name','attribute_value_group_lang','attribute_value_group_content','attribute_value_group_sort','attribute_value_group_display','attribute_value_group_status'),
	'attribute_value_lang' => array('attribute_value_lang_id','attribute_value_id','language_id','attribute_value_lang_title','attribute_value_lang_status'),
	'category' => array('category_id','parent_id','category_name','category_url','category_type','category_type_display','category_path','category_image','category_price_grade','category_nav_image','category_nav_image_bg','category_nav_url','category_sort','category_status','category_pid_count'),
	'category_description' => array('category_description_id','category_id','language_id','category_description_name','category_description_title','category_description_footer','category_description_keyword','category_description_meta'),
	'category_narrow_price' => array('category_narrow_price_id','category_id','category_narrow_price_start','category_narrow_price_end','category_narrow_price_sort','category_narrow_price_status'),
	'category_product' => array('category_product_id','category_id','product_id','category_product_status'),
	'category_redirect' => array('category_redirect_id','from_category_id','to_category_id','category_redirect_url','category_redirect_type','category_redirect_status','category_redirect_time_start','category_redirect_time_end'),
	'category_related' => array('category_related_id','category_id','category_related_content','category_related_type','category_related_sort','category_related_status'),
	'category_template' => array('category_template_id','category_id','language_id','category_template_content','category_template_status'),
	'complexattr' => array('complexattr_id','complexattr_title'),
	'complexattr_lang' => array('complexattr_lang_id','complexattr_id','language_id','complexattr_lang_title'),
	'complexattr_sku' => array('complexattr_sku_id','complexattr_id','complexattr_value_id','product_id','product_sku','complexattr_sku_status'),
	'complexattr_value' => array('complexattr_value_id','complexattr_id','complexattr_value_title'),
	'complexattr_value_lang' => array('complexattr_value_lang_id','complexattr_value_id','language_id','complexattr_value_lang_title'),
	'coupon' => array('coupon_id','coupon_code','coupon_name','coupon_type','coupon_language','coupon_limit_total','coupon_limit_user','coupon_range_type','coupon_range','coupon_status','coupon_time_start','coupon_time_end'),
	'coupon_effect' => array('coupon_effect_id','coupon_id','coupon_effect_type','coupon_effect_price','coupon_effect_value','coupon_effect_status'),
	'currency' => array('currency_id','currency_code','currency_rate','currency_format'),
	'email_template' => array('email_template_id','language_id','email_template_type','email_template_reference','email_template_status'),
	'keywords' => array('keywords_id','language_id','keywords_type','keywords_title','keywords_url','keywords_highlight','keywords_sort','keywords_status'),
	'product' => array('product_id','category_id','brand_id','product_name','product_url','product_type','product_path','product_image','product_price','product_price_market','product_sales','product_status','product_time_sync','product_time_initial_active'),
	'product_description_us' => array('product_description_id','product_id','product_description_name','product_description_content'),
	'product_description_de' => array('product_description_id','product_id','product_description_name','product_description_content'),
	'product_description_es' => array('product_description_id','product_id','product_description_name','product_description_content'),
	'product_description_it' => array('product_description_id','product_id','product_description_name','product_description_content'),
	'product_description_fr' => array('product_description_id','product_id','product_description_name','product_description_content'),
	'product_description_br' => array('product_description_id','product_id','product_description_name','product_description_content'),
	'product_description_ru' => array('product_description_id','product_id','product_description_name','product_description_content'),
	'product_gallery' => array('product_gallery_id','product_id','product_gallery_path','product_gallery_sort','product_gallery_status'),
	'product_image' => array('product_image_id','product_id','product_sku','product_image_path','product_image_sort','product_image_status'),
	'product_recommend' => array('product_recommend_id','product_id','category_id','product_recommend_sort','product_recommend_status'),
	'product_sku' => array('product_sku_id','product_id','product_sku_code','product_sku_type','product_sku_image','product_sku_price','product_sku_price_market','product_sku_price_purchase','product_sku_price_cost','product_sku_length','product_sku_width','product_sku_height','product_sku_weight','product_sku_sensitive_type','product_sku_warehouse','product_sku_flg_infringe','product_sku_status'),
	'promote_discount' => array('promote_discount_id','promote_discount_title','promote_discount_type','promote_discount_rule','promote_discount_effect_type','promote_discount_effect_value','promote_discount_status','promote_discount_time_start','promote_discount_time_end'),
	'promote_range' => array('promote_range_id','promote_discount_id','promote_bundle_id','promote_range_type','promote_range_content','promote_range_status'),
	'sizechart_product' => array('sizechart_product_id','product_id','sizechart_product_title','sizechart_product_content','sizechart_product_status'),
	'widget_image' => array('widget_image_id','widget_image_version','widget_image_position','widget_image_title','widget_image_url','widget_image_image'),
	'widget_product' => array('widget_product_id','widget_product_title','widget_product_mainproduct_id','widget_product_mainproduct_title','widget_product_content','widget_product_sort','widget_product_status'),
	'slogan' => array('slogan_id','slogan_content','slogan_status','slogan_time_start','slogan_time_end'),
	'product_slogan' => array('product_slogan_id','product_id','slogan_id','product_slogan_status'),
	'note' => array('note_id','note_content','note_status','note_time_start','note_time_end'),
	'note_range' => array('note_range_id','note_id','category_id','product_id','note_range_status'),
	'product_icon' => array('product_icon_id','product_id','product_icon_type','product_icon_status'),
	'recommendation_manual' => array('recommendation_manual_id','category_id','product_id','recommendation_manual_content','recommendation_manual_status'),
	'recommendation_statistics' => array('product_id','recommendation_statistics_content'),
	'promotion' => array('promotion_id','promotion_template','promotion_title','promotion_url','promotion_banner','promotion_config','promotion_status','promotion_time_start','promotion_time_end'),
	'promotion_detail' => array('promotion_detail_id','promotion_id','category_id','promotion_detail_title','promotion_detail_url','promotion_detail_config','promotion_detail_content','promotion_detail_sort','promotion_detail_status'),
	'blacklist' => array('blacklist_id','blacklist_type','blacklist_content','blacklist_status'),
	'discount' => array('discount_id','discount_title','discount_url','discount_type','discount_condition','discount_type_effect','discount_effect','discount_status','discount_time_start','discount_time_finish'),
	'discount_range' => array('discount_range_id','discount_id','category_id','product_id','exclude_product_id','discount_range_status'),
	'search_optimization' => array('search_optimization_id','language_id','search_optimization_content','search_optimization_url','search_optimization_status','search_optimization_time_start','search_optimization_time_end'),
	'ad_category' => array('ad_category_id','category_id','ad_category_content','ad_category_status','ad_category_time_start','ad_category_time_end'),
	'brand' => array('brand_id','brand_category_id','brand_title','brand_url','brand_icon','brand_banner','brand_description','brand_sort','brand_status','brand_pid_count'),
	'brand_category' => array('brand_category_id','brand_category_title','brand_category_url','brand_category_image','brand_category_sort','brand_category_status'),
	'brand_exclude' => array('brand_exclude_id','brand_id','product_id','brand_exclude_status'),
	'widget_brand' => array('widget_brand_id','product_id','widget_brand_sort','widget_brand_status'),
	'country_shipping_rule' => array('country_shipping_rule_id','country_code','country_shipping_rule_shipping_code','country_shipping_rule_limit_weight','country_shipping_rule_limit_volume','country_shipping_rule_limit_length','country_shipping_rule_status_active','country_shipping_rule_status_disable','country_shipping_rule_status_sensitive_disable','country_shipping_rule_status_battery_disable'),
	'deal' => array('deal_id','deal_title','deal_type','deal_url','deal_image','deal_content','deal_sort','deal_status'),
);

//多语言范围
$language_range_array = array(1=>'us',6=>'br',2=>'de',3=>'es',5=>'fr',4=>'it',7=>'ru');

//海外仓库范围(de是德国，es是西班牙，uk是英国，us是美国，au是澳大利亚)
$warehouse_range_array = array('de'=>'Germany','es'=>'Spain','uk'=>'United Kingdom','us'=>'United States','au'=>'Australia');
//shipping_list
$shipping_list = array(
		1=>'airmail',2=>'standard',3=>'express',
		4=>'register_airmail',5=>'register_standard',6=>'cnmail',7=>'freesea',
);
//memcache缓存时间配置
$mem_expired_time = array(
	'base_category'=>7200,
	'top_category'=>7200,
	'second_category'=>7200,
	'third_category'=>7200,
	'other_category'=>7200,
	'last_category'=>7200,
	'category_related'=>7200,
    'ad_category'=>7200,
    'brand_category'=>7200,
	
	'attribute_category'=>7200,//category的attribute属性值
	'attribute_lang'=>7200,//category的attribute属性值
	'category_search_match'=>7200,//分类下商品结果
	'category_product_type'=>7200,//某分类下，主分类商品副分类商品id缓存
	'attribute_product'=>7200,//属性值对应商品
	'category_attribute_product_cache'=>7200,//分类页controller全部数据缓存
	
	"index_image"=>7200,//首页焦点图
	'index_keywords'=>7200,
		
	"index_detal"=>900,//首页商品列表,缓存15分钟
	"widget_image"=>7200,//首页店铺数据

	"product_info"=>7200,//商品详情
	"product_discount_range"=>7200,//商品促销活动
	"product_discount"=>7200, //促销详情
	"base_currency"=>15600,//汇率
	'product_sku'=>7200,//sku信息
	'product_sku_attr'=>7200,//sku的属性信息
	'category_template'=>86400,//商品下方分类描述
	
	'category_product_recommend'=>7200,//分类下商品推荐
	'category_redirect'=>7200,//分类跳转
	'product_icon'=>7200,//商品icon标志
	'product_slogan'=>7200,//商品slogan标志
	'search_recommend_product'=>7200,//搜索结果页，推荐商品
    'search_optimization'=>7200,//搜索结果页，特定关键词跳转
	'product_complexattr'=>7200,//商品主属性及属性组缓存器
	'product_category_recommend'=>7200,//商品同父类下推荐商品
	'product_category_bundle'=>7200,//商品，分类下的捆绑id
	'product_p_bundle'=>7200,//商品，捆绑及非捆绑商品id的捆绑
	'bundle_product'=>7200,//捆绑下商品列表
	'recommendation_statistics_product'=>7200,//also_like商品
	'product_note'=>7200,//商品单品页Note标语
	'product_qna'=>7200,//商品qna
	'product_attr_with_block'=>7200,//商品全部属性包含block
	'product_attr_id'=>7200,//商品属性
	'product_attr_value_id'=>7200,//属性组
	'product_vice_image'=>7200,//商品属性简介图片
	'product_sizechart'=>7200,//商品尺寸
	'customer_all_review'=>7200,//用户所有评论
	'customer_product_review'=>7200,//用户的某商品评论
	'country_shipping_rule'=>7200,//shipping 运送规则
	'express_zone'=>7200,//express zone表数据
	'express_country2zone'=>7200,//express country2zone
	'adyen_list'=>7200,//adyen列表
	'coupon_code_info'=>7200,//coupon表
	'place_order_country'=>7200,
	'all_country'=>36000,//国家编码对应国家名称信息
	'email_template'=>7200,
	'atoz_view'=>604800,//atoz_view页面7天
	'product_tags_desc'=>7200,
	'each_buyer_category_products'=>600,//分类页商品列表
    'view_cate_product'=>7200,//副分类产品
    'fullcut_discounts'=>7200,//某一商品的满减促销
    'each_buyer_brand_products'=>7200,//品牌商品
    'widget_brand'=>7200,//品牌热门商品
    'deal_list'=>7200,//Deal列表
    'deal'=>7200,//单个Deal
    
    'search_optimization'=>21600,//搜索人工干预
);

//rewards
define("REWARDS_DISCOUNT",0.8);

define("PRODUCT_HOT_TYPE_VALUE", 2);//商品hot值
define("PRODUCT_NEW_DAYS", 30);//商品为New的天数两周（商品上架日期）

//当前widget_iamge版本号
define("WIDGET_IMAGE_VERSION", 1);

//category_related type类型
define('CATEGORY_RELATED_TYPE_CATEGORY', 1);//分类
define('CATEGORY_RELATED_TYPE_PRODUCT', 2);//商品

//category表中 type值
define('CATEGORY_TYPE_MAIN', 1);//主分类商品
define('CATEGORY_TYPE_VICE', 2);//副分类商品

define('CATEGORY_TYPE_NO_DISPLAY', 1);
define('CATEGORY_TYPE_TRAN_DISPLAY', 2);//横版
define('CATEGORY_TYPE_VER_DISPLAY', 4);//竖版

define('SEARCH_RESULT_NUM', 12);//搜索结果数小于该数字时，新增推荐数据

//购物车中商品类型
define("CART_TYPE_DEFALUT",0);

define('BASIC_URL',config_item('base_url').'/');

$payment_list = array(
	1 => 'paypal_ec',
    7 => 'adyen',
    2 => 'paypalsk',//非adyen中的paypal
    3 => 'bank',
    4 => 'credit_card',
	20 =>'alipay' ,
    21 => 'amex',
    22 => 'bcmc', // => 'Bancontact_Mister-Cash.jpg',
    23 => 'cartebancaire',
    24 => 'diners',
    25 => 'discover' ,
    26 => 'ebanking_FI' ,
    27 => 'giropay',
    28 => 'ideal',
	29 => 'jcb',
    30 => 'maestro' ,
    31 => 'maestrouk',
    32 => 'mc' ,
	33 => 'paypal',//PPUK
    34 => 'qiwiwallet',
    35 => 'safetypay',
    36 => 'directEbanking' ,//Sofortüberweisung
    37 => 'unionpay' ,
    38 => 'visa' ,
    39 => 'bankTransfer_IBAN',
    40 => 'bankTransfer_BE',
    41 => 'bankTransfer_CH',
    42 => 'bankTransfer_DE',
    43 => 'bankTransfer_GB',
    44 => 'bankTransfer_NL',
);

$payment_name = array(
    1 => 'PayPal',
    2 => 'PayPal',
    3 => 'Wire Transfer',
    20 =>'AliPay',
    21 => 'American Express',
    22 => 'Bancontact/Mister Cash',
    23 => 'Carte Bancaire',
    24 => 'Diners Club',
    25 => 'Discover',
    26 => 'Finnish E-Banking',
    27 => 'GiroPay',
    28 => 'iDEAL',
    29 => 'JCB',
    30 => 'Maestro',
    31 => 'Maestro UK',
    32 => 'MasterCard',
    33 => 'PayPal',
    34 => 'Qiwi Wallet',
    35 => 'SafetyPay',
    36 => 'Sofortüberweisung',
    37 => 'UnionPay',
    38 => 'VISA',
    39 => 'Bank Transfer International',
    40 => 'Bank Transfer BE',
    41 => 'Bank Transfer CH',
    42 => 'Bank Transfer DE',
    43 => 'Bank Transfer GB',
    44 => 'Bank Transfer NL',
);
/* End of file application_config.php */
/* Location: ./application/config/application_config.php */
