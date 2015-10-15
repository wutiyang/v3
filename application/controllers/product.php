<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Product extends Dcontroller {
	public function index($productId = 0){
		//商品id
		$pid = (int)$productId;
		if(!is_numeric($pid) && !$pid){
			redirect(genURL(""));
		}
		$language_code = currentLanguageCode();
		
		//商品基本信息
		$product_base_info = $this->getProductInfo($pid);
        if(empty($product_base_info) || $product_base_info['product_status'] == 2){//商品不存在或因侵权而下架
            redirect(genURL(''));
        }
        if(!strstr($_SERVER['REQUEST_URI'],$product_base_info['product_url'])){//商品url不规范跳转到规范商品url
            redirect(genURL($product_base_info['product_url']),'',301);
        }
        //判断product是单sku还是多sku**********************
        $sold_out = false;//是否下架
        $single_sku_type = 1;//单sku还是多sku标识
        $this->load->model("attributeproductmodel","sku");
        $single_sku_info = array();
        $product_warehouse_class = $product_warehouse = '';//默认非海外仓
        if($product_base_info['product_type']==1){//单sku
            $warehouse_code = '';
            $single_sku_value = '';
            $only_sku_product = $this->sku->productAllBaseSku($pid);
            if(empty($only_sku_product)) $sold_out = true; //sku不存在，则表明下架
            else {
                $single_sku_value = $only_sku_product[0]['product_sku_code'];
                $warehouse_code = strtolower($only_sku_product[0]['product_sku_warehouse']);
                $single_sku_info = $only_sku_product[0];
            }

            global $warehouse_range_array;//海外仓库范围
            if(array_key_exists($warehouse_code,$warehouse_range_array)){
                $product_warehouse = $warehouse_range_array[$warehouse_code];
                $product_warehouse_class = strtolower($warehouse_code);
            }

            $this->_view_data['single_sku_value'] = $single_sku_value;//单sku值
        }elseif($product_base_info['product_type']==2){//多sku，前端展示选项及json处理
            $single_sku_type = 2;
            $product_main_attr = $this->productMainAttr($pid);

            if($product_main_attr && !empty($product_main_attr)){
                $single_sku_info = current($product_main_attr['sku_data']);
            }
        }
        //商品状态下架设置
        if(!$sold_out && $product_base_info['product_status'] != 1)
            $sold_out = true;
        //折扣及价格（包含商品基本信息）
        //$product_extend_price = $this->singleProductWithPrice($product_base_info,$single_sku_info);
        $product_extend_price = $this->singleProductWithPrice($product_base_info);
        if($product_base_info['product_type']==2){
            $attr_and_attrvalue = array();
            //多sku-json处理
            if($product_main_attr && !empty($product_main_attr)){
                $sku_and_attrvalue = json_encode(array());
                $attr_and_attrvalue = $product_main_attr['attr_data'];
                //对每个sku的价格进行判断是否会加价
                $product_discount_number = number_format($product_extend_price['product_discount_number'],2,'.',',');//折扣,50%
                $product_price_market = number_format($product_base_info['product_price_market'],2,'.',',');//原价,单位为美元
                $product_price = number_format($product_base_info['product_price'],2,'.',',');//现价，单位为美元
                //前端js使用的json数据
                $sku_and_attrvalue = json_encode($this->formatAttrAndBundlePrice($product_main_attr['sku_data'],$product_price_market,$product_price,$product_discount_number));
                $this->_view_data['attr_and_attrvalue'] = $attr_and_attrvalue;
                $this->_view_data['sku_and_attrvalue'] = $sku_and_attrvalue;
            }else{
                $sold_out = true;
            }
        }
		//面包屑
		$category_id = $product_base_info['category_id'];
		$category_path = $product_base_info['product_path'];
        //slimbanner
        $this->_view_data['slim_banner'] = $this->getSlimBanner($category_path);
		$crumbs_list = $this->getCategoryCrumbs($category_path);

		//商品下方分类描述
		$category_template = '';
		$this->load->model("categorymodel","category");
		$category_ids = explode('/',$product_base_info['product_path']);
		$category_template_list = $this->category->getCategoryTemplateByCategory($category_ids,currentLanguageId());
		$category_template_list = reindexArray($category_template_list,'category_id');
		$category_ids = array_reverse($category_ids);
		foreach($category_ids as $category_id){
			if(!isset($category_template_list[$category_id])) continue;
			$category_template = $category_template_list[$category_id]['category_template_content'];
			break;
		}
		$this->_view_data['category_template'] = $category_template;

		//商品扩展信息(价格，折扣，图片)
		$this->load->model("goodsmodel","product");
		//图片
		$product_extend_images = $this->product->productImageList($pid);
		$this->_view_data['product_warehouse'] = $product_warehouse;
		$this->_view_data['product_warehouse_class'] = $product_warehouse_class;
		$this->_view_data['single_sku_type'] = $single_sku_type;
		$this->_view_data['sold_out'] = $sold_out;

		//相同父分类下，其他分类商品推荐（右上）
		$catregory_recommend_product = $this->categoryRecommendProduct($category_id,$category_path);

		//also_like推荐数据（右下）
        $alsolike_data = $this->product->alsolikeProductWithPid($pid,$language_code);

		//also_like价格处理
		foreach ($alsolike_data as $also_key=>&$also_val){
			$also_val = $this->singleProductWithPrice($also_val);
		}
		//组合促销商品(包含价格处理)
		$bundle_product_list = $this->productBundle($pid,$category_path,3);

		//cookie记录最近浏览商品
        $visited_Product_list = $this->_setGoodsVisitCookie($pid);
        $view_history_list = array();
        if(is_array($visited_Product_list) && !empty($visited_Product_list))
            $visited_Product_list = array_diff($visited_Product_list, array($pid));
        if(!empty($visited_Product_list)){
            $visited_Product_list = $this->product->getProductList($visited_Product_list,$status = 1,0,$language_code);
            foreach ($visited_Product_list as $view_key=>$view_product_info){
                if($view_product_info){
                    //促销价格
                    $view_product_data = $this->singleProductWithPrice($view_product_info);
                    $view_history_list[] = $view_product_data;
                }
            }
        }
		//$this->_initCookieForAjax($product_base_info);
		
		//问答
        $qna_list = array();
        $qna_nums = 0;
		$this->load->model("qnamodel","qna");
		$qnas = $this->qna->qnaListWithPid($pid,1,5);
        if($qnas['data'] && !empty($qnas['data'])){
            $qna_list = $qnas['data'];
            //查询qna的用户信息
            $this->load->model("customermodel","user");
            foreach ($qna_list as $k=>&$v){
                $customer_id = $v['customer_id'];
                $user_name = $this->user->nameWithUid($customer_id);
                $v['customer_name'] = $user_name;
            }
            $qna_nums = $qnas['nums'];
        }
        $this->_view_data['qna_list'] = $qna_list;
        $this->_view_data['qna_nums'] = $qna_nums;
		
		//商品(属性，尺寸，图片，简介（小心处理，html标签）)
		$product_specifications = $this->specificationsAttr($pid);
		$this->_view_data['product_specifications'] = $product_specifications;
		$this->load->model("imageproductmodel","imageproduct");
		$product_vice_image_list = $this->imageproduct->imageListWithPid($pid);
		$this->_view_data['product_vice_image_list'] = $product_vice_image_list;
		
		$this->load->model("sizechartmodel","sizechart");
		$product_sizechart_data = $this->sizechart->sizechartListWithPid($pid,currentLanguageId());
		//数据格式转化
		$product_sizechart_list = $this->formatSizechart($product_sizechart_data);
		$this->_view_data['product_sizechart_list'] = $product_sizechart_list;
		
		//评论（评论人数，星级别，评论内容，评论人，是否审核）
		$this->load->model("reviewmodel","review");
		$this->load->model("reviewhelpfulmodel","reviewhelpful");
		$review_list = $this->review->reviewListWithPid($pid);
		$all_review_data = $this->reviewWithUserScore($review_list);
		$review_user_nums = $all_review_data['review_nums'];
		
		//评论数
		$reviewNums = isset($review_list['nums'])?$review_list['nums']:0;
        $this->_view_data['review_nums'] = $reviewNums;
//		$reviewNums = reindexArray($reviewNums,'product_id');
//		$this->_view_data['review_nums'] = isset($reviewNums[$pid]['num'])?$reviewNums[$pid]['num']:0;

		$review_total_score = $all_review_data['total_score'];
		
		//处理 星级别信息
        $review_data = array_slice($all_review_data['data'],0,5);//新增评论人信息
		$star_info = $this->reviewWithStar(count($all_review_data['data']),$review_total_score);
		$star_level = $star_info['star_level'];
		$average_score = $star_info['average_score'];

        //处理 收藏
        if($this->customer->checkUserLogin()){
            $userId = $this->customer->getCurrentUserId();
            $this->load->model("wishlistmodel","wishlist");
            $result = $this->wishlist->getUserCollecteList($userId, 1);
            $history_wish_list = array_reduce($result, create_function('$v,$w', '$v[]=$w["product_id"];return $v;'));
            if(is_array($history_wish_list) && in_array($pid,$history_wish_list)){
                $product_base_info['love'] = 1;
            }
            //like信息
            $helpfulInfos = $this->reviewhelpful->getHelpfulInfos($productId,$userId);
        }else{
        	//like信息
        	$helpfulInfos = array();
        }

        //处理like unlike信息
		$review_data = $this->_processReviewWithHelpfulInfo($review_data,$helpfulInfos);

		$this->_view_data['star_num'] = $star_level;
		$this->_view_data['average_star_num'] = $average_score;
		$this->_view_data['review_list'] = array_slice($review_data, 0,5);
		
		//GA统计(google_tag_params)
		$this->_getGoogleTagParams($pid, $product_extend_price, $crumbs_list);
		//GA统计-datalayers
		$this->_getGaDatalayers($pid,$crumbs_list,$product_extend_price);
		//GA统计-detail
		$this->_getGaDetail($pid,$product_extend_price);
        $this->_setGaDatailProductShow($bundle_product_list,$view_history_list,$alsolike_data);
		$this->_view_data['view_history_list'] = $view_history_list;
		$this->_view_data['alsolike_data'] = $alsolike_data;
		$this->_view_data['catregory_recommend_product'] = $catregory_recommend_product;
		$this->_view_data['bundle_product_list'] = $bundle_product_list;
		$this->_view_data['product_extend_images'] = $product_extend_images;
        if(isset($product_extend_price['slogan'][0])){
            $product_extend_price['slogan'] = json_decode($product_extend_price['slogan'][0]['slogan_content'],true);
            $product_extend_price['slogan'] = $product_extend_price['slogan'][currentLanguageId()];
        } else {
            $product_extend_price['slogan'] = '';
        }
		$this->_view_data['product_extend_price'] = $product_extend_price;
        if(!isset($product_base_info['product_description_name'])) $product_base_info['product_description_name'] = '';
        $visible_content = isset($product_base_info['product_description_content'])?$product_base_info['product_description_content']:'';
        $visible_content = strip_tags($visible_content);
        $visible_content = str_replace(array(' ','&nbsp;',"\n","\r","\r\n","\t"),'',$visible_content);
        $visible_content = trim($visible_content);
        if(strlen($visible_content) <= 10) $product_base_info['product_description_content'] = '';
		$this->_view_data['product_base_info'] = $product_base_info;
		$this->_view_data['crumbs_list'] = $crumbs_list;
		$this->_view_data['currency'] = currentCurrency();
        //添加部分list参数对应表
        $product_base_info['product_basediscount_price'] = $product_base_info['product_discount_price'] = isset($product_base_info["discount_price"])?$product_base_info["discount_price"]:$product_base_info['product_price'];
        //$this->dataLayerPushImpressions(array($product_base_info),'Product Detail Page');
        if(isset($product_extend_price['product_description_name']) && isset($product_extend_price['product_discount_price']) && isset($product_extend_price['product_currency'])){
            //seo
            $this->_view_data['title'] = $product_extend_price['product_description_name'];
            $this->_view_data['seo_keywords'] = $product_extend_price['product_description_name'];
            $this->_view_data['description'] = sprintf(lang('description'),$product_extend_price['product_description_name'],$product_extend_price['product_currency'].$product_extend_price['product_discount_price']);
        }
         //$this->database->dumpSQL('slave');die;
         
        //获取通知详情 
        $this->_view_data['notes'] = $this->getNoteData($product_base_info['product_id'],$product_base_info['product_path'],$product_extend_price['product_discount_price']);
        
		parent::index();
	}
	
	/**
	 * 获取note提示数据
	 * @param unknown $product
	 * @return multitype:Ambigous <multitype:, unknown> Ambigous <multitype:, multitype:string > Ambigous <multitype:, multitype:multitype:unknown  >
	 * @author Edison
	 */
	private function getNoteData($productId,$productPath,$productDiscountPrice){
		$result = array();
		
		//common note
		$commonNote = $this->getCommonNote($productId,$productPath);
		//reward note
		$rewardsNote = $this->getRewardsNote($productDiscountPrice);
		//满减 note
		$fullcutNote = $this->getFullcutNote($productId,$productPath);
		
		$result['common_note'] = isset($commonNote)?$commonNote:array();
		$result['rewards_note'] = isset($rewardsNote)?$rewardsNote:array();
		//促销的notes
		$promotionNotes = array();
		if(!empty($fullcutNote)){
			foreach ($fullcutNote as $val){
				$promotionNotes[] = $val;
			}
		}
		$result['promotion_note'] = $promotionNotes;
		
		return $result;
	}
	
	/**
	 * common note
	 * @param unknown $productId
	 * @param unknown $product_path
	 * @return unknown 
	 * @author Edison
	 */
	private function getCommonNote($productId,$product_path){
		$this->load->model("notemodel","note");
		$note_data = $this->note->NoteWithProductAndCategory($productId,$product_path,currentLanguageId());
		return $note_data;
	}
	
	/**
	 * reward note
	 * @param unknown $price 价格
	 * @param unknown $currency 货比符号
	 * @return multitype:string array('content'=>'content')
	 * @author Edison
	 */
	private function getRewardsNote($price){
		$result = array();
		
		$productCurrency = '$';
		$currency_format = $this->getCurrencyNumber();
		if($currency_format){
			$productCurrency = $currency_format['currency_format'];
		}
		//rewards根据价格计算相应reward
		$rate = $this->getCustomerLevel();
		$reward = $productCurrency.number_format($price*0.01*$rate,2,'.',',');
		
		$content = sprintf(lang('earn_x_rewards'),$reward);
		$result['content'] = $content;
		
		return $result;
	}
	
	/**
	 * 满减 note
	 * @param unknown $productId
	 * @param unknown $product_path
	 * @return multitype:|multitype:multitype:mixed unknown array('content'=>'content','url'=>'url')
	 * @author Edison
	 */
	private function getFullcutNote($productId,$product_path){
		$result = array();
		$this->load->model("discountrangemodel","discountrange");
		$this->load->model("discountmodel","discount");
		
		//获取商品的所属分类
		$categoryIds = explode('/', $product_path);

		//获取满减促销的相应信息
		$param['discount_type'] = 5;
		$discount_infos = $this->discount->getActiveDiscountNew($param);
		//去除过期
		$requestTime = requestTime();
		foreach ($discount_infos as $k=>$v){
			if($v['discount_status'] != STATUS_ACTIVE){
				unset($discount_infos[$k]);
			}
			if($requestTime < strtotime($v['discount_time_start'])){
				unset($discount_infos[$k]);
			}
			if($requestTime > strtotime($v['discount_time_finish'])){
				unset($discount_infos[$k]);
			}
		}
		$discount_infos = reindexArray($discount_infos, 'discount_id');

		//获取满减促销ids
		$discountIds = array_keys($discount_infos);
		if(empty($discountIds)){
			return $result;
		}
		//获取商品所属的满减促销信息，返回的数据为满减促销id
		$discounts = $this->discountrange->getPidFullcutDiscounts($productId,$discountIds,$categoryIds);
		
		if(empty($discounts)){
			return $result;
		}else{
			$discountInfo = null;
			//
			foreach ($discounts as $val){
				if(isset($discount_infos[$val])){
					$arr = array();
					//得到满减促销详情
					$discountInfo = $discount_infos[$val];
					//note信息处理处理
					$titles = json_decode($discountInfo['discount_title'],true);
					$content = $titles[currentLanguageId()];
						
					$productCurrency = '$';
					$discountCondition = $discountInfo['discount_condition'];
					$discountEffect = $discountInfo['discount_effect'];
					
					$currency_format = $this->getCurrencyNumber();
					if($currency_format){
						$productCurrency = $currency_format['currency_format'];
						$discountCondition = round($discountCondition*$currency_format['currency_rate'],2);
						$discountEffect = round($discountEffect*$currency_format['currency_rate'],2);
					}
						
					$content = str_replace('{$condition_price}', $productCurrency.ceil($discountCondition), $content);
					//2是满减 1是满折
					if($discountInfo['discount_type_effect'] == 2){
						$content = str_replace('{$effect_price}', $productCurrency.floor($discountEffect), $content);
					}else{
						$content = str_replace('{$effect_price}', $discountInfo['discount_effect'].'%', $content);
					}

					$arr['discount_condition'] = $discountInfo['discount_condition'];
					$arr['content'] = $content;
					$arr['url'] = $discountInfo['discount_url'];
					
					$result[] = $arr;
				}
			}
			
			//排序
			if(!empty($result)){
				for($i=0;$i<count($result);$i++){
					$p = $i;
					for($j=$i+1; $j<count($result); $j++) {
						if($result[$p]['discount_condition'] > $result[$j]['discount_condition']) {
							$p = $j;
						}
					}
					
					if($p != $i) {
						$tmp = $result[$p];
						$result[$p] = $result[$i];
						$result[$i] = $tmp;
					}
				}
			}
			
			return $result;
		}
	}
	
	/**
	 * @desc 获取用户rate级别
	 * @return unknown
	 */
	private function getCustomerLevel(){
		$rate = 1;
		if($this->customer->checkUserLogin()){
			$customer_id = $this->customer->getCurrentUserId();
			return $rate = $this->customer->getRewordsRate($customer_id);
		}else{
			return $rate;
		}
	
	}

    private function _setGaDatailProductShow($bundle_product_list,$view_history_list,$alsolike_data){
        $i = 1;
        $list = 'Product Detail Page';
        $product_list = array();
        foreach($bundle_product_list as $value){
            $product_list[] = array('id'=>$value['product_id'],'price'=>$value['product_discount_price'],'list'=>$list,'position'=>$i++);
        }
        foreach($view_history_list as $value){
            $product_list[] = array('id'=>$value['product_id'],'price'=>$value['product_discount_price'],'list'=>$list,'position'=>$i++);
        }
        foreach($alsolike_data as $value){
            $product_list[] = array('id'=>$value['product_id'],'price'=>$value['product_discount_price'],'list'=>$list,'position'=>$i++);
        }
        $ga_str = json_encode($product_list);
        $this->_view_data['ga_show'] = $ga_str;
    }

	private function _processReviewWithHelpfulInfo($review_data,$helpfulInfos){
		$helpfulInfos = reindexArray($helpfulInfos,'review_id');
		foreach ($review_data as &$data) {
			$data['like'] = 0;
			$data['unlike'] = 0;

			if(isset($helpfulInfos[$data['review_id']])){
				$helpfulInfo = $helpfulInfos[$data['review_id']];
				//1 有帮助 2没帮助
				if($helpfulInfo['review_helpful_type'] == 1){
					$data['like'] = 1;
                    $data['review_count_helpful']++;
				}else{
					$data['unlike'] = 1;
                    $data['review_count_nothelpful']++;
				}
			}
			
		}
		return $review_data;
	}
	
	private function _getGaDatalayers($pid,$crumbs_list,$product_extend_price){
		$this->ga_dataLayer = array();
		$this->ga_dataLayer['productId'] = $pid;
		
		$k = 1;
		foreach ($crumbs_list as $key=>$val){
			$page = 'pagecategoryL'.$k;
			//$$page  = htmlspecialchars($val['category_name']);
			$this->ga_dataLayer[$page] = htmlspecialchars($val['category_name']);
			$k++;
		}
		//新增GA代码
		$this->ga_dataLayer['ProductName'] = $product_extend_price['product_description_name'];
		$description = $product_extend_price['product_description_content'];
		$description = str_replace('<br>', '', $description);
		$description = str_replace("\r",' ',$description);
		$description = str_replace("\n",' ',$description);
		$description = str_replace("\t",' ',$description);
		$description = str_replace("[",'',$description);
		$description = str_replace("]",'',$description);
		$description = preg_replace('/[\x00-\x20]/',' ',$description);
		$description = eb_substr($description,255);
		$this->ga_dataLayer['description'] = $description;
		$this->ga_dataLayer['brand'] = '';
		$product_extend_price['brand_id'] = 2;//test
		if(isset($product_extend_price['brand_id']) && $product_extend_price['brand_id']){
			$this->load->model('brandmodel','brandmodel');
			$brand_info = $this->brandmodel->getBrandInfoByBid($product_extend_price['brand_id']);
			if(!empty($brand_info)){
				$this->ga_dataLayer['brand'] = $brand_info['brand_title'];
			}
		}
		
		$this->ga_dataLayer['price'] = $product_extend_price['product_discount_price'];
		$this->ga_dataLayer['MarketPrice'] = $product_extend_price['product_price_market'];
		$this->ga_dataLayer['currency'] = $this->currency;
		$this->ga_dataLayer['ProductURL'] = genURL($product_extend_price['product_url']);
		$this->ga_dataLayer['ImageURL'] = PRODUCT_IMAGEM_URL.$product_extend_price['product_image'];
		$this->ga_dataLayer['valid'] = 0;
		if($product_extend_price['product_status']==0){
			$this->ga_dataLayer['valid'] = date("Y-m-d H:i:s",time());
		}
		
		$this->_view_data['ga_dataLayer'] = json_encode($this->ga_dataLayer);
	}
	
	private function _getGoogleTagParams($pid,$product_extend_price,$crumbs_list){
		$this->ecomm_prodid = 'eachbuyer_usd_'.$pid.'_us';
		$this->ecomm_pagetype = 'product';
		$this->ecomm_pname = $product_extend_price['product_name'];
		$this->ecomm_pcat = array();
		foreach ($crumbs_list as $crumbs_key=>$crumbs_val){
			array_push($this->ecomm_pcat, htmlspecialchars($crumbs_val['category_name']));
		}
		$this->ecomm_pvalue = $product_extend_price['product_basediscount_price'];
	}
	
	private function _getGaDetail($pid,$product_extend_price){
		$dataLayerDetail['detail']['products'] = array(0=>array('id'=>$pid,'price'=>$product_extend_price['product_discount_price']));
		$this->_view_data['dataLayer_detail'] = json_encode($dataLayerDetail); 
	}
	/**
	 * @desc 根据评论列表返回评论列表相关的评论人及总评分，星级别
	 * @param unknown $review_list
	 * @param number $show_review_nums
	 */
	public function reviewStar($review_list,$show_review_nums = 5){
		$review_nums = 0;//总评论人数
		$review_total_socre = 0;//评论总分
		$show_review_list = array();//显示的评论数据
		$user_review_nus = 0;//用户评论该商品的次数
		if(!empty($review_list)){
			$this->load->model("customermodel","user");
			foreach ($review_list as $review_k=>&$review_v){
				if($review_v['review_status']==1){
					$review_nums++;
					$review_total_socre+= $review_v['review_score'];
				}
				if($review_k < $show_review_nums){
					$show_review_list[] = $review_v;
				}
	
				//每个评论的评论人信息
				$user_id = $review_v['customer_id'];
				$user_name = $this->user->nameWithUid($user_id);
				$review_v['user_name'] = $user_name;
			}
		}
			
	
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
	
		$this->_view_data['star_num'] = $star_num;
		$this->_view_data['average_star_num'] = $average_star_num;
		$this->_view_data['review_nums'] = $review_nums;
		$this->_view_data['review_list'] = $show_review_list;
	}
	
	//改商品id的sku是否影响价格，加价
	private function formatAttrAndBundlePrice($sku_data,$product_price_market,$product_price,$product_discount_number){
		if(empty($sku_data)) return array();
		$currency_format = $this->getCurrencyNumber();
		$real_discount = (100-$product_discount_number)/100;

		global $warehouse_range_array;
		foreach ($sku_data as $main_key=>&$main_val){
            $new_product_price_market = $product_price_market;
            $new_product_price = $product_price;
			//返回前端的仓库处理
			$lower_warehouse = strtolower($main_val['product_sku_warehouse']);
			if(array_key_exists($lower_warehouse, $warehouse_range_array)){
				$main_val['product_sku_warehouse'] = $warehouse_range_array[$lower_warehouse];
				$main_val['product_sku_warehouse_class'] = $lower_warehouse;
			}else{
				$main_val['product_sku_warehouse'] = '';
				$main_val['product_sku_warehouse_class'] = '';
			}
				
			
				$main_val['product_currency'] = "$";
			
			//该sku对价格有调整
			if((int) $main_val['product_sku_price']!=0 || (int) $main_val['product_sku_price_market']!=0) {
				$new_product_price_market = $new_product_price_market+$main_val['product_sku_price_market'];
                $new_product_price = $new_product_price+$main_val['product_sku_price'];
			}
			
			//再打折
			$main_val['elidePrice'] = round($new_product_price_market,2);
			//
			$new_product_price = $real_discount == 1?$new_product_price:$new_product_price_market*$real_discount;
			$new_product_price = number_format(round($new_product_price,2),2,'.',',');
			$main_val['salePrice'] = number_format($new_product_price,2,'.',',');
			
			//汇率
			if($currency_format){
				$main_val['product_currency'] = $currency_format['currency_format'];
				$main_val['salePrice'] = number_format(round($main_val['salePrice']*$currency_format['currency_rate'],2),2,'.',',');
				$main_val['elidePrice'] = number_format(round($main_val['elidePrice']*$currency_format['currency_rate'],2),2,'.',',');
			}
		
			unset($main_val['product_sku_price_market'],$main_val['product_sku_price']);
		}	

		return $sku_data;
	}
	
	//sizechart的数据格式化
	private function formatSizechart($sizechart_data){
		$result = array();
		if(!$sizechart_data || empty($sizechart_data)) return $result;
		 
		$language_id = currentLanguageId();
		
		//标题
		$all_language_title = json_decode($sizechart_data[0]['sizechart_product_title'],true);
		foreach ($all_language_title as $title_k=>$title_v){
			$title[] = $title_v[$language_id];
		}
		$result['Centimeters']['title'] = $title;
		$result['Inches']['title'] = $title;
		
		$title_num = count($title);
		$all_content = json_decode($sizechart_data[0]['sizechart_product_content'],true);
		//echo "<pre>";print_r($all_content);//die;
		//内容（“/”，“数组”格式需要进一步处理）
		foreach ($all_content as $k=>$v){
			$count = count($v);
			if($count<$title_num)continue;
			foreach ($v as $key=>$val){
				if($val=="/") {unset($result['Inches'][$k]);unset($result['Centimeters'][$k]);continue;}
				if(is_array($val)){
					$result['Centimeters'][$k][] = implode("~", $val);
					$new_val = array();
					foreach ($val as $keys=>$values){
						$new_val[] = round($values*0.3937008,2);
					}
					$result['Inches'][$k][] = implode("~", $new_val);
					
				}else{
					$result['Centimeters'][$k][] = $val;
					if(is_numeric($val)){
						$result['Inches'][$k][] = round($val*0.3937008,2);
					}else{
						$result['Inches'][$k][] = $val;
					}
					
				}
			}
		}
		
		return $result;
	}
	
	//商品附属属性
	private function specificationsAttr($productId){
		$result = array();
		if(!$productId || !is_numeric($productId)) return $result;
		$language_code = currentLanguageCode();
		$language_id = currentLanguageId();
		$this->load->model("attributeproductmodel","attribute");
		$this->load->model("attributecategorymodel","attributecategory");
		$attr_info = $this->attribute->productAllAttrIds($productId);
		
		$block_array = array();
		//每个block对应内容获取
		if($attr_info){
			foreach ($attr_info as $k=>$v){
				$block_id = $v['attribute_block_id'];
				$attr_id = $v['attribute_id'];
                $attr_value_id = $v['attribute_value_id'];
                $attr_value = $v['attribute_value'];

				if(!in_array($block_id, $block_array))
					$block_array[$block_id] = $block_id;
                $attr_ids[$attr_id] = $attr_id;
                $attr_value_ids[$attr_value_id] = $attr_value_id;
                if($attr_value_id)
                    $attr_tree[$block_id][$attr_id][$attr_value_id] = $attr_value_id;
                else if($attr_value != '')
                    $attr_tree[$block_id][$attr_id]['value'] = $attr_value;
			}
			
            //获取区块信息
            $block_list = array();
            $block_list = $this->attributecategory->attrBlocks($block_array,$language_id);
            if(empty($block_list)) return $result;
            $block_result = array();
            foreach($block_list as $value1){
                if(isset($value1['attribute_block_id']))$block_result[$value1['attribute_block_id']] = $value1;
            }
            $attr_list = array();
            $attr_list = $this->attributecategory->getAttributesLangCache($attr_ids, $language_id);
            $attr_result = array();
            if(!empty($attr_list)){
            	foreach($attr_list as $key2=>$value2){
            		if(isset($value2['attribute_id'])){
                        $attr_result[$value2['attribute_id']] = $value2;
                        $attr_result[$value2['attribute_id']]['sort'] = $key2;
                    }
            	}
            }
            $attr_value_list = array();
            $attr_value_list = $this->attributecategory->getAttributeValueWithValueidsCache($attr_value_ids, $language_id);
            $attr_value_result = array();
            if(!empty($attr_value_list)){
            	foreach($attr_value_list as $value3){
            		if(isset($value3['attribute_value_id']))$attr_value_result[$value3['attribute_value_id']] = $value3;
            	}
            }
            
            foreach($attr_tree as $block_id=>$attr_ids){
               if(!isset($block_result[$block_id]) || empty($attr_ids)) continue;
            	$result[$block_id] = $block_result[$block_id];
                $result[$block_id]['attr'] = array();
                foreach($attr_ids as $attr_id=>$attr_value_ids){
                    if(isset($attr_result[$attr_id])){
                        $result[$block_id]['attr'][$attr_id] = $attr_result[$attr_id];
                        $result[$block_id]['attr'][$attr_id]['attr_value'] = array();
                        foreach($attr_value_ids as $attr_value_id=>$attr_value){
                            if(isset($attr_value_result[$attr_value_id]))
                                $result[$block_id]['attr'][$attr_id]['attr_value'][] = $attr_value_result[$attr_value_id];
                            else if(trim($attr_value))
                                $result[$block_id]['attr'][$attr_id]['attr_value'][]['attribute_value_lang_title'] = $attr_value;
                        }
                    }
                    if(!isset($result[$block_id]['attr'][$attr_id]['attr_value']) || !count($result[$block_id]['attr'][$attr_id]['attr_value']))
                        unset($result[$block_id]['attr'][$attr_id]);
                }
                if(isset($result[$block_id]['attr']) && is_array($result[$block_id]['attr']))
                    $result[$block_id]['attr'] = array_sort($result[$block_id]['attr'],'sort');
            }
		}else{
			return $result;
		}
		return $result;
		
	}
	//绑定商品
	private function productBundle($productId,$category_path,$pagesize=3){
		$result = array();
		if(!$productId || !is_numeric($productId)) return $result;
		$language_code = currentLanguageCode();

		//捆绑销售产品
		$this->load->model("bundlemodel","bundle");
		$bundle_product_ids = $this->bundle->bundleWithProductId($productId,$category_path,$language_code);
        $bundle_product_list = $this->getProductSkuInfoByPids($bundle_product_ids,3,$productId);
		return $bundle_product_list;
	}
	
	/**
	 * @desc 根据分类id及分类的path路径，获取该父分类的商品列表
	 * @param unknown $category_id
	 * @param unknown $category_path
	 * @return multitype:|unknown
	 */
	private function categoryRecommendProduct($category_id,$category_path){
		$result = array();
		if(!$category_id || !is_numeric($category_id)) return $result;

		$all_category = $this->_view_data['all_category'];
		//获取该分类的父id
		if($category_id==$category_path){//顶级分类,父id=0
			$parent_catid = 0;			
		}else{
			$path_array = explode("/", $category_path);
            $count_path = count($path_array);
            $parent = $count_path - 2 > 0?$count_path - 2:0;
            $parent_catid = $path_array[$parent];
//			foreach ($path_array as $k=>$v){
//				if($v==$category_id && $k-1>=0){
//					$parent_catid = $path_array[$k-1];
//					if(!isset($all_category[$parent_catid])) $parent_catid = 0;
//				}else{
//					$parent_catid = 0;
//				}
//			}
			
		}
		
//		//获取父分类的子分类id,自己除外
//		$recommend_category_ids = array();
//		foreach ($all_category[$parent_catid] as $key=>$val){
//			if($val['category_id']!=$category_id) $recommend_category_ids[$val['category_id']] = $val['category_id'];
//		}
//
//        //分类id对应的上架商品，获取八个
//        $this->load->model("categorymodel","category");
//        $recommend_product_lists = $this->category->incategoryProductRecommend($recommend_category_ids,8);
//
//        return $recommend_product_lists;

        //获取父分类的子分类id,自己除外
        $recommend_category_lists = array();
        if(!empty($all_category[$parent_catid]))
            foreach ($all_category[$parent_catid] as $key=>$val){
                if($val['category_id']!=$category_id && count($recommend_category_lists) <= 8) $recommend_category_lists[$val['category_id']] = $val;
            }
//		echo '<pre>';print_r($recommend_category_lists);exit;
		return $recommend_category_lists;
	}
	
	/**
	 * @desc 商品sku属性信息
	 * @param unknown $productId
	 * @return multitype:|unknown
	 */
	private function productMainAttr($productId){
		$result = array();
		if(!$productId || !is_numeric($productId)) return $result;
		//获取sku基本信息
		$this->load->model("attributeproductmodel","attribute");
		
		//获取sku的属性及属性组值信息，多语言
		$sku_connect_attr_info = $this->attribute->productAttrWithSku($productId);
		if(!empty($sku_connect_attr_info)){
			//sku对应的，原价格，最新sku价格，仓库,sku上下架状态
			$sku_base_info = $this->attribute->productAllBaseAllSku($productId);
			$able_sku_array = array();
			foreach($sku_base_info as $value){
				$sku_base_list[$value['product_sku_code']] = $value;
				if($value['product_sku_status']) array_push($able_sku_array, $value['product_sku_code']);
			}
			
			//所有的属性，对应的属性组值 ，数据（先排除下架sku属性及属性组id）
			foreach ($sku_connect_attr_info as $first_k=>$first_v){
				if(!in_array($first_v['product_sku'], $able_sku_array) ) unset($sku_connect_attr_info[$first_k]);
			}
			$all_attr_data = $this->formatAttrWithAttrgroup($sku_connect_attr_info);
			
			$sku_attr_value = array();
			foreach ($sku_connect_attr_info as $k=>$v){
				$sku_value = $v['product_sku'];
                if(isset($sku_base_list[$sku_value])){
                    $sku_attr_value[$sku_value]['product_sku_code'] = $sku_base_list[$sku_value]['product_sku_code'];
                    $sku_attr_value[$sku_value]['product_sku_warehouse'] = $sku_base_list[$sku_value]['product_sku_warehouse'];
                    $sku_attr_value[$sku_value]['product_sku_price_market'] = $sku_base_list[$sku_value]['product_sku_price_market'];
                    $sku_attr_value[$sku_value]['product_sku_price'] = $sku_base_list[$sku_value]['product_sku_price'];
                    $sku_attr_value[$sku_value]['attr'][] = $v['complexattr_value_id'];
                    $sku_attr_value[$sku_value]['sku_status'] = $sku_base_list[$sku_value]['product_sku_status'];
                } else {
                    unset($result['attr_data'][$v['complexattr_id']]['attr_value'][$v['complexattr_value_id']]);
                }
			}
			
			$result['attr_data'] = $all_attr_data;
			$result['sku_data'] = $sku_attr_value;
		}
		
		return $result;
	}
	
	//根据商品sku信息，获取该sku的属性及属性组信息
	private function formatAttrWithAttrgroup($sku_data){
		$result  = $complexattr_value_infos = $complexattr_value_info = array();
		if(!is_array($sku_data) || empty($sku_data)) return $result;
		
		$language_id = currentLanguageId();
		$this->load->model("attributeproductmodel","attribute");
		$att_ids = $attr_value_ids = array();
		foreach ($sku_data as $k=>$v){
			//属性信息
			$att_id = $v['complexattr_id'];
			array_push($att_ids, $att_id);
		}	
		$complexattr_value_infos = $this->attribute->complexattrBatchInfo($att_ids,$language_id);

		foreach ($complexattr_value_infos as $attr_v){
			$new_att_id = $attr_v['complexattr_id'];
			$result[$new_att_id] = $attr_v;
		}

		foreach ($sku_data as $key=>$val){
			//属性信息
			$att_id = $val['complexattr_id'];
			//属性组信息
			$attr_value_id = $val['complexattr_value_id'];
			
			array_push($attr_value_ids, $attr_value_id);
		}
		$complexattr_value_info = $this->attribute->complexattrBatchValueInfo($attr_value_ids,$language_id);
		//组合同一个组商品
		$complexattr_groupvalue_info = array();
		foreach ($complexattr_value_info as $attr_value){
			$value_id = $attr_value['complexattr_value_id'];
			$complexattr_groupvalue_info[$value_id] = $attr_value;
		}

		foreach ($sku_data as $k=>$v){
			//属性组信息
			$new_attr_value_id = $v['complexattr_value_id'];
			//属性信息
			$new_att_id = $v['complexattr_id'];
			if(isset($result[$att_id]) && isset($complexattr_groupvalue_info[$new_attr_value_id])){
				$result[$new_att_id]['attr_value'][$new_attr_value_id] = $complexattr_groupvalue_info[$new_attr_value_id];
			}
		}

		return $result;
	}
	private function formatAttrWithAttrgroup_bak($sku_data){
		$result = array();
		if(!is_array($sku_data) || empty($sku_data)) return $result;
	
		$language_id = currentLanguageId();
		$this->load->model("attributeproductmodel","attribute");
		foreach ($sku_data as $k=>$v){
			//属性信息
			$att_id = $v['complexattr_id'];
				
			$complexattr_info = $this->attribute->complexattrInfo($att_id,$language_id);
			if(!empty($complexattr_info)) $result[$att_id] = $complexattr_info[0];
				
		}
	
		foreach ($sku_data as $key=>$val){
			//属性信息
			$att_id = $val['complexattr_id'];
			//属性组信息
			$attr_value_id = $val['complexattr_value_id'];
			$complexattr_value_info = $this->attribute->complexattrValueInfo($attr_value_id,$language_id);
			//echo "<pre>";print_r($complexattr_value_info);
			if(!empty($complexattr_value_info)) $result[$att_id]['attr_value'][$attr_value_id] = $complexattr_value_info[0];
		}
	
		//echo "<pre>vvvvv";print_r($result);die;
		return $result;
	}
	/**
	 * @desc 返回商品基本详情信息
	 * @param unknown $productId
	 * @return multitype:
	 */
	private function getProductInfo($productId){
		$result = array();
		if(!$productId || !is_numeric($productId)) return $result;
		$language_code = currentLanguageCode();
		
		$this->load->model("goodsmodel","product");
		$result = $this->product->getinfo($productId,0,$language_code);
		return $result;
	}
	
	//单品页该用户能否评论（ajax请求）
	public function reviewAjax(){
		//获取参数 pid
		$pid = $this->input->post("prcId");
	
		if($this->customer->checkUserLogin()){
			$user_id = $this->session->get("user_id");
			//评论该商品的次数
			$this->load->model("reviewmodel","review");

			//获取商品列表
			//是否为空
			//如果为空提示未购买 
			//如果不为空 获取评论列表 查看是否已经评论

			//获取已经评价的列表
			$reviewList = $this->review->reviewListByUserId($user_id,array('product_id'=>$pid));
			//获取满足条件的商品列表
			$orderProductList = $this->review->orderProductListByUserId($user_id,$pid);

			$arr = array();
			foreach ($orderProductList as $orderProduct) {
				$arr[$orderProduct['order_id'].$orderProduct['product_id'].$orderProduct['product_sku']] = 1;
			}
			//未购买提示
			if(empty($arr)){
				$data = array("status"=>1001,"msg"=>"unbuy","data"=>"");
				$this->ajaxReturn($data);
			}

			//判断是否已评论
			foreach ($reviewList as $review) {
				if(isset($arr[$review['order_id'].$review['product_id'].$review['product_sku']])){
					unset($arr[$review['order_id'].$review['product_id'].$review['product_sku']]);
				}
			}



			//已评论提示
			if(empty($arr)){
				$data = array("status"=>1002,"msg"=>"reviewed","data"=>"");
				$this->ajaxReturn($data);
			}else{
				$data = array("status"=>200,"msg"=>"","data"=>"");
			}

			//$user_review_list = $this->review->customerReviewWithPid($user_id,$pid);

			//if(!empty($user_review_list)){
			//	$data = array("status"=>200,"msg"=>"ok","data"=>"ok");
			//}else{
			//	$data = array("status"=>1002,"msg"=>"reviewed or nobuy","data"=>null);
			//}
	
		}else{
			$data = array("status"=>1007,"msg"=>'no login',"data"=>null);
		}
	
		$this->ajaxReturn($data);
	}
	
	//收藏ajax
	public function addWish(){
		//获取参数 pid
		$pid = $this->input->get("prcId");
		
		$history_wish_list = $this->session->get("ebwishlis");
		if(!$history_wish_list){
			$add_wish_list = json_encode(array($pid));
		}else{
			$add_wish_list = json_decode($history_wish_list,true);
			$add_wish_list[] = $pid;
			$add_wish_list = array_unique($add_wish_list);
			$add_wish_list = json_encode($add_wish_list);
		}
	
		//参数错误
		if(!$pid || !is_numeric($pid)){
			$data = array("status"=>1004,'msg'=>'error pid','data'=>null);
			$this->ajaxReturn($data);
		}
	
		$data = array("status"=>0,'msg'=>'No login','data'=>null);
	
		if($this->customer->checkUserLogin()){
			$userId = $this->customer->getCurrentUserId();
			//加入用户收藏表
			$this->load->model("wishlistmodel","wishlist");
            $result = $this->wishlist->collectProducts($userId, $pid);

			if($result){
                //加入cookie
                $this->session->set("ebwishlist", $add_wish_list);
				$data = array("status"=>200,'msg'=>'ok','data'=>$result);
				$this->ajaxReturn($data);
			}else{
				$data = array("status"=>1005,'msg'=>'add wish error','data'=>null);
				$this->ajaxReturn($data);
			}
		}else{
			$this->ajaxReturn($data);
		}
	}
	
	//取消收藏ajax
//	public function cancelWish(){
//		//获取参数 pid
//		$pid = $this->input->get("prcId");
//
//		//参数错误
//		if(!$pid || !is_numeric($pid)){
//			$data = array("status"=>1004,'msg'=>'error pid','data'=>null);
//			$this->ajaxReturn($data);
//		}
//
//		$data = array("status"=>0,'msg'=>'No login','data'=>null);
//
//		if($this->customer->checkUserLogin()){
//			$userId = $this->customer->getCurrentUserId();
//			//加入用户收藏表
//			$this->load->model("wishlistmodel","wishlist");
//			$result = $this->wishlist->cancelCollect($userId, $pid);
//			if($result){
//				$data = array("status"=>200,'msg'=>'ok','data'=>$result);
//				$this->ajaxReturn($data);
//			}else{
//				$data = array("status"=>1005,'msg'=>'cancel wish error','data'=>null);
//				$this->ajaxReturn($data);
//			}
//		}else{
//			$data = array("status"=>0,'msg'=>'no login','data'=>null);
//			$this->ajaxReturn($data);
//		}
//	}
	
	//写入qna问题
	public function addqna(){
		//$question_type = $this->input->post("ques");
		$title = $this->input->post("title");
		$content = $this->input->post("content");
		$pid = $this->input->post("qnapid");

		//检查用户是否登录
		if($this->customer->checkUserLogin()){
			$user_id = $this->session->get('user_id');
			if(trim($title) && trim($content) && $pid){
				$content = htmlspecialchars($content);
				$title = htmlspecialchars($title);
				$data = array(
						'customer_id' => $user_id,
						'qna_status'=>STATUS_PENDING,
						'qna_content'=>$content,
						'qna_title' => $title,
						'product_id' => $pid,
						'qna_time_create' => date('Y-m-d H:i:s',requestTime()),
						'qna_time_lastmodified' => date('Y-m-d H:i:s',requestTime()),
				);

				if($this->database->master->insert('eb_qna', $data) ) {
					return true;
				}else{
					return false;
				}
			}
	
		}else{
			redirect(genURL("login/index"));
		}
	
	}
	
	//点赞ajax
	public function ebhelpful(){
		$data = array("status"=>200,"msg"=>"",'data'=>null);

		$helpful = $this->input->post("bool");
		$reviewId = $this->input->post("indexId");
		$productId = $this->input->post("productId");

		if(!$this->customer->checkUserLogin()){
			$data['status'] = 1007;
			$data['msg'] = 'no login';
			$this->ajaxReturn($data);
		}

		if(!in_array($helpful, array(1,2))){
			$data['status'] = 2200;
			$data['msg'] = 'helpful invalid param';
			$this->ajaxReturn($data);
		}

		if(!$reviewId || !is_numeric($reviewId)){
			$data['status'] = 2200;
			$data['msg'] = 'review invalid param';
			$this->ajaxReturn($data);
		}

		$this->load->model('reviewmodel','review');
		$this->load->model('reviewhelpfulmodel','reviewhelpful');
		$review = $this->review->getReviewById($reviewId);

		if($review['product_id'] != $productId){
			$data['status'] = 2200;
			$data['msg'] = 'product invalid param';
			$this->ajaxReturn($data);
		}

		$userId = $this->customer->getCurrentUserId();

		$helpfulInfo = $this->reviewhelpful->getHelpInfoByReviewId($reviewId,$userId);

		//处理已有点赞情况
		if(!empty($helpfulInfo)){
			$result = $this->reviewhelpful->deleteHelpful($helpfulInfo['review_helpful_id']);
			if($result){
				//减少相应数量
				$this->review->processReviewLikeUnlikeCount($reviewId,$type='decr',$helpfulInfo['review_helpful_type']);

				//如果传递的表达，和以前一样那么取消以前的操作同时减少相应数量
				if($helpfulInfo['review_helpful_type'] == $helpful){
					$data['status'] = 200;
					$data['msg'] = 'has do it!';
					$this->ajaxReturn($data);
				}else{
					$createResult = $this->reviewhelpful->createHelpful(array(
						'product_id'=>$productId,
						'review_id'=>$reviewId,
						'customer_id'=>$userId,
						'review_helpful_type'=>$helpful,
						'review_helpful_time_create'=>date('Y-m-d H:i:s',time()),
					));

					if($createResult){
						//增加相应数量
						$this->review->processReviewLikeUnlikeCount($reviewId,$type='incr',$helpful);
						$data['status'] = 200;
						$data['msg'] = 'success!';
						$this->ajaxReturn($data);
					}else{
						//新增失败
						$data['status'] = 200;
						$data['msg'] = 'fail retry!';
						$this->ajaxReturn($data);
					}
				}	
			}else{
				//删除原内容失败
				$data['status'] = 200;
				$this->ajaxReturn($data);
			}
		}else{
			//没有点赞情况 创建新的helpful 同时增加相应数量
			$createResult = $this->reviewhelpful->createHelpful(array(
				'product_id'=>$productId,
				'review_id'=>$reviewId,
				'customer_id'=>$userId,
				'review_helpful_type'=>$helpful,
				'review_helpful_time_create'=>date('Y-m-d H:i:s',time()),
			));

			if($createResult){
				//增加相应数量
				$this->review->processReviewLikeUnlikeCount($reviewId,$type='incr',$helpful);
				$data['status'] = 200;
				$data['msg'] = 'success!';
				$this->ajaxReturn($data);
			}else{
				//新增失败
				$data['status'] = 200;
				$data['msg'] = 'fail retry!';
				$this->ajaxReturn($data);
			}
		}

		$this->ajaxReturn($data);
		//header('Content-Type:application/json; charset=utf-8');
		//exit(json_encode(array('status' => 1234,'pid'=>$pid,'helpful'=>$helpful )));
	}
	
	//加入单个商品购物车
	public function addCart(){
		$this->load->model("cartmodel","cart");
		$all_params_string = $this->input->post("argumentAll");
		//371771-1-EB8347
		$return_data = array(
				'status'=>'1004',//缺少参数
				'msg'=>'miss param',
				'data'=>null,
		);
		
		if(stripos($all_params_string, "-")){
			$param_array = explode("-", $all_params_string);
			$count = count($param_array);
			if($count==3){
				$pid = is_numeric($param_array[0])?$param_array[0]:0;
				$quantity = is_numeric($param_array[1])?$param_array[1]:1;
				$product_sku = $param_array[2]?$param_array[2]:null;
				if($product_sku==null){
					$this->ajaxReturn($return_data);
				}
				$data = array(
						'product_id'=>$pid,
						'product_sku'=>$product_sku,
						'product_quantity'=>$quantity,
				);
				
				if($this->customer->checkUserLogin()){
					$data['customer_id'] = $this->session->get("user_id");
				}else{
					$data['cart_session'] = $this->session->sessionID();
				}
				
				$result = $this->cart->add($data);//加入购物车
				if($result){
					$status = 200;
					$msg = "OK";
					$data = "OK";
				}else{
					$status = '1006';
					$msg = "insert error";
					$data = "ERROR";
				}
				$return_data = array(
						'status'=>$status,//缺少参数
						'msg'=>$msg,
						'data'=>$data,
				);
			}
			
		}
		
		$this->ajaxReturn($return_data);
	}
	
	//组合商品加入购物车ajax
	public function bundleInCart(){
		$all_params_string = $this->input->post("argumentAll");
//        $all_params_string = array('371774-1-EB8364','371743-1-EB8385','371773-1-EB8363','371771-1-EB8347');//测试数据
		$return_data = array(
				'status'=>'1004',//缺少参数
				'msg'=>'miss param',
				'data'=>null,
		);
		$this->load->model("cartmodel","cart");
		if($this->customer->checkUserLogin()){
			$data['customer_id'] = $this->session->get("user_id");
		}else{
			$data['cart_session'] = $this->session->sessionID();
		}
		$insert_data = array();
		foreach ($all_params_string as $k=>$v){
			if(stripos($v, "-")){
				$param_array = explode("-", $v);
				$count = count($param_array);
				if($count==3){
					$pid = is_numeric($param_array[0])?$param_array[0]:0;
					$quantity = is_numeric($param_array[1])?$param_array[1]:1;
					$product_sku = $param_array[2]?$param_array[2]:null;
					if($product_sku==null){
						$this->ajaxReturn($return_data);
					}
					$data['product_id'] = $pid;
					$data['product_sku'] = $product_sku;
					$data['product_quantity'] = $quantity;
				}else{
					$this->ajaxReturn($return_data);
				}
				$insert_data[] = $data;
			}
		}
		//echo "<pre>";print_r($insert_data);die;
		if(!empty($insert_data)) $this->cart->batchAdd($insert_data);
		$this->ajaxReturn(array('msg'=>'OK','status'=>200,'data'=>true));
	}
	
	/**
	 * 商品的跳转
	 * @param integer $productId 商品的id
	 */
	private function _redirectProduct($productId) {
		// 将商品对应跳转的关系写入缓存
		$mem_key = 'product_rediect_pid_'.$productId;
		global $mem_expired_time;
		
		$redirectProductId = $this->memcache->get( $mem_key);
		if( $redirectProductId === false ) {
			// 读取商品对应的跳转的关系
			global $redirectProductIdArray;
	
			// 取出跳转商品id
			$redirectProductId = isset($redirectProductIdArray[$productId]) && !empty($redirectProductIdArray[$productId]) ? $redirectProductIdArray[$productId] : 0 ;
			//设置缓存
			$this->memcache->set( $mem_key, $redirectProductId,$mem_expired_time['category_redirect']);
		}
	
		// 进行跳转操作
		if( empty($redirectProductId)) {
			$redirectProductId = 0 ;
		}
	
		return (int)$redirectProductId ;
	}
	
	/**
	 * 商品访问记录（提供recent view数据）
	 */
	private function _setGoodsVisitCookie($pid){
        $newVisitedProsUnserialize = array();
		$visitedPros = $this->input->cookie('userVisitedPros');
		$visitedProsUnserialize = $visitedPros?@unserialize($visitedPros):array();
        if(is_array($visitedProsUnserialize) && !empty($visitedProsUnserialize)){
            $visitedProsUnserialize = array_merge(array($pid),$visitedProsUnserialize);
            $newVisitedProsUnserialize = array_unique($visitedProsUnserialize);
        } else
            $newVisitedProsUnserialize = array($pid);
		if(count($newVisitedProsUnserialize)>25) {$newVisitedProsUnserialize = array_slice($newVisitedProsUnserialize,0,7);}
		$this->input->set_cookie(array(
				'name' => 'userVisitedPros',
				'value' => serialize($newVisitedProsUnserialize),
				'expire' => requestTime() + 86400,
				'domain' => COMMON_DOMAIN,
				'path' => id2name('cookie_path',$GLOBALS,'/'),
				'secure' => id2name('cookie_secure',$GLOBALS,false),
		));
        return $newVisitedProsUnserialize;
	}
	
	/**
	 * 验证。(内容完整性和是否侵权)
	 */
	private function _check(&$proInfo){
		if (!$proInfo) {//If the product info is empty, show the 404 page.
			return false;
		}
	
		if($proInfo['product_status']==2){//If it is infringement product, redirect to the home page.
			redirect(home(), 'Location', 301);
		}
	
		/* url校正。*/
		$requestUri = filter_input(INPUT_SERVER,'REQUEST_URI')!=''?filter_input(INPUT_SERVER,'REQUEST_URI'):$_SERVER['REQUEST_URI'];
		list($curUri) = explode('?',ltrim($requestUri,'/'));
		if($curUri != $proInfo['product_url']) {
			redirect( HelpUrl::getUrl($proInfo['url'],false,false), 'Location', 301 );
		}
	
		/* 处理defaultlang（当前语言没有描述时url将会加上此参数并跳转，有此参数时商品信息将取英语。）,以及没有content时取英语数据。 */
		$paramDefaultlang = $this->input->get('defaultlang');//url参数：defaultlang。（带有此参数则代表产品信息取英文数据）
		if (($paramDefaultlang == 1 || !isset($proInfo['content'])) && $this->_curLanguageId != 1) {
			$proInfo = current($this->_ProductModel->getProInfoById($proInfo['id'], 1, 2)); //默认取英语的
		}
	
		return true;
	}
	
	/**
	 * 设置cookie方便ajax交互。
	 * @author Terry
	 */
	private function _initCookieForAjax(&$proInfo){
		$goodsAjaxCookie = $this->input->cookie('goodsAjax');
		$goodsAjax = $goodsAjaxCookie?unserialize($goodsAjaxCookie):array();
	
		$secondKillStart = (isset($proInfo['secondKill']) && !$proInfo['secondKill']['is_foreshow'])?TRUE:FALSE;
		$goodsAjax[$proInfo['product_id']] = array(
				'protuct_id'=>$proInfo['product_id'],
				//'sku'=>$proInfo['product_sku'],
				'secondKillStart'=>$secondKillStart , 
				'time' => requestTime() 
		);
		//删除时间过期的cookie 防止cookie
		$criticalityCount = 10 ;
		if( count( $goodsAjax ) > $criticalityCount ){
			$i = 1 ;
			$criticalityValue = count( $goodsAjax ) - $criticalityCount ;
			foreach ( $goodsAjax as $k => $v ){
				//数大于临界值
				if( $i <= $criticalityValue || empty( $v ) ){
					unset( $goodsAjax[ $k ] ) ;
				}
				//时间超过1天 情况
				if( isset( $v['time'] ) && ( (int)$v['time'] <= ( requestTime() - 86400 ) ) ){
					unset( $goodsAjax[ $k ] ) ;
				}
				$i++ ;
			}
		}
	
		$this->input->set_cookie(array(
				'name' => 'goodsAjax',
				'value' => serialize($goodsAjax),
				'expire' => requestTime() + 86400,
				'domain' => COMMON_DOMAIN,
				'path' => id2name('cookie_path',$GLOBALS,'/'),
				'secure' => id2name('cookie_secure',$GLOBALS,false),
		));
	}

}
