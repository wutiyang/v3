<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('SEARCH_MODULE_PATH', dirname(__FILE__));

require_once SEARCH_MODULE_PATH.'/lib/XS.php';

class Xsearch {
	/*
	 *  自动调用get*** 方法
	 *  args[0] 为要设定值
	 *  args[1] 为默认值
	 */
	public function __call($funcName, $args) {

		$funcName = strtolower(substr($funcName, 3));
		echo $funcName;die;
		$cookie = '';
		if(empty($args[0])) {

		if (empty($_COOKIE['search'][$funcName])) {
		// 默认值
		$cookie = $args[1];
		setcookie("search[$name]",$args[1]);
		} else {
		$cookie = $_COOKIE['search'][$funcName];
		}
		} else {
		$cookie = $args[0];
		setcookie("search[$funcName]",$args[0]);
		}

		return $cookie;
	}
}

/*class SearchHelper {

	public function uri($baseUri, $args = array()) {
		$uriArgs = array();
		if(!empty($args)) {
			foreach($args as $key => $value) {
				$uriArgs[] = $key.'='.$value;
			}
		}
		$uriArgs = implode('&', $uriArgs);
		
		if(!empty($uriArgs)) {
			if(strpos($baseUri, '?') == false) {
				$baseUri .= '?';
			} else {
				if(substr($baseUri, -1, 1) != '?') {
					$baseUri .= '&';
				}
			}        
		}

		return $baseUri.$uriArgs;      
	}

	public function genPager($baseUrl, $count, $page = 1, $pageSize = 40) {
		if ($count > $pageSize) {
			
			$totalPage = ceil($count / $pageSize);
			// page begin
			$pb = max($page - 5, 1);
			// page end
			$pe = min($pb + 10,  $totalPage);
			if($page > 1) {
				$pPrev = $page - 1;
			} else {
				$pPrev = $page;
			}
			
			$pager = '<a title="Prev" class="p_previous" href="'.$baseUrl.'&page='.$pPrev.'" rel="nofollow"><&nbsp;Previous</a>';
			
			if($page > 9 ){
				$pager .= '<a href="'.$baseUrl.'&page=1">1</a><a>...</a>';
			}
			
			do {
				$pager .= ($pb == $page) ? '<a class="current">' . $page . '</a>' : '<a href="' . $baseUrl . '&page=' . $pb . '">' . $pb . '</a>';
			} while ($pb++ < $pe);
			
			if( ( $totalPage - $page ) > 10){
				$pager .= '<a>...</a><a href="'.$baseUrl.'&page='.$totalPage .'">' . $totalPage .'</a>';
			}
			
			if($page < $pe -1) {
				$pNext = $page + 1;
			} else {
				$pNext = $page;
			}
			$pager .= '<a title="Next" href="'.$baseUrl.'&page='.$pNext.'" class="p_next" rel="nofollow"> Next&nbsp;> </a>';
		}
		return $pager;
	}

}

class SearchCookie {

	/*
	 *  自动调用get*** 方法
	 *  args[0] 为要设定值
	 *  args[1] 为默认值
	 * /
	public function __call($funcName, $args) {

		$funcName = strtolower(substr($funcName, 3));
		$cookie = '';
		if(empty($args[0])) {

			if (empty($_COOKIE['search'][$funcName])) {
				// 默认值
				$cookie = $args[1];
				setcookie("search[$name]",$args[1]);
			} else {
				$cookie = $_COOKIE['search'][$funcName];
			}
		} else {
			$cookie = $args[0];
			setcookie("search[$funcName]",$args[0]);
		}

		return $cookie;
	}
}
*/