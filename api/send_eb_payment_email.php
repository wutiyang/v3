<?php
/** 
 * @desc 未付款提醒邮件
 * @User: WTY
 * @Date: 15-8-10
 * @Time: 16:16
 */
 
define( 'ROOT_DIR' , dirname( __FILE__ ) . '/../');
include_once dirname( __FILE__ ). '/system_config.php';

try
{
	$page = 1;//页数
	$pagesize = 10;//每页数量
	
	//获取总记录数
	$total_num = getUnpaidOrderTotal();
	$max_page = ceil($total_num/$pagesize);
	
	//获取未付款订单信息
	while($max_page > $page){
		$info = getOrderWithPage($page, $pagesize);
		echo "<pre>bbb";print_r($info);die;
		
		sleep(1);
		$page++;
	}
	//发送购买邮件提醒
	
	//日志记录
	
}catch(Exception $e)
{
	print $e->getMessage();exit(); 
}

//获取所有未付款订单
function getOrderWithPage($page = 1, $pagesize = 10){
	$data = array();
	
	//获取数据库
	$mysql_connect = getSqlConn();
	
	$sql = 'select order_id,order_code,language_id,customer_id,address_id,order_status_email_pay1,order_status_email_pay2,order_time_create,order_time_lastmodified from `eb_order` where order_status=1 order by order_time_lastmodified asc limit '.($page-1)*$pagesize.','.$pagesize;

	$result = mysql_query($sql,$mysql_connect) or die(mysql_error());
	
	while($info = mysql_fetch_assoc($result)){
		array_push($data, $info);	
	}
	
	// 关闭连接
	mysql_close($mysql_connect);
	
	return $data;
}

function getUnpaidOrderTotal(){
	//获取数据库
	$mysql_connect = getSqlConn();
	$sql = "select count(*) as num from `eb_order` where order_status=1";
	
	$result = mysql_query($sql,$mysql_connect) or die(mysql_error());
	
	$info = mysql_fetch_assoc($result);
	
	$total_num = $info['num'];
	
	// 关闭连接
	mysql_close($mysql_connect);
	
	return $total_num;
}

//链接数据库
function getSqlConn(){
	global $system_config;//数据库配置信息
	
	$dbhost = $system_config['eb_db_config']['eb_pc_site']['host'];
	$dbuser = $system_config['eb_db_config']['eb_pc_site']['user'];
	$dbpsd = $system_config['eb_db_config']['eb_pc_site']['passwd'];
	$dbport = $system_config['eb_db_config']['eb_pc_site']['port'];
	$dbname = $system_config['eb_db_config']['eb_pc_site']['name'];
	
	$conn=mysql_connect($dbhost, $dbuser, $dbpsd);
	mysql_select_db($dbname, $conn);
	if (!$conn)
	{
		die('Could not connect: ' . mysql_error());
	}
    // 释放资源
   // mysql_free_result($result);
   return $conn;
}
?>