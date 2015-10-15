<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require dirname(__FILE__).'/common/DController.php';

class Atoz_view extends Dcontroller {
	var $show_category_list = array(
			'1' => array(
					array('multilingual_name'=>'Car DVR','url'=>'Car-DVR-c15603/'),
					array('multilingual_name'=>'CCTV Surveillance Systems','url'=>'cctv-product-c15034/'),
					array('multilingual_name'=>'CCTV Cameras','url'=>'CCTV-Cameras-c15667/'),
					array('multilingual_name'=>'Headlamps','url'=>'Headlamps-c15662/'),
					array('multilingual_name'=>'LED Ceiling Lights','url'=>'ceiling-lights-c15301/'),
					array('multilingual_name'=>'LED Candle Lights','url'=>'candle-bulbs-c15359/'),
					array('multilingual_name'=>'LED Light Bulbs','url'=>'led-light-bulbs-c15354/'),
					array('multilingual_name'=>'LED Spot Lights','url'=>'spot-bulbs-c15356/'),
					array('multilingual_name'=>'LED Lights & Lighting','url'=>'lighting-c15294/'),
					array('multilingual_name'=>'Car Lights','url'=>'led-lights-c15082/'),
					array('multilingual_name'=>'Jewelry','url'=>'jewelry-c15277/'),
					array('multilingual_name'=>'Rings','url'=>'rings-c15282/'),
					array('multilingual_name'=>'Watches','url'=>'watches-c15278/'),
					array('multilingual_name'=>"Men's Watches",'url'=>'men-s-watches-c15286/'),
					array('multilingual_name'=>'Couple Watches','url'=>'Couple-Watches-c15394/'),
					array('multilingual_name'=>'Quartz Watches','url'=>'Quartz-Watches-c15700/'),
			),
			'2' => array(
					array('multilingual_name'=>'Coche DVR','url'=>'Car-DVR-c15603/'),
					array('multilingual_name'=>'Producto CCTV','url'=>'cctv-product-c15034/'),
					array('multilingual_name'=>'Cámaras de CCTV','url'=>'CCTV-Cameras-c15667/'),
					array('multilingual_name'=>'lámpara de cabeza','url'=>'Headlamps-c15662/'),
					array('multilingual_name'=>'Luces de Techo','url'=>'ceiling-lights-c15301/'),
					array('multilingual_name'=>'Lámparas LED en forma de vela','url'=>'candle-bulbs-c15359/'),
					array('multilingual_name'=>'Bombillas LED','url'=>'led-light-bulbs-c15354/'),
					array('multilingual_name'=>'Lámparas LED de foco','url'=>'spot-bulbs-c15356/'),
					array('multilingual_name'=>'Luces LED y Gadgets','url'=>'lighting-c15294/'),
					array('multilingual_name'=>'Luces LED','url'=>'led-lights-c15082/'),
					array('multilingual_name'=>'Joyeria','url'=>'jewelry-c15277/'),
					array('multilingual_name'=>'Anillos','url'=>'rings-c15282/'),
					array('multilingual_name'=>'Relojes','url'=>'watches-c15278/'),
					array('multilingual_name'=>'Relojes para Hombre','url'=>'men-s-watches-c15286/'),
					array('multilingual_name'=>'Relojes de Pareja','url'=>'Couple-Watches-c15394/'),
					array('multilingual_name'=>'Relojes de Cuarzo','url'=>'Quartz-Watches-c15700/'),
			),
			'3' => array(
					array('multilingual_name'=>'Coche DVR','url'=>'Car-DVR-c15603/'),
					array('multilingual_name'=>'Producto CCTV','url'=>'cctv-product-c15034/'),
					array('multilingual_name'=>'Cámaras de CCTV','url'=>'CCTV-Cameras-c15667/'),
					array('multilingual_name'=>'lámpara de cabeza','url'=>'Headlamps-c15662/'),
					array('multilingual_name'=>'Luces de Techo','url'=>'ceiling-lights-c15301/'),
					array('multilingual_name'=>'Lámparas LED en forma de vela','url'=>'candle-bulbs-c15359/'),
					array('multilingual_name'=>'Bombillas LED','url'=>'led-light-bulbs-c15354/'),
					array('multilingual_name'=>'Lámparas LED de foco','url'=>'spot-bulbs-c15356/'),
					array('multilingual_name'=>'Luces LED y Gadgets','url'=>'lighting-c15294/'),
					array('multilingual_name'=>'Luces LED','url'=>'led-lights-c15082/'),
					array('multilingual_name'=>'Joyeria','url'=>'jewelry-c15277/'),
					array('multilingual_name'=>'Anillos','url'=>'rings-c15282/'),
					array('multilingual_name'=>'Relojes','url'=>'watches-c15278/'),
					array('multilingual_name'=>'Relojes para Hombre','url'=>'men-s-watches-c15286/'),
					array('multilingual_name'=>'Relojes de Pareja','url'=>'Couple-Watches-c15394/'),
					array('multilingual_name'=>'Relojes de Cuarzo','url'=>'Quartz-Watches-c15700/'),
			),
			'4' => array(
					array('multilingual_name'=>'Auto DVR','url'=>'Car-DVR-c15603/'),
					array('multilingual_name'=>'Prodotti di CCTV','url'=>'cctv-product-c15034/'),
					array('multilingual_name'=>'Telecamere CCTV','url'=>'CCTV-Cameras-c15667/'),
					array('multilingual_name'=>'Faro frontale','url'=>'Headlamps-c15662/'),
					array('multilingual_name'=>'Luci da Soffitto','url'=>'ceiling-lights-c15301/'),
					array('multilingual_name'=>'Lampadine LED a candela','url'=>'candle-bulbs-c15359/'),
					array('multilingual_name'=>'Lampadine LED','url'=>'led-light-bulbs-c15354/'),
					array('multilingual_name'=>'Riflettori LED','url'=>'spot-bulbs-c15356/'),
					array('multilingual_name'=>'Luci LED e gadget','url'=>'lighting-c15294/'),
					array('multilingual_name'=>'Luce LED','url'=>'led-lights-c15082/'),
					array('multilingual_name'=>'Gioielli','url'=>'jewelry-c15277/'),
					array('multilingual_name'=>'Anelli','url'=>'rings-c15282/'),
					array('multilingual_name'=>'Orologi','url'=>'watches-c15278/'),
					array('multilingual_name'=>'Orologi Uomo','url'=>'men-s-watches-c15286/'),
					array('multilingual_name'=>'Orologi per coppie','url'=>'Couple-Watches-c15394/'),
					array('multilingual_name'=>'Orologi al quarzo','url'=>'Quartz-Watches-c15700/'),
			),
			'5' => array(
					array('multilingual_name'=>'Voiture DVR','url'=>'Car-DVR-c15603/'),
					array('multilingual_name'=>'Produit du CCTV','url'=>'cctv-product-c15034/'),
					array('multilingual_name'=>'Caméras CCTV','url'=>'CCTV-Cameras-c15667/'),
					array('multilingual_name'=>'Lampes frontales','url'=>'Headlamps-c15662/'),
					array('multilingual_name'=>'Lumières LED du plafond','url'=>'ceiling-lights-c15301/'),
					array('multilingual_name'=>'Eclairage Chandelle LED','url'=>'candle-bulbs-c15359/'),
					array('multilingual_name'=>'Ampoules LED','url'=>'led-light-bulbs-c15354/'),
					array('multilingual_name'=>'Eclairage Spot LED','url'=>'spot-bulbs-c15356/'),
					array('multilingual_name'=>'Lumière LED & éclairage','url'=>'lighting-c15294/'),
					array('multilingual_name'=>'Lumières LED','url'=>'led-lights-c15082/'),
					array('multilingual_name'=>'Bijou','url'=>'jewelry-c15277/'),
					array('multilingual_name'=>'Anneaux','url'=>'rings-c15282/'),
					array('multilingual_name'=>'Montres','url'=>'watches-c15278/'),
					array('multilingual_name'=>'Montres pour Homme','url'=>'men-s-watches-c15286/'),
					array('multilingual_name'=>'Montres couple','url'=>'Couple-Watches-c15394/'),
					array('multilingual_name'=>'Montres Quartz','url'=>'Quartz-Watches-c15700/'),
			),
			'6' => array(
					array('multilingual_name'=>'DVR Carro','url'=>'Car-DVR-c15603/'),
					array('multilingual_name'=>'Produtos CCTV','url'=>'cctv-product-c15034/'),
					array('multilingual_name'=>'Câmeras CCTV','url'=>'CCTV-Cameras-c15667/'),
					array('multilingual_name'=>'lanterna de cabeça','url'=>'Headlamps-c15662/'),
					array('multilingual_name'=>'Luzes de teto','url'=>'ceiling-lights-c15301/'),
					array('multilingual_name'=>'Lâmpadas LED em Forma de Vela','url'=>'candle-bulbs-c15359/'),
					array('multilingual_name'=>'Lâmpadas LED','url'=>'led-light-bulbs-c15354/'),
					array('multilingual_name'=>'Lâmpadas LED de Foco','url'=>'spot-bulbs-c15356/'),
					array('multilingual_name'=>'Luzes LED & Gadgets','url'=>'lighting-c15294/'),
					array('multilingual_name'=>'Luzes LED','url'=>'led-lights-c15082/'),
					array('multilingual_name'=>'bijuterias','url'=>'jewelry-c15277/'),
					array('multilingual_name'=>'anéis','url'=>'rings-c15282/'),
					array('multilingual_name'=>'relógios','url'=>'watches-c15278/'),
					array('multilingual_name'=>'Relógios masculinos','url'=>'men-s-watches-c15286/'),
					array('multilingual_name'=>'Relógios de Casais','url'=>'Couple-Watches-c15394/'),
					array('multilingual_name'=>'Relógios em Quartzo','url'=>'Quartz-Watches-c15700/'),
			),
			'7' => array(
					array('multilingual_name'=>'Автомобильный видеорегистратор','url'=>'Car-DVR-c15603/'),
					array('multilingual_name'=>'CCTV продукты','url'=>'cctv-product-c15034/'),
					array('multilingual_name'=>'CCTV камеры','url'=>'CCTV-Cameras-c15667/'),
					array('multilingual_name'=>'Головной прожектор','url'=>'Headlamps-c15662/'),
					array('multilingual_name'=>'Потолочные LED светильники','url'=>'ceiling-lights-c15301/'),
					array('multilingual_name'=>'Светодиодные лампы-свечи','url'=>'candle-bulbs-c15359/'),
					array('multilingual_name'=>'Светодиодные лампы','url'=>'led-light-bulbs-c15354/'),
					array('multilingual_name'=>'Точечные светодиодные лампы','url'=>'spot-bulbs-c15356/'),
					array('multilingual_name'=>'LED огни & освещение','url'=>'lighting-c15294/'),
					array('multilingual_name'=>'Светодиодные фонари','url'=>'led-lights-c15082/'),
					array('multilingual_name'=>'Бижутерия','url'=>'jewelry-c15277/'),
					array('multilingual_name'=>'Кольца','url'=>'rings-c15282/'),
					array('multilingual_name'=>'Часы','url'=>'watches-c15278/'),
					array('multilingual_name'=>'Мужские часы','url'=>'men-s-watches-c15286/'),
					array('multilingual_name'=>'Часы для пары','url'=>'Couple-Watches-c15394/'),
					array('multilingual_name'=>'Кварцевые часы','url'=>'Quartz-Watches-c15700/'),
			),
	);
	public function index($key=''){
		// 		url 1普通 2md5
		// 		状态 1普通 2noindex 3删除
		$showCount = 25;
		$languageId = currentLanguageId();
		
		if(empty($key)){
			show_404();
			return;
		}
		
		//$keywords = $this->input->get('key');
		$keywords = $key;
		preg_match("/(.*)-([\da-z]{32})$/i",$keywords, $matches );
		if(isset($matches[2]) && !empty($matches[2])){
			$keywords = $matches[1];
			$word_id = $matches[2];
		}
		
		$keywords = eb_htmlspecialchars(urldecode($keywords));

		//mem_key
		global $mem_expired_time;
		$mem_key = md5("each_buyer_atoz_view".$keywords.$languageId);

		$keywords = trim( str_replace('-',' ',$keywords) );

		//seo info
		$this->_view_data['title'] = sprintf(lang('title'),$keywords);
		$this->_view_data['seo_keywords'] = sprintf(lang('keywords'),$keywords);
		$this->_view_data['description'] = sprintf(lang('description'),$keywords,$keywords);
		
		$this->load->model('producttagsmodel','producttags');
		$this->load->model('categorymodel','category');
		$this->load->model('reviewmodel','review');
		$this->load->model('producttagsdescriptionmodel','ptagsdesc');

		//$this->memcache->delete($mem_key);
		
		if(!$data = $this->memcache->get($mem_key)){
			//根据关键词 查询分类信息
			$buyResultInfo = $this->producttags->getInfoByMd5( md5( $keywords )  , $languageId );
			
			$keywordInfo['word'] = $keywords;
			if( isset( $buyResultInfo['category_id'] ) ){
				$keywordInfo['category_id'] = (int) $buyResultInfo['category_id'];
			}else{
				$keywordInfo['category_id'] = 0;
			}
			$keywordInfo['name'] = '';
			
			try {
				$this->load->library( 'xsearch/Xsearch' );
				$xs = new XS( 'product_description_' . $languageId );
				$search = $xs->search;
				//不开启模糊查询
				//$search->setFuzzy();
				$search->setCharset( 'UTF-8' );
				$keywords = substr($keywordInfo['word'], 0, 30);

				// 指定分类搜索
				$category = intval($keywordInfo['category_id']);

				if( !empty( $category ) ){
					$category = $this->category->getCategoryWithId($category);
					if(!empty($category)){
						$keywordInfo['name'] = $category['category_name'];
					}
					
					$subCats = $this->category->getSubCatIdbyCatId( $category );
					$subCatIds = extractColumn($subCats, 'category_id');
					if( !empty( $subCatIds ) && is_array( $subCatIds ) ){
						foreach ( $subCatIds as $v ){
							$sc = 'category_id:'.(int)$v['id'];
							$search->addQueryString($sc, CMD_QUERY_OP_OR);
						}
					}
				}

				$search->addQueryString($keywords . ' OR product_id:' . $keywords);
				$search->setLimit( $showCount * 2 , 0);
				$search->setFacets( 'category_id' );
				$result = $search->search();

				$count = $search->getLastCount();

				if( !$count ){
					$xs = new XS('product_description_' . $languageId );
					$search = $xs->search;
					//开启模糊查询
					$search->setFuzzy();
					$search->setCharset('UTF-8');
					$keywords = substr($keywordInfo['word'], 0, 30);
					// 指定分类搜索
					if( !empty( $subCatIds ) && is_array( $subCatIds ) ){
						foreach ( $subCatIds as $v ){
							$sc = 'category_id:'.(int)$v['id'];
							$search->addQueryString($sc, CMD_QUERY_OP_OR);
						}
					}
					$search->addQueryString($keywords . ' OR product_id:' . $keywords);
					$search->setLimit( $showCount*2 , 0);
					$search->setFacets( 'category_id' );
					$result = $search->search();
				}
			} catch ( XSException $e ) {
				$error = strval( $e );
				echo $error; die;
			}
			$goodsIdArray = extractColumn( $result , 'product_id');
			shuffle( $goodsIdArray );
			
			$tempArray = array_chunk($goodsIdArray, 25);
			
			
			$right_pid_arr = isset($tempArray[0]) ? $tempArray[0] : array();
			$left_pid_arr = isset($tempArray[1]) ? array_splice($tempArray[1], 0, 10) : array();
			
			//TODO
			
			$goodsList = $this->searchGoodsinfoWithPids($right_pid_arr);
			$list = $this->searchGoodsinfoWithPids($left_pid_arr);
			
			//评论数
			$nums = $this->review->reviewNumsByPids($right_pid_arr);
			$reviewNums = array();
			foreach ($nums as $num){
				$reviewNums[$num['product_id']] = $num['num'];
			}

			$data = array('goodsList'=>$goodsList,'list'=>$list,'reviewNums'=>$reviewNums,'keywordInfo'=>$keywordInfo);

			$this->memcache->set($mem_key, $data,$mem_expired_time['atoz_view']);

		}else{
			$goodsList = $data['goodsList'];
			$list = $data['list'];
			$reviewNums = $data['reviewNums'];
			$keywordInfo = $data['keywordInfo'];
		}
		
		$this->_view_data['featuredCategories'] = $this->show_category_list[$languageId];
		$this->_view_data['keywords'] = $keywordInfo['word'];

		$this->_view_data['goodsList'] = $goodsList;
		$this->_view_data['list'] = $list;
		$this->_view_data['reviewNums'] = $reviewNums;

		$this->_view_data['keywordsDesc'] = $this->ptagsdesc->getCikuDesc($keywordInfo['category_id'], $keywordInfo['word'], $keywordInfo['name'] , currentLanguageId() );
		
		//处理最右侧焦点图
		$imageAdsList = array();
		$this->load->model("imageadmodel","imagead");
		$location_ad_array = array(2,3);
		//从库里获取
		$image_ads = $this->imagead->getLocationWithIds($location_ad_array);
		if(isset($image_ads[2])){
			foreach ($image_ads[2] as $ad) {
				if(strtotime($ad['ad_time_start']) < time() && strtotime($ad['ad_time_end']) > time()){
					$ad['ad_content'] = json_decode($ad['ad_content'],true);
					$imageAdsList[] = $ad['ad_content'][currentLanguageId()];
				}
			}
		}
		if(isset($image_ads[3])){
			foreach ($image_ads[3] as $ad) {
				if(strtotime($ad['ad_time_start']) < time() && strtotime($ad['ad_time_end']) > time()){
					$ad['ad_content'] = json_decode($ad['ad_content'],true);
					$imageAdsList[] = $ad['ad_content'][currentLanguageId()];
				}
			}
		}
		$this->_view_data['imageAdsList'] = $imageAdsList;

		//不显示多语言链接
		$this->_view_data['noAlternateList'] = true;
		
		$this->dataLayerPushImpressions($list,'ATZ');
		
		parent::index();
	}
	
	//根据商品id（数组，批量）获取商品信息
	private function searchGoodsinfoWithPids($goodsIdArray){
		$result = array();
	
		$this->load->model("goodsmodel","ProductModel");
		//****************************************测试时
		$data = $this->ProductModel->getProductList( $goodsIdArray, $status=1,0,currentLanguageCode());
		
		$result = $this->productWithPrice($data);
	
		return $result;
	}
	
	
	
}
