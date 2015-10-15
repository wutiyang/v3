<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Shipping_special_note extends Dcontroller {

        public function index(){
            $this->load->model("goodsmodel","product");
            $show_pids = $this->product->getSepecialShipProductIds();
            $show_product = $this->product->getProductList($show_pids,0,0,currentLanguageCode());
            $show_product = $this->productWithPrice($show_product);
            $this->_view_data['products'] = $show_product;
            $this->_view_data['product_currency'] = $this->currency;
            parent::index();
        }
} 
