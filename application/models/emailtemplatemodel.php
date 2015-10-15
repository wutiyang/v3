<?php

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * 系统邮件类
 * @author lucas
 */
class Emailtemplatemodel extends CI_Model {

	//表名 eb_pc_site:system_email
	private $_tableName_email_template = 'email_template';

	/*
	 * 初始化
	 */
	public function __construct(){
		parent::__construct();
	}

	/**
	 * 获得系统邮件EID和状态信息
	 * @param integet $type 类别ID
	 * @param integet $languageId 语言ID
	 * @return Array 返回 分类广告位数据
	 * @author wty
	 */
	public function getSystemEmailTemplateInfo( $type, $languageId = 1 ){
		$result = array();
		if(!$type || !is_numeric($type)) return $result;
		
		global $mem_expired_time;
		$mem_key = md5("each_buyer_email_template_".$type."_".$languageId);
		
		$result = $this->memcache->get($mem_key);
		if($result === false){
			$this->database->slave->select('*');
			$this->database->slave->from('eb_email_template');
			$this->database->slave->where('email_template_status',1);
			$this->database->slave->where('language_id',$languageId);
			$this->database->slave->where('email_template_type',$type);
			$query = $this->database->slave->get();
			$data = $query->result_array();

			if(!empty($data))$result = $data[0];
			
			$this->memcache->set($mem_key, $result,$mem_expired_time['email_template']);
		}
		
		return $result;
	}

	/**
	 * @desc 获取email魔板id
	 * @param unknown $type
	 * @param number $lanauge_id
	 * @return boolean|Ambigous <>
	 * @author wty
	 */
	public function getEmailTemplateId($type , $lanauge_id = 1){
		if(!$type || !is_numeric($type)) return false;
		
		$email_template_info = $this->getSystemEmailTemplateInfo( $type, $lanauge_id);
		if(!empty($email_template_info)) return $email_template_info['email_template_reference'];
		else return false;
	}
	
