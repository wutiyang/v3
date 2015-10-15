<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';
/**
 * @desc 购物车
 * @author Wty
 *
 */
class Cart extends Dcontroller {
	private $items_count = 0;
	
	public function index(){
		$this->load->model("wishlistmodel","wishlist");
		
		$this->_view_data['login_type'] = 0;//未登录
		$this->_view_data['wishlist'] = array();
		//购物车列表
		$cart_data = $this->ajaxCartData();
		
		//GA统计(google_tag_params)
		$this->_getGoogleTagParams($cart_data);
		//GA统计-datalayers
		$this->_getGaDatalayers($cart_data);
		
		//*****************计算返回给前端显示的总价，总节省价 start*************
		$items_count = count($cart_data);
		$reword = 0;
		$currency = "$";
		$base_total_price = 0;

        $language_code = currentLanguageCode();
		global $warehouse_range_array;
        //敏感物品
        $this->load->model("goodsmodel","product");
        $sensitive_range_array = $this->product->getSepecialShipProductIds();
		foreach ($cart_data as $cart_key=>&$cart_val){
            //添加该商品是否下架 为真 下架
            $cart_val['sell_out'] = $this->product->checkProductStatusAndSku($cart_val['product_id'],$cart_val['product_sku'],$language_code)?false:true;
            $cart_val['product_warehouse'] = isset($cart_val['product_warehouse'])?$cart_val['product_warehouse']:'';
			$warehouse_name = strtolower($cart_val['product_warehouse']);
            if(in_array($cart_val['product_id'], $sensitive_range_array)){
                $cart_val['product_warehouse'] = '';
                $cart_val['product_warehouse_class'] = 'distri-icon';
            }elseif(array_key_exists($warehouse_name, $warehouse_range_array)){
                $cart_val['product_warehouse'] = lang('shipping_time_ships_only')." ".$warehouse_range_array[$warehouse_name];
                $cart_val['product_warehouse_class'] = $warehouse_name;
            }else{
				$cart_val['product_warehouse'] = '';
				$cart_val['product_warehouse_class'] = '';
			}
		}
		//*************计算返回给前端显示的总价，总节省价 end*************
		
		//***************登录用户 收藏列表 start******************
        $islogin = $this->customer->checkUserLogin();
		if($islogin){
			$this->_view_data['login_type'] = 1;//已登录
			//读取收藏列表商品
			$user_id = $this->session->get("user_id");
			$wishlist_data = $this->wishlist->getUserCollecteList($user_id);
			$wishlist_data = $this->productInfoWithCartinfo($wishlist_data);
			
			//收藏列表商品属性
			$product_sku_all = array();
			foreach ($wishlist_data as $wish_key=>$wish_val){
				$product_info = $wish_val['product_info'];
				$product_sku_all[] = $this->productAllAttr($product_info);
			}
			$this->_view_data['wishlist'] = $product_sku_all;
		}
		//***************登录用户 收藏列表 end******************

		$this->_view_data['product_currency'] = $this->currency;
		$this->_view_data['count'] = $this->items_count;
		if($currency_format = $this->getCurrencyNumber()){
			$currency = $currency_format['currency_format'];
		}
		$this->_view_data['new_currency'] = $currency;
        //添加部分list参数对应表
        $product_list = $this->getProductList($cart_data);
        $this->dataLayerPushImpressions($product_list,'Shopping Cart');
        $this->_view_data['islogin'] = $islogin;
        $redirect_url = $this->input->get("url");
        if($redirect_url != ''){
            $sell_out = false;
            foreach($cart_data as $v){
                if(isset($v['sell_out']) && $v['sell_out']){$sell_out = true;break;}
            }
            if(!$sell_out){
                redirect(genURL($redirect_url));
            }

        }
        //合并购物车cookie
        $this->getCartMerge();
        $total_price = 0;
        $save_price = 0;
        $cart_group = $this->getRangePlanGroup($cart_data);
        if(is_array($cart_group) && !empty($cart_group)){
            foreach($cart_group as &$cart_itme){
                foreach($cart_itme['product_list'] as &$product){
                    $total_price +=  round($product['view_new_product_sum'],2);
                    $save_price += round($product['product_quantity']*($product['product_info']['product_price_market']) - $product['view_new_product_sum'],2);
                }
            }
        }
        $rate = $this->getCustomerLevel();
        $rate_price = round($total_price*0.01*$rate,2);
        $this->_view_data['total_price'] = $total_price;
        $this->_view_data['save_price'] = $save_price;
        $this->_view_data['reword'] = $rate_price;
        //echo '<pre>';print_r($cart_group);exit;
        $this->_view_data['cart_group'] = $cart_group;
        parent::index();
	}

