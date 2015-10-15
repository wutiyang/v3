<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';

class category extends Dcontroller {
	private $currency_format;
	
	public function __construct(){
		parent::__construct();
		$this->currency_format = $this->getCurrencyNumber();
		$this->_view_data['new_currency'] = "$";
		$this->_view_data['currency_code'] = $this->current_currency_code = currentCurrency();
		if($this->currency_format){
			$this->_view_data['new_currency'] = $this->current_currency = $this->currency_format['currency_format'];
		}
	}
	public function index($categoryId = 0, $page = 1){
		$all_param = array();

		$sort = $this->input->get('sort');
        //对价格范围的搜索进行处理
        $priceRangeSearch = trim($this->input->get('search_price_range'));
        $basicParam = array();
        $basicParam = $this->_setParam($priceRangeSearch, $sort);
		//分类id，不存在时的处理（0，表中不存在，非数字的处理）
		$category_id = intval($categoryId);
		$language_id = currentLanguageId();
		//优先301，302，404处理
		$this->_urlRedirect301And302($category_id);

		//获取当前分类，及footer信息，语言处理
		$this->load->model("categorymodel","category");
		$category_info = $this->category->getCategoryinfo($category_id,$language_id);
        if(empty($category_info)){
            //该分类数据不存在或status值不为1
            redirect(genURL(''));
        }
        $this->_view_data['category_info'] = $category_info;
        $uri = explode('?',$_SERVER['REQUEST_URI']);
        if(!strstr($uri[0],$category_info['category_url'])){//商品url不规范跳转到规范商品url
            $url = $category_info['category_url'].(isset($uri[1])?'?'.$uri[1]:'');
            header("Location: ".$url, TRUE, 301);
        }

		//获取面包屑信息
		$category_path = $category_info['category_path'];
        //slimbanner
        $this->_view_data['slim_banner'] = $this->getSlimBanner($category_path);
		$crumbs_list = $this->getCategoryCrumbs($category_path);
		
		//及相关分类，及分类desc名称
		$category_related_lists = $this->category->getRelatedCategoryList($category_id,$language_id);
//		echo "<pre>";print_r($category_related_lists);exit;
		//只处理一级和二级分类
		$cate_array = explode("/", $category_info['category_path']);
		$category_level = count($cate_array);

		$childrenCategoryAndProductList = array();
		if($category_level<3){
			$childrenCategoryAndProductList = $this->childrenCategoryAndRelatedProduct($category_id);
		}

		//判断检查url
		//$this->_checkUrl($category_info);

		//*********************************
		//分类下的全部pid，全部属性及对应pid信息——慢
		$cache_search_result = $this->allPidAndAttributes($category_id);
		//echo "<pre>all";print_r($cache_search_result['attribute_data']);
		//*********************************

		//获取其他参数
		$attrNarrowSearch = $this->input->get('attr');
		$specialCharactersNs = array( '%', '^', '[', ']', '{', '}', '€', '¥', '£', '<', '>', '=', '+', '*', '\\', "\n", "\n\r", "&nbsp", "\r", "\t", "'", '"');
		$attrNarrowSearch = str_replace( $specialCharactersNs, '', $attrNarrowSearch );

		$priceRange = trim($this->input->get('price_range')); //价格属性
		if(empty( $priceRange )) { 
			$priceRange = array(); 
		}else{
			$all_param['price_range'] = $priceRange;
			if(stripos($priceRange , ",")){
				$oldpriceRange = explode(",", $priceRange);
				$priceRange = array();
				foreach ($oldpriceRange as $kr=>$vr){
					$priceRange[$vr]= $vr;
				}
			}else{
				$priceRange = array($priceRange=>$priceRange);
			}
		}

		$attrNarrowSearch = $this->_checkUrlAttr($attrNarrowSearch);

		//获取所有的选择的属性id
		$attrIdsArray = $attrNarrowSearch['attrGroups'];

		//没选择或已选择的属性对应属性组值的“并集”商品pid列表
		$selected_attribute_and_return_pid = $this->getSelectedAttrAndReturnPid($attrIdsArray,$cache_search_result,$priceRange);

		$selected_attribute_product_list = $selected_attribute_and_return_pid["selected_attribute_product_list"];
		$return_product_ids_list = $selected_attribute_and_return_pid["return_product_ids_list"];
//		$return_product_ids_num = $selected_attribute_and_return_pid["return_product_ids_num"];

        $currency_format = $this->getCurrencyNumber();
        if(!$currency_format['currency_rate']){
            $currency_format['currency_rate'] = 1;
            $currency_format['currency_format'] = '$';
        }
		//具体返回展示的商品详情
		$show_product_lists = array();
		if(!empty($return_product_ids_list)){
			foreach ($return_product_ids_list as $p_id){
				$show_product_lists[$p_id] = $cache_search_result['all_product_list'][$p_id];
			}
			if($priceRangeSearch){//价格区间以“，”分割
				$all_param['search_price_range'] = $priceRangeSearch;
				if(stripos($priceRangeSearch,",")!==false){
					$range_array = explode(",", $priceRangeSearch);
					$start_price = $range_array[0]?$range_array[0]:0;
					$end_price = $range_array[1]?$range_array[1]:0;
                    $start_price= round($start_price/$currency_format['currency_rate'],2, PHP_ROUND_HALF_DOWN);
                    $end_price= round($end_price/$currency_format['currency_rate'],2, PHP_ROUND_HALF_DOWN);
				}else{
					$start_price = 0;
					$end_price = trim($priceRangeSearch);
                    $end_price= round($end_price/$currency_format['currency_rate'],2, PHP_ROUND_HALF_DOWN);
				}
                $min = min($start_price,$end_price);
                $max = max($start_price,$end_price);
				foreach ($show_product_lists as $p_id=>&$p_info){
					$market_price = $p_info['product_price'];
					if($market_price < $min || $market_price > $max){
						unset($show_product_lists[$p_id]);
					}
				}
				$return_product_ids_num = count($show_product_lists);
			}
			
		}

		//对所有属性计算每个属性的个数及链接，及是否选中状态值
		$attribute_data = $this->dynamicCountAttributeData($selected_attribute_product_list,$cache_search_result['attribute_data'],$attrNarrowSearch,$cache_search_result['all_product_id_list'],$priceRange);
		//echo "<pre>";print_r($attribute_data);die;
        $attribute_price = $attribute_data['price'];
        unset($attribute_data['price']);
        //整理价格区间的货币种类
        foreach($attribute_price as &$v){
            $v['product_currency'] = $currency_format['currency_format'];
            $v['category_narrow_price_start'] = round($v['category_narrow_price_start']*$currency_format['currency_rate'],2);
            $v['category_narrow_price_end'] = round($v['category_narrow_price_end']*$currency_format['currency_rate'],2);
        }

        $attribute_data['price'] = $attribute_price;
		//返回对应分页数据
		//echo "<pre>";print_r($basicParam);die;
		
		//对返回的数据"排序"处理
		switch ($sort){
			case 2://时间排序
				$show_product_lists = array_sort($show_product_lists,"product_time_initial_active","desc");
				$this->_view_data['sort'] = 2;
				break;
            case 3://价格排序
                //折扣前排序
                $show_product_lists = array_sort($show_product_lists,"product_price");
                //折扣后排序
//                $show_product_lists = array_sort($show_product_lists,"product_discount_price");
                $this->_view_data['sort'] = 3;
                break;
            case 4://价格降序排序
                $show_product_lists = array_sort($show_product_lists,"product_price","desc");
//                $show_product_lists = array_sort($show_product_lists,"product_discount_price","desc");
                $this->_view_data['sort'] = 4;
                break;
			default://默认排序
                //获取推荐商品
				$recommend_product = $this->getCategoryProductRecommend($category_id);
				$first_recommend_lists = array();
				if(!empty($recommend_product)){
					//交集
					$recommend_product_ids = array_intersect(array_keys($show_product_lists), array_keys($recommend_product));
					//取出推荐商品
					if(!empty($recommend_product_ids)){
						foreach ($recommend_product_ids as $kre=>$vre){
							$first_recommend_lists[$recommend_product[$vre]] = $show_product_lists[$vre];
                            $first_recommend_lists[$recommend_product[$vre]]['sort'] = $recommend_product[$vre];
							unset($show_product_lists[$vre]);
						}
                        //推荐商品排序
                        $first_recommend_lists = array_sort($first_recommend_lists,"sort");
					}
				}
                $show_product_lists = array_sort($show_product_lists,"product_sales","desc");
                $show_product_lists = array_merge($first_recommend_lists,$show_product_lists);
				$this->_view_data['sort'] = 1;
		}

		//分页处理

        $pagesize=48;
//        var_dump(CATEGORY_TYPE_VER_DISPLAY.$category_info['category_type']);echo "<pre>";print_r($attribute_data);
        if($category_info['category_type_display']==CATEGORY_TYPE_VER_DISPLAY){//竖版排列
            $pagesize = 40;
        }
        if( isset( $attrNarrowSearch['parameter']  ) &&( count( $attrNarrowSearch['parameter'] ) > 0 )){
            $basicParam['attr'] = implode( ',', array_keys( $attrNarrowSearch['parameter'] ) )  ;
        }
        $parameter_array = isset($attrNarrowSearch['parameter'])?$attrNarrowSearch['parameter']:array();
        $return_product_ids_num = count($show_product_lists);
        $this->_pagination( $category_info, $page, $return_product_ids_num, $basicParam ,$parameter_array,$pagesize);
        //分页截取数据
        $show_product_lists = array_slice($show_product_lists,$pagesize*($page-1),$pagesize);
        $this->load->model("goodsmodel","product");
        $show_product_lists = $this->product->showProductList($show_product_lists,currentLanguageCode());
        //促销价格及折扣//**滞后原因商品价格排序按照折扣之前排序——慢
        $show_product_lists = $this->productWithPrice($show_product_lists);
        //echo '<pre>';print_r($show_product_lists);exit;
        $this->_view_data['return_product'] = $show_product_lists;
		//GA统计(google_tag_params)
		$this->_getGoogleTagParams($crumbs_list);
		//GA统计-datalayers
		$this->_getGaDatalayers($crumbs_list,$this->_view_data['return_product']);
		
		$this->_view_data['all_param'] = $all_param;//所有url参数，除sort排序（单独处理）
		$this->_view_data['basicParam'] = $basicParam;
		$this->_view_data['Total_num'] = $return_product_ids_num;
		$this->_view_data['crumbs_list'] = $crumbs_list;
		$this->_view_data['category_related_lists'] = $category_related_lists;
		$this->_view_data['category_level'] = $category_level;
		$this->_view_data['children_category'] = $childrenCategoryAndProductList;
		$this->_view_data['attribute_data'] = $attribute_data;

        //添加部分list参数对应表
        $this->dataLayerPushImpressions($this->_view_data['return_product'],'Category Listing Page');
        //$this->database->dumpSQL('slave');exit;
        $this->_setMateView($category_info,$attribute_data,$attrNarrowSearch,$page);
		parent::index();
	}

