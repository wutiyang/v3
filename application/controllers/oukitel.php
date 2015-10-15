<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';

class Oukitel extends Dcontroller {

	public function index(){
		$this->load->model("goodsmodel","ProductModel");

		$list = $this->ProductModel->getProductList(array(370987,373335,374450,369130,371956));
		$list = $this->productWithPrice($list);

		$result = array();
		foreach($list as $record){
			$product_currency = trim($record['product_currency']);

			$currency1 = '';
			$currency2 = $product_currency;

			preg_match('/[A-Za-z\.]*/',$product_currency,$name);
			$name = strval(current($name));

			if($name!= ''){
				$currency1 = $name.' ';
				$currency2 = str_replace($name,'',$product_currency);
			}
			$result[$record['product_id']] = array(
				'product_price_market' => $record['product_price_market'],
				'product_discount_price' => $record['product_discount_price'],
				'product_currency1' => $currency1,
				'product_currency2' => $currency2,
				'product_url' => $record['product_url'],
			);
		}

		//render page
		$this->_view_data['product_list'] = $result;
		$this->_view_data['name'] = 'oukitel';
		$this->_view_data['title'] = lang('title');
		parent::index();
	}
}

/* End of file oukitel.php */
/* Location: ./application/controllers/default/oukitel.php */