	//合并购物车cookie
	private function getCartMerge(){
		$cart_merge = get_cookie('cart_merge');
		$this->_view_data['cart_merge'] = $cart_merge;
		$cart_merge = set_cookie('cart_merge','');//清除
	}
	
    private function getProductList($cart_list){
        if ( empty ( $cart_list  ) ){
            $products = '';
            return $products;
        }
        foreach($cart_list as $cart_product){
            $products[] = $cart_product['product_info'];
        }
        return $products;
    }

	private function _getGoogleTagParams($cart_data){
		$this->ecomm_prodid = array();
		$this->ecomm_pagetype = 'cart';
		$this->ecomm_pname = array();
		$this->ecomm_pcat = array();
		$this->ecomm_pvalue = 0;
		foreach ($cart_data as $cart_key=>&$cart_val){
			$this->ecomm_pvalue += $cart_val['product_quantity']*$cart_val['product_info']['product_basediscount_price'];
			array_push($this->ecomm_prodid, 'eachbuyer_usd_'.$cart_val['product_id'].'_us');
			array_push($this->ecomm_pname, $cart_val['product_info']['product_name']);
            if(isset($cart_val['category_name']))
			    array_push($this->ecomm_pcat, $cart_val['category_name']);
		}
	}
	
	private function _getGaDatalayers($cart_data){
		$this->ga_dataLayer = array();
		$k = 1;
		$productIds = array();
		$prices = array();
		$quantities = array();
		//新增ga代码
		$this->ga_dataLayer['curency'] = $this->currency;
		//新增ga代码
		$origin_price = array();
		
		foreach ($cart_data as $key=>$val){
			if($k<4){
				$product_name = 'productId'.$k;
				$price_name = 'price'.$k;
				$quantity = 'quantity'.$k;
				$this->ga_dataLayer[$product_name] = $val['product_id'];
				$this->ga_dataLayer[$price_name] = $val['product_info']['product_basediscount_price'];
				$this->ga_dataLayer[$quantity] = $val['product_quantity'];
				array_push($productIds,$val['product_id']);
				array_push($prices, $val['product_info']['product_basediscount_price']);
				array_push($quantities, $val['product_quantity']);
			}
			//新增ga代码
			array_push($origin_price, $val['product_info']['product_discount_price']);
			
			$k++;
		}
		$this->ga_dataLayer['productIds'] = implode('|', $productIds);
		$this->ga_dataLayer['prices'] = implode('|', $prices);
		$this->ga_dataLayer['quantities'] = implode('|', $quantities);
		//新增ga代码
		$this->ga_dataLayer['prices_origin'] = implode('|', $origin_price);
		$this->_view_data['ga_dataLayer'] = json_encode($this->ga_dataLayer);
	}
	
