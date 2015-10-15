<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 价格模型
 * @author Administrator
 *
 */
class pricemodel extends CI_Model {
	/**
	 * @desc 获取单个商品的价格信息
	 * @param unknown $product_id
	 */
	public function getPriceWithSingleProduct($product_id,$language_id){
		if(!is_numeric($product_id) || !$product_id) return false;
		
		$this->load->model("discountmodel","discount");
		
	}
	
	/**
	 * @desc 根据多个商品id获取商品价格信息
	 * @param unknown $product_array
	 */
	public function getPriceWithArray($product_array,$language_id){
		
	}
	
	//折扣类
		//
	//满减类
		//减钱promote_discount+promote_range
	
	//捆绑销售类
	
	//赠品类
	
	//团购类
	
	//秒杀类
	
	//多倍积分 类
	
	//汇率
	
}