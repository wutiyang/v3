<div class="Customer-QA detail-tab" id="detail-tab3">
        <div class="icon-que">
            <a class="icon-view" id="askView" href="javascript:;"><?php echo lang('ask_a_question');?></a>
        </div>
        <!-- icon-que end -->
<script type="text/html" id="questionPop">
            <div class="ask show" id="Pop" style="display:block;">
            <a href="javascript:;" title="Close" class="close" id="close">关闭</a>
            <h3>Ask a new question</h3>
            <form class="remark" action="<?php echo genURL('product/addqna')?>" method="post">
                <!-- div class="rat">
                    What would you like to ask about?
                    <label><input type="radio" name="ques"><?php echo lang('product_description');?></label>
                    <label><input type="radio" name="ques"><?php echo lang('shipping_or_payment');?></label>
                    <label><input type="radio" name="ques"><?php echo lang('customer_service_issues');?></label>
                </div-->
                <div class="tips">
                    <input type="hidden" name="qnapid" value="<?php echo $product_base_info['product_id']?>" />
                    <label><i></i><?php echo lang('your_question');?>:</label>
                    <input class="title-it" type="text" name="title" id="question-tit">
                    <em class="error-tit"></em>
                </div>
                <div class="tips">
                    <label class="mess"><i></i><span><?php echo lang('add_additional_details');?>:</span></label>
                    <textarea cols="30" rows="10" class="review-it" id="reviewTxt" name="content"></textarea> 
                </div>
                <div class="errtext">
                    <p class="tips">
                        <em class="error-tit"><i class="error-icon"></i><?php echo lang('content_is_required');?></em>
                        <span><i class="num-to">0</i>-<i class="num">1000</i> words</span>
                    </p>
                    <input type="button" value="<?php echo lang('submit_question');?>" class="sub icon-view" id="sub-btn">
                    <a class="view-a" href=""><?php echo lang('cancel');?></a>
                </div>
            </form>
            <div class="success hide">
                <p><?php echo lang('thank_you_for_review_tips');?></p>
                <a class="icon-view ok" id="writeOk" href="javascript:;">OK</a>
            </div>
        </div>
</script>
        <div class="question">
            <div class="recent-tit">
                <h3><?php echo lang('recent_questions');?></h3>
                <?php 
                if(isset($qna_nums) && $qna_nums){
                ?>
                <a class="view-all" href="<?php echo genURL("qna/".$product_base_info['product_url'])?>"><?php echo sprintf(lang('see_all_xxx_questions'),$qna_nums);?>>></a>
                <?php
                }
                ?>
                
            </div>
            <!-- question-tit end -->
            <?php 
            if(count($qna_list)){
            ?>
            <ul class="faqs">
            	<?php 
            	foreach ($qna_list as $qna_k=>$qna_v){
            	?>
            	<li class="que-list">
                    <div class="ask-que"><i class="q"></i>
                        <p class="ask-tit"><strong><?php echo $qna_v['qna_title'];?></strong></p>
                        <p class="pose"><?php echo $qna_v['qna_content'];?></p>
                        <p class="by">by <?php echo $qna_v['customer_name'];?> on <?php echo date("F d,y",strtotime($qna_v['qna_time_create']));?></p>
                    </div>
                    <div class="answer"><i class="a"></i>
                        <p class="pose"><?php echo $qna_v['qna_answer'];?></p>
                        <p class="by">by <?php echo $qna_v['user_name'];?> on <?php echo date("F d,y",strtotime($qna_v['qna_time_reply']));?></p>
                    </div>
                </li>
            	<?php
            	}
            	?>
            </ul>
            <?php
            }else{
            ?>
			<div class="init">
                <?php echo lang('no_question_tips');?><a class="init-vie" id="askOk" href="javascript:;" rel="nofollow"><?php echo lang('ask_a_question');?></a>
            </div>            
            <?php	
            }
            ?>
            <ul class="faqs"></ul>
        </div>
        <!-- question end -->
    </div>
