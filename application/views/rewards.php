<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/order_list.css?v=<?php echo STATIC_FILE_VERSION ?>">
<div class="wrap breadcrumbs">
    <i class="icon-home">&nbsp;</i>
    <a href="<?php echo genURL('/')?>"><?php echo lang('home');?></a>
    <i class="icon-arr-right">&nbsp;</i>
    <a href="<?php echo genURL('order_list');?>"><?php echo lang('my_account');?></a>
    <i class="icon-arr-right">&nbsp;</i>
    <span><?php echo lang('my_rewards');?></span>
</div>
<div class="main rewards" id="">
	<?php include dirname(__FILE__).'/account/nav.php'; ?>
 	<div class="content">
        <?php if($user['customer_rewards'] > 0){ ?>
        <div class="success"><i></i><?php echo sprintf(lang('rewards_notice1'),$user['customer_rewards']) ?><br /><?php echo sprintf(lang('rewards_notice2'),$user['customer_rewards_rate'].'%') ?></div>
        <?php } ?>
   		<h3 class="column-now"><?php echo lang('my_rewards');?></h3>
        <div class="balance clearfix">
            <p><?php echo lang('reward_balance');?>: <span>$ <?php echo $user['customer_rewards'];?></span></p>
            <!--
            <a href="javascript:void(0)" class="earn"><?php echo lang('earn_more_rewards');?></a>
            -->
        </div>
        <h4 class="reward-tit"><?php echo lang('transaction_history');?></h4>
    	<table class="order-table rewards-tab">
        	<thead>
            	<tr>
                	<th class="t1"><?php echo lang('date');?></th>
                    <th class="t2"><?php echo lang('amount');?></th>
                    <th class="t3"><?php echo lang('text_description');?></th>
                </tr>
            </thead>
            <tbody>
            	<?php foreach ($rewardsHistoryList as $rewardsHistory):?>
            	<tr>
                	<td class="t1"><?php echo date('M d,Y H:i:s',strtotime($rewardsHistory['rewards_history_time_create']));?></td>
                    <td class="t2">
                    	<?php if(in_array($rewardsHistory['rewards_history_type'], array(0,1,3,5))):?>
                    	<i class="add"></i>
                    	<?php else:?>
                    	<i class="minus"></i>
                    	<?php endif;?>
                    	<?php echo currencyAmount($rewardsHistory['rewards_history_value']);?>
                    </td>
                    <td class="t3">
                    <?php echo sprintf(lang('rewards_description'.$rewardsHistory['rewards_history_type']),$rewardsHistory['order_code']);?>
                    </td>
                </tr>
                <?php endforeach;?>
                <?php if($nums > $pagesize):?>
                <tr class="last">
                    <td class="t1">&nbsp;</td>
                    <td class="t2">&nbsp;</td>
                    <td class="t3">
                        <button type="button" id="more" title="More" class="btn34-gray btn-w86 cancel">
                            <span class="btn-right"><span class="btn-text"><?php echo lang('more');?></span></span>
                        </button>
                    </td>
                </tr>
                <?php endif;?>
                <input type="hidden" name="page" id="page" value="<?php if($nums <= $pagesize) echo -1; else echo 2;?>"/>
            </tbody>
        </table>
        <h3 class="question-tit"><?php echo lang('questions');?></h3>
        <ul class="reward-question">
            <li class="clearfix">
                <div class="que-lt">
                    <h4><?php echo lang('rewards_question1');?></h4>
                    <p><?php echo lang('rewards_answer1');?></p>
                </div>
                <div class="que-rt">
                    <h4><?php echo lang('rewards_question2');?></h4>
                    <p><?php echo lang('rewards_answer2');?></p>
                </div>
            </li>
            <li class="clearfix">
                <div class="que-lt">
                    <h4><?php echo lang('rewards_question3');?></h4>
                    <p><?php echo lang('rewards_answer3');?></p>
                </div>
                <div class="que-rt">
                    <h4><?php echo lang('rewards_question4');?></h4>
                    <p><?php echo lang('rewards_answer4');?></p>
                </div>
            </li>

        </ul>
        <!-- 
        <div class="show-pager">
        	<a href="javascript:;" class="p-previous-un">Previous</a><a href="javascript:void(0)" class="current">1</a><a href="" rel="next">2</a><a href="">3</a>......<a href="">6</a><a href="" class="p-next">Next</a>
		</div>
         -->
    </div>
</div>
<!--main end-->
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/html" id="rewardsTips">
<div class="per-confirm" id="Pop">
    <a href="javascript:;" title="Close" class="close" id="close"><?php echo lang('close');?></a>
    <h3><?php echo lang('earn_more_rewards_tips');?></h3>
    <div class="btn-form">
        <a class="btn34-gray btn-text" href="<?php echo genURL('/');?>"><?php echo lang('go_shopping');?></a>
        <span class="or"><?php echo lang('or_upper');?></span>
        <a class="btn34-org btn-text" href="<?php echo genURL('review_create');?>"><?php echo lang('write_a_review');?></a>
    </div>
</div>
</script>
<script src="<?php echo RESOURCE_URL ?>js/order_list.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/common/utils.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/login.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>