	//获取商品sku属性信息（根据商品信息中的id）
	private function productAllAttr($product_info){
		$sold_out = false;//是否下架
        $single_sku_type = 0;
		$this->load->model("attributeproductmodel","sku");
        $pid = 0;
        if(isset($product_info['product_id']))
		    $pid = $product_info['product_id'];
		if(isset($product_info['product_type']) && $product_info['product_type']==1){//单sku
            $single_sku_value = '';
			$single_sku_type = 1;
			$only_sku_product = $this->sku->productAllBaseSku($pid);
			if(empty($only_sku_product)) $sold_out = true; //sku不存在，则表明下架
			else $single_sku_value = $only_sku_product[0]['product_sku_code'];
				
			$product_info['single_sku_value'] = $single_sku_value;//单sku值
		}elseif(isset($product_info['product_type']) && $product_info['product_type']==2){//多sku，前端展示选项及json处理
			$single_sku_type = 2;
			//商品属性及属性组,及对应仓库国家
			$attr_and_attrvalue = array();
			$sku_and_attrvalue = json_encode(array());
			$product_main_attr = $this->productMainAttr($pid);
				
			if($product_main_attr && !empty($product_main_attr)){
				$attr_and_attrvalue = $product_main_attr['attr_data'];
				//对每个sku的价格进行判断是否会加价
				$product_discount_number = $product_info['product_discount_number'];//折扣,50%
				$product_price_market = $product_info['product_price_market'];//原价,单位为美元
				$product_price = $product_info['product_price'];//现价，单位为美元
				//前端js使用的json数据
				$sku_and_attrvalue = json_encode($this->formatAttrAndBundlePrice($product_main_attr['sku_data'],$product_price_market,$product_price,$product_discount_number));
			}else{
				$sold_out = true;
			}
				
			$product_info['attr_and_attrvalue'] = $attr_and_attrvalue;
			$product_info['sku_and_attrvalue'] = $sku_and_attrvalue;
		} else {
            $sold_out = true;
        }
		
		$product_info['single_sku_type'] = $single_sku_type;
		$product_info['sold_out'] = $sold_out;
		
		return $product_info;
	}
	
	/**
	 * @desc 商品sku属性信息
	 * @param unknown $productId
	 * @return multitype:|unknown
	 */
	private function productMainAttr($productId){
		$result = array();
		if(!$productId || !is_numeric($productId)) return $result;
		//$language_id = currentLanguageId();
		//获取sku基本信息
		$this->load->model("attributeproductmodel","attribute");
	
		//获取sku的属性及属性组值信息，多语言
		//$productId = 358104;//TEST***********************************
		$sku_connect_attr_info = $this->attribute->productAttrWithSku($productId);

		if(!empty($sku_connect_attr_info)){
			//所有的属性，对应的属性组值 ，数据
			$all_attr_data = $this->formatAttrWithAttrgroup($sku_connect_attr_info);
			$result['attr_data'] = $all_attr_data;
				
			//sku对应的属性组值
			//sku对应的，原价格，最新sku价格，仓库
			$sku_base_info = $this->attribute->productAllBaseSku($productId);

			$sku_attr_value = array();
			global $warehouse_range_array;
			foreach ($sku_connect_attr_info as $k=>$v){
                if(!isset($sku_base_info[0])) continue;
				$sku_value = $v['product_sku'];
				$sku_attr_value[$sku_value]['product_sku_code'] = $sku_base_info[0]['product_sku_code'];
				$warehouse_name = strtolower($sku_base_info[0]['product_sku_warehouse']);
				if(array_key_exists($warehouse_name, $warehouse_range_array)){
					$sku_attr_value[$sku_value]['product_sku_warehouse'] = $warehouse_range_array[$warehouse_name];
					$sku_attr_value[$sku_value]['product_sku_warehouse_class'] = $warehouse_name;
				}else{
					$sku_attr_value[$sku_value]['product_sku_warehouse'] = '';
					$sku_attr_value[$sku_value]['product_sku_warehouse_class'] = '';
				}
				
				
				$sku_attr_value[$sku_value]['product_sku_price_market'] = $sku_base_info[0]['product_sku_price_market'];
				$sku_attr_value[$sku_value]['product_sku_price'] = $sku_base_info[0]['product_sku_price'];
	
				$sku_attr_value[$sku_value]['attr'][] = $v['complexattr_value_id'];
			}
				
			$result['sku_data'] = $sku_attr_value;
		}
	
		return $result;
	}
	
	//根据商品sku信息，获取该sku的属性及属性组信息
	private function formatAttrWithAttrgroup($sku_data){
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
	
		return $result;
	}
	
