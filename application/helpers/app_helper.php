<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * xss参数注入过滤
 * @param  string $val 接受的参数
 *
 * @return string $val
 */
if ( ! function_exists('removeXSS')){
	function removeXSS($val) {
		// remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
		// this prevents some character re-spacing such as <java\0script>
		// note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
		// $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val); // 这里里面把逗号过滤了
		$val = preg_replace('/([\x00-\x08])/', '', $val);
	
		//替换转移字符 原标签=urlencode后的字符串  "<"="%3C";">"="%3E";"</"="%3C/"
		$strFilterUrlEncode = array( '%3C' , '%3E' , '%3C/' , '%22','(' , ')' ,'"','<' , '>' , 'cookie' , 'document' , 'script' , '%', ':','#','$','&','^', '[', ']', '{', '}', '€', '¥', '£', '*', '\\', "\n", "\r", "\t", );
		$val = str_replace( $strFilterUrlEncode , '', $val );
	
		// straight replacements, the user should never need these since they're normal characters
		// this prevents like <IMG SRC=@avascript:alert('XSS')>
		$search = 'abcdefghijklmnopqrstuvwxyz';
		$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$search .= '1234567890!@#$%^&*()';
		$search .= '~`";:?+/={}[]-_|\'\\';
		for ($i = 0; $i < strlen($search); $i++) {
			// ;? matches the ;, which is optional
			// 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
	
			// @ @ search for the hex values
			$val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
			// @ @ 0{0,7} matches '0' zero to seven times
			$val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
		}
	
		// now the only remaining whitespace attacks are \t, \n, and \r
		$ra1 = array(
				'javascript',
				'vbscript',
				'expression',
				'applet',
				'meta',
				'xml',
				'blink',
				'link',
				'style',
				'script',
				'embed',
				'object',
				'iframe',
				'frame',
				'frameset',
				'ilayer',
				'layer',
				'bgsound',
				'base',
				'alertdocument',
		);
		$ra2 = array(
				'onabort',
				'onactivate',
				'onafterprint',
				'onafterupdate',
				'onbeforeactivate',
				'onbeforecopy',
				'onbeforecut',
				'onbeforedeactivate',
				'onbeforeeditfocus',
				'onbeforepaste',
				'onbeforeprint',
				'onbeforeunload',
				'onbeforeupdate',
				'onblur',
				'onbounce',
				'oncellchange',
				'onchange',
				'onclick',
				'oncontextmenu',
				'oncontrolselect',
				'oncopy',
				'oncut',
				'ondataavailable',
				'ondatasetchanged',
				'ondatasetcomplete',
				'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave',
				'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange',
				'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress',
				'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter',
				'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel',
				'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange',
				'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete',
				'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop',
				'onsubmit', 'onunload'
		);
	
		$ra = array_merge($ra1, $ra2);
	
		$found = true; // keep replacing as long as the previous round replaced something
		while ($found == true) {
			$val_before = $val;
			for ($i = 0; $i < sizeof($ra); $i++) {
				$pattern = '/';
				for ($j = 0; $j < strlen($ra[$i]); $j++) {
					if ($j > 0) {
						$pattern .= '(';
						$pattern .= '(&#[xX]0{0,8}([9ab]);)';
						$pattern .= '|';
						$pattern .= '|(&#0{0,8}([9|10|13]);)';
						$pattern .= ')*';
					}
					$pattern .= $ra[$i][$j];
				}
				$pattern .= '/i';
				$replacement = substr($ra[$i], 0, 2) . '_V_' . substr($ra[$i], 2); // add in <> to nerf the tag
				$val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
				if ($val_before == $val) {
					// no replacements were made, so exit the loop
					$found = false;
				}
			}
		}
	
		return $val;
	}
}

/**
 * 获取EACHBUYER HOME地址
 *
 * @return string  返回图片URL
 * @author BRYAN - NYD  <ningyandong@hofan.cn>
 */
