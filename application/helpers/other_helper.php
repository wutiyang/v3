<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('getPagination')){
	function getPagination($input){
		$result = array();
        if(!isset($input['start']) || !validate($input['start'])){
            $result['start'] = LIST_DEFAULT_START;
		}else{
			$result['start'] = intval($input['start']);
		}

        if(!isset($input['limit']) || !validate($input['limit'])){
            $result['limit'] = LIST_DEFAULT_LIMIT;
		}else{
			$result['limit'] = intval($input['limit']);
		}
        
        return $result;
	}
}

/**
 * 分类页面url301和302跳转
 * @param  array $data 跳转信息
 * @param  string $str 跳转后缀链接 17948
 */
function url301And302Redirect($dataArray = array(), $str = '') {
	if(is_array($dataArray) && count($dataArray) > 0) {
		$nowTime = date('Y-m-d H:i:s', requestTime());
		foreach ($dataArray as $key => $data) {
			// 判断原分类id和目标分类id不相等的时候进行跳转
			if($data['to_category_id'] != $data['from_category_id']) {
				// 判断时间是否过期
				if($data['category_redirect_time_start'] <= $nowTime && $nowTime < $data['category_redirect_time_end'] ) {
					// 判断目标分类id是否为0
					if( (empty($data['to_category_id']) || $data['to_category_id'] == 0)  && empty( $data['url'] )) {
						// 跳转类型判断处理
						if($data['category_redirect_type'] == 1) {
							redirect(genURL($str),'location', 301);
						} else {
							redirect(genURL($data['category_redirect_url']),'location', 302);
						}
						break;
					} else{
						if( !empty ($data['to_category_id'])) {
							$targetCategoryId = (int)$data['to_category_id'];
							$str = str_replace( '{$targetCategoryId}', $targetCategoryId, $str );
						}elseif(!empty( $data['category_redirect_url'])){
							$str =  $data['category_redirect_url'];
						}
						// 跳转类型判断处理
						if($data['category_redirect_type'] == 1) {
							redirect( genURL($str, true), 'location', 301 );
						} else {
							redirect( genURL($str, true), 'location', 302 );
						}
						break;
					}
				}
			}
		}
	}
}

/**
 * 获取当前服务器的时间戳地址
 *
 * @author BRYAN - NYD  <ningyandong@hofan.cn>
 */
if ( ! function_exists('requestTime')){
	function requestTime() {
		return (int)$_SERVER[ 'REQUEST_TIME' ];
	}
}

if ( ! function_exists('getFilter')){
	function getFilter($input,$filterList){
		
		$filter = array();
		if(is_array($input) && is_array($filterList)){
			foreach($input as $key => $value){
				if(array_key_exists($key,$filterList) && $value != ''){
					$filter[$filterList[$key]] = $value;
				}
			}
		}

		return $filter;
	}
}

if ( ! function_exists('getFilterExplode')){
	function getFilterExplode($input,$filterList,$delimiter = ','){
		$filter = array();
		if(is_array($input) && is_array($filterList)){
			foreach($input as $key => $value){
				if(array_key_exists($key,$filterList) && $value != ''){
					$value = str_replace(array(',','，',' ','/',"\n"),$delimiter,$value);
					$value = explode($delimiter,$value);
					if(!empty($value)) $filter[$filterList[$key]] = $value;
				}
			}
		}

		return $filter;
	}
}

if ( ! function_exists('prepare_file_name')){
	function prepare_file_name(&$filename){
		$filename_replace = array('\\','/',':','*','?','"','\'','<','>','|');

		foreach($filename_replace as $token){
			$filename = str_replace($token,'_',$filename);
		}
	}
}

if ( ! function_exists('remove_javascript')){
	function remove_javascript(&$content){
		while(true){
			$start = stripos($content,'<script');
			$end = stripos($content,'</script>');
			if($start === false || $end === false) break;
			$content = substr($content,0,$start).substr($content,$end+9);
		}
	}
}