    private function _setMateView($category_info,$ns_info = array(),$selected = array(),$page=1){
//        echo '<pre>';print_r($ns_info);die;
        $selected_name = array();
        foreach($selected['attrGroups'] as $group_id=>$value_ids){
            foreach($value_ids as $value_id){
                if(isset($ns_info[$group_id]['group'][$value_id]['new_group_lang']))
                    $selected_name[] = $ns_info[$group_id]['group'][$value_id]['new_group_lang'];
            }
        }
        $selected_str = implode('-',$selected_name);
        $category_name = $category_info['category_description_name'];
        $page = intval($page) > 1 ? ' - '.lang('page').' '.$page:'';
        $title = $category_info['category_description_title'];
        $title = str_replace('{$ns_values}',$selected_str,str_replace('%s',$title,lang('title')));
        $title = str_replace('{$category_name}',$category_name,$title);
        $title = $title.$page;
        $keywords = $category_info['category_description_keyword'];
        $keywords = str_replace('{$ns_values}',$selected_str,str_replace('%s',$keywords,lang('keywords')));
        $description = $category_info['category_description_meta'];
        $description = str_replace('{$ns_values}',$selected_str,str_replace('%s',$description,lang('title')));
        $description = str_replace('{$category_name}',$category_name,$description);
        $description = $description.$page;
        $this->_view_data['title'] = $title;
        $this->_view_data['seo_keywords'] = $keywords;
        $this->_view_data['description'] = $description;
    }

