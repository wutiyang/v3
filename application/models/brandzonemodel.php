<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc 品牌brandzone model
 * @author WTY
 *
 */
class brandzonemodel extends CI_Model {
	
	public function __construct(){
		parent::__construct();
	}

	//一级品牌分类
	
	//某一级品牌类下，二级品牌类
	
	//单个一级品牌类信息
	
	//单个二级品牌类信息
	
	
	//所有一级二级分类信息
	public　function getAllList(){
		
	}
	
	//一级二级级联关系数据树（递归实现，目前只有两层）
	public function buildTree($data,$root = 0){
		$result = array();
		if(isset($data[$root])){
			$result = $data[$root];
		}
		foreach($result as $k=>$v){
			$result[$k]['children'] = $this->buildTree($data,$v['id']);
		}

		return $result;
	}
}