if ( ! function_exists('remove_css')){
	function remove_css(&$content){
		while(true){
			$start = stripos($content,'<style');
			$end = stripos($content,'</style>');
			if($start === false || $end === false) break;
			$content = substr($content,0,$start).substr($content,$end+8);
		}
	}
}

if ( ! function_exists('html2text')){
	function html2text($text){
		$text = strval($text);
		$text = preg_replace('/<br>/i',"\n",$text);
		$text = preg_replace('/<br\s\/>/i',"\n",$text);
		$text = preg_replace('/<br\/>/i',"\n",$text);
		$text = preg_replace('/<p[^>]+>/i',"\n",$text);
		$text = preg_replace('/<\/p>/i',"\n",$text);
		$text = preg_replace('/<\/div>/i',"\n",$text);
		$text = preg_replace('/<div.*>/i',"\n",$text);
		$text = preg_replace('/<\/ol>/i',"\n",$text);
		$text = preg_replace('/<ol.*>/i',"\n",$text);
		$text = preg_replace('/<\/ul>/i',"\n",$text);
		$text = preg_replace('/<ul.*>/i',"\n",$text);
		$text = preg_replace('/<h\d[^>]+>/i',"\n",$text);
		$text = preg_replace('/<\/h\d>/i',"\n",$text);
		$text = strip_tags($text);
		$text = trim($text,"\n");
		$text = str_replace("\n ","\n",$text);
		$text = str_replace('&nbsp;',' ',$text);
		$text = htmlspecialchars_decode($text);

		return $text;
	}
}

if ( ! function_exists('is_email')){
	function is_email($input){
		if(empty($input) || is_array($input)) { return false; }
		if(strpos($input,'@') === false) { return false; }
		if(strpos($input,'.') === false) { return false; }
		if(!preg_match("/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i",$input)) { return false; }

		return true;
	}
}

if ( ! function_exists('currencyAmount')){
	function currencyAmount($amount,$currencyCode = 'USD'){
		$CI = & get_instance();
		$CI->load->model('currencymodel','currencymodel');
		$currency_list = $CI->currencymodel->currencyList();
		$currency_list = reindexArray($currency_list, 'currency_code');
		
		if(isset($currency_list[$currencyCode])){
			return sprintf($currency_list[$currencyCode]['currency_format'],$amount);
		}else{
			return $amount;
		}
		return true;
	}
}

if ( ! function_exists('eb_substr')){
	function eb_substr($str,$length = 0,$append = true){
		$str = trim($str);
		$strlength = strlen($str);

		if ($length == 0 || $length >= $strlength){
			return $str;
		}elseif ($length < 0){
			$length = $strlength + $length;
			if ($length < 0){
				$length = $strlength;
			}
		}

		if (function_exists('mb_substr')){
			$newstr = mb_substr($str, 0, $length, 'utf-8');
		}elseif (function_exists('iconv_substr')){
			$newstr = iconv_substr($str, 0, $length, 'utf-8');
		}else{
			$newstr = substr($str, 0, $length);
		}

		if ($append && $str != $newstr){
			$newstr .= '...';
		}

		return $newstr;
	}

}

if ( ! function_exists('encrypt')){
	function encrypt($str,$key = 'eachbuyer_bWF0dA=='){
		$key .= ':';

		return base64_encode($key.$str);
	}
}

if ( ! function_exists('decrypt')){
	function decrypt($str,$key = 'eachbuyer_bWF0dA=='){
		$str = base64_decode($str);
		if($str == '') return '';
		list($decrypt_key,$str) = explode(':',$str);
		if($decrypt_key == $key && is_string($str) && $str != '') return $str;

		return '';
	}
}