	private function _getGoogleTagParams($crumbs_list){
		$this->ecomm_pagetype = 'category';
		$this->ecomm_pcat = array();
		foreach ($crumbs_list as $crumbs_key=>$crumbs_val){
			array_push($this->ecomm_pcat, htmlspecialchars($crumbs_val['category_name']));
		}
		$this->ecomm_pcat = implode('_',$this->ecomm_pcat);
	}
	
	private function _getGaDatalayers($crumbs_list,$last_product_lists){
		$this->ga_dataLayer = array();
		$c_num = $p_num = 1;
		foreach ($crumbs_list as $crumbs_key=>$crumbs_val){
			if($c_num<4){
				$c_name = 'pagecategoryL'.$c_num;
				$this->ga_dataLayer[$c_name] = htmlspecialchars($crumbs_val['category_name']);
			}
			$c_num++;
		}
		
		foreach ($last_product_lists as $product_key=>$product_val){
			if($p_num<4){
				$p_name = 'productId'.$p_num;
				$this->ga_dataLayer[$p_name] = $product_val['product_id'];
			}
			$p_num++;
		}
		
		$this->_view_data['ga_dataLayer'] = json_encode($this->ga_dataLayer);
	}
	//获取分类下所有可用推荐商品
	private function getCategoryProductRecommend($category_id){
		$result = array();
		if(!$category_id || !is_numeric($category_id))return $result;
		
		$this->load->model("recommendproductmodel","recommend");
		$result = $this->recommend->getinfo($category_id);
		return $result;
	}
	
	//动态计算并处理所有属性的个数，链接，是否被选中
	private function dynamicCountAttributeData($selected_attribute_product_list,$attribute_data,$attrNarrowSearch,$all_product_id_list,$priceNarrowSearch = array()){
		$attrIdsArray = array();
		$attrIdsArray = $attrNarrowSearch['attrGroups'];
//		if(!empty($attrIdsArray)){//有选中属性时
			$attribute_data = $this->selectedDynamicAttr($selected_attribute_product_list,$attribute_data,$attrNarrowSearch,$all_product_id_list,$priceNarrowSearch);
//		}else{//无选择属性情况下
//			$attribute_data = $this->noSelectedDynamicAttr($selected_attribute_product_list,$attribute_data,$attrNarrowSearch,$all_product_id_list,$priceNarrowSearch);
//		}
		//echo "<pre>";print_r($attribute_data);die;
		return $attribute_data;
	}

