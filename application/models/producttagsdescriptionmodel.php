<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 评论model
 * @author Administrator
 *
 */
class producttagsdescriptionmodel extends CI_Model {
	//private $memcache;
	
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}

	public function getCikuDesc($cat_id, $keywords, $cat_name , $currentLanguageId ){

		//mem_key
		global $mem_expired_time;
		$mem_key = md5("each_buyer_product_tags_desc_".$cat_id.$keywords.$currentLanguageId);
		
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->select('product_tags_description_content');
			$this->database->slave->from('eb_product_tags_description');
			$this->database->slave->where('language_id', $currentLanguageId );

			if( !empty( $cat_id ) && (int)$cat_id > 0 ){
				$this->database->slave->where('category_id', $cat_id );
			}

			$this->database->slave->limit( 30 );
			$query = $this->database->slave->get();
			$result = $query->result_array();
			$result = extractColumn( $result , 'product_tags_description_content' );

			$this->memcache->set($mem_key, $result,$mem_expired_time['product_tags_desc']);
		}
		
		$desc = '';
		$result_count = count( $result );
		if( !empty( $result ) && is_array( $result ) && $result_count >= 1 ){
			shuffle( $result );
			$desc = trim( $result [ 0 ] ) ;
		}

		$desc = str_replace('{keytag}', '<span style="color:#dd7503;">' . $keywords . '</span>', $desc);
		$desc = str_replace('{cat_name}', '<span style="color:#dd7503;">' . $cat_name . '</span>', $desc);

		return $desc;
	}
		
}
