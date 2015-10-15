<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/19
 * Time: 13:54
 */

class brandcategory extends DController {

    public function index($bcid){
        $sort = $this->input->get('sort');
        $page = $this->input->get('page');
        if(!$page || !is_numeric($page)) $page = 1;
        //对价格范围的搜索进行处理
        $priceRangeSearch = trim($this->input->get('search_price_range'));

        $this->load->model('brandcategorymodel','BCmodel');
        //分类id，不存在时的处理（0，表中不存在，非数字的处理）
        $bcid = intval($bcid);
        $language_id = currentLanguageId();
        $brand_category_info = $this->BCmodel->getBrandCategoryByBcid($bcid,$language_id);
        //默认跳转
        if(empty($brand_category_info)){
            //该分类数据不存在或status值不为1
            redirect(genURL(''));
        }
        //url标准化方法
        $this->formatUrl($brand_category_info);
        $this->_view_data['category_info'] = $brand_category_info;
        $language_code = currentLanguageCode();
        $this->load->model('brandmodel','brandmodel');
        //获取品牌列表
        $brand_list = $this->brandmodel->getBrandListByBcid($bcid);
        $this->_view_data['brand_list'] = $brand_list;
        $brand_ids = array();
        $where = array();
        if(!empty($brand_list)){
            foreach($brand_list as $brand){
                $where['brand_id'][] = $brand['brand_id'];
                $brand_ids = $brand['brand_id'];
            }
        } else {
            //该分类没有品牌数据
            redirect(genURL(''));
        }
        //获取剔除的该品牌分类的商品列表
        $exclude_product_ids = $this->brandmodel->getExcludeProductInfo($brand_ids);
        //获取剔除后的该品牌分类的商品列表
        $this->load->model('goodsmodel','product');
        $product_list = array();
        //没有限制条件的商品为没有品牌数据的，所以避开查出，为以后如果显示该分类时准备
        if(!empty($where)){
            $product_list = $this->product->getProductListByWhere($where,STATUS_ACTIVE,$exclude_product_ids);
        }
        //对商品列表进行排序筛选处理
        $product_list = $this->formatProductList($product_list,$sort,$priceRangeSearch);
        //统计商品个数
        $product_ids_num = count($product_list);
        $pagesize = 48;
        //分页处理
        $this->_pagination( $brand_category_info['brand_category_url'], $page, $product_ids_num, $this->_view_data['all_param'] ,$pagesize);
        //分页截取数据
        $product_list = array_slice($product_list,$pagesize*($page-1),$pagesize);
        //促销价格及折扣
        $product_list = $this->productWithPrice($product_list);
        //做商品最后的数据显示处理(多语言，新品，多sku等)
        $product_list = $this->product->showProductList($product_list,$language_code);
        $this->dataLayerPushImpressions($product_list,'Brand Category Page');
        $this->_view_data['product_list'] = $product_list;
        $this->_view_data['product_num'] = $product_ids_num;
        $this->_setMateView($brand_category_info,$page);
        parent::index();
    }

    function _setMateView($brand_category_info,$page = 1){
        $page = intval($page) > 1 ? ' - '.lang('page').' '.$page:'';
        $title = str_replace('%s',$brand_category_info['brand_category_title'],lang('title'));
        $title = $title.$page;
        $keywords = $brand_category_info['brand_category_title'];
        $keywords = str_replace('%s',$keywords,lang('keywords'));
        $description = $brand_category_info['brand_category_title'];
        $description = str_replace('%s',$description,lang('title'));
        $this->_view_data['title'] = $title;
        $this->_view_data['seo_keywords'] = $keywords;
        $this->_view_data['description'] = $description;
    }

    /**
     * 对商品进行排序和筛选处理
     * @param $product_list 商品列表
     * @param $sort 排序方式
     * @param $priceRangeSearch 价格区间
     * @return array
     */
    public function formatProductList($product_list,$sort,$priceRangeSearch){
        $currency_format = $this->getCurrencyNumber();
        if(!$currency_format['currency_rate']){
            $currency_format['currency_rate'] = 1;
            $currency_format['currency_format'] = '$';
        }
        //价格筛选
//        if(strstr($priceRangeSearch,',') !== false){
        $priceRangeSearch = explode(',',$priceRangeSearch);

        $start_price = $priceRangeSearch[0]?$priceRangeSearch[0]:'';
        $end_price = isset($priceRangeSearch[1])?$priceRangeSearch[1]:'';
        $start_price= $end_price == '' ?'':round($start_price/$currency_format['currency_rate'],2, PHP_ROUND_HALF_DOWN);
        $end_price= $end_price == '' ?'':round($end_price/$currency_format['currency_rate'],2, PHP_ROUND_HALF_DOWN);
        if($start_price <> '' && $end_price <> ''){
            foreach($product_list as $id=>&$product){
                $price = $product['product_price'];
                if(($start_price <> '' && $price < $start_price) || ($end_price <> '' && $price > $end_price))
                    unset($product_list[$id]);
            }
            $all_param['search_price_range'] = $start_price.','.$end_price;
        }
//        }

        //对返回的数据"排序"处理
        switch ($sort){
            case 2://时间排序
                $product_list = array_sort($product_list,"product_time_initial_active","desc");
                $this->_view_data['sort'] = 2;
                break;
            case 3://价格排序
                //折扣前排序
                $product_list = array_sort($product_list,"product_price");
                //折扣后排序
//                $product_list = array_sort($product_list,"product_discount_price");
                $this->_view_data['sort'] = 3;
                break;
            case 4://价格降序排序
                $product_list = array_sort($product_list,"product_price","desc");
                $this->_view_data['sort'] = 4;
                break;
            default://默认排序
                $product_list = array_sort($product_list,"product_sales","desc");
                $this->_view_data['sort'] = 1;
        }
        $all_param['sort'] = $sort?$sort:1;
        $this->_view_data['all_param'] = $all_param;
        $this->_view_data['new_currency'] = $currency_format['currency_format'];
        return $product_list;
    }

    /**
     * 默认品牌分类url标准化
     * @param $info
     */
    public function formatUrl($info){
        if(!isset($info['brand_category_url'])) redirect(genURL(''));
        $url = $info['brand_category_url'];
        $uri = explode('?',$_SERVER['REQUEST_URI']);
        if(!strstr($uri[0],$url)){//商品url不规范跳转到规范商品url
            $url = $url.(isset($uri[1])?'?'.$uri[1]:'');
            header("Location: ".$url, TRUE, 301);
        }
    }

    /**
     * 分页处理
     * @param  array $category_url 分类地址
     * @param  integer $page 页码
     * @param  integer $count 商品总数
     * @param  array $basicParam URL参数
     * @author qcn qianchangnian@hofan.cn
     * @return array 分类信息
    */
    protected function _pagination($category_url, $page = 1, $count = 0, $basicParam = array(),$pagesize = 48) {
        $this->_view_data['pagination']['current_page'] = $page;
        $this->_view_data['pagination']['total_page'] = $count > 0 ? ceil( $count / $pagesize ) : 1;
        $urlTmp = trim( $category_url ) ;
        $this->_view_data['pagination']['default_href'] = genURL( $urlTmp , false, $basicParam );
        $basicParam['page'] = '%u';
        $this->_view_data['pagination']['href'] = genURL( $urlTmp,false,$basicParam);
    }
} 