	//改商品id的sku是否影响价格，加价
	private function formatAttrAndBundlePrice($sku_data,$product_price_market,$product_price,$product_discount_number){
		if(empty($sku_data)) return array();
		$currency_format = $this->getCurrencyNumber();
		$real_discount = (100-$product_discount_number)/100;
	
		foreach ($sku_data as $main_key=>&$main_val){
			$main_val['product_currency'] = "$";
				
			//该sku对价格有调整
			if((int) $main_val['product_sku_price']!=0 || (int) $main_val['product_sku_price_market']!=0) {
				$product_price_market = $product_price_market+$main_val['product_sku_price_market'];
				$product_price = $product_price+$main_val['product_sku_price'];
			}
				
			//再打折
			$new_product_price_market = $product_price_market*$real_discount;
			$new_product_price_market = round($new_product_price_market,2);
			$main_val['elidePrice'] = $new_product_price_market;
			//
			$new_product_price = $product_price*$real_discount;
			$new_product_price = round($new_product_price,2);
			$main_val['salePrice'] = $new_product_price;
				
			//汇率
			if($currency_format){
				$main_val['product_currency'] = $currency_format['currency_format'];
				$main_val['salePrice'] = round($main_val['salePrice']*$currency_format['currency_rate'],2);
				$main_val['elidePrice'] = round($main_val['elidePrice']*$currency_format['currency_rate'],2);
			}
	
			unset($main_val['product_sku_price_market'],$main_val['product_sku_price']);
		}
	
		return $sku_data;
	}
	
	/**
	 * @desc 根据商品id获取该商品信息及价格
	 * @param unknown $cart_data(二维数组)
	 */
	private function productInfoWithCartinfo($cart_data,$need_extend_price = 1){
		$result = array();
		if(empty($cart_data)) return $result;

		$this->load->model("goodsmodel","product");
		$language_code = currentLanguageCode();
		
		foreach ($cart_data as $k=>&$v){
			$product_id = $v['product_id'];
			$product_sku = isset($v['product_sku'])?$v['product_sku']:null;
			//商品基本信息
			$product_info = $this->product->getinfoNostatus($product_id,$language_code);
            $sku_info = array();
            if($product_sku){
                //获取商品仓库信息
                $sku_info = $this->skuinfoWithSku($product_sku,$product_id);
                if(!empty($sku_info)) $v['product_warehouse'] = $sku_info['product_sku_warehouse'];
            } else {
                //下架商品状态标识
                $v['sell_out'] = $this->product->checkProductStatusAndSku($product_id,$product_sku,$language_code)?false:true;
            }
			if($need_extend_price)$product_info = $this->singleProductWithPrice($product_info,$sku_info);//扩展价格
			$v['product_info'] = $product_info;
			 
		}
		return $cart_data;
	}
	

	//更新购物车中商品(登录用户，非登录用户)---数量
	public function updatequantity(){
        $product_quantity = 0;
		$all_params = $this->input->get("parameter");
		if(stripos($all_params, "-")){
			$params_array = explode("-", $all_params);
			$cart_id = isset($params_array[2])?$params_array[2]:0;
			$product_id = isset($params_array[0])?$params_array[0]:0;
			$product_sku = isset($params_array[1])?$params_array[1]:"";
			$product_quantity = isset($params_array[3])?$params_array[3]:1;
		}
        //返回给前端js的json数据
        $json_return_data = $this->ajaxFormatCartData();

        if(empty($all_params) || !$all_params){
            $return_data['status'] = 1010;//参数有误
            $return_data['msg'] = "Error PARAMS";
            $return_data['data'] = $json_return_data;
        }

        if(!$product_quantity || !is_numeric($product_quantity)){
            $return_data['status'] = 1008;//表明更新数量
            $return_data['msg'] = "Error QUANTITY";
            $return_data['data'] = $json_return_data;

            $this->ajaxReturn($return_data);
        }

        $this->load->model("cartmodel","cart");
        //判断是否登录
        if($this->customer->checkUserLogin()){
            $user_id = $this->customer->getCurrentUserId();
            $data['customer_id'] = $user_id;
        }else{
            $session_id =$this->session->sessionID();
            $data['cart_session'] = $session_id;
        }
        $data['product_quantity'] = $product_quantity;
        if($cart_id && is_numeric($cart_id)){
            //通过cart_id更新
            $result = $this->cart->updateCart($data,$cart_id);
        }else{
            //通过pid，sku，userid更新
            $data['product_sku'] = $product_sku;
            $data['product_id'] = $product_id;
            $result = $this->cart->updateCartWithSkuWithUserid($data);
        }

        if($result){
				$json_return_data = $this->ajaxFormatCartData();//重新请求一次
				$return_data['status'] = 200;
				$return_data['msg'] = "OK";
				$return_data['data'] = $json_return_data;
					
			}else{
				$return_data['status'] = 1009;//
				$return_data['msg'] = "update error";
				$return_data['data'] = $json_return_data;
			}
		
		$this->ajaxReturn($return_data);
	}
	
