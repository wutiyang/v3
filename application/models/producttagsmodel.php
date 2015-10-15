<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 价格模型
 * @author Administrator
 *
 */
class producttagsmodel extends CI_Model {
	public function getInfoByMd5($md5,$languageId =1){
		global $language_list ;
		$result = array();
		if( !empty( $keywordMd5 )  && !empty( $language_list[ $languageId ] ) ) {
// 			$mcResult = $this->memcache->get( self::BUY_KEYWORD_INFO_MEM_KEY , array( $keywordMd5 , $languageId ) );
			//get db
// 			if( $mcResult === FALSE ){
				$this->database->slave->from( 'eb_product_tags_' . $languageId );
				$this->database->slave->where( 'product_tags_id' , $keywordMd5 );
				$this->database->slave->limit( 1 );
				$list = $this->database->slave->get();
				// 获取不到数据 返回FALSE
				if( $list !=FALSE ){
					$list = $list->result_array() ;
					if( isset( $list[0] ) ){
// 						$this->memcache->set( self::BUY_KEYWORD_INFO_MEM_KEY , $list[0] , array( $keywordMd5 , $languageId ) );
						$result = $list[0] ;
					}
				}
// 			}else {
// 				$result = $mcResult ;
// 			}
		
			//过滤状态被删除的信息
			if( isset( $result[ 'product_tags_status' ] ) && $result[ 'product_tags_status' ] == 3 ){
				$result = array();
			}
		}
		
		return $result ;
	}
}