	//没有选中(除价格外)属性情况下，全部属性的信息计算处理
//	private function noSelectedDynamicAttr($selected_attribute_product_list,$attribute_data,$attrNarrowSearch,$all_product_id_list,$priceNarrowSearch){
//		//遍历所有属性组
//		foreach ($attribute_data as $attrid=>&$group){
//			if($attrid=="price"){//价格
//				foreach ($group as $price_key=>&$price_val){
//					$price_product_list = array_keys($price_val['product_list']);
//					//与所有的商品id交集
//					$intersect_price_list = array_intersect($all_product_id_list,$price_product_list);
//					$price_val['nums'] = count($intersect_price_list);
//
//					if(!empty($priceNarrowSearch)){
//						$newpricearray = $priceNarrowSearch;
//						if(in_array($price_key+1, $priceNarrowSearch)){//去掉本身
//							$price_val['selected'] = true;
//							unset($newpricearray[$price_key+1]);
//							if(count($newpricearray)==1){
//								$price_val['link'] = "?price_range=".($price_key+1);
//							}else{
//								$price_val['link'] = "?price_range=".implode(",", $newpricearray);
//							}
//						}else{//在原有基础上新增
//							$price_val['selected'] = false;
//							$price_val['link'] = "?price_range=".implode(",", $priceNarrowSearch).",".($price_key+1);
//						}
//						unset($price_val['product_list']);
//					}else{
//						$price_val['selected'] = false;
//						unset($price_val['product_list']);
//						$price_val['link'] = "?price_range=".($price_key+1);
//					}
//
//				}
//			}else{//属性
//
//				foreach ($group['group'] as $attvalue_id=>&$attvalue_val){
//					$attributevalue_product_list = array_keys($attvalue_val['product_list']);
//					//与所有的商品id交集
//					$intersect_attrval_list = array_intersect($all_product_id_list,$attributevalue_product_list);
//					$attvalue_val['nums'] = count($intersect_attrval_list);
//                    if(!$attvalue_val['nums']){
//                        //去掉没有产品的属性值
//                        unset($group['group'][$attvalue_id]);continue;
//                    }
//					$attvalue_val['selected'] = false;
//
//                    $arr_name = $attribute_data[$attrid]['attribute_name'];
//                    if(isset($attribute_data[$attrid]['group'][$attvalue_id]['attribute_value_group_name'])){
//                        $arrvalue_name =$attribute_data[$attrid]['group'][$attvalue_id]['attribute_value_group_name'];
//                        $attrs_names = $arr_name."-".$arrvalue_name;
//                    }
//					if(!empty($priceNarrowSearch)){
//						$attvalue_val['link'] = $attrs_names."/?attr=".$attrid."_".$attvalue_id."&price_range=".implode(",", $priceNarrowSearch);
//					}else{
//						$attvalue_val['link'] = $attrs_names."/?attr=".$attrid."_".$attvalue_id;
//					}
//
//				}
//                if(!count($group['group'])){
//                    //去掉没有属性值的属性组
//                    unset($attribute_data[$attrid]);
//                }
//			}
//		}
//
//		return $attribute_data;
//	}
	
	//获取有选择的属性情况下，全部属性信息
	private function selectedDynamicAttr($selected_attribute_product_list,$attribute_data,$attrNarrowSearch,$all_product_id_list,$priceNarrowSearch){
		$other_call_selected_attr_pid_list = array();
        //已选中的属性名及属性id
        $selected_attr_name = array();
        $selected_attr_id = array();
        $add_params = array();
        if(isset($attrNarrowSearch) && !empty($attrNarrowSearch)){
            if(!empty($attrNarrowSearch['parameter']))
                foreach($attrNarrowSearch['parameter'] as $id=>$value)
                    $selected_attr_id[$id] = $id;
            if(!empty($attrNarrowSearch['attrGroups']))
                foreach($attrNarrowSearch['attrGroups'] as $block_id=>$value){
                    foreach($value as $key=>$group_id){
                        if(isset($attribute_data[$block_id]['group'][$group_id]['attribute_value_group_name'])){
                            $selected_attr_name[$block_id.'_'.$group_id] =$attribute_data[$block_id]['attribute_name'].'-'. $attribute_data[$block_id]['group'][$group_id]['attribute_value_group_name'];
                        }
                    }
                }
        }
//        echo '<pre>';var_dump($selected_attr_name,$selected_attr_id);exit;
		$attrIdsArray = $attrNarrowSearch['attrGroups'];
		foreach ($attribute_data as $k_attr=>&$v_attr){
			//获取价格属性的pid数据，进行计算
			if(isset($v_attr['group']) && count($v_attr['group'])){
				//获取"其他"选中的属性id的并集的交集
				if(isset($selected_attribute_product_list[$k_attr])){
					$copy_selected_product_list = $selected_attribute_product_list;
					unset($copy_selected_product_list[$k_attr]);
					$other_call_selected_attr_pid_list = $copy_selected_product_list;
				}else{
					$other_call_selected_attr_pid_list = $selected_attribute_product_list;
				}

				$other_call_selected_attr_pid_list = array_intersect_upgrade($other_call_selected_attr_pid_list);
				
				//对属性组值的个数：与其他 已选中的 属性并集的 交集进行交集
				foreach ($v_attr['group'] as $v_attr_key=>&$v_attr_val){
					$attr_value_nums = 0;
					//该属性组对应商品id列表
					if(isset($v_attr_val['product_list']) && count($v_attr_val['product_list'])){
						$theattr_product_list = array_intersect_upgrade(array($other_call_selected_attr_pid_list,array_keys($v_attr_val['product_list'])));
                        //与所有的商品id交集
                        $intersect_attrval_list = array_intersect($all_product_id_list,$theattr_product_list);
                        $attr_value_nums = count($intersect_attrval_list);
					}
					//隐藏商品列表
					unset($v_attr_val['product_list']);
						
					//个数
					$v_attr_val['nums'] = $attr_value_nums;
					//判断已选中的属性及属性组中是否有该属性下的属性组，从而判断是否选中
                    $attr_url_name = $v_attr['attribute_name'].'-'.$v_attr_val['attribute_value_group_name'];
                    $attr_url_id = $k_attr."_".$v_attr_key;
                    $new_selected_attr_id = $selected_attr_id;
                    $new_selected_attr_name = $selected_attr_name;
                    if(array_search($attr_url_id,$new_selected_attr_id)){
                        //已选择时显示去掉该属性
                        unset($new_selected_attr_id[$attr_url_id]);
                        unset($new_selected_attr_name[$attr_url_id]);
                        $v_attr_val['selected'] = true;
                    } else {
                        //未选择时添加该属性
                        $new_selected_attr_id[$attr_url_id] = $attr_url_id;
                        $new_selected_attr_name[$attr_url_id] = $attr_url_name;
                        $v_attr_val['selected'] = false;
                    }
                    if(!$v_attr_val['nums'] &&  !$v_attr_val['selected']){
                        //去掉没有产品的属性值
                        unset($v_attr['group'][$v_attr_key]);continue;
                    }
                    $v_attr_val['link'] = '';
                    $add_params['attr'] = $new_selected_attr_id;
					if(!empty($priceNarrowSearch)){
                        $add_params['price_range'] = $priceNarrowSearch;
                    }

                    $v_attr_val['link'] = $this->urlFormat($new_selected_attr_name,$add_params);
				}
				if(!count($v_attr['group'])){
                    unset($attribute_data[$k_attr]);
                }
			}
			
		}
		
		//处理价格的个数和链接，状态
		if(isset($attribute_data['price'])){
			$newpriceNarrowSearch = array();
            $add_params = array();
			foreach ($priceNarrowSearch as $kp=>$vp){
				$newpriceNarrowSearch[$vp] = $vp;
			}
			//其他选中属性的并集进行交集
			$copy_price_product_list = $selected_attribute_product_list;
			unset($copy_price_product_list['price']);
			$other_call_selected_pice_pid_list = array_intersect_upgrade($copy_price_product_list);

			foreach ($attribute_data['price'] as $kprice=>&$vprice){
                $new_priceNarrowSearch = $newpriceNarrowSearch;
                $vprice['link'] = '';
                if(!empty($selected_attr_name) && !empty($selected_attr_id))
                    $add_params['attr'] = $selected_attr_id;
				$price_list = array_keys($vprice['product_list']);
				//交集
                if(!empty($other_call_selected_pice_pid_list) && !empty($selected_attr_id))
				    $result = array_intersect($other_call_selected_pice_pid_list, $price_list);
                else
                    $result = $price_list;
				$vprice['nums'] = count($result);
				unset($vprice['product_list']);
				//是否选中
				$selected_key = $kprice+1;
                if(in_array($selected_key, $new_priceNarrowSearch)){
                    //已选择时显示去掉该属性
                    $vprice['selected'] = true;
                    unset($new_priceNarrowSearch[$selected_key]);
                } else {
                    //未选中时添加该属性
                    $vprice['selected'] = false;
                    $new_priceNarrowSearch[$selected_key] = $selected_key;
                }
                $add_params['price_range'] = $new_priceNarrowSearch;
                $vprice['link'] = $this->urlFormat($selected_attr_name,$add_params);
			}
		}
        $this->_view_data['selected_attr_name'] = $selected_attr_name;
        $this->_view_data['selected_attr_id'] = $selected_attr_id;
		//echo "<pre>2222222222";print_r($attribute_data);die;
		return $attribute_data;
	}

