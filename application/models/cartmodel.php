<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc cart model
 * @author Administrator
 *
 */
class Cartmodel extends CI_Model {

    private $cart_merge = false;

	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * @desc 单商品加入购物车
	 * @param unknown $data
	 * @return boolean
	 */
	public function add($data){
		if(empty($data)) return false;
		
		//默认情况下
		if(!isset($data['cart_type']))$data['cart_type'] = CART_TYPE_DEFALUT;
		if(!isset($data['product_quantity'])) $data['product_quantity'] = 1;
		
		if( (isset($data['customer_id']) || isset($data['cart_session']) ) && isset($data['product_sku'])){
			//检测该用户购物车是否存在该商品
			if(isset($data['customer_id']) && !empty($data['customer_id'])){//登录用户
					$exists_num = $this->checkProductCartWithCustomerid($data);
					$this->database->master->where("customer_id",$data['customer_id']);
			}elseif(isset($data['cart_session']) && empty($data['customer_id']) && !empty($data['cart_session'])){//非登录用户
					$exists_num = $this->checkProductCartWithSessionid($data);
					$this->database->master->where("cart_session",$data['cart_session']);
			}
			
			$this->database->master->where("product_sku",$data['product_sku']);
			$this->database->master->where("cart_type",$data['cart_type']);
			
			if($exists_num){
				//更新
				$data['product_quantity'] = $data['product_quantity']+$exists_num;
				$result = $this->database->master->update('eb_cart', $data);
			}else{
				$result = $this->database->master->insert('eb_cart', $data);
			}
			
			return $result;
	
		}else{
			return false;
		}
		
	}
	
	/**
	 * @desc 检查customerid用户是否存在某sku，某type的购物车中商品
	 */
	public function checkProductCartWithCustomerid($data){
		$nums = 0;
		if(empty($data) || !isset($data['customer_id']) || !isset($data['product_sku']) || !isset($data['cart_type'])) return $nums;

		$this->database->master->from("eb_cart");
		$this->database->master->where("customer_id",$data['customer_id']);
		$this->database->master->where("product_sku",$data['product_sku']);
		$this->database->master->where("cart_type",$data['cart_type']);
		$query = $this->database->master->get();
		$result = $query->result_array();
		return count($result);
	}
	
	/**
	 * @desc 检查sessionid用户是否存在某sku，某type的购物车中商品
	 * @param unknown $data
	 * @return number
	 */
	public function checkProductCartWithSessionid($data){
		$nums = 0;
		if(empty($data) || !isset($data['cart_session']) || !isset($data['product_sku']) || !isset($data['cart_type'])) return $nums;
		
		$this->database->master->from("eb_cart");
		$this->database->master->where("cart_session",$data['cart_session']);
		$this->database->master->where("product_sku",$data['product_sku']);
		$this->database->master->where("cart_type",$data['cart_type']);
		$query = $this->database->master->get();
		$result = $query->result_array();
		return count($result);
	}
	
	/**
	 * @desc 批量商品加入购物车
	 * @param unknown $data
	 * @return boolean
	 */
	public function batchAdd($data){
        //数据完整性验证
		if(empty($data)) return false;
        foreach ($data as $k=>$v){
            if( (!isset($v['customer_id']) && !isset($v['cart_session']) ) || !isset($v['product_sku']))
                return false;
        }
        //合并已存在购物车中的数据
        $this->database->master->from("eb_cart");
        if(isset($data[0]['customer_id']))
            $this->database->master->where('customer_id',$data[0]['customer_id']);
        else
            $this->database->master->where('cart_session',$data[0]['cart_session']);
        $query = $this->database->master->get();
        $cart_list = $query->result_array();
        $skus = array();
        foreach($cart_list as $v){
            $skus[$v['cart_id']] = $v['product_sku'];
            $quantitys[$v['cart_id']] = $v['product_quantity'];
        }

        //修改已有的商品数据
        if(!empty($skus)){
            foreach($data as $k=>$v){
                $cart_id = 0;
                $cart_id = array_search($v['product_sku'],$skus);
                if($cart_id){
                    $product['product_quantity'] = $quantitys[$cart_id] + $v['product_quantity'];
                    $result[] = $this->database->master->update('eb_cart',$product, array('cart_id' => $cart_id));
                    unset($data[$k]);
                }
            }
        }
        //添加没有的商品数据
        if(!empty($data))
            $result[] = $this->database->master->insert_batch('eb_cart', $data);
		if(!empty($result) && !array_search(false,$result)) {
				return true;
			}else{
				return false;
			}
	}
	