	//ajax删除购物车中某商品(登录用户，非登录用户)
	public function delcartproduct(){
		$all_params = $this->input->post("pid");
		if(stripos($all_params, "-")){
			$params_array = explode("-", $all_params);
			$cart_id = isset($params_array[2])?$params_array[2]:0;
			$product_id = isset($params_array[0])?$params_array[0]:0;
			$product_sku = isset($params_array[1])?$params_array[1]:"";
		}
        if($cart_id && is_numeric($cart_id)){
            $this->load->model('cartmodel','cart');
            $result = $this->cart->delCartWithCartId($cart_id);

            //返回给前端js的json数据
            $json_return_data = $this->ajaxFormatCartData();

            if($result){
                $return_data['status'] = 200;
                $return_data['msg'] = "OK";
                $return_data['data'] = $json_return_data;

            }else{
                $return_data['status'] = 1009;//
                $return_data['msg'] = "delete error";
                $return_data['data'] = $json_return_data;
            }
        }
		$this->ajaxReturn($return_data);
	}
	
	//ajax移除商品到wishlist(并从购物车中删除)
	public function carttowishlist(){
		$all_params = $this->input->post("pid");
		if(stripos($all_params, "-")){
			$params_array = explode("-", $all_params);
			$cart_id = isset($params_array[2])?$params_array[2]:0;
			$product_id = isset($params_array[0])?$params_array[0]:0;
			$product_sku = isset($params_array[1])?$params_array[1]:"";
		}
		//判断是否登录（只有的登录用户才有该操作）
		if($this->customer->checkUserLogin()){
			
			if(!$product_id || !is_numeric($product_id) || !$cart_id || !is_numeric($cart_id)){
				$json_return_data = $this->ajaxFormatCartData();
				
				$return_data['status'] = 1010;//参数有误
				$return_data['msg'] = "Error PARAMS";
				$return_data['data'] = $json_return_data;
					
				$this->ajaxReturn($return_data);
			}
			
			$user_id = $this->customer->getCurrentUserId();
			
			$this->database->master->trans_begin();//开启事务
			
			//加入wishlist
			$this->load->model("wishlistmodel","wishlist");
			$this->load->model("cartmodel","cart");
			
			$wishlist_result = $this->wishlist->checkUserGoodsCollected( $product_id, $user_id  );
			if(!$wishlist_result){
				//没有收藏
				$this->wishlist->collectProducts($user_id,$product_id);
			}else{
				//判断收藏的状态
				if($wishlist_result[0]['wishlist_status']!=STATUS_ACTIVE){
					//改变状态
					$this->wishlist->updateStatusWithCid($wishlist_result[0]['wishlist_id']);	
				}
				
			} 
			
			//删除购物车中该商品
			$this->cart->delCartWithCartId($cart_id);
			$result = false;
			if ($this->database->master->trans_status() === FALSE)
			{
				$this->database->master->trans_rollback();
			}
			else
			{
				$this->database->master->trans_commit();
				$result = true;
			}	
				
			//重新获取购物车商品数据，返回给前端
			$json_return_data = $this->ajaxFormatCartData();
			if($result){
				$return_data['status'] = 200;
				$return_data['msg'] = "OK";
				$return_data['data'] = $json_return_data;
					
			}else{
				$return_data['status'] = 1009;//
				$return_data['msg'] = "delete error";
				$return_data['data'] = $json_return_data;
			}
			
		}else{
			$return_data['status'] = 1007;//表明没有登录
			$return_data['msg'] = "NO LOGIN";
			$return_data['data'] = null;
		}
		
		$this->ajaxReturn($return_data);
	}
	