    private function urlFormat($url_add = array(),$add_params = array(),$selected = 1){
        $get_info = $_GET;
        if($selected) {
            unset($get_info['attr']);
            unset($get_info['price_range']);
        }
        if(!empty($add_params)){
            foreach($add_params as $key=>$value){
                if((is_array($value) && empty($value)) || $value == '') continue;
                if(is_array($value)) {
                    asort($value);
                    $value = implode(',',$value);
                }
                $get_info[$key] = $value;
            }
        }
        $url = '';
        $url_add_str = '';
        $text = '';
        if(!empty($url_add)){
            if(is_array($url_add)){
                $url_add_str = implode('/',$url_add).'/';
            } else
                $url_add_str = $url_add;
        }
        $text .= $url_add_str;
        $text = trim($text);
        $text = preg_replace('/[^a-zA-Z0-9_\/?=,&-]/','-',$text);
        //echo $text.'<br/>';
        $text = preg_replace('/-(?=-)/', '', $text);
        $text = strtolower($text);
        if($url_add_str != '')
            $url = 'ns';
        if(isset($this->_view_data['category_info']['category_url']))
            $url .= $this->_view_data['category_info']['category_url'].$text;
        return genURL($url,true,$get_info);
    }
	/**
	 * @desc 
	 * @param unknown $attrIdsArray
	 * @param unknown $cache_search_result
	 * @return multitype:number multitype:multitype:  Ambigous <unknown, multitype:>
	 */
	private function getSelectedAttrAndReturnPid($selectedAttrIdsArray,$cache_search_result,$priceRange= array()){
		$selected_attribute_product_list = array();
		if(isset($selectedAttrIdsArray) && count($selectedAttrIdsArray)){
			foreach ($selectedAttrIdsArray as $attrId_key=>$attrId_val){
				$insert_assoc_product = array();
				foreach ($attrId_val as $attrValue){
					//获取该属性值id对应的pid列表,对全部的pid进行交集(可用)
                    if(isset($cache_search_result['attribute_data'][$attrId_key]['group'][$attrValue]['product_list'])){
                        $attrvalue_product_list = array_keys($cache_search_result['attribute_data'][$attrId_key]['group'][$attrValue]['product_list']);
                        $insert_assoc_product[$attrId_key][$attrValue] = array_intersect($cache_search_result['all_product_id_list'],$attrvalue_product_list);
                    }
				}
				
				//获取该属性id的并集
				$selected_attribute_product_list[$attrId_key] = array();
				if(!empty($insert_assoc_product[$attrId_key])){
					foreach ($insert_assoc_product[$attrId_key] as $group_key=>$group_val){
						$selected_attribute_product_list[$attrId_key] = array_merge($selected_attribute_product_list[$attrId_key],$group_val);
					}
				}
				
			}
			//对已选择的属性id的pid列表进行交集( ***最终返回展示的商品列表****)
			$return_product_ids_list = array_intersect_upgrade($selected_attribute_product_list);
			$return_product_ids_num = count($return_product_ids_list);
		}else{//没有已选中的属性情况下
			$return_product_ids_list = $cache_search_result['all_product_id_list'];
			$return_product_ids_num = count($cache_search_result['all_product_id_list']);
		}
		
		//是否选中“价格范围”
		if(!empty($priceRange)){
			$return_price_intersect = $this->getPriceProductIntersect($priceRange,$cache_search_result['attribute_data']['price'],$cache_search_result['all_product_id_list']);
			//加上价格属性的并集信息
			$selected_attribute_product_list['price'] = $return_price_intersect['selected_price'];
			//价格并集与上面所有的并集
			$return_product_ids_list = array_intersect($return_product_ids_list, $return_price_intersect['return_product_ids_list']);
			$return_product_ids_num = count($return_product_ids_list);
		}
		
		$data = array("selected_attribute_product_list"=>$selected_attribute_product_list,"return_product_ids_list"=>$return_product_ids_list,"return_product_ids_num"=>$return_product_ids_num);
		
		return $data;
	}
	