	//更新购物车(根据主键id)
	public function updateCart($data,$cart_id){
		if(empty($data) || !$cart_id || !is_numeric($cart_id)) return false;
		
		$result = $this->database->master->update('eb_cart', $data, array('cart_id' => $cart_id));
		if($result) return true;
		else return false;
		
	}
	
	//根据customer_id,cart_type,product_sku来更新
	public function updateCartWithSkuWithUserid($data){
		$result = false;
		
		if(!empty($data) && isset($data['customer_id']) && isset($data['product_sku']) && isset($data['cart_type'])){
			if(!isset($data['cart_type'])) $data['cart_type'] = CART_TYPE_DEFALUT;
			$this->database->master->where("customer_id",$data['customer_id']);
			$this->database->master->where("cart_type",$data['cart_type']);
			$this->database->master->where("product_sku",$data['product_sku']);
			
			$result = $this->database->master->update('eb_cart', $data);
		}
		
		return $result;

	}
	
	//批量更新 
	public function batchUpdateCart($data,$key){
			
	}
	
	//合并购物车
	public function mergeCart($user_id = 0, $session_id = 0){
		if(!$user_id || !$session_id || !is_numeric($user_id)) return false;
        set_cookie('cart_merge', 0);
        $this->cart_merge = false;
		//登录后的购物车
		$login_cart_list = $this->cartListWithLoginUser($user_id);
		//登录前的购物车
		$exists_data = array();
		$nologin_cart_list = $this->cartListWithSessionid($session_id);
		if(!empty($login_cart_list)){
			if(!empty($nologin_cart_list)){
				set_cookie('cart_merge', 1);//表明有合并购物车
                $this->cart_merge = true;
				foreach ($login_cart_list as $l_key=>$l_val){
					$master_sku = empty($l_val['cart_master_sku'])?0:$l_val['cart_master_sku'];
					$product_sku = $l_val['product_sku'];
					$type = $l_val['cart_type'];
					$key = $type."_".$master_sku."_".$product_sku;
					$exists_data[$key] = $l_val;
				}
				
				foreach ($nologin_cart_list as $n_key=>$n_val){
					$nologin_master_sku = empty($n_val['cart_master_sku'])?0:$n_val['cart_master_sku'];
					$nologin_product_sku = $n_val['product_sku'];
					$nologin_type = $n_val['cart_type'];
					$no_key = $nologin_type."_".$nologin_master_sku."_".$nologin_product_sku;
					
					if(array_key_exists($no_key, $exists_data)){
						//更新
						$quantity = $n_val['product_quantity']+$exists_data[$no_key]['product_quantity'];
						$exists_data[$no_key]['product_quantity'] = $quantity;
						
						//删除
						$this->delCartWithCartId($n_val['cart_id']);
						
					}else{
						//更新
                        $n_val['customer_id'] = $user_id;
                        $n_val['cart_session'] = 0;
						$exists_data[$no_key] = $n_val;
					}
					
				}
				
				return $this->database->master->update_batch('eb_cart', $exists_data, 'cart_id');
			}
		}else{
			if(!empty($nologin_cart_list)){
				//批量更新user_id
				$this->batchUpdateNologinCart($user_id, $nologin_cart_list);
			}
			
		}		
		
		return false;
	}

    public function getCartMerge(){
        return $this->cart_merge;
    }