	//wishlist中商品添加到购物车，并从wishlist中删除(AJAX)
	public function wishlisttocart(){
		$all_params = $this->input->post("argumentAll");
		
		if(stripos($all_params, "-")){
			$params_array = explode("-", $all_params);
			$product_id = isset($params_array[0])?$params_array[0]:0;
			$product_sku = isset($params_array[2])?$params_array[2]:"";
			$product_quantity = isset($params_array[1])?$params_array[1]:1;
		}else{
			$status = 1010;//参数有误
			$msg = "Error PARAMS";
			$data = null;
			$this->newAjaxReturn($status,$data,$msg);
		}
		
		//只有登录用户
		if($this->customer->checkUserLogin()){
			if(!$product_sku || empty($product_sku) || !$product_id || !is_numeric($product_id)){
				//重新获取购物车商品数据，返回给前端
				$json_return_data = $this->ajaxFormatCartData();
				
				$status = 1010;//参数有误
				$msg = "Error PARAMS";
				$data = $json_return_data;
				$this->newAjaxReturn($status,$data,$msg);
			}
			
			$this->load->model("wishlistmodel","wishlist");
			$this->load->model("cartmodel","cart");
			$user_id = $this->customer->getCurrentUserId();
			$this->database->master->trans_begin();//开启事务
			//添加商品到购物车
			$data['customer_id'] = $user_id;
			$data['product_id'] = $product_id;
			$data['product_sku'] = $product_sku;
			$this->cart->add($data);
			//从wishlist中删除
			$this->wishlist->cancelCollect( $product_id, $user_id );
			$result = false;
			if ($this->database->master->trans_status() === FALSE)
			{
				$this->database->master->trans_rollback();
			}
			else
			{
				$this->database->master->trans_commit();
				$result = true;
			}
			
			$json_return_data = $this->ajaxFormatCartData();
			
			if($result){
				$return_data['status'] = 200;
				$return_data['msg'] = "OK";
				$return_data['data'] = $json_return_data;
					
			}else{
				$return_data['status'] = 1009;//
				$return_data['msg'] = "add cart error";
				$return_data['data'] = $json_return_data;
			}
		}else{
			//$session_id =$this->session->sessionID();
			$return_data['status'] = 1007;//表明没有登录
			$return_data['msg'] = "NO LOGIN";
			$return_data['data'] = null;
				
		}
		
		$this->ajaxReturn($return_data);
	}
	
	/**
	 * @desc 内部调用购物车商品数据
	 * @return Ambigous <multitype:, unknown>
	 */
	private function ajaxCartData(){
		$this->load->model("cartmodel","cart");
		if($this->customer->checkUserLogin()){
			$user_id = $this->session->get("user_id");
			$cart_list = $this->cart->cartListWithLoginUser($user_id);
			$cart_data = $this->productInfoWithCartinfo($cart_list);//扩展价格
			
		}else{
			$session_id = $this->session->sessionID();
			$cart_list = $this->cart->cartListWithSessionid($session_id);
			$cart_data = $this->productInfoWithCartinfo($cart_list);//扩展价格
			
		}
		
		//获取sku对应属性信息
		$this->load->model('categorymodel','category');
		$this->items_count = 0;
		foreach ($cart_data as $key=>&$val){
			$this->items_count += $val['product_quantity'];
			$sku = $val['product_sku'];
			$attr_value_info = $this->attrAndValueWithSku($sku);
			if(!empty($attr_value_info)){
				foreach ($attr_value_info as $attr_key=>$attr_val){
					$val['attr'][$attr_key]['attr_name'] = $attr_val['attr_name'];
					$val['attr'][$attr_key]['attr_value_name'] = $attr_val['attr_value_name'];
				}
			}
			//对应分类信息
			$category_info = $this->category->getCategoryWithId($val['product_info']['category_id']);
			if(!empty($category_info)) $val['category_name'] = $category_info['category_name'];
		}
		return $cart_data;
	}
	