	//获取价格的并集数据
	private function getPriceProductIntersect($priceRange,$all_price_attribute,$all_product_id_list){
		$selected_price_product_list = $return_product_ids_list = array();
		foreach ($priceRange as $k_p=>$v_p){
			$attr_key = $v_p-1;
			if(isset($all_price_attribute[$attr_key])){
				$this_price_product_list = array_intersect($all_product_id_list, array_keys($all_price_attribute[$attr_key]['product_list']));
				$selected_price_product_list = array_merge($selected_price_product_list,$this_price_product_list);
			}
		}
			
		//价格属性的pid并集（已经对全部pid进行了交集）
		$selected_price = $selected_price_product_list;
		$return_product_ids_list = $selected_price_product_list;
		$return_product_ids_num = count($return_product_ids_list);
		
		$data = array("selected_price"=>$selected_price,"return_product_ids_list"=>$return_product_ids_list,"return_product_ids_num"=>$return_product_ids_num);
		
		return $data;
	}
	
	//获取全部的分类pid及属性对应pid数据，用于后期计算
	private function allPidAndAttributes($category_id){
		$category_memcache = new CI_Memcache();
        //每个属性对应名称及lang语言及值
        $language_id = currentLanguageId();
		$mem_category_key = md5("category_attribute_product_".$category_id.'_language_id_'.$language_id);
		//$category_memcache->delete($mem_category_key);
		
		//if(!$cache_search_result = $category_memcache->get($mem_category_key)){
			$cache_search_result = $main_products_list = $vice_product_list = array();
			global $mem_expired_time;
			$this->load->model("categorymodel","category");
			//该分类及其所有子分类下所有商品（主分类，副分类：category表中type）  (***数据量大是个问题**)
			$vice_product_list = $this->viceCategoryProductList($category_id);//副分类商品列表
            //主分类处理,直接在eb_product表中根据product_path字段进行like——数据整理慢
			$main_products_list = $this->category->newMaincategoryProduct($category_id);
			//合并去重副分类，主分类商品
			$all_product_id_list = array_merge(array_keys($main_products_list),array_keys($vice_product_list));//所有商品id
			$all_product_list = $main_products_list;
			if(!empty($vice_product_list)){
				foreach ($vice_product_list as $vice_k=>$vice_v){
					$all_product_list[$vice_k] = $vice_v;
				}
			}

			//该分类所有属性及属性对应商品信息
			$attribute_data = $this->categoryAttribute($category_id);

			$price_attr_data = $this->priceAttribute($category_id,$all_product_list);
			$attribute_data['price'] = $price_attr_data;
			//echo "<pre>";print_r($attribute_data);die;
			$cache_search_result['vice_product_list'] = $vice_product_list;
			$cache_search_result['main_products_list'] = $main_products_list;
			$cache_search_result['all_product_id_list'] = $all_product_id_list;
			$cache_search_result['all_product_list'] = $all_product_list;
			$cache_search_result['attribute_data'] = $attribute_data;
			//$cache_search_result['price_attr_data'] = $price_attr_data;
				
			//$category_memcache->set($mem_category_key, $cache_search_result,$mem_expired_time['category_attribute_product_cache']);
		//}
		//echo "<pre>";print_r($cache_search_result['all_product_list']);die;
		return $cache_search_result;
	}
	
