<?php
/**
 * suggest.php
 * Eachbuyer 提取搜索建议(输出JSON)
 *
 * 该文件由 xunsearch PHP-SDK 工具自动生成，请根据实际需求进行修改
 * 创建时间：2013-09-05 20:30:42
 */
// 加载 XS 入口文件
define('IN_ECS', true);
// require(dirname(__FILE__) . '/../../../includes/init.php');
require_once dirname(__FILE__).'/lib/XS.php';

// Prefix Query is: term (by jQuery-ui)
$q = isset($_REQUEST['term']) ? trim($_REQUEST['term']) : '';

//$q = get_magic_quotes_gpc() ? stripslashes($q) : $q;
$terms = array();
if (!empty($q) && strpos($q, ':') === false) {
	try {
		$xs = new XS('goods_description_'.$two_domain_id);
        //$q_arr = explode(' ', $q);
        //$q = array_pop($q_arr);
        //$q_str = implode(' ', $q_arr);
		$terms = $xs->search->setCharset('UTF-8')->getExpandedQuery($q);
		if( $terms ){
			$regx = '/' . $q . '/i';
			foreach( $terms as &$term ){
				$term = preg_replace( $regx, '<em>$0</em>', $term);
			}
		}


        // 进行第二个联想时，应保证跟第1个联合，有结果
        /*
        $exist_terms = array();
        foreach($terms as $term) {
            $term = $q_str. ' '. $term;
            if($xs->search->count($term)) {
                $exist_terms[] = $term;
            }
        }*/
	} catch (XSException $e) {

	}
}

// output json
header("Content-Type: application/json; charset=utf-8");
echo json_encode($terms);
exit(0);