	/**
	 * @desc ajax返回前端json购物车数据格式
	 */
	private function ajaxFormatCartData(){
		$return_data = array();
		$cart_data = $this->ajaxCartData();
        $language_code = currentLanguageCode();
		
		$subtotal = 0;
		$saving = 0;
		$currency = "$";

		global $warehouse_range_array;
        $cart_group = $this->getRangePlanGroup($cart_data);
        $product_group = array();
        foreach($cart_group as $group_key=>$cart_item){

            $product_group[$group_key]['view_title'] = $cart_item['view_title'];
            $product_group[$group_key]['view_short_title'] = $cart_item['view_short_title'];
            $product_group[$group_key]['discount_url'] = $cart_item['discount_url'];
            $product_group[$group_key]['is_off'] = $cart_item['is_off'];
            $list = array();
            foreach ($cart_item['product_list'] as $k=>$v){
                //添加该商品是否下架 为真 下架
                $list[$k]['sell_out'] = $this->product->checkProductStatusAndSku($v['product_id'],$v['product_sku'],$language_code)?false:lang('sorry_the_product_sold_out');;
                $currency = $v['product_info']['product_currency'];
                $list[$k]['pid'] = $v['product_id'];
                $list[$k]['sku'] = $v['product_sku'];
                $list[$k]['cid'] = $v["cart_id"];
                $list[$k]['pic'] = PRODUCT_IMAGES_URL.$v["product_info"]['product_image'];
                $list[$k]['src'] = genURL($v['product_info']['product_url']);
                $list[$k]['title'] = $v['product_info']['product_description_name'];
                $list[$k]['attr'] = isset($v['attr'])?$v['attr']:array();

                $cart_val['product_warehouse'] = isset($cart_val['product_warehouse'])?$cart_val['product_warehouse']:'';
                $warehouse_name  = isset($v['product_warehouse'])?strtolower($v['product_warehouse']):'';
                if(array_key_exists($warehouse_name, $warehouse_range_array)){
                    $list[$k]['warehouse'] = "Ships only to ".$warehouse_range_array[$warehouse_name];
                    $list[$k]['warehouse_class'] = $warehouse_name;
                }else{
                    $list[$k]['warehouse'] = "";
                    $list[$k]['warehouse_class'] = "";
                }

                $list[$k]['originalPrice'] = $currency.number_format($v['product_info']['product_price_market'],2,'.',',');
                $list[$k]['salePrice'] = $currency.number_format($v['product_info']['product_discount_price'],2,'.',',');
                $list[$k]['savePrice'] = $currency.number_format($v['product_info']['product_price_market']-$v['product_info']['product_discount_price'],2,'.',',');
                $list[$k]['quantity'] = $v["product_quantity"];
                $list[$k]['off_price'] = $v['view_product_off'] > 0 ? lang('save').' '.$v['product_info']['product_currency'].number_format($v['view_product_off'],2,'.',',') : '';
                $total_price = round($v['view_new_product_sum'],2);
                //单个商品的总金额
                $list[$k]['price'] = $currency.number_format($total_price,2,'.',',');
                $subtotal +=$total_price;
                $saving += round($v['product_info']['product_price_market']*$v["product_quantity"]-$v['view_new_product_sum'],2);
            }
            $product_group[$group_key]['product_list'] = $list;
        }

		
		$return_data['list'] = $product_group;
		$return_data['count'] = $this->items_count;
		$return_data['subtotal'] = $currency.number_format($subtotal,2,'.',',');
		$return_data['savings'] = $currency.number_format($saving,2,'.',',');
		$rate = $this->getCustomerLevel();
		$return_data['reward'] = $currency.number_format($subtotal*0.01*$rate,2,'.',',');
		//echo json_encode($return_data);die;
		return json_encode($return_data);
	}
	
	/**
	 * @desc 获取用户rate级别
	 * @return unknown
	 */
	public function getCustomerLevel(){
		$rate = 1;
		if($this->customer->checkUserLogin()){
			$customer_id = $this->customer->getCurrentUserId();
			return $rate = $this->customer->getRewordsRate($customer_id);
		}else{
			return $rate;
		}
		
	}
	
}
