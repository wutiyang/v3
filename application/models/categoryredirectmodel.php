<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * 分类页面跳转
 * 功能说明：
 * 1.	跳转支持可设置301、302跳转，默认301跳转。
 * 2.	跳转截止时间设置到日期，如设置截止日期到 2014-11-19，则截止时间为 2014-11-19 23:59:59。
 * 		跳转截止时间提供选日期选择器，跳转截止时间默认为三个月（不支持不限制，与UE不同）。
 * 3.	列表排序，优先显示还在生效的跳转规则，按照设置时间由近及远排序。列表中可选择显示已过期或未过期的的规则，默认全部。
 * 		如选择已过期或未过期，按照选择的条件根据设置时间由近及远排序。
 * 4.	当设置的跳转时间超过了设置的跳转截止时间则跳转自动失效。设置好后跳转即可生效（从MC推送到前台后）。
 * 5.	已设置的跳转规则，点击操作的放大镜（icon样式不做特殊要求），弹出新建/修改跳转规则的弹层，可修改规则，修改后设置时间更新为修改的时间。
 * 6.	目标跳转分类ID设置为0，则表示跳转到首页。
 *
 * @author TIM
 */
class categoryredirectmodel extends CI_Model {

	//表名
	private $_tableName = 'eb_category_redirect';
	//private $memcache;

	/*
	 * 初始化
	 */
	public function __construct(){
		parent::__construct();
		//$this->memcache = new CI_Memcache();
	}

	/**
	 * 获取分类指定跳转信息
	 * @param  integer $categoryId 分类id
	 */
	public function getRedirectInfoByCategoryId($categoryId = 0) {
		// 判断分类ID为空的时候直接返回空
		if(empty($categoryId)) { return array(); }

		// 设置memcache key
		$mem_key = md5("redirect_category_info_".$categoryId);
		global $mem_expired_time;
		
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$dateTime = date('Y-m-d H:i:s', requestTime());
			
			$this->database->slave->from($this->_tableName);
			$this->database->slave->where('from_category_id', $categoryId);
			$this->database->slave->where('category_redirect_status', 1);
            $this->database->slave->where('category_redirect_time_end >', $dateTime);
            $this->database->slave->where('category_redirect_time_start <', $dateTime);
			$this->database->slave->order_by('category_redirect_id', 'desc');
			// 一段时间区间内，同一个原分类id指向多个跳转的分类id的时候，随机取出一个
			$query = $this->database->slave->get();
			$result = $query->result_array();
			
			$this->memcache->set( $mem_key , $result , $mem_expired_time['category_redirect'] );
		}
		
		// 结果返回
		return $result;
	}

}