	/**
	 * @desc 批量更新没有登录前的，不存在购物车中的商品
	 * @param unknown $user_id
	 * @param unknown $data array( array(), array(), array());二维数组
	 */
	private function batchUpdateNologinCart($user_id,$data){
		if($user_id && is_numeric($user_id) && is_array($data) && count($data)){
			foreach($data as $k=>&$v){
                $v['customer_id'] = $user_id;
                $v['cart_session'] = 0;
			}

			return $this->database->master->update_batch('eb_cart', $data, 'cart_id');
		}
	}
	
	/**
	 * @desc 根据customer_id 获取购物车列表
	 * @param number $user_id
	 * @return multitype:
	 */
	public function cartListWithLoginUser($user_id = 0){
		$result = array();
		if(!$user_id || !is_numeric($user_id)) return $result;
		
		$this->database->master->from("eb_cart");
		$this->database->master->where("customer_id",$user_id);
		$query = $this->database->master->get();
		$result = $query->result_array();
		if(!$result){
			$result = array();
		}
		return $result;
	}
	
	/**
	 * @desc 获取未登录前的用户购物车商品（根据session_id）
	 * @param unknown $session_id
	 * @return multitype:
	 */
	public function cartListWithSessionid($session_id){
		$result = array();
		if(!$session_id) return $result;
		
		$this->database->master->from("eb_cart");
		$this->database->master->where("cart_session",$session_id);
		$this->database->master->where("customer_id",0);
		$query = $this->database->master->get();
		$result = $query->result_array();
		if(!$result){
			$result = array();
		}
		return $result;
	}
	
	/**
	 * @desc 
	 * @param unknown $id 用户id或sessionid
	 * @param bool $type (默认为非登录用户)
	 */
	public function cartNumsWithUser($session_id,$type = false){
		$nums = 0;
		if(empty($session_id)) return $nums;
		if($type===false){//非登录用户购物车数量
			$data = $this->cartListWithSessionid($session_id);
		}else{
			$data = $this->cartListWithLoginUser($session_id);
		}
        $sum = 0;
        foreach($data as $value){
            $sum += intval($value['product_quantity']);
        }
		return $sum;
	}
	
	/**
	 * @desc 删除购物车中商品，根据cart_id
	 * @param unknown $cart_id
	 * @return boolean
	 */
	public function delCartWithCartId($cart_id){
		if(!$cart_id || !is_numeric($cart_id)) return false;
	
		return $this->database->master->delete('eb_cart', array('cart_id' => $cart_id));
	
	}
	
	public function delCartWithSessionId($session_id){
		if(empty($session_id)) return false;
		
		return $this->database->master->delete('eb_cart', array('cart_session' => $session_id));
	}
	
	//删除购物车单个商品(登录用户)
	public function delCart($user_id,$product_id,$type,$product_sku,$master_product_sku){
		if($user_id && is_numeric($user_id) && is_numeric($product_id) && $product_sku){
			$this->database->master->where("customer_id",$user_id);
			$this->database->master->where("product_id",$product_id);
			$this->database->master->where("cart_type",$type);
			$this->database->master->where("product_sku",$product_sku);
			$this->database->master->where("cart_master_sku",$master_product_sku);
			$this->database->master->delete("eb_cart");
			return true;
		}
		return false;
	}
	
	//非登录用户，删除购物车单个商品
	public function delCartNologin($session_id,$product_id,$type,$product_sku,$master_product_sku){
		if($session_id && is_numeric($product_id) && $product_sku){
			$this->database->master->where("cart_session",$session_id);
			$this->database->master->where("product_id",$product_id);
			$this->database->master->where("cart_type",$type);
			$this->database->master->where("product_sku",$product_sku);
			$this->database->master->where("cart_master_sku",$master_product_sku);
			$this->database->master->delete("eb_cart");
			return true;
		}
		return false;
		
	}
	
	/**
	 * @desc 批量删除某用户购物车商品
	 * @param unknown $user_id
	 * @return boolean
	 */
	public function bathDelCartWithUserId($user_id){
		if(!$user_id || !is_numeric($user_id)) return false;
		$this->database->master->where("customer_id",$user_id);
		$this->database->master->delete("eb_cart");
	}
	//判断是否有赠品？？？？
	
	//促销情形
	
	
	
}
