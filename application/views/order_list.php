<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/order_list.css?v=<?php echo STATIC_FILE_VERSION ?>">
<div class="wrap breadcrumbs">
    <i class="icon-home">&nbsp;</i>
    <a href="<?php echo genURL('/')?>"><?php echo lang('home');?></a>
    <i class="icon-arr-right">&nbsp;</i>
    <a href="<?php echo genURL('order_list');?>"><?php echo lang('my_account');?></a>
    <i class="icon-arr-right">&nbsp;</i>
    <span><?php echo lang('my_order');?></span>
</div>
<div class="main order-list" id="orderList">
    <?php include dirname(__FILE__).'/account/nav.php'; ?>
    <div class="content">
   		<h3 class="column-now"><?php echo lang('my_order');?></h3>
   		<?php if($nums == 0){ ?>
   		   <div class="null"><?php echo lang('msg_no_order') ?><a class="btn34-org btn-text" href="<?php echo genUrl() ?>"><?php echo lang('btn_continue_shopping') ?></a></div>
        <?php }else{ ?>
            <table class="order-table">
            <thead>
            	<tr>
                	<th class="first"><?php echo lang('order_no');?></th>
                    <th><?php echo lang('order_date');?></th>
                    <th><?php echo lang('order_total');?></th>
                    <th><?php echo lang('order_status');?></th>
                    <th><?php echo lang('order_action');?></th>
                </tr>
            </thead>
            <tbody>
            	<?php foreach ($orderList as $order){ ?>
            	<tr>
                	<td align="center" class="w1"><a href="<?php echo genURL('order_detail/'.$order['order_id']) ?>"><?php echo $order['order_code'];?></a></td>
                    <td align="center" class="w2"><?php echo date('M d,Y',strtotime($order['order_time_create']));?></td>
                    <td align="center" class="w3"><?php echo currencyAmount($order['order_price'],$order['order_currency']);?></td>
                    <td class="w4">
                        <?php if(in_array($order['order_status'],array(OD_PAID,OD_PAIDCONFIRM,OD_AUDIT,OD_PROCESSING,OD_DELIVER))){ ?>
                            <i class="type-2">&nbsp;</i>
                        <?php }elseif(in_array($order['order_status'],array(OD_DELIVERED,OD_COMPLETED))){ ?>
                            <i class="type-1">&nbsp;</i>
                        <?php }else{ ?>
                            <i class="type-3">&nbsp;</i>
                        <?php }?>

                        <?php if(!in_array($order['order_status'],array(OD_DELIVERED,OD_COMPLETED,OD_CANCEL))){ ?>
                        <i class="describe">
                            <div class="popBox">
                                <div class="pop-icon"> <em>◆</em>
                                <span>◆</span></div>
                                <div class="con">
                                    <?php if(in_array($order['order_status'],array(OD_CREATE,OD_PAYING))){ ?>
                                        <p><?php echo lang('msg_order_incomplete');?></p>
                                    <?php }elseif(in_array($order['order_status'],array(OD_PAID,OD_PAIDCONFIRM,OD_AUDIT,OD_PROCESSING,OD_DELIVER))){ ?>
                                        <p><?php echo lang('msg_order_processing');?></p>
                                        <div class="space1"><a href="<?php echo genURL('review_create') ?>" class="hover-uorange"><?php echo lang('write_review') ?></a></div> 
                                    <?php } ?>
                                </div>   
                             </div> 
            			</i>
            			<?php } ?>

                        <?php if(in_array($order['order_status'],array(OD_CREATE,OD_PAYING))){ ?>
                            <?php echo lang('order_status_pending');?>
                        <?php }elseif(in_array($order['order_status'],array(OD_PAID,OD_PAIDCONFIRM,OD_AUDIT,OD_PROCESSING,OD_DELIVER))){ ?>
                            <?php echo lang('order_status_processing');?>
                        <?php }elseif(in_array($order['order_status'],array(OD_DELIVERED,OD_COMPLETED))){ ?>
                            <?php echo lang('order_status_shipped');?>
                        <?php }elseif(in_array($order['order_status'],array(OD_CANCEL))){ ?>
                            <?php echo lang('order_status_canceled');?>
                        <?php } ?>

                    </td>
                    <td class="w5">
                    	<p><a href="<?php echo genURL('order_detail/'.$order['order_id']) ?>" class="hover-uorange"><?php echo lang('view_order');?></a></p>
                        
                        <?php if($order['flg_repay']){ ?>
                            <P><a href="<?php echo genURL('repay/'.$order['order_id']) ?>" class="hover-uorange btn-complete" dom="repay" topay="" reorder="" ajax=""><?php echo lang('complete_your_payment') ?></a></P>
                        <?php } ?>

                        <?php if(!in_array($order['order_status'],array(OD_CREATE,OD_PAYING,OD_CANCEL))){ ?>
                    	   <p><a href="<?php echo genURL('review_create') ?>" class="hover-uorange"><?php echo lang('write_review') ?></a></p>
                        <?php } ?>
                        
                    </td>
                </tr>
                <?php } ?>
            </tbody>
            </table>
            <div class="show-pager"><?php include dirname(__FILE__).'/common/pagination.php'; ?></div>
		<?php } ?>
    </div>
</div>
<!--main end-->
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script src="<?php echo RESOURCE_URL ?>js/order_list.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>