<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!class_exists('Dcontroller')) {
	class Dcontroller extends CI_controller {
		protected $prefix = "payment_";//payment前缀
		
		protected $_view_data = array();
        protected $active_range = array();
		protected $_flg_language_switchable = true;
		protected $_flg_mobile_compatible = false;
		private $cat_li_number = 20;
		//public $flush_cache = false;
		
		protected $ecomm_prodid = '';
		protected $ecomm_pagetype = '';
		protected $ecomm_pname = '';
		protected $ecomm_pcat = '';
		protected $ecomm_pvalue = '';
		protected $ga_dataLayer = '';
		
		protected $ratecny = '';
		protected $top_banner = true;//顶通banner是否显示
		
		public function __construct(){
			parent::__construct();
			$this->load->helper(array('app','cookie','url','array','other','language'));
			$this->load->library(array('session','memcache','log','database'));

			$this->load->model('Keywordsmodel','m_keywords');
			$this->load->model("customermodel","customer");
			$this->load->model('currencymodel','currencymodel');
			
			$this->_resolveCurrentLanguage();
			$this->_resolveCurrentCurrency();
			$this->_resolveWebgainsParameter();

			$current_page = strtolower(get_class($this));
			$language_code = currentLanguageCode();
			//cny汇率
			$this->_getCnyRate();
			
			$this->load->language('common',$language_code);
			$this->load->language($current_page,$language_code);

			/*
			 * check user login & save user info into session
			*/
			$this->_addUserInfo();
			
			//分类信息
			$this->load->model("categorymodel","cate_model");
			$all_cate = $this->cate_model->getAllCateinfos(currentLanguageId());
			$tree = spreadArray($all_cate,'parent_id');
			$this->_view_data['all_category'] = $tree;
			
			//echo "<pre>";print_r($tree);die;
			$tree = $this->cate_model->buildTree($tree);
			//atoz characters
			$charactersList = array(
					'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0-9'
			);
			$this->_view_data['charactersList'] = $charactersList;
			//全站顶部广告位 start
			$this->load->model("imageadmodel","imagead");
			$sit_top_image_ad = $this->imagead->getLocationWithId(6);
			$this->_view_data['sale_banner_date_end'] = 0;
			if(!empty($sit_top_image_ad)){
				$sit_top_imagead_info = $sit_top_image_ad[0];
				$language_id = currentLanguageId();
				if(strtotime($sit_top_imagead_info['ad_time_start']) < time() && strtotime($sit_top_imagead_info['ad_time_end']) > time()){
					$imagead_content = json_decode($sit_top_imagead_info['ad_content'],true);
					$sit_top_imagead_info['content'] = $imagead_content[$language_id];
					unset($sit_top_imagead_info['ad_content']);
					$this->_view_data['sit_top_imagead'] = $sit_top_imagead_info;
					$sale_banner_date_end = strtotime($sit_top_imagead_info['ad_time_end'])-time();
					if($sale_banner_date_end > 0) $this->_view_data['sale_banner_date_end'] = $sale_banner_date_end;
				}
			}
			$this->_view_data['top_banner'] = $this->top_banner;
			//全站顶部广告位 end
			
			//多列处理
			$last_list = $this->formatCateCol($tree);
			$tongji_userdata = $this->session->get('tongji_userdata');
			//统计用户信息
			if(isset($tongji_userdata)){
				$this->_view_data['tongji_userdata'] = $tongji_userdata;
			}
			$this->_view_data['cate_tree'] = $last_list;
			$this->_view_data['keywords'] = '';
			$this->_view_data['language_code'] = $language_code;
			$this->_view_data['current_page'] = $current_page;
			$this->_view_data['currency'] = currentCurrency();
		}

		public function index($page=''){
			$this->_addHeadInfo();
			$this->_addHeaderInfo();
			
			/*echo '<pre>';
			 print_r($this->database->slave);
			die();*/

			//$this->database->dumpSQL('slave');
			//die();

			$this->database->close();

			//GA统计
			$this->all_google_tag_params($this->ecomm_prodid ,$this->ecomm_pagetype ,$this->ecomm_pname ,$this->ecomm_pcat ,$this->ecomm_pvalue );
			
			$this->load->view(strtolower(get_class($this)),$this->_view_data);
			/*if(!empty($page)){
				$this->load->view($page,$this->_view_data);	
			}else{
				$this->load->view(strtolower(get_class($this)),$this->_view_data);
			}*/

		}
		
		public function index2($page=''){
			$this->_addHeadInfo();
			$this->_addHeaderInfo();
				
			/*echo '<pre>';
			 print_r($this->database->slave);
			die();*/
		
			$this->database->close();
		
			//GA统计
			$this->all_google_tag_params($this->ecomm_prodid ,$this->ecomm_pagetype ,$this->ecomm_pname ,$this->ecomm_pcat ,$this->ecomm_pvalue );
				
			if(!empty($page)){
			 $this->load->view($page,$this->_view_data);
			}else{
			$this->load->view(strtolower(get_class($this)),$this->_view_data);
			}
		
		}

		protected function _resolveWebgainsParameter(){
			$utm_medium = $this->input->get('utm_medium');
			$utm_source = $this->input->get('utm_source');
			$source = $this->input->get('source');
			$utm_campaign = $this->input->get('utm_campaign');

			if ( $utm_medium === false || $utm_source === false ) return false;

			$utm_medium = trim($utm_medium);
			$utm_source = trim($utm_source);
			$source = trim($source);
			$utm_campaign = trim($utm_campaign);
			$expiresTime = 45*86400;
			if($utm_campaign == 47947) $expiresTime = 86400;

			if($utm_medium == 'NetworkAffiliates' || $utm_medium == 'aff' || $utm_medium == 'mediaffiliation'){
				set_cookie('eb_smclog',"w=$source&s=$utm_source&m=$utm_medium&c=$utm_campaign",$expiresTime);
			}

			return true;
		}

		protected function _resolveCurrentLanguage(){
			global $language_list;

			$domain = $_SERVER['HTTP_HOST'];
			$domain = explode('.', $domain);
			$domain = $domain[0];
			$domain = substr($domain,0,2);

			$language_id = DEFAULT_LANGUAGE;
			foreach($language_list as $id => $code){
				if($domain == $code){
					$language_id = $id;
					break;
				}
			}

			$this->language_id = $language_id;
		}

		protected function _resolveCurrentCurrency(){
			global $currency_list;
			global $language_info;

			$currency = DEFAULT_CURRENCY;
			$currencyFromUrl = $this->input->get('currency');
			$currencyFromCookie = get_cookie('currency');
			
			if($currencyFromUrl !== false){
				$currency = strtoupper(trim($currencyFromUrl));
			}elseif($currencyFromCookie !== false){
				$currency = strtoupper(trim($currencyFromCookie));
			}else{
				$currency = $language_info[$this->language_id]['currency'];
			}

			if(!in_array($currency,$currency_list)) $currency = DEFAULT_CURRENCY;

			//set_cookie('currency',$currency,864000);
			$this->currency = $currency;
		}

		protected function _addHeadInfo(){
			global $base_url;
			global $language_info;
			global $base_url_mobile;
			$language_id = currentLanguageId();
			$this->_view_data['head'] = array();

			if($_SERVER['REQUEST_URI'] && strpos($_SERVER['REQUEST_URI'],'?')!==false){
				$canonical = $_SERVER['REQUEST_URI'];
				$canonical = substr($canonical,1,strpos($_SERVER['REQUEST_URI'],'?')-1);
				$canonical = genURL($canonical);
				$this->_view_data['head']['canonical'] = $canonical;
			}

			$alternate_list = array();
			if($this->_flg_language_switchable){
				foreach($base_url as $id => $url){
					$common_code = $language_info[$id]['common_code'];
					$alternate_list[$common_code] = $url.uri_string();
				}
			}
			if($this->_flg_mobile_compatible){
				$alternate_list['m'] = $base_url_mobile[$language_id].uri_string();
			}
			$this->_view_data['head']['alternate_list'] = $alternate_list;
		}

		protected function _addHeaderInfo(){
			global $base_url;
			global $language_info;
			global $currency_list;
			$language_id = currentLanguageId();
			$this->_view_data['header'] = array();

			$this->_view_data['header']['currency_list'] = $currency_list;

			$language_list = array();
            $query_str = '';
            if($_SERVER['QUERY_STRING'])
                $query_str = '?'.$_SERVER['QUERY_STRING'];
			foreach($base_url as $id => $url){
				$language_list[] = array(
					'id' => $id,
					'title' => $language_info[$id]['title'],
					'url' => $url.uri_string().$query_str,
					'current' => ($language_id == $id),
				);
			}
			$this->_view_data['header']['language_list'] = $language_list;

			$all_keyworys_type = array(KEYWORDS_TYPE_SEARCH,KEYWORDS_TYPE_HEADER,KEYWORDS_TYPE_FOOTER);
			$all_keywords_list = $this->m_keywords->getKeywordsListWithTypeArray($all_keyworys_type,$language_id);
			$header_keywords_list = array();
			foreach ($all_keywords_list as $keywords_key=>$keywords_val){
				if($keywords_val['keywords_type']==KEYWORDS_TYPE_SEARCH){
					$this->_view_data['header']['search_keywords_list'][] = $keywords_val;
				}elseif($keywords_val['keywords_type']==KEYWORDS_TYPE_HEADER){
					$header_keywords_list[] = $keywords_val;
				}elseif($keywords_val['keywords_type']==KEYWORDS_TYPE_FOOTER){
					$this->_view_data['header']['footer_keywords_list'][] = $keywords_val;
				}
			}
			$this->_view_data['header']['header_keywords_list'] = spreadArray($header_keywords_list,'keywords_highlight');
			
			/*$this->_view_data['header']['search_keywords_list'] = $this->m_keywords->getKeywordsList(KEYWORDS_TYPE_SEARCH,$language_id);
			
			$header_keywords_list = $this->m_keywords->getKeywordsList(KEYWORDS_TYPE_HEADER,$language_id);
			$this->_view_data['header']['header_keywords_list'] = spreadArray($header_keywords_list,'keywords_highlight');

			$footer_keywords_list = $this->m_keywords->getKeywordsList(KEYWORDS_TYPE_FOOTER,$language_id);
			$this->_view_data['header']['footer_keywords_list'] = $footer_keywords_list;*/
		}
			
		/**
		 * @desc 获取币种及汇率值；默认为“$” 时返回false
		 * @return multitype:string unknown |boolean
		 */
		public function getCurrencyNumber($flush_cache = false){
			$this->load->model("currencymodel","currencymodel");
			$info = $this->currencymodel->todayCurrency($flush_cache);

			$result = $this->floatcmp($info['currency_rate'], 1);
			if(!$result){
				$format_string = trim(str_replace("%s", "", $info['currency_format']));
					
				return array("currency_rate"=>$info['currency_rate'],"currency_format"=>$format_string);
			}
			return false;
		}
		
		/**
		 * @desc 根据汇率编码获取汇率货币单位
		 * @param unknown $currency_code
		 * @return string|unknown
		 */
		public function getCurrencyWithCode($currency_code){
			if($currency_code=='USD' || empty($currency_code)) return '$';
			$this->load->model('currencymodel','currencymodel');
			$need_currency = $this->currencymodel->getConfigCurrency($currency_code);
			return $need_currency;
		}
		
		/**
		 * @desc 比较两个浮点数大小
		 * @param unknown $f1
		 * @param unknown $f2
		 * @param number $precision
		 * @return boolean
		 */
		public function floatcmp($f1,$f2,$precision = 10) {// are 2 floats equal
			$e = pow(10,$precision);
			$i1 = intval($f1 * $e);
			$i2 = intval($f2 * $e);
			return ($i1 == $i2);
		}
		
		//获取对应促销活动等折扣价格
		public function productWithPrice($data){
			if(!count($data) && !is_array($data)) return false;
			$this->load->model("discountmodel","discount");
			$this->load->model("discountrangemodel","discountrange");
            $currency_format = $this->getCurrencyNumber();

			foreach ($data as $k=>&$v){
				if(isset($v['product_id']) && $v['product_id'] && is_numeric($v['product_id'])){
					$product_id = $v['product_id'];
		
					$front_price = $v['product_price'];
					$market_price = $v['product_price_market'];
					$discount_infos = $this->singleProductDiscount($product_id,$market_price);

                    $v['product_basediscount_price'] = $v['product_discount_price'] = isset($discount_infos["discount_price"])?$discount_infos["discount_price"]:$front_price;
					$v['product_discount_number'] = isset($discount_infos["discount_number"])?$discount_infos["discount_number"]:0;
					$v['product_currency'] = "$";
					//汇率转换
					if($currency_format){
						$v['product_currency'] = $currency_format['currency_format'];
                        $v['product_basediscount_price'] = $v['product_discount_price'] = round($v['product_discount_price']*$currency_format['currency_rate'],2);
						$v['product_price_market'] = round($v['product_price_market']*$currency_format['currency_rate'],2);
					}
					
					//开始结束时间
					$v['discount_start'] = isset($discount_infos["discount_info"]['discount_time_start'])?$discount_infos["discount_info"]['discount_time_start']:'';
					$v['discount_end'] = isset($discount_infos["discount_info"]['discount_time_finish'])?$discount_infos["discount_info"]['discount_time_finish']:'';
					
				}
		
			}
			return $data;
		}
		
		//获取对应促销活动等折扣价格
		protected function singleProductWithPrice($data,$sku_array = array()){
			if(isset($data['product_id']) && $data['product_id'] && is_numeric($data['product_id'])){
				$product_id = $data['product_id'];

				$front_price = isset($sku_array['product_sku_price']) && $sku_array['product_sku_price']?$sku_array['product_sku_price']+$data['product_price']:$data['product_price'];
				$market_price = isset($sku_array['product_sku_price']) && $sku_array['product_sku_price_market']?$sku_array['product_sku_price_market']+$data['product_price_market']:$data['product_price_market'];
				$discount_infos = $this->singleProductDiscount($product_id,$market_price);
				//折扣后价钱（美元价）
				$data['product_discount_price'] = isset($discount_infos["discount_price"])?$discount_infos["discount_price"]:$front_price;
				//折扣数
				$data['product_discount_number'] = isset($discount_infos["discount_number"])?$discount_infos["discount_number"]:0;
				$data['product_currency'] = "$";
				
				//汇率转换前价格
				$data['product_baseprice'] = $front_price;//price+sku价格
				$data['product_basediscount_price'] = $data['product_discount_price'];//汇率前美元折扣后价
				$data['product_baseprice_market'] = $data['product_price_market'] = $market_price;//汇率前美元市场价
				$data['product_new_currency_price'] = $data['product_price'];//汇率后product_price
				//汇率转换
				$currency_format = $this->getCurrencyNumber();
				if($currency_format){
					$data['product_currency'] = $currency_format['currency_format'];
					$data['product_discount_price'] = round($data['product_discount_price']*$currency_format['currency_rate'],2);
					$data['product_price_market'] = round($data['product_price_market']*$currency_format['currency_rate'],2);
					$data['product_new_currency_price'] = round($front_price*$currency_format['currency_rate'],2);//汇率后product_price
				}
				//开始结束时间
				$data['discount_start'] = isset($discount_infos["discount_info"]['discount_time_start'])?$discount_infos["discount_info"]['discount_time_start']:'';
				$data['discount_end'] = isset($discount_infos["discount_info"]['discount_time_finish'])?$discount_infos["discount_info"]['discount_time_finish']:'';
			}
		
			return $data;
		}
		
		/**
		 * @desc 根据商品id获取促销活动后的折扣及折扣价(针对单个商品，最简单折扣)
		 * @param unknown $product_id
		 * @param unknown $front_price
		 * @return multitype:Ambigous <unknown, number> unknown
		 */
		public function singleProductDiscount($product_id,$market_price,$flush_cache = false){
			$result = array();
			$discount_price = $market_price;//product_price_market，大价格
			$this->load->model("discountmodel","discount");
			$this->load->model("discountrangemodel","discountrange");
		
			$active_range = $this->active_range;
			if(empty($active_range)){
				//折扣率
				/*$param['promote_discount_type'] = 1;
				$discount_info = $this->discount->getActiveDiscount($param);
				$active_range = $this->discountrange->getRangeByDiscountIds($discount_info);
				*/
				$param['discount_type'] = 1;
				$param['discount_type_effect'] = 1;
				$discount_info = $this->discount->getActiveDiscountNew($param);
				$active_range = $this->discountrange->getNewRangeByDiscountIds($discount_info);
				$this->active_range = $active_range;
			}
			if(isset($active_range[$product_id])){
				//找出最大折扣*********************************************
				$discount_ids_array = array();
				$max_discount_id = 0;
				$max_discount_num = 0;
				$discountinfos = "";
				//存在折扣价
				$nowTime = requestTime();
				$defaultTime =  0;
				//找出符合条件，最大折扣
				foreach ($active_range[$product_id] as $discount_k=>$discount_v){
					$start_time = isset($discount_v['discount_time_start'])?strtotime($discount_v['discount_time_start']):$defaultTime;
					$end_time = isset($discount_v['discount_time_finish'])?strtotime($discount_v['discount_time_finish']):$defaultTime;
					$discount_effect_value = isset($discount_v['discount_effect'])?$discount_v['discount_effect']:0;
					$discount_id = isset($discount_v['discount_id'])?$discount_v['discount_id']:0;
					if($discount_effect_value > $max_discount_num && $start_time< $nowTime && $end_time > $nowTime){
						$max_discount_num = $discount_effect_value;
						$max_discount_id = $discount_id;
						$discountinfos = $discount_v;
					}
				}
		
				$result['discount_id'] = $max_discount_id;
				$result['all_discount_ids'] = $discount_ids_array;
				$result['discount_info'] =$discountinfos;
				$result['discount_number'] = $max_discount_num;
		
				if($max_discount_num!=0){
					$real_discount = (100-$max_discount_num)/100;
					$discount_price = $market_price*$real_discount;
					$discount_price = round($discount_price,2);
					$result['discount_price'] = $discount_price;
				}
			}
			//echo "<pre>bbbb";print_r($result);die;
			return $result;
		}
		/**
		 * @desc 对所有discount_ids列表中找出最大的折扣数
		 * @param unknown $all_discount_ids
		 * @return Ambigous <number, unknown>
		 */
		/*public function singleProductBatchDiscount($all_discount_ids){
			$max_discount_id = 0;
			$max_discount_num = 0;
			$discountinfos = "";
			
			$this->load->model("discountmodel","discount");
			
			//所有折扣discount_id详情
			$all_discount_infos = $this->discount->getBatchDiscountWithIds($all_discount_ids);
            $nowTime = requestTime();
			//找出符合条件，最大折扣
			foreach ($all_discount_infos as $discount_k=>$discount_v){
				$start_time = isset($discount_v['promote_discount_time_start'])?strtotime($discount_v['promote_discount_time_start']):0;
				$end_time = isset($discount_v['promote_discount_time_end'])?strtotime($discount_v['promote_discount_time_end']):0;
				$discount_effect_value = isset($discount_v['promote_discount_effect_value'])?$discount_v['promote_discount_effect_value']:0;
				$discount_id = isset($discount_v['promote_discount_id'])?$discount_v['promote_discount_id']:0;
				
				if($discount_effect_value > $max_discount_num && $start_time<$nowTime && $end_time > $nowTime){
					$max_discount_num = $discount_effect_value;
					$max_discount_id = $discount_id;
					$discountinfos = $discount_v;
				}
			}
			
			return $max_discount_num;
		}*/

        /** 处理商品优惠分组的统一接口
         * @param array $product_list
         * 必要结构描述：
         * $product_list = array(''=>array(  'product_quantity'=>'',
         *                                   'product_info'=>array(
         *                                      'product_discount_price'=>'',
         *                                      'product_basediscount_price'=>'',
         *                                      'product_path'=>'')))
         */
		public function getRangePlanGroup($product_list = array()){
            if(!is_array($product_list) || empty($product_list)) return $product_list;
            $product_group = array();
            $this->load->model("discountmodel","discount");
            $this->load->model("discountrangemodel","discountrange");
            //满减活动——暂时写死只有满减活动，以后兴许会有其他活动的方式
            $param['discount_type'] = 5;
            //获取有效满减活动信息
            $discount_info = $this->discount->getActiveDiscountNew($param);
            //优惠方案的汇率转换
            $currency_format = $this->getCurrencyNumber();
            if(!$currency_format){
                $currency_format['currency_rate'] = 1;
                $currency_format['currency_format'] = '$';
            }
            $nowTime = date('Y-m-d H:i:s', requestTime());
            $language_id = currentLanguageId();
            if(!empty($discount_info)){
                foreach($discount_info as $key=>&$row){
                    //去掉无效的优惠活动
                    if($row['discount_time_start'] > $nowTime || $row['discount_time_finish'] < $nowTime){
                        unset($discount_info);
                        continue;
                    }
                    $row['view_discount_condition'] = number_format($row['discount_condition']*$currency_format['currency_rate'],2,'.',',');
                    if($row['discount_type_effect'] == 1){
                        $row['view_discount_effect'] = number_format($row['discount_effect']*$currency_format['currency_rate'],2,'.',',');
                    } else {
                        $row['view_discount_effect'] = $row['discount_effect'];
                    }
                    $row['currency'] = $currency_format['currency_format'];
                    $title_array = json_decode($row['discount_title'],true);
                    $row['view_title'] = $title_array[$language_id];
                    if($row['discount_type_effect'] == 1){
                        $view_discount_effect = $row['view_discount_effect'].'%';
                    } else {
                        $view_discount_effect = $row['currency'].$row['view_discount_effect'];
                    }
                    $replace_array = array($row['currency'].$row['view_discount_condition'],$view_discount_effect);
                    //少多语言前缀
                    $row['view_title'] = str_replace(array('{$condition_price}','{$effect_price}'),$replace_array,$row['view_title']);
                    $row['discount_url'] = $row['discount_url'] == ''?'#':genURL($row['discount_url']);
                }
            }

            //获取有效满减活动的详细信息(满减规则)
            $active_range = $this->discountrange->getRangeByDiscountInfo($discount_info);
            //计算单品总价格
            foreach($product_list as &$product){
                $product['view_product_sum'] = $product['product_info']['product_discount_price'] * $product['product_quantity'];
                $product['product_sum'] = $product['product_info']['product_basediscount_price'] * $product['product_quantity'];
            }
            //商品分组——将商品发放到每一个满足促销条件的促销规则中
            $product_group = $this->getRangeProductGroup($product_list,$active_range);
            //循环商品组，查看是否能够激活促销条件
            foreach($product_group as &$Range_row){
                $Range_row = $this->formatRangeGroupInfo($Range_row);
            }
            //echo '<pre>';print_r($product_group);exit;
            $product_group = array_sort($product_group,'off_price','desc');
            //找出有优惠的分组，对照其商品列表是否有重合，如有重合需要去掉重合商品重新计算该分组信息
            $product_ids = array();
            foreach($product_group as &$Range_row){
                //第一个条件判断其是否有优惠类型，第二个条件判断其是否打折
                if(!isset($Range_row['discount_type']) || !$Range_row['is_off'] || !isset($Range_row['product_list'])) continue;
                $count_product = count($Range_row['product_list']) + count($product_ids);
                if($count_product){
                    $item_product_ids = array_keys($Range_row['product_list']);
                    $new_product_ids = array_merge($product_ids,$item_product_ids);
                    //出现重合情况
                    if(count($product_ids) < $count_product){
                        $release_pids = array_intersect($product_ids,$item_product_ids);
                        //去掉重合的商品
                        foreach($release_pids as $pkey){
                            unset($Range_row['product_list'][$pkey]);
                        }
                        //重新计算该分组信息
                        $Range_row = $this->formatRangeGroupInfo($Range_row);
                        if($Range_row['is_off'])
                            $product_ids = $new_product_ids;
                    } else {
                        $product_ids = $new_product_ids;
                    }
                }
            }
            //经过上轮处理product_ids这个变量中已经存储了所有满足优惠条件的商品id，此时需要处理的就是所有未满足优惠条件的商品的去重
            foreach($product_group as &$Range_row){
                if(!isset($Range_row['product_list']) || $Range_row['is_off']) continue;
                $count_product = count($Range_row['product_list']) + count($product_ids);
                if($count_product) {
                    $item_product_ids = array_keys($Range_row['product_list']);
                    $new_product_ids = array_merge($product_ids, $item_product_ids);
                    //出现重合情况
                    if (count($product_ids) < $count_product) {
                        $release_pids = array_intersect($product_ids, $item_product_ids);
                        //去掉重合的商品
                        foreach ($release_pids as $pkey) {
                            unset($Range_row['product_list'][$pkey]);
                        }
                        //重新计算该分组信息
                        $Range_row = $this->formatRangeGroupInfo($Range_row);
                    }
                }
                $product_ids = $new_product_ids;
            }
            //去掉没有商品的优惠组
            foreach($product_group as $key=>$value){
                if(!isset($value['product_list']) || empty($value['product_list'])) unset($product_group[$key]);
            }
            sort($product_group);
            //echo '<pre>';print_r($product_group);exit;
            return $product_group;
        }

        /**
         * 针对优惠组进行数据规范化处理
         * 该处需要处理处几个值，以便以后使用，金额单位为美元
         * off_price优惠的金额
         * is_off是否优惠
         * group_sum分组的最终总金额
         * short_sum差多少钱满足优惠条件
         * @param $Range_row
         * @return array
         */
        public function formatRangeGroupInfo($Range_row){
            $Range_row['is_off'] = false;//是否优惠
            $Range_row['short_sum'] = 0;//差多少满足优惠
            $Range_row['view_short_sum'] = 0;
            $Range_row['off_price'] = 0;
            $Range_row['view_off_price'] = 0;
            if(isset($Range_row['discount_type'])){
                if($Range_row['discount_type'] == 5){
                    //满减规则的计算
                    $Range_row = $this->formatFullRangeGroupInfo($Range_row);
                }
            } else {
                $Range_row = $this->formatDefaultGroupInfo($Range_row);
            }
            return $Range_row;
        }

        /**
         * 给商品分到符合条件的优惠组中
         * @param array $product_list
         * @param array $active_range
         * @return array
         */
        public function getRangeProductGroup($product_list = array(),$active_range = array()){
            if(!is_array($product_list) || empty($product_list)) return $product_list;
            //循环商品列表，将商品放入所有优惠的规则里进行处理
            //没有参与折扣的商品
            $no_off = array();
            foreach($product_list as $product){
                $category_ids = array();
                if(isset($product['product_info']['product_path']))
                    $category_ids = explode('/',$product['product_info']['product_path']);
                //无效商品不参加活动
                if(empty($category_ids)) continue;
                //促销标识，有参与促销活动则
                $is_off = false;
                if(!empty($active_range)){
                    foreach($active_range as $key=>&$value){
                        if(!empty($value['category_ids'])){
                            //分类id有交集并且不在排除的商品ID数组中——放入该满减规则中
                            $category_list = array_intersect($category_ids,$value['category_ids']);
                            if(!empty($category_list) && !in_array($product['product_id'],$value['exclude_product_ids'])){
                                //参与满减活动
                                $is_off = true;
                                $value['product_list'][$product['product_id'].$product['product_sku']] = $product;
                            }
                        }
                        if(!empty($value['product_ids'])){
                            //商品ID在该满减的绑定商品id数组中——放入该满减规则中
                            if(in_array($product['product_id'],$value['product_ids'])){
                                //参与满减活动
                                $is_off = true;
                                $value['product_list'][$product['product_id'].$product['product_sku']] = $product;
                            }
                        }
                    }
                }
                //没有折扣信息的商品放在0组
                if(!$is_off)
                    $no_off['product_list'][$product['product_id']] = $product;
            }
            //分组完毕-整理数据，key值为0的分组为没有折扣活动的分组
            $product_group = $active_range;
            $product_group[0] = $no_off;
            return $product_group;
        }

        /**
         * 满减&满折算法分组处理
         * @param array $full_range
         * @return array
         */
        public function formatFullRangeGroupInfo($full_range = array()){
            if(!is_array($full_range) || empty($full_range)) return false;
            $currency_format = $this->getCurrencyNumber();
            if(!$currency_format){
                $currency_format['currency_rate'] = 1;
                $currency_format['currency_format'] = '$';
            }
            //设置初值
            $sum = 0;//总和
            $new_sum = 0;//新的商品总和
            $view_new_sum = 0;
            $view_sum = 0;//新的商品显示总和
            $off_sum = 0;//计算中的优惠金额总和
            $view_off_sum = 0;//计算中的优惠金额总和(汇率转换后的)
            if(!empty($full_range['product_list'])){
                foreach($full_range['product_list'] as &$product){
                    //单个商品的总价
                    $sum += $product['product_sum'];
                    $view_sum += $product['view_product_sum'];
                }
            }

            if($sum >= $full_range['discount_condition']){
                $full_range['is_off'] = true;
                //满足满减信息
                if($full_range['discount_type_effect'] == 2){
                    //减去固定金额
                    $full_range['off_price'] = round($full_range['discount_effect'],2);
                    //逐个商品均摊金额
                    foreach($full_range['product_list'] as $key => &$product){
                        $product['product_off'] = round($full_range['discount_effect'] * ($product['product_sum'] / $sum),2);
                        $product['new_product_sum'] = round($product['product_sum'] - $product['product_off'],2);
                        $off_sum += $product['product_off'];
                        $new_sum += $product['new_product_sum'];

                        $product['view_product_off'] = round($product['product_off'] * $currency_format['currency_rate'],2);
                        $product['view_new_product_sum'] = round($product['view_product_sum'] - $product['view_product_off'],2);
                        $view_off_sum += $product['view_product_off'];
                        $view_new_sum += $product['view_new_product_sum'];
                    }
                } else if($full_range['discount_type_effect'] == 1) {
                    $full_range['off_price'] = round($sum * $full_range['discount_effect']/100,2);
                    //逐个商品均摊金额
                    foreach($full_range['product_list'] as $key => &$product){
                        $product['product_off'] = round($product['product_sum'] * $full_range['discount_effect']/100,2);
                        $product['new_product_sum'] = round($product['product_sum'] - $product['product_off'],2);
                        $off_sum += $product['product_off'];
                        $new_sum += $product['new_product_sum'];

                        $product['view_product_off'] = round($product['product_off'] * $currency_format['currency_rate'],2);
                        $product['view_new_product_sum'] = round($product['view_product_sum'] - $product['view_product_off'],2);
                        $view_off_sum += $product['view_product_off'];
                        $view_new_sum += $product['view_new_product_sum'];
                    }
                }
                //处理商品均摊计算中误差值
                $full_range['product_list'][$key]['new_product_sum'] = $full_range['product_list'][$key]['new_product_sum'] - ($full_range['off_price'] - $off_sum);
                $full_range['product_list'][$key]['product_off'] = $full_range['product_list'][$key]['product_off'] + ($full_range['off_price'] - $off_sum);
                $full_range['view_off_price'] = number_format(round($full_range['off_price'] * $currency_format['currency_rate'],2),2,'.',',');
                $full_range['product_list'][$key]['view_new_product_sum'] = $full_range['product_list'][$key]['view_new_product_sum'] - ($full_range['view_off_price'] - $view_off_sum);
                $full_range['product_list'][$key]['view_product_off'] = $full_range['product_list'][$key]['view_product_off'] + ($full_range['view_off_price'] - $view_off_sum);
            } else {
                $full_range['short_sum'] = round($full_range['discount_condition'] - $sum,2);
                $full_range['view_short_sum'] = round($full_range['view_discount_condition'] - $view_sum,2);
                if(isset($full_range['product_list']) && is_array($full_range['product_list']) && !empty($full_range['product_list'])){
                    foreach($full_range['product_list'] as $key => &$product){
                        $product['product_off'] = 0;
                        $product['new_product_sum'] = $product['product_sum'];
                        $off_sum += $product['product_off'];
                        $new_sum += $product['new_product_sum'];

                        $product['view_product_off'] = 0;
                        $product['view_new_product_sum'] = $product['view_product_sum'];;
                        $view_off_sum += $product['view_product_off'];
                        $view_new_sum += $product['view_new_product_sum'];
                    }
                }
            }
            $full_range['group_sum'] = $sum;
            $full_range['view_group_sum'] = $view_sum;
            $full_range['group_new_sum'] = $new_sum - ($full_range['off_price'] - $off_sum);
            $full_range['view_group_new_sum'] = $view_new_sum - ($full_range['view_off_price'] - $view_off_sum);
            return $full_range;
        }

        /**
         * 默认分组数据处理
         * @param array $product_group
         * @return array
         */
        public function formatDefaultGroupInfo($product_group = array()){
            if(!is_array($product_group) || empty($product_group)) return false;
            $sum = 0;
            $view_sum = 0;
            if(isset($product_group['product_list']) && !empty($product_group['product_list'])){
                foreach($product_group['product_list'] as &$product){
                    $product['view_new_product_sum'] = $product['view_product_sum'];
                    $product['new_product_sum'] = $product['product_sum'];
                    $product['product_off'] = $product['view_product_off'] = 0;
                    $sum += $product['product_sum'];
                    $view_sum += $product['view_product_sum'];
                }
            }
            $product_group['view_title'] = '';
            $product_group['discount_url'] = '';
            $product_group['view_short_title'] = '';
            //省的钱数
            $product_group['off_price'] = 0;
            $product_group['is_off'] = false;
            $product_group['group_sum'] = $sum;
            $product_group['view_group_sum'] = $view_sum;
            return $product_group;
        }

		/**
		 * @desc 分类多列处理
		 * @param unknown $tree
		 */
		public function formatCateCol($tree){
			$last_list = array();
			foreach ($tree as $kone=>$vone){
				if(empty($vone['children'])) {
					$last_list[$kone] = $vone;
					unset($last_list[$kone]['children']);
					continue;
				}
					
				$last_list[$kone] = $vone;
				unset($last_list[$kone]['children']);
				$col_num = 0;
					
				//存在二级分类
				//$vtwo 为二级分类
				//$vtwo['children']为三级分类
				foreach ($vone['children'] as $ktwo=>$vtwo){
					$col_num++;
		
					if($col_num <=$this->cat_li_number){
						$last_list[$kone]['col_one'][$ktwo] = $vtwo;
						unset($last_list[$kone]['col_one'][$ktwo]['children']);
					}elseif($col_num>$this->cat_li_number && $col_num<(2*$this->cat_li_number)){
							
						$last_list[$kone]['col_two'][$ktwo] = $vtwo;
						unset($last_list[$kone]['col_two'][$ktwo]['children']);
					}else{
						$last_list[$kone]['col_three'][$ktwo] = $vtwo;
						unset($last_list[$kone]['col_three'][$ktwo]['children']);
					}
		
					if(empty($vtwo['children'])) continue;
					//该二级分类下存在三级分类
					foreach ($vtwo['children'] as $kthree=>$vthree){
						$col_num++;
							
						if($col_num <=$this->cat_li_number){
							$last_list[$kone]['col_one'][$ktwo]['children'][$kthree] = $vthree;
							unset($last_list[$kone]['col_one'][$ktwo]['children'][$kthree]['children']['children']);
						}elseif($col_num>$this->cat_li_number && $col_num<(2*$this->cat_li_number)){
							/*if(!isset($last_list[$kone]['col_two'][$ktwo])){
							 $last_list[$kone]['col_two'][$ktwo] = $vtwo;
							unset($last_list[$kone]['col_two'][$ktwo]['children']);
							}*/
		
							$last_list[$kone]['col_two'][$ktwo]['children'][$kthree] = $vthree;
							unset($last_list[$kone]['col_two'][$ktwo][$kthree]['children']);
						}else{
							/*if(!isset($last_list[$kone]['col_three'][$ktwo])){
							 $last_list[$kone]['col_three'][$ktwo] = $vtwo;
							unset($last_list[$kone]['col_three'][$ktwo]['children']);
							}*/
		
							$last_list[$kone]['col_three'][$ktwo]['children'][$kthree] = $vthree;
							unset($last_list[$kone]['col_three'][$ktwo]['children'][$kthree]['children']['children']);
						}
							
					}
				}
			}
			return $last_list;
		
		}
		
		/**
		 * @desc 获取该分类path的面包屑分类信息
		 * @param unknown $category_path(某分类下的path值)
		 * @return multitype:|multitype:unknown
		 */
		public function getCategoryCrumbs($category_path,$flush_cache = false){
			$result = array();
			if(!$category_path) return $result;
		
			$language_id = currentLanguageId();
			$this->load->model("categorymodel","category");
			if(is_numeric($category_path) && $category_path){
				$category_info = $this->category->getCategoryinfo($category_path,$language_id,$flush_cache);
				if(!isset($category_info['category_id'])) return $result;
				$result[$category_info['category_id']] = $category_info;
			}else{
				if(strpos($category_path,"/")){
					$category_ids = explode("/", $category_path);
					foreach ($category_ids as $category_id){
						$category_info = $this->category->getCategoryinfo($category_id,$language_id,$flush_cache);
                        if(isset($category_info['category_id']))
						    $result[$category_info['category_id']] = $category_info;
					}
				}
			}
		
			return $result;
		}
		
		/*
		 * check user login & save user info into session
		*/
		protected function _addUserInfo(){
			$user = false;
			//auto login
			if(!$this->customer->checkUserLogin()){
				
				// $user_id = $this->input->cookie('ECS[user_id]');
				// $user_name = $this->input->cookie('ECS[user_name]');
				// $password = $this->input->cookie('ECS[password]');
				$user_id = isset($_COOKIE['ECS']['user_id'])?$_COOKIE['ECS']['user_id']:'';
				$user_name = isset($_COOKIE['ECS']['user_name'])?$_COOKIE['ECS']['user_name']:'';
				$password = isset($_COOKIE['ECS']['password'])?$_COOKIE['ECS']['password']:'';

				if($user_id !== false && $user_name !== false && $password !== false){

					if(is_email($user_name)){
						$user = $this->customer->getUserByEmail($user_name);
					}else{
						$user = $this->customer->getUserByName($user_name);
					}
		
					if(!empty($user) && $user['customer_id'] == $user_id && $user['customer_password'] == $password){
						$this->session->set('user_id',$user['customer_id']);
						$this->session->set('user_name',$user['customer_name']);
						$this->session->set('email',$user['customer_email']);
						$this->customer->updateUserLoginInfo($user['customer_id']);
					}else{
						// unset_cookie('ECS[user_id]');
						// unset_cookie('ECS[user_name]');
						// unset_cookie('ECS[password]');
						setcookie('ECS[user_id]','',time()-1,'/',COMMON_DOMAIN);
						setcookie('ECS[user_name]','',time()-1,'/',COMMON_DOMAIN);
						setcookie('ECS[password]','',time()-1,'/',COMMON_DOMAIN);
					}
				}
			}
			$this->load->model('cartmodel','cartmodel');
			if($this->customer->checkUserLogin()){
				$user = array(
						'user_id' => $this->customer->getCurrentUserId(),
						'user_name' => $this->customer->getCurrentUserName(),
						'email' => $this->customer->getCurrentUserEmail(),
				);
				//购物车数量
				$cart_nums = $this->cartmodel->cartNumsWithUser($this->customer->getCurrentUserId(),$type = true);
			}else{
				$cart_nums = $this->cartmodel->cartNumsWithUser($this->session->sessionID());
			}
			$this->_view_data['user'] = $user;
			$this->_view_data['cart_nums'] = $cart_nums;
		}
		
		protected function ajaxReturn($data,$type='json') {
			if(func_num_args()>2) {// 兼容3.0之前用法
				$args           =   func_get_args();
				array_shift($args);
				$info           =   array();
				$info['data']   =   $data;
				$info['info']   =   array_shift($args);
				$info['status'] =   array_shift($args);
				$data           =   $info;
				$type           =   $args?array_shift($args):'';
			}
			
			header('Content-Type:application/json; charset=utf-8');
			exit(json_encode($data));
			/*if(empty($type)) $type  =   DEFAULT_AJAX_RETURN;
			switch (strtoupper($type)){
				case 'JSON' :
					// 返回JSON数据格式到客户端 包含状态信息
					header('Content-Type:application/json; charset=utf-8');
					exit(json_encode($data));
				case 'XML'  :
					// 返回xml格式数据
					header('Content-Type:text/xml; charset=utf-8');
					exit(xml_encode($data));
				case 'JSONP':
					// 返回JSON数据格式到客户端 包含状态信息
					header('Content-Type:application/json; charset=utf-8');
					$handler  =   isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
					exit($handler.'('.json_encode($data).');');
				case 'EVAL' :
					// 返回可执行的js脚本
					header('Content-Type:text/html; charset=utf-8');
					exit($data);
				default     :
					// 用于扩展其他返回格式数据
					tag('ajax_return',$data);
				
			}*/
		}
		
		protected function newAjaxReturn($status = 0,$data = null,$msg = "") {
			$data = array();
			$data['status'] = $status;
			$data['msg'] = $msg;
			$data['data'] = $data;
			header('Content-Type:application/json; charset=utf-8');
			exit(json_encode($data));
			
		}
		
		/**
		 * 分页处理
		 * @param  string $baseurl 相对路径
		 * @param  integer $page 页码
		 * @param  integer $count 商品总数
		 * @param  array $basicParam URL参数
		 * @author qcn qianchangnian@hofan.cn
		 * @return array 分类信息
		 */
		public function _basepagination($baseurl, $page = 1, $count = 0, $pagesize = 8, $allParam = array()) {
			$this->_view_data['pagination']['current_page'] = $page;
			$this->_view_data['pagination']['total_page'] = $count > 0 ? ceil( $count / $pagesize ) : 1;
		
			$urlTmp = trim( $baseurl ) ;
		
			$allParam['page'] = "%u";
			$this->_view_data['pagination']['href'] = genURL( $urlTmp,false,$allParam);
			$this->_view_data['pagination']['default_href'] = genURL( $urlTmp , true, $allParam );
		}
		
		/**
		 * @desc 返回 1 评论人数； 2 评论总分； 3 增加评论人信息的评论信息
		 * @param unknown $review_list
		 * @return multitype:number Ambigous <multitype:, unknown> Ambigous <number, unknown>
		 */
		public function reviewWithUserScore($review_list){
			$review_nums = 0;//总评论人数
			$review_total_socre = 0;//评论总分
			$data = array();
		
			if(!empty($review_list)){
				$this->load->model("customermodel","user");
				foreach ($review_list as $review_k=>&$review_v){
					if($review_v['review_status']==1){
						$review_nums++;
						$review_total_socre+= $review_v['review_score'];
					}
					//数据中新增评论用户信息
					$user_id = $review_v['customer_id'];
					$user_name = $this->user->nameWithUid($user_id);
					$review_v['user_name'] = $user_name;
				}
				$data = $review_list;
			}
		
			return array(
					'data'=>$data,
					'review_nums'=>$review_nums,
					'total_score'=>$review_total_socre,
			);
		
		}
		
		/**
		 * @desc 返回平均分，星级别
		 * @param unknown $review_list   (全部评论)
		 * @param number $show_review_nums
		 */
		public function reviewWithStar($review_nums = 0 ,$review_total_socre = 0){
			// $review_nums //总评论人数
			// $review_total_socre  //评论总分
			$star_num = 10;//星级别
			$average_star_num = 5;//平均分
		
			if($review_nums!=0){
				$average_star_num = round($review_total_socre/$review_nums,1);
				if(stripos($average_star_num, ".")){
					$num_array = explode(".", $average_star_num);
					if($num_array[1]<4 && $num_array[1]>0){
						$star_num = 2*$num_array[0];
					}elseif($num_array[1]>=4 && $num_array[1]<=8){
						$star_num = $num_array[0]*2 + 1;
					}else{
						$star_num = 2*round($average_star_num);
					}
				}elseif($average_star_num < 5){
					$star_num = 2*$average_star_num;
				}else{
					$average_star_num = 5;
					$star_num = 10;
				}
			}
		
			return array(
					'star_level'=>$star_num,
					'average_score'=> $average_star_num,
			);
		}
		
		//根据商品sku获取属性，属性组信息
		protected function attrAndValueWithSku($sku){
			if(!$sku) return array();
			$language_id = currentLanguageId();
			$this->load->model("attributeproductmodel","attribute");
			$result = $this->attribute->attrAndValueWithSku($sku);
			if($result){
				foreach ($result as $k=>&$v){
					$attr_id = $v['complexattr_id'];
					$attr_value_id = $v['complexattr_value_id'];
					$attr_info = $this->attribute->complexattrInfo($attr_id,$language_id);
					if($attr_info){
						$v['attr_name'] = $attr_info[0]['complexattr_lang_title'];
					}
					$attr_value_info = $this->attribute->complexattrValueInfo($attr_value_id,$language_id);
					if($attr_value_info){
						$v['attr_value_name'] = $attr_value_info[0]['complexattr_value_lang_title'];
					}
				}
					
			}
			return $result;
		}
		
		//根据多个商品sku获取属性，属性组信息
		protected function batchAttrAndValueWithSkuArray($sku_array){
			if(empty($sku_array) || !is_array($sku_array)) return array();
			
			$language_id = currentLanguageId();
			$this->load->model("attributeproductmodel","attribute");
			//所有属性信息
			$result = $this->attribute->getAttrAndValueWithSkus($sku_array);
			//获取属性值信息
			if($result){
				//所有属性值信息
				foreach ($result as $key=>&$val){
					foreach ($val as $k=>&$v){
						$attr_id = $v['complexattr_id'];
						$attr_value_id = $v['complexattr_value_id'];
						$attr_info = $this->attribute->complexattrInfo($attr_id,$language_id);
						if($attr_info){
							$v['attr_name'] = $attr_info[0]['complexattr_lang_title'];
						}
						$attr_value_info = $this->attribute->complexattrValueInfo($attr_value_id,$language_id);
						if($attr_value_info){
							$v['attr_value_name'] = $attr_value_info[0]['complexattr_value_lang_title'];
						}
					}
				}
			}
			return $result;
		}
		
		/**
		 * @desc 根据sku及pid获取商品sku属性（长，宽，高，仓库等信息）
		 * @param unknown $sku
		 * @param unknown $product_id
		 */
		protected function skuinfoWithSku($product_sku,$product_id){
			$result = array();
			if(!$product_sku || empty($product_sku) || !$product_id || !is_numeric($product_id))return $result;
			
			$this->load->model("attributeproductmodel","productsku");
			$sku_info = $this->productsku->productSkuinfoWithSku($product_sku,$product_id);
			if(!empty($sku_info)) $result = $sku_info[0];
			
			return $result;
		}
		
		/**
		 * @desc 返回GA统计（ google_tag_params)
		 * @param unknown $ecomm_prodid
		 * @param string $ecomm_pagetype
		 * @param unknown $ecomm_pname
		 * @param unknown $ecomm_pcat
		 * @param unknown $ecomm_pvalue
		 * @return string
		 */
		public function all_google_tag_params($ecomm_prodid = array(),$ecomm_pagetype = '',$ecomm_pname = array(),$ecomm_pcat = array(),$ecomm_pvalue = array()){
			$return_data['ecomm_prodid'] = $ecomm_prodid;
			$return_data['ecomm_pagetype'] = $ecomm_pagetype;
			$return_data['ecomm_pname'] = $ecomm_pname;
			$return_data['ecomm_pcat'] = $ecomm_pcat;
			$return_data['ecomm_pvalue'] = $ecomm_pvalue;
			$google_tag_params = json_encode($return_data);
			$this->_view_data['google_tag_params'] = $google_tag_params;
			//return json_encode($return_data);
		}
		
		/**
		 * 数据结构可能不同，则同类本身实现
		 * 相应页面刷新时，GA数据统计
		 */
		protected function dataLayerPushImpressions(  $productList = array() , $list = ''  ){
			if ( empty ( $productList  ) ){
				$this->_view_data['dataLayerProducts'] = '';
				return ;
			}
			$position = 1;
			$products = array();
			foreach( $productList as $sku){
				$products[] = array(
						'id' => $sku['product_id'],
						'price'=> $sku['product_basediscount_price'],  //美元价格
						'list' => $list,
						'position' => $position++
				);
			}
		
			$this->_view_data['dataLayerProducts'] = json_encode( $products );
		}
		
		//支付成功后给GA发送订单数据
		protected function _processGAInfo($order){
			$cid = $order['order_gaid'];
			global $payment_list;
			global $shipping_method_list;
			$ga_order = array(
					'v' => 1,
					'tid' => 'UA-44016380-1',
					'cid' => $cid,
					't' => 'transaction',
					'dh' => 'eachbuyer.com',
					'ds' => 'web',
					'ti' => $order['order_code'],
					'tr' => $order['order_baseprice'],
					'tt' => $order['order_baseprice_discount'],
					'ts' => $order['order_baseprice_shipping'] + $order['order_baseprice_insurance'],
					'tcc' => $order['order_coupon'],
					'pa' => 'purchase',
			);
			$this->load->model('ordermodel','ordermodel');
			$goods_list = $this->ordermodel->getProductListByOrderId($order['order_id']);
			foreach ($goods_list as $index => $record) {
				$index += 1;
				$ga_order['pr' . $index . 'id'] =  $record['product_id'];
				$ga_order['pr' . $index . 'pr'] =  $record['order_product_baseprice'];
				$ga_order['pr' . $index . 'qt' ] =  $record['order_product_quantity'];
			}
			
			//curl发送
			$this->_CurlInfoToGA($ga_order);
		}
		
		//curl数据到GA
		private function _CurlInfoToGA($params){
			$this->load->library('curl');
			return $this->curl->_simple_call('post', 'http://www.google-analytics.com/collect', $params);
		}
		
		protected function recommendProductEamilTemplate($recommend_list){
			if(empty($recommend_list)) return false;
			$this->load->model('emailtemplatemodel','emailtemplate');
			$recommend_product_html = $this->emailtemplate->getEmailRecommendProductDom($recommend_list);
		}
		

		/**
		 * @desc 根据国家编码返回国家名称
		 * @param unknown $country_code
		 * @return NULL|Ambigous <string, unknown>
		 */
		protected function getCountryName($country_code){
			if(empty($country_code)) return null;
			$all_country_list = $this->_country();
			$country_name = '';
			foreach ($all_country_list as $key=>$val){
				if($val['country_iso2']==$country_code) $country_name = $val['country_name'];
			}
			return $country_name;
		}
		
		//获取adyen列表
		protected function adyenList($price,$country_code){
			//全部列表
			$all_adyen_list = $this->_allAdyenList($price,$country_code);
			//echo "<pre>adyennnnnn";print_r($all_adyen_list);die;
			//筛选列表
			$adyen_list = $this->_filterAdyenList($all_adyen_list);
			return $adyen_list;
		
		}
		
		/**
		 * @desc 筛选支付方式
		 * @param unknown $adyen_list
		 * @return multitype:
		 */
		private function _filterAdyenList($adyen_list){
			$return_adyen_list = array();
			if(empty($adyen_list) || !isset($adyen_list['paymentMethods'])){
				$language_code = strtolower(currentLanguageCode());//国家				
				$return_adyen_list[$this->prefix.'2'] = array(
						'picname'=>RESOURCE_URL ."images/paymentMethod/"."paypalsk.jpg",
						'name'=>'paypalsk',
						'checked'=>0,
						'id'=>2
				);
				$return_adyen_list[$this->prefix.'3'] = array(
						'picname'=>RESOURCE_URL ."images/paymentMethod/bank.jpg",
						'name'=>'bank',
						'checked'=>0,
						'id'=>3,
				);
			}else{
				global $payment_list;
				$new_payment_list = array_flip($payment_list);
				$exists_iban = false;//默认不存在
				$exists_adyen_paypal = false;//默认不存在
				
				$prefix = "payment_";
				foreach ($adyen_list['paymentMethods'] as $k=>&$v){
					//根据最低金额及本站的范围列表筛选排除adyen中列表(配置文件中)**（暂时去掉）*
					//判断国际银行(bankTransfer_IBAN),3
					if(strpos($v['brandCode'], 'bankTransfer')!==false){
						$exists_iban = true;
					}
					//判断adyen中是否paypal，不存在显示本地paypal
					if($v['brandCode']=='paypal'){
						$exists_adyen_paypal = true;
					}
						
					$code = $v['brandCode'];
					if(!isset($new_payment_list[$code])) continue;
					$key = $new_payment_list[$code];
					$return_adyen_list[$this->prefix.$key] = array(
							'picname'=>RESOURCE_URL ."images/paymentMethod/".$code.".jpg",
							'name'=>$code,
							'id'=>$key,
							'checked'=>0,
							//'id'=>$new_payment_list[$v['brandCode']]
					);
				}
				if($exists_adyen_paypal===false){
					//if($exists_adyen_paypal===true){
					$return_adyen_list[$this->prefix.'2'] = array(
							'picname'=>RESOURCE_URL ."images/paymentMethod/"."paypalsk.jpg",
							'name'=>'paypalsk',
							'checked'=>0,
							'id'=>2
					);
				}
				if($exists_iban===false){//不存在时，需要增加本地银行到支付列表  br.png
					$language_code = strtolower(currentLanguageCode());//国家
					$return_adyen_list[$this->prefix.'3'] = array(
							'picname'=>RESOURCE_URL ."images/paymentMethod/bank.jpg",
							'name'=>'bank',
							'checked'=>0,
							'id'=>3,
					);
				}
			}
		
			//echo "<pre>";print_r($return_adyen_list);die;
			return $return_adyen_list;
		}
		
		/**
		 * @desc 从adyen接口获取列表
		 * @return mixed
		 */
		private function _allAdyenList($price,$country_code){
			$price = 1;
			$currencyCode = strtoupper($this->currency);//货币code
			$this->load->library('Payment/adyen');
			$adyen_list = $this->adyen->callDirectoryLookupRequest($country_code,$currencyCode,$price);
			$adyen_list = json_decode($adyen_list,true);
			return $adyen_list;
		}
		
		//国家
		protected function _country(){
			$this->load->model("currencymodel","currencymodel");
			$country_list = $this->currencymodel->allCountry();
			return $country_list;
		}
		
		/**
		 * @desc 人民币对美元汇率
		 * @return Ambigous <number, unknown>
		 */
		protected function _getCnyRate(){
			if($this->ratecny == ''){
				$this->ratecny = 1;
				$this->load->model("currencymodel","currencymodel");
				$currency = $this->currencymodel->currencyList(true);
				foreach ($currency as $currency_key=>$currency_val){
					if($currency_val['currency_code'] == strtoupper("CNY"))
						$this->ratecny = 	$currency_val['currency_rate'];
				}
			}
			
			return $this->ratecny;
		}
		
		/**
		 * @desc 记录order动作，库操作
		 * @param unknown $order_id
		 * @param string $status
		 * @param string $order_action_info
		 */
		protected function _writeOrderAction($order_id,$status = ORDER_ACTION_TYPE_CREATE,$order_action_info = ''){
			//新增order_action动作
			$order_action = array(
					'order_action_time_create'=>date('Y-m-d H:i:s',time()),
					'order_id'=>$order_id,
					'order_action_type'=>$status,
					'order_action_info'=>$order_action_info,
			);
			$this->ordermodel->createOrderAction($order_action);
			
		}
		
		/**
		 * @desc 支付成功邮件
		 * @param unknown $order
		 * @return unknown
		 */
		protected function SuccessEmail($order){
			if(!isset($order['order_email']) || 
					!isset($order['language_id']) || 
					!isset($order['order_address_country']) || 
					!isset($order['order_id']) || 
					!isset($order['shipping_id']) ||
					!isset($order['payment_id']) || 
					!isset($order['order_address_firstname']) || 
					!isset($order['order_address_lastname']) ||
					!isset($order['order_time_create'])
			) return false;
			
			$type = 2;
			$email = $order['order_email'];
			$LanguageId = $order['language_id'];
			
			$order['country'] = $this->getCountryName($order['order_address_country']);
			
			//获取订单商品id
			$this->load->model('ordermodel','ordermodel');
			$order_product_infos = $this->ordermodel->getProductListByOrderId($order['order_id']);
			//不存在商品时
			if(empty($order_product_infos))return false;
			
			$productIds = array();
			foreach ($order_product_infos as $key=>$val){
				$productIds[] = $val['product_id'];
			}
			
			//获取推荐商品详情
			$this->load->model('goodsmodel','product');
			$recommend_goods_list = $this->product->getEmailRecommendProduct($productIds, $limit = 4, $order['language_id']);
			if(empty($recommend_goods_list)) return false;
			
			//价格促销
			$recommend_goods_list = $this->productWithPrice($recommend_goods_list);
			
			$order = eb_htmlspecialchars( $order );
			//订单商品信息dom
			$this->load->model('emailtemplatemodel','emailtemplate');
			$orderInfoDomArray = $this->emailtemplate->getEmailOrderInfoDom( $order, $order_product_infos, 'order' );
			
			//推荐商品， 邮件模板
			$this->load->model('emailtemplatemodel','emailtemplatemodel');
			$item_reo = $this->emailtemplatemodel->getEmailRecommendProductDom( $recommend_goods_list ,$order['order_currency']);
			
			global $base_url,$shipping_list,$payment_list;
			$shipping_id = str_replace('shippingid', '', $order['shipping_id']);
			$payment_id = str_replace('payment_', '', $order['payment_id']);
			$params = array(
					'SITE_DOMAIN' => substr($base_url[currentLanguageId()],0,-1),
					'USER_NAME' => $order['order_address_firstname']." ".$order['order_address_lastname'],
					'SITE_DOMAIN1' => COMMON_DOMAIN, //域名
					//'SUBSCRIBE_LINK' => genURL('common/validateSubscribe').'?email='.encrypt($email).'&utm_source=System_Own&utm_medium=Email&utm_campaign=Newsletter-confirm&utm_nooverride=1',
					'CS_EMAIL' => 'cs@eachbuyer.com',
					'ITEM_REO' =>$item_reo,
					'ORDER_NUM' => $order['order_code'],
					'ORDER_TIME' => date('F j, Y h:i:s A e', strtotime($order['order_time_create'])),
					'ORDER_INFO' => $orderInfoDomArray['order_info'],
					'SHIP_ADDRESS' => $orderInfoDomArray['address'],
					'SHIP_WAY' => isset($shipping_list[$shipping_id])?$shipping_list[$shipping_id]:null,
					'PAY_WAY' => $payment_list[$payment_id],
			);
			$this->load->model('emailmodel','emailmodel');
			$result = $this->emailmodel->subscribe_sendMail( $type, $LanguageId ,$email,$params);
			return $result;
		}
		
	}
	
}
/* End of file home.php */
/* Location: ./application/controllers/home.php */
