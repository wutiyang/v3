<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//对二维数组某键值进行排序
if (!function_exists('array_sort')) {
	function array_sort($arr,$keys,$type='asc'){
		$keysvalue = $new_array = array();
		foreach ($arr as $k=>$v){
			$keysvalue[$k] = $v[$keys];
		}
		if($type == 'asc'){
			asort($keysvalue);
		}else{
			arsort($keysvalue);
		}
		reset($keysvalue);
		foreach ($keysvalue as $k=>$v){
			$new_array[$k] = $arr[$k];
		}
		return $new_array;
	}
}
if (!function_exists('array_intersect_upgrade')) {
	function array_intersect_upgrade($data){
		$result = array();
		$i=1;
		foreach($data as $key=>$value) {
			if(!empty($value)) {
				if($i>1) {
					$result = array_intersect($result,$value);
				}else{
					$result = $value;
				}
				$i++;
			}
		}
		return $result;
	}
}

if ( ! function_exists('spreadArray')){
	function spreadArray($orig,$column){		
		$result = array();

		if(is_array($orig)){
			foreach($orig as $record){

				if(isset($record[$column])){
					$key = $record[$column];
				}else{
					$key = 0;
				}

				$result[$key][] = $record;
			}
		}

		return $result;
	}
}

if ( ! function_exists('extractColumn')){
	function extractColumn($list,$column){		
		$result = array();

		if(is_array($list)){
			foreach($list as $record){

				if(isset($record[$column])){
					$result[] = $record[$column];
				}
			}
		}
		$result = array_unique($result);

		return $result;
	}
}

if ( ! function_exists('reindexArray')){
	function reindexArray($orig,$column){		
		$result = array();

		if(is_array($orig)){
			foreach($orig as $record){

				if(isset($record[$column])){
					$key = $record[$column];
				}else{
					$key = 0;
				}

				$result[$key] = $record;
			}
		}

		return $result;
	}
}

if ( ! function_exists('sortArray')){
	function sortArray(&$arr,$column,$dir = SORT_ASC){//SORT_DESC
		$sortColumn = array();
		foreach ($arr as $key => $row) {
			$sortColumn[$key]  = $row[$column];
		}

		array_multisort($sortColumn,$dir,$arr);		
	}
}

if ( ! function_exists('id2name')){
	function id2name($id,$arr,$default = ''){
		if(isset($arr[$id])){
			return $arr[$id];
		}else{
			return $default;
		}
	}
}