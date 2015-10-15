<div class="Customer-QA detail-tab" id="detail-tab3">
        <div class="icon-que">
            <a class="icon-view" id="askView" href="javascript:;">Ask a Question </a>
        </div>
        <!-- icon-que end -->
<script type="text/html" id="questionPop">
            <div class="ask show" id="Pop" style="display:block;">
            <a href="javascript:;" title="Close" class="close" id="close">关闭</a>
            <h3>Ask a new question</h3>
            <form class="remark" action="<?php echo genURL('qna/addqna')?>" method="post">
                <!-- div class="rat">
                    What would you like to ask about?
                    <label><input type="radio" name="ques">Product description</label>
                    <label><input type="radio" name="ques">Shipping or payment</label>
                    <label><input type="radio" name="ques">Customer service issues</label>
                </div-->
                <div class="tips">
                    <input type="hidden" name="qnapid" value="<?php echo $product_base_info['product_id']?>" />
                    <label><i></i>Your question:</label>
                    <input class="title-it" type="text" name="title" id="question-tit">
                    <em class="error-tit"></em>
                </div>
                <div class="tips">
                    <label class="mess"><i></i><span>Add additional details:</span></label>
                    <textarea cols="30" rows="10" class="review-it" id="reviewTxt" name="content"></textarea> 
                </div>
                <div class="errtext">
                    <p class="tips">
                        <em class="error-tit"><i class="error-icon"></i>content is required</em>
                        <span><i class="num-to">0</i>-<i class="num">1000</i> words</span>
                    </p>
                    <input type="button" value="Submit Question" class="sub icon-view" id="sub-btn">
                    <a class="view-a" href="">Cancel</a>
                </div>
            </form>
            <div class="success hide">
                <p>Thank you for your  review！<br>Hope  other customer  will be helpe</p>
                <a class="icon-view ok" id="writeOk" href="javascript:;">OK</a>
            </div>
        </div>
</script>
        <div class="question">
            <div class="recent-tit">
                <h3>Recent Questions</h3>
                <?php 
                if(count($qna_list)){
                ?>
                <a class="view-all" href="<?php echo genURL("qna/".$product_base_info['product_url'])?>">See All <?php echo count($qna_list);?> Questions>></a>
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
                        <p class="ask-tit"><strong><?php echo $qna_v['qna_title']?></strong></p>
                        <p class="pose"><?php echo $qna_v['qna_content']?></p>
                        <p class="by">by Johnny on <?php echo date("F d,y",strtotime($qna_v['qna_time_create']));?></p>
                    </div>
                    <div class="answer"><i class="a"></i>
                        <p class="pose"><?php echo $qna_v['qna_answer']?></p>
                        <p class="by">by Johnny on <?php echo date("F d,y",strtotime($qna_v['qna_time_reply']));?></p>
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
                There are no question yet,be the first to<a class="init-vie" id="askOk" href="javascript:;">Ask a question</a>
            </div>            
            <?php	
            }
            ?>
            <ul class="faqs"></ul>
        </div>
        <!-- question end -->
    </div>