	/**
	 * 检查处理属性参数
	 * @param  string $attrNarrowSearch 分类选择的属性
	 * @return array 商品属性
	 */
	protected function _checkUrlAttr($attrNarrowSearch) {
		$resultAttr = array('parameter' => array() , 'attrGroups' => array() );
		if(!empty($attrNarrowSearch)) {
			//根据逗号分隔多个属性
			$paramUrl = explode(',', $attrNarrowSearch);
			if(!empty($paramUrl)) {
				foreach ($paramUrl as $key => $value) {
					$paramUrlArray = explode('_', $value);
					$attrId = isset($paramUrlArray[0] ) ?  (int)$paramUrlArray[0] : 0 ;
					$groupId = isset($paramUrlArray[1] ) ? (int)$paramUrlArray[1] : 0 ;
					if( ( $attrId > 0 ) && ( $groupId > 0 ) ){
						$keyTmp = $attrId . '_' . $groupId ;
						$resultAttr ['parameter'][ $keyTmp ] = FALSE ;
						$resultAttr ['attrGroups'][ $attrId ][ $groupId ] = $groupId ;
					}
				}
			}
		}
		return $resultAttr;
	}
	
	/**
	 * 检查url
	 * @param  array $category 分类信息
	 * @author qcn qianchangnian@hofan.cn
	 */
	protected function _checkUrl($category) {
		if( strpos ( trim( $_SERVER['REQUEST_URI'] ) , trim( $category['category_url'] ) ) === FALSE ){
			redirect( genURL( $category['category_url'], ture), 'location', 301 );
		}
	}
	
	/**
	 * 判断这个分类是不是影藏分类，如果是影藏分类就直接跳转至首页
	 * @param  array $category 分类信息
	 * @author qcn qianchangnian@hofan.cn
	 */
	protected function _checkCategoryDisplay($category) {
		if($category['status'] == 0) {
			redirect( genURL(''));
		}
	}
	
	//获取分类价格区间
	public function priceAttribute($category_id,$all_product_list){
		$result = array();
		if(!$category_id || !is_numeric($category_id)) return $result;
		$this->load->model("attributecategorymodel","attribute_category");
		
		//测试用id****************************
		//$category_id = 15010;
		$result = $this->attribute_category->categoryPriceAttribute($category_id);

		foreach ($result as $price_key=>&$price_val){
			$start_price = $price_val['category_narrow_price_start'];
			$end_price = $price_val['category_narrow_price_end'];
			$price_val['product_list'] = array();
			foreach ($all_product_list as $product_id=>$product_info){
				$product_market_price = $product_info['product_price'];
				if( ($product_market_price-$start_price)>0 && ($end_price-$product_market_price)>0 ){
					$price_val['product_list'][$product_id] = $product_id;
                    unset($all_product_list[$product_id]);
				} 
			}
            $price_val['nums'] = count($price_val['product_list']);
		}

		return $result;
	}
	
	//副分类商品列表
	private function viceCategoryProductList($category_id){
		$result = array();
		if(!$category_id || !is_numeric($category_id)) return $result;
		
		$product_list = $this->viceCategory($category_id);
		$this->load->model("goodsmodel","goods");
		if(!empty($product_list)){
			foreach ($product_list as $k=>$v){
				$product_ids[] = $v['product_id'];
			}
            //取出简单商品列表
            $info = $this->goods->getProductList($product_ids,1,0,currentLanguageCode(),true);
            foreach($info as $product){
                $product_id = $product['product_id'];
                $result[$product_id] = $product;
            }
		}	
		
		return $result;
	}
	
	//获取副分类商品id列表
	private function viceCategory($category_id){
		$result = array();
		if(!$category_id || !is_numeric($category_id)) return $result;
		
		$this->load->model("categorymodel","category");
		//like所有的category_path
		$all_search_category_lists = $this->category->categorySearchLists($category_id);
		
		$all_vice_category_ids = array();
		//副分类处理,获取所有副分类category_id，并且可用的
		foreach ($all_search_category_lists as $k_search=>$v_search){
			if($v_search['category_type']==CATEGORY_TYPE_VICE && $v_search['category_status']==STATUS_ACTIVE){
				$all_vice_category_ids[] = $v_search['category_id'];
			}
		}
		
		if(!empty($all_vice_category_ids)){
			//根据副分类商品表，获取所有商品product_id列表
			$this->load->model("goodsmodel","goods");
			$result = $this->goods->categoryProductList($all_vice_category_ids);
		}
		
		return $result;
	}
	
	//获取当前url的基本路径
	private function getAttributeLink(){
		
	}
	
