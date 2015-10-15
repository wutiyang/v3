<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require dirname(__FILE__).'/common/DController.php';

class craw extends Dcontroller {
	//DOM句柄
	private $doc=null;
	
	public function __construct(){
		//$this->doc = new DomDocument();
	}
	
	public function index(){
		//获取url列表
		$url_list = $this->pageList();
		// 新建一个Dom实例
		$html = new simple_html_dom();
		echo "<pre>vvvv";print_r($html);die;
		//循环获取每个url内容
		foreach ($url_list as $k=>$v){
			// 从url中加载
			$html = file_get_html($v);
			//逐个分析dom节点，提取数据
				// 找到所有class=foo的元素
				$img = $html->find('.img');
				echo $img;die;
			//保存到一个csv页面
		}
			
	}
	
	public  function pageList(){
		$list = array(
			'http://www.aliexpress.com/category/5090301/mobile-phones.html?spm=2114.20011408.2.1.3hMBOO&site=glo',
		);
		return $list;
	}
	
	public function loadfile($url){
		//get_header判断
		
		//
	}
	
}

/* End of file faq.php */
/* Location: ./application/controllers/default/faq.php */