function home() {
	return SITE_HOME_URL ;
}

/**
 * 获取当前服务器的时间戳地址
 *
 * @author BRYAN - NYD  <ningyandong@hofan.cn>
 */
function requestTime() {
	return (int)$_SERVER[ 'REQUEST_TIME' ];
}

if ( ! function_exists('diffBetweenTwoDays')){
	function diffBetweenTwoDays ($active_time,$days = 14){
		$start_time = strtotime($active_time);
		$now_time = time(); 
		
		if($start_time){
			$rarrow_days = ($now_time - $start_time) / 86400;
			if($rarrow_days <= $days) return true;
			else return false;
		}else{
			return false;
		}
		
	}
}

/**
 * 计算两个日期之间天数
 * @param unknown $date1
 * @param unknown $date2
 * @return number
 */
if ( ! function_exists('howbetweendates')){
	function howbetweendates($date){
		$start_date = strtotime($date);
		$days = ceil(abs(time() - $start_date)/86400);
		return $days;
	}
}

if ( ! function_exists('genURL')){
	function genURL($content = '',$ending_with_slash = false,$param = array(),$ssl = false){
		global $base_url;
		$language_id = currentLanguageId();
		$baseurl = id2name($language_id,$base_url);
		if($ssl && defined('SSL_ENABLED') && SSL_ENABLED === TRUE) $baseurl = str_replace('http://','https://',$baseurl);

		$res = $baseurl.trim($content,'/');
		if($ending_with_slash) $res .= '/';

		if(is_array($param) && !empty($param)){
			$res .= '?';
			foreach($param as $key => $value){
				$param[$key] = $key .'='.$value;
			}
			$res .= implode('&',$param);
		}

		return $res;
	}
}

if ( ! function_exists('gensslURL')){
	function gensslURL($content = '',$ending_with_slash = false,$param = array()){
		global $base_url;
		$CI = & get_instance();
		$language_id = currentLanguageId();
		$baseurl = id2name($language_id,$base_url,BASIC_URL);
		if(defined('SSL_ENABLED') && SSL_ENABLED === TRUE) $baseurl = str_replace('http://','https://',$baseurl);

		$res = $baseurl.trim($content,'/');
		if($ending_with_slash) $res .= '/';

		if(is_array($param) && !empty($param)){
			$res .= '?';
			foreach($param as $key => $value){
				$param[$key] = $key .'='.$value;
			}
			$res .= implode('&',$param);
		}

		return $res;
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

if ( ! function_exists('currentLanguageId')){
	function currentLanguageId(){
		$ci = & get_instance();

		return $ci->language_id;
	}
}

if ( ! function_exists('currentLanguageCode')){
	function currentLanguageCode(){
		$ci = & get_instance();
		global $language_list;

		return $language_list[$ci->language_id];
	}
}

if ( ! function_exists('currentCurrency')){
	function currentCurrency(){
		$ci = & get_instance();

		return $ci->currency;
	}
}

if ( ! function_exists('generateGACid')){
	function generateGACid(){
		$CI = & get_instance();
		$ga = $CI->input->cookie('_ga');
		if($ga !== false){
			list($version,$domainDepth,$cid1,$cid2) = explode('.',$ga,4);
			return $cid1.'.'.$cid2;
		}else{
			return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
					// 32 bits for "time_low"
					mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
					// 16 bits for "time_mid"
					mt_rand( 0, 0xffff ),
					// 16 bits for "time_hi_and_version",
					// four most significant bits holds version number 4
					mt_rand( 0, 0x0fff ) | 0x4000,
					// 16 bits, 8 bits for "clk_seq_hi_res",
					// 8 bits for "clk_seq_low",
					// two most significant bits holds zero and one for variant DCE1.1
					mt_rand( 0, 0x3fff ) | 0x8000,
					// 48 bits for "node"
					mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
			);
		}
	}
}