	//单个分类下attribute属性处理(属性名称)
	public function categoryAttribute($category_id){
		$result = array();
		if(!$category_id || !is_numeric($category_id)) return $result;
		
		//
		$this->load->model("attributecategorymodel","attribute_category");
		$data = $this->attribute_category->attributeWithCateId($category_id);
        $ids = array();
        foreach($data as $k=>$v){
            $ids[] = $v['attribute_id'];
            $attribute_category_ids[] = $v['attribute_category_id'];
        }
		//每个属性对应名称及lang语言及值
		$language_id = currentLanguageId();
        $name_list = $this->attribute_category->getAttributeAndLang($ids,$language_id);
        $attr_cate_ids = array();
		foreach ($data as $k=>$v){

			$attribute_id = $v['attribute_id'];
			$result[$attribute_id] = $name_list[$attribute_id];
            $result[$attribute_id]['sort'] = $k;
			//$result['name'][] = $name_list;
			$attr_cate_ids[$attribute_id] = $v['attribute_category_id'];
		}
        //每个属性对应可用值
        $attr_list = $this->attribute_category->groupWithAttributeCategoryId($attr_cate_ids,$language_id);
        if(!empty($attr_list)){
            foreach($attr_list as $attribute_id=>$value_list){
                $result[$attribute_id]['group'] = $value_list;
            }
        }

        $result = array_sort($result,'sort');
//		echo "<pre>attribute_value";print_r($result);die;
		return $result;
	}
	
	
	//获取某分类id下的所有子分类信息及子分类相关商品，及子分类的子分类信息
	public function childrenCategoryAndRelatedProduct($category_id){
		$result = array();
		$language_id = currentLanguageId();
		
		//所有分类信息
		$all_category = $this->_view_data['all_category'];
		
		//获取一级子分类信息
		$children_category = isset($all_category[$category_id])?$all_category[$category_id]:array();
		if(empty($children_category)) return $result;
		
		$result['children'] = $children_category;
		$this->load->model("categorymodel","cate_model");

		//获取二级子分类，及一级子分类的相关商品信息
		foreach ($children_category as $key=>$val){
			$cate_id = $val['category_id'];
			$type = $val['category_type'];
			//$cate_id = 15294;//测试数据
            $children_cate_ids[$key] = $cate_id;
			//echo $cate_id;die;//16290
			
			//获取子分类信息
			$children_children_category = isset($all_category[$cate_id])?$all_category[$cate_id]:array();
			$result['children_children'][$key]['son_category'] = $children_children_category;
		}
        $product_list = $this->cate_model->getCategoryRelatedProductList($children_cate_ids,$language_id);
//        echo '<pre>';print_r($product_list);
        foreach($children_cate_ids as $key=>$cate_id){
            $value_list = array();
            if(isset($product_list[$cate_id])){
                $value_list = $this->productWithPrice($product_list[$cate_id]);
                //*****商品价格没有做汇率处理***
            }
            $result['children_children'][$key]["son_product"] = $value_list;
        }
//        echo '<pre>';print_r($result);exit;
//        $this->database->dumpSQL('slave');
//        die();
		
		return $result;
	}
	
	/**
	 * 分页处理
	 * @param  array $category 分类信息
	 * @param  integer $page 页码
	 * @param  integer $count 商品总数
	 * @param  array $basicParam URL参数
	 * @author qcn qianchangnian@hofan.cn
	 * @return array 分类信息
	 */
	protected function _pagination($category, $page = 1, $count = 0, $basicParam = array(),$attrNarrowSearch = array(),$pagesize = 48) {
		$this->_view_data['pagination']['current_page'] = $page;
		$this->_view_data['pagination']['total_page'] = $count > 0 ? ceil( $count / $pagesize ) : 1;
		
		//ns 特殊处理
		//去掉 attr 当值为空
		$urlTmp = trim( $category['category_url'] ) ;
		if( !empty($attrNarrowSearch ) && is_array( $attrNarrowSearch ) && ( count( $attrNarrowSearch ) > 0 ) ){
			$attrNarrowSearchPagination = $attrNarrowSearch ;
			
			ksort( $attrNarrowSearchPagination );
			$basicParam['attr'] = implode(',', array_keys( $attrNarrowSearchPagination ) );
			if( count( $attrNarrowSearchPagination ) <= 3 ){
				$urlTmp = 'ns'. $urlTmp . implode('/' , array_keys($attrNarrowSearchPagination) ) .'/' ;
			}
	
		}else{
			if ( isset( $basicParam['attr'] ) ){
				unset( $basicParam['attr'] );
			}
		}
		
		$this->_view_data['pagination']['href'] = genURL( $urlTmp . '%u.html',false,$basicParam);
		$this->_view_data['pagination']['default_href'] = genURL( $urlTmp , true, $basicParam );
	}
	
	/**
	 * 设置展示的参数
	 * @param boolean $priceMax 最大价格
	 * @param boolean $priceMin 最小价格
	 * @param boolean $display 展示类型
	 * @param boolean $sort 排序
	 */
	protected function _setParam( $priceRange = '', $sort = false) {
		$basicParam = array(); //初始化数组
		//网盟的参数 带上
		if( isset( $_GET ) && is_array( $_GET ) && (count( $_GET )> 0 ) ){
			foreach ( $_GET as $name => $value ){
				$name = removeXSS( $name );
				$value = removeXSS( $value );
				if( !empty( $name ) && !empty( $value ) ){
					$basicParam[ $name ] = $value ;
				}
			}
		}
		if( !empty( $priceRange ) ) {
			$basicParam['search_price_range'] = trim( $priceRange );
		}
	
		if($sort !== false) {
			// xss 过滤
			$specialCharacters = array( '%', '^', '[', ']', '{', '}', '€', '¥', '£', '<', '>', '=', '+', '*', '\\', "\n", "\n\r", "&nbsp", "\r", "\t", "'", '"', ",");
			$sort = str_replace( $specialCharacters, '', $sort );
			$basicParam['sort'] = trim($sort);
		}
		
		return $basicParam;
	}
	
	//301 or 302 跳转
	protected function _urlRedirect301And302($categoryId = 0) {
		//分类id不存在时404页面跳转
		if ( $categoryId <= 0 ) {
			show_404();
			return;
		}

		$this->load->model("categoryredirectmodel",'Categoryredirect');

		// 取出分类页面id的跳转信息（包括时间和类型）
		$categoryRedirectInfo = $this->Categoryredirect->getRedirectInfoByCategoryId($categoryId);

		// 执行跳转操作
		url301And302Redirect($categoryRedirectInfo, '-c{$targetCategoryId}/');
	}


}
?>
