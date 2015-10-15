<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Atoz extends Dcontroller {
	
	public function index($key='',$page=1){
		// 		url 1普通 2md5
		// 		状态 1普通 2noindex 3删除
		$languageId = currentLanguageId();
		
		if(empty($key)){
			show_404();
			return;
		}
		
		//TODO方法重用  焦点图
		$this->load->model("imageadmodel","imagead");
		$location_ad_array = array(1,2,3);
		//从库里获取
		$image_ads = $this->imagead->getLocationWithIds($location_ad_array);
		//对焦点图，时间进行判断
		$image_lists = $this->handleLocation($image_ads);
		$this->_view_data['image_ad'] = $image_lists;
		
// 		$page = $this->input->get('page');
		if(empty($page)){
			$page = 1;
		}

		//seo info
		$seoPageInfo = $page == 1?'':' - '.lang('page').' - '.$page;
		$this->_view_data['title'] = sprintf(lang('title'),$key).$seoPageInfo;

		$key = strtolower($key);
		
		$fileName = $languageId . '/' . $page;
		if ( $key === '0-9' ){
			$fileName = '0' . $fileName; // 01/page
		}else {
			$fileName = $key . $fileName; //a1/page
		}

		$html_file_path = ROOT_PATH . 'data/atoz/' . $fileName; // /home/www/devbranches/nyd03/sitemap_html/m1/1
		if( !file_exists( $html_file_path ) ){
 			show_404();
			//echo '404';
			return;
		}

		$atozArr = array(  );
		$totalPage  = 0 ;
		$handle = fopen( $html_file_path ,"r");
		while ( !feof ( $handle ) ){
			$buffer  = fgets( $handle, 40960 );
			$buffer = trim($buffer);
			if ( empty( $buffer ) ) {
				continue;
			}

			//处理每行数据，第一行#111/222,其他行 如果,后面有数字2 则url带有md5 获取url和name
			if ( ! ( strpos( $buffer, '#' ) !== false && strpos( $buffer, '/' ) !== false ) ){
				if(  strpos( $buffer, ',' ) === false ){
						continue;
				}
				$atoz  = explode( ',', $buffer);
				$md5 = '';
				if ( intval ( $atoz[1] ) == 2 ){
					$md5 = '-'. md5( $atoz[0] );
				}
				$url  = 'buy-' . filterName( $atoz[0]  ) . $md5 . '.html';
				$url = genURL( $url  ) ;

				$atozArr[] =  array(
					'name' => $atoz[0] ,
					'url' => $url,
				);
			}else {
				$pagesArr = explode('/', $buffer);
				$pagesArr[0] = trim( $pagesArr[0] , '#');
				$pagesArr[0] = intval( $pagesArr[0] ) ;
				if ( intval( $pagesArr[1] ) ) {
					$totalPage = intval( $pagesArr[1] );
				}
			}

		}
		fclose ($handle);

		//字母列表
		$charactersList = array(
			'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
		);

		$this->_view_data['keyWord'] = strtoupper( $key );
		$this->_view_data['atozArr'] = $atozArr;
		$this->_view_data['charactersList'] = $charactersList;

		$pagination = array();
		$pagination['href'] = genURL( strtoupper( $key ) .'_%d.html');
		$pagination['defaultHref'] = genURL( strtoupper( $key ) .'.html' );
		$pagination['currentPage'] = $page;
		$pagination['totalPage'] = $totalPage;
		$this->_view_data['pagination'] = $pagination;
		$this->_view_data['key'] = $key;

		$this->_view_data['head']['title'] = lang('product_list').':'. strtoupper($key) ." - ".lang('page')." {$page} - " . ucfirst( COMMON_DOMAIN );
		if($page < 2){
			$this->_view_data['head']['title'] = lang('product_list').':'. strtoupper($key) ." - " . ucfirst( COMMON_DOMAIN );
		}
		
		//分页处理
		$this->_basepagination(strtoupper($key).'_%d.html',$page,$totalPage,1,array(),false);

		//不显示多语言链接
		$this->_view_data['noAlternateList'] = true;
		
		parent::index();
	}
	
	/**
	 * 处理分类相应内容
	 * @see Dcontroller::_basepagination()
	 */
	public function _basepagination($baseurl, $page = 1, $count = 0, $pagesize = 8, $allParam = array(), $isShowPageParam=true) {
		$this->_view_data['pagination']['current_page'] = $page;
		$this->_view_data['pagination']['total_page'] = $count > 0 ? ceil( $count / $pagesize ) : 1;
	
		$urlTmp = trim( $baseurl ) ;
			
		if($isShowPageParam){
			$allParam['page'] = "%u";
		}
		$this->_view_data['pagination']['href'] = genURL( $urlTmp,false,$allParam);
		$this->_view_data['pagination']['default_href'] = genURL( str_replace('_%d','',$urlTmp),true,$allParam);
	}
	
	/**
	 * @desc 处理焦点图（是否过期）
	 * @param unknown $image_ads
	 * @return Ambigous <multitype:, mixed>
	 */
	private function handleLocation ($image_ads){
		$image_lists = array();
		$lauageid = currentLanguageId();
	
		if(isset($image_ads[1])){//是否存在焦点轮播图
			foreach($image_ads[1] as $kone=>$vone){
				$one_start_time = strtotime($vone['ad_time_start']);
				$one_end_time = strtotime($vone['ad_time_end']);
	
				if($one_start_time<=time() && $one_end_time>=time()){
					$content = json_decode($vone['ad_content'],true);
					$vone['lan_content'] = $content[$lauageid];
					$image_lists[1][]= $vone;
				}
			}
		}
		if(isset($image_ads[2])){//是否存在焦点图-右上
			foreach($image_ads[2] as $ktwo=>$vtwo){
				$two_start_time = strtotime($vtwo['ad_time_start']);
				$two_end_time = strtotime($vtwo['ad_time_end']);
					
				if($two_start_time<=time() && $two_end_time>=time()){
					$two_content = json_decode($vtwo['ad_content'],true);
					$vtwo['lan_content'] = $two_content[$lauageid];
					$image_lists[2][]= $vtwo;
				}
			}
		}
		if (isset($image_ads[3])){//是否存在焦点图-右下
			foreach($image_ads[3] as $kt=>$vt){
				$three_start_time = strtotime($vt['ad_time_start']);
				$three_end_time = strtotime($vt['ad_time_end']);
					
				if($three_start_time<=time() && $three_end_time>=time()){
					$tcontent = json_decode($vt['ad_content'],true);
					$vt['lan_content'] = $tcontent[$lauageid];
					$image_lists[3][]= $vt;
				}
			}
		}
	
		return $image_lists;
	}
}