	/**
	 * 获得系统邮件模板订单商品信息dom
	 * @param array $order 订单信息
	 * @param array $orderGoodsList 订单商品列表
	 * @return array
	 * @author wty
	 */
	public function getEmailOrderInfoDom( $order, $orderGoodsList, $source = '' ){
		$emailTemplate = array();
		//订单信息
		$order_info = '';
		$this->load->model('goodsmodel','product');
		global $language_range_array;
		//货币单位
		$this->load->model('currencymodel','currencymodel');
		$currency_info = $this->currencymodel->getConfigCurrency($order['order_currency']);
		$order_currency = isset($currency_info['currency_format'])?str_replace('%s', '', $currency_info['currency_format']):'$';
		
		foreach ($orderGoodsList as $key=>$val){
			//获取商品链接
			$language_code = $language_range_array[$order['language_id']];
			$product_id = $val['product_id'];
			$productInfo = $this->product->getinfoNostatus($product_id,$language_code);
			if(empty($productInfo)) continue;
			
			$sourceUrl = '';
			if( $source === 'order' ){
				$sourceUrl = '?utm_source=System_Own&utm_medium=Email&utm_campaign=order_new&utm_nooverride=1';
			}elseif( $source === 'payment' ){
				$sourceUrl = '?utm_source=System_Own&utm_medium=Email&utm_campaign=got-payment&utm_nooverride=1';
			}
			$productUrl = genURL($productInfo['product_url']).$sourceUrl;
			//判断催款邮件不显示价格
			$goodsPrice = isset( $val['order_product_price_subtotal'] ) ? $val['order_product_price_subtotal'] : 0;
			
			$order_info .= sprintf( '<tr>
				<td bgcolor="#F4F4F4" colspan="2">
				<table width="700" cellspacing="1" cellpadding="2" border="0" align="center">
					<tr>
						<td width="90" height="30" align="center" bgcolor="#FFFFFF"  style="font-family: Arial, sans-serif; font-size: 12px; color: #000000;">%u</td>
						<td width="90" align="center" bgcolor="#FFFFFF"><span style="font-family: Arial, sans-serif; font-size: 12px; color: #000000;">%s</span></td>
						<td width="354" bgcolor="#FFFFFF" style="font-family: Arial, sans-serif; font-size: 12px; color: #000000;"><a href="%s" style="font-family: Arial, sans-serif; font-size: 12px; color: #2d72cc;">%s</a></td>
						<td width="40" align="center" bgcolor="#FFFFFF"  style="font-family: Arial, sans-serif; font-size: 12px; color: #000000;">%u</td>
						<td width="100" align="center" bgcolor="#FFFFFF"  style="font-family: Arial, sans-serif; font-size: 12px; color: #000000;">%s</td>
					</tr>
				</table>
				</td>
			</tr>', $val['product_id'], $val['product_sku'], $productUrl, $val['order_product_name'], $val['order_product_quantity'], $order_currency.' '.$goodsPrice );
		}
		
		$order_info .= '<tr>
							<td valign="top" bgcolor="#F4F4F4" align="center" colspan="2">
								<table width="700" cellspacing="0" cellpadding="0" border="0">
									<tr>
										<td bgcolor="#FFFFFF" align="right">
											<table cellspacing="6" cellpadding="0" border="0">
												<tr>
													<td align="right" style="font-family: Arial, sans-serif; font-size: 14px; color: #000000;"><span style="font-family: Arial, sans-serif; font-size: 15px; color: #000000;">'. lang('order_detail_subtotal') . ':' . $order_currency.''.$order['order_price_subtotal'] .'</span></td>
												</tr>';
		if( $order['order_price_shipping'] > 0 ){//运费
			$order_info .= 							'<tr>
													<td align="right" style="font-family: Arial, sans-serif; font-size: 14px; color: #000000;"><span style="font-family: Arial, sans-serif; font-size: 15px; color: #000000;">'. lang('order_detail_shipping_handling') . ':' . $order_currency.''.$order['order_price_shipping'] .'</span></td>
												</tr>';
		}
		if( $order['order_price_insurance'] > 0 ){//保险费
			$order_info .= 							'<tr>
													<td align="right" style="font-family: Arial, sans-serif; font-size: 14px; color: #000000;"><span style="font-family: Arial, sans-serif; font-size: 15px; color: #000000;">'. lang('order_detail_shipping_insurance') . ':' . $order_currency.''.$order['order_price_insurance'] .'</span></td>
												</tr>';
		}
		if( $order['order_price_discount'] > 0 ){//保险费
			$order_info .= 							'<tr>
													<td align="right" style="font-family: Arial, sans-serif; font-size: 14px; color: #000000;"><span style="font-family: Arial, sans-serif; font-size: 15px; color: #000000;">'. lang('discount_savings') . ':' . $order_currency.''.$order['order_price_discount'] .'</span></td>
												</tr>';
		}
		$order_info .= 							'<tr>
													<td height="1" align="right" bgcolor="#000000"></td>
												</tr><tr>
													<td align="right" style="font-family: Arial, sans-serif; font-size: 18px; color: #000000;">'. lang('order_detail_total') .' :<span style="font-family: Arial, sans-serif; font-size: 24px; color: #FF0000;">'. $order_currency.''.$order['order_price'] .'</span></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>';
		$emailTemplate['order_info'] = $order_info;
		
		
		$address = $order['order_address_street'].'<br />';
		$address .= $order['order_address_city'].', '.$order['order_address_state'].', '.$order['order_address_postalcode'].'<br />';
		$address .= $order['order_address_country'].'<br />';
		$address .= 'T: '. $order['order_address_phone'] .'<br />';
		$emailTemplate['address'] = $address;
		
		return $emailTemplate;
	}

	/**
	 * 获得系统邮件模板催款订单商品信息dom
	 * @param array $orderGoodsList 订单商品列表
	 * @param int $languageID 订单语言ID
	 * @param string $languageCode 订单语言code
	 * @param string $orderFrom 订单来源
	 * @return array
	 * @author lucas
	 */
	public function getEmailReminderOrderInfoDom( $orderGoodsList, $languageId, $currency , $source = '' ){
		//订单信息
		$order_info = '';
		$this->load->model('goodsmodel','product');
		global $language_range_array;
		//货币单位
		$this->load->model('currencymodel','currencymodel');
		foreach ( $orderGoodsList as $key => $record ) {
			//获得商品链接
			$language_code = $language_range_array[$languageId];
			$product_id = $record['product_id'];
			$productInfo = $this->product->getinfoNostatus($product_id,$language_code);
			if(empty($productInfo)) continue;
			
			$productUrl = $productInfo['product_url'];

			$sourceUrl = '';
			if( $source === 'paying24checkout' ){
				$sourceUrl = '?utm_source=System_Own&utm_medium=Email&utm_campaign=2-day-reminder-checkout&utm_nooverride=1';
				//'%%SITE_DOMAIN%%/repay/%%ORDER_ID%%?utm_source=System_Own&utm_medium=Email&utm_campaign=2-day-reminder-checkout&utm_nooverride=1'
			}elseif( $source === 'paying24reorder' ){
				$sourceUrl = '?utm_source=System_Own&utm_medium=Email&utm_campaign=2-day-reminder-reorder&utm_nooverride=1';
				//'%%SITE_DOMAIN%%/order_list/reorder/%%ORDER_ID%%?utm_source=System_Own&utm_medium=Email&utm_campaign=2-day-reminder-reorder&utm_nooverride=1'
			}elseif( $source === 'paying48' ){
				$sourceUrl = '?utm_source=System_Own&utm_medium=Email&utm_campaign=7-day-reminder&utm_nooverride=1';
				//'%%SITE_DOMAIN%%/repay/%%ORDER_ID%%?utm_source=System_Own&utm_medium=Email&utm_campaign=7-day-reminder&utm_nooverride=1'
			}
			$productUrl = $productUrl.$sourceUrl;

			$order_info .= sprintf('<tr>
				<td colspan="2" bgcolor="#F4F4F4"><table width="700" border="0" align="center" cellpadding="2" cellspacing="1">
				<tr>
					<td width="90" height="30" align="center" bgcolor="#FFFFFF"  style="font-family: Arial, sans-serif; font-size: 12px; color: #000000;">%u</td>
					<td width="90" align="center" bgcolor="#FFFFFF"><span style="font-family: Arial, sans-serif; font-size: 12px; color: #000000;">%s</span></td>
					<td width="458" bgcolor="#FFFFFF" style="font-family: Arial, sans-serif; font-size: 12px; color: #000000;"><a href="%s" style="font-family: Arial, sans-serif; font-size: 12px; color: #2d72cc;">%s</a></td>
					<td width="40" align="center" bgcolor="#FFFFFF"  style="font-family: Arial, sans-serif; font-size: 12px; color: #000000;">%u</td>
				</tr>
				</table></td>
			</tr>', $record['product_id'], $record['product_sku'], $this->ssi_genURL($productUrl,$languageId), $record['order_product_name'], $record['order_product_quantity']);
		}

		return $order_info;
	}

	/**
	 * 获得发送系统邮件推荐商品DOM
	 * @param string $languageId 语言
	 * @return string recommendProductDOM
	 * @author wty
	 */
	public function getEmailRecommendProductDom( $recommendProList ,$currency_code = 'USD'){
		if(empty($recommendProList)) return '';
		//货币单位
		$this->load->model('currencymodel','currencymodel');
		$currency_info = $this->currencymodel->getConfigCurrency($currency_code);
		$order_currency = isset($currency_info['currency_format'])?str_replace('%s', '', $currency_info['currency_format']):'$';
		
		$recommendProDom = '<table width="720" cellspacing="2" cellpadding="0" border="0"><tr>';
		if( count( $recommendProList ) > 0 ){
			foreach ( $recommendProList as $key => $proRecord ) {
				$recommendProDom .= '<td valign="top" bgcolor="#FFFFFF"><table width="178" border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td><a href="'. genURL($proRecord['product_url']) .'"><img src="'. PRODUCT_IMAGEM_URL.$proRecord['product_image'] .'" alt="'. $proRecord['product_description_name'] .'" width="176" height="176" /></a></td>
										</tr>
										<tr>
											<td><table width="178" border="0">
											<tr>
												<td><span style="font-family: Arial, sans-serif; font-size: 12px; color: #000000;display: block;height: 42px;line-height: 14px;overflow: hidden;"><a href="'. genURL($proRecord['product_url']) .'">'. $proRecord['product_description_name'] .'</a></span></td>
											</tr>
											<tr>
												<td style="font-family: Arial, sans-serif; font-size: 14px; color: #000000;"><span style="font-family: Arial, sans-serif; font-size: 12px; color: #000000; text-decoration:line-through">'. $order_currency.$proRecord['product_price_market'] .'</span></td>
											</tr>
											<tr>
												<td><span style="font-family: Arial, sans-serif; font-size: 14px; color: #ff0000;">'. $order_currency.$proRecord['product_discount_price'] .'</span></td>
											</tr>';
											if( $proRecord['product_discount_number'] > 0 ){
				$recommendProDom .= 		'<tr>
												<td><table width="100" border="0" cellpadding="5" cellspacing="0">
												<tr>
													<td height="20" align="center" bgcolor="#f99501"><span style="font-family: Arial, sans-serif; font-size: 16px; color: #FFFFFF;">'. $proRecord['product_discount_number'] .'% OFF</span></td>
												</tr>
												</table></td>
											</tr>';
											}
				$recommendProDom .=		'</table></td>
										</tr>
									</table></td>';
			}
		}
		$recommendProDom .= '</tr></table>';
		return $recommendProDom;
	}
	
	/**
	 * 获得发送系统邮件推荐商品DOM(ssi模式下)
	 * @param string $languageId 语言
	 * @return string recommendProductDOM
	 * @author wty
	 */
	public function getEmailRecommendProductDomSSI( $recommendProList ,$currency = '$',$lanuage_id = 1){
		$recommendProDom = '<table width="720" cellspacing="2" cellpadding="0" border="0"><tr>';
		if( count( $recommendProList ) > 0 ){
			foreach ( $recommendProList as $key => $proRecord ) {
				$recommendProDom .= '<td valign="top" bgcolor="#FFFFFF"><table width="178" border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td><a href="'. $this->ssi_genURL($proRecord['product_url'],$lanuage_id) .'"><img src="'. PRODUCT_IMAGEM_URL.$proRecord['product_image'] .'" alt="'. $proRecord['product_description_name'] .'" width="176" height="176" /></a></td>
										</tr>
										<tr>
											<td><table width="178" border="0">
											<tr>
												<td><span style="font-family: Arial, sans-serif; font-size: 12px; color: #000000;display: block;height: 42px;line-height: 14px;overflow: hidden;"><a href="'. $this->ssi_genURL($proRecord['product_url'],$lanuage_id) .'" >'. $proRecord['product_description_name'] .'</a></span></td>
											</tr>
											<tr>
												<td style="font-family: Arial, sans-serif; font-size: 14px; color: #000000;"><span style="font-family: Arial, sans-serif; font-size: 12px; color: #000000; text-decoration:line-through">'. $currency.$proRecord['product_price_market'] .'</span></td>
											</tr>
											<tr>
												<td><span style="font-family: Arial, sans-serif; font-size: 14px; color: #ff0000;">'. $currency.$proRecord['product_discount_price'] .'</span></td>
											</tr>';
				if( $proRecord['product_discount_number'] > 0 ){
					$recommendProDom .= 		'<tr>
												<td><table width="100" border="0" cellpadding="5" cellspacing="0">
												<tr>
													<td height="20" align="center" bgcolor="#f99501"><span style="font-family: Arial, sans-serif; font-size: 16px; color: #FFFFFF;">'. $proRecord['product_discount_number'] .'% OFF</span></td>
												</tr>
												</table></td>
											</tr>';
				}
				$recommendProDom .=		'</table></td>
										</tr>
									</table></td>';
			}
		}
		$recommendProDom .= '</tr></table>';
		return $recommendProDom;
	}
	/**
	 * 根据用户id批量获取购物车商品ID
	 * @param  string $userIds 用户id数组
	 * @author lucas
	 * @return array
	 */
	public function getCartByUser( $userIds ) {
		if( is_array( $userIds ) ){
			$userIds = implode(',', $userIds);
		}

		$proIds = array();
		if( count( $userIds ) > 0 ) {
			//由于主从库有延迟 因此这里 读 主库
			$this->db_ebmaster_read->select('user_id, product_id');
			$this->db_ebmaster_read->from('cart');
			$this->db_ebmaster_read->where_in('user_id', $userIds);
			$this->db_ebmaster_read->order_by('create_time','desc');
			$result = $this->db_ebmaster_read->get()->result_array();

			foreach ( $result as $record ) {
				$proIds[ $record['user_id'] ][] = $record['product_id'];
			}
		}

		return $proIds;
	}

	private function ssi_genURL($content = '',$language_id = 1){
		global $base_url;
		$baseurl = id2name($language_id,$base_url);
		$res = $baseurl.trim($content,'/');
		return $res;
	}
}