if ( ! function_exists('filterName')){
	function filterName( $name  = '') { 
		if( empty( $name ) ){
			return '';
		}
		
		$name = strtolower ( $name );
		$name = trim( $name );
		$name = str_replace('+', '', $name);
		//$search数组 里的字符串顺序很重要
		$search = array(  ' & ' ,  '& ' ,  ' &' , ' ' ,  '/'  );
		$name = str_replace($search, '-', $name );		
		$name = str_replace( array("'" ), '', $name );	
		$name = htmlspecialchars( $name );
		$name = removeXSS( $name );
	
		return $name;
	}
}

if ( ! function_exists('productUrl')){
	function productUrl( $name  = '' , $id = '') { 
		if( empty( $name ) || empty($id)){
			return '';
		}
// 		$name = filterName($name);
// 		$url = $name.'-p'.$id.'.html';
// 		return genUrl($url);

		$CI = & get_instance();
		$CI->load->helper('text');
		
		$name = preg_replace('/[^a-zA-Z0-9_-]/i','-',$name);
		$name = preg_replace('/-(?=-)/', '', $name);
		$name = convert_accented_characters($name);
		$url = "$name-p$id.html";
		
		return genURL(strtolower($url));
		
	}
}

if (!function_exists('eb_htmlspecialchars')) {

	/**
	 * Htmlspecialchars the data.
	 * @param string or array $data The data need to htmlspecialchars.
	 * @author	Albie
	 */
	function eb_htmlspecialchars($data) {
		if (is_array($data)) {
			$dataFormat = array();
			foreach ($data as $k => $v) {
				$dataFormat[$k] = eb_htmlspecialchars($v);
			}
		} else {
			$dataFormat = htmlspecialchars($data, ENT_QUOTES);
		}
		return $dataFormat;
	}

}

if (!function_exists('eb_email')) {
	function eb_email($eid,$email,$params) {
		$params['eid'] = $eid;
		$params['email'] = $email;

		$data = @get_headers('https://ebm.cheetahmail.com/api/login1?name=eachbuyer@api&cleartext=Forward@15',1);
		//$data = get_headers('http://ebm.cheetahmail.com/api/login1?name=eachbuyer@api&cleartext=Forward@15',1);
		
		if($data === false || !is_array($data) || !isset($data['set-cookie'])) return false;
		$data = $data['set-cookie'];

		$context = array();
		$context['http'] = array(
			'method' => 'POST',
			'header' => 'Content-type: application/x-www-form-urlencoded'."\r\n".'Cookie: '.$data,
			'content' => http_build_query($params, '', '&'),
		);
		$result = file_get_contents('https://ebm.cheetahmail.com/ebm/ebmtrigger1', false, stream_context_create($context));
		
		return $result;
	}
}

if ( ! function_exists('exchangePrice')){
	function exchangePrice($price = 0,$currency = '' , $rateNumber = ''){
		$CI = & get_instance();
		if(empty($currency)){
			$currency = $CI->currencymodel->currentCurrency();//当前汇率
		}
		if($currency !== false){
			if( empty( $rateNumber ) ){
				$currencyInfo = $CI->currencymodel->getConfigCurrency($currency, 1);
				$price = sprintf("%.2f",$price*$currencyInfo['currency_rate']);
			}else{
				$price = sprintf("%.2f", $price * $rateNumber );
			}
		}
		return $price;
	}
}

/**
 * 当前货币状态下的显示
 */
if ( ! function_exists('currentPrice')){
	function currentPrice($price = 0,$currency = '' , $rateNumber = ''){
		$CI = & get_instance();
		if(empty($currency)){
			$currency = $CI->currencymodel->currentCurrency();//当前汇率
		}
		if($currency !== false){
			if( empty( $rateNumber ) ){
				$currencyInfo = $CI->currencymodel->getConfigCurrency($currency, 1);
				$price = sprintf("%.2f",$price*$currencyInfo['currency_rate']);
			}else{
				$price = sprintf("%.2f", $price * $rateNumber );
			}
		}
		return $price;
	}
}
