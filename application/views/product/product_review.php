<div class="recent detail-tab" id="detail-tab2">
        <div class="recent-grade">
            <i class="star<?php echo $star_num?>"></i>
            <span><b><?php echo $average_star_num?></b>
            <?php if(isset($review_nums) && $review_nums>0){ ?>
            (<?php echo $review_nums?>)
            <?php } ?>
            </span>
            <a class="icon-view" id="writeView" href="javascript:void(0);" url="<?php echo genURL('review_create/index',false,array('product_id'=>$product_base_info['product_id']));?>"><?php echo lang('write_a_review');?></a><?php //echo lang('and_get_rewards');?>
            <span class="hint"><?php echo lang('and');?> <?php echo $notes['rewards_note']['content'];?>
                <em class="describe">
                    <div class="popBox">
                        <div class="con">
                            <p>
	                            <?php echo lang('review_earn_x_rewards_tips');?>
	                            <br><span class="red">*<?php echo lang('effective_review');?> :</span><?php echo lang('effective_review_desc');?>
                            </p>
                        </div>   
                    </div>
                </em>
            </span>
            <?php 
            if(isset($review_nums) && $review_nums>0){ 
			?>
            <a class="view-all" href="<?php echo genURL("review/".$product_base_info['product_url'])?>"><?php echo sprintf(lang('see_all_xxx_reviews'),$review_nums);?> >></a>
            <?php 
			} 
			?>
        </div>
        <!-- grade end -->
        <!-- hover write -->
        <div class="write show">
            <h3><?php echo lang('write_a_review');?><i class="del"></i></h3>
            <form class="remark" action="" method="post">
                <div class="rat">
                    <label><i></i><?php echo lang('rating');?>:</label>
                    <span><i class="star10"></i><i class="star5 rating"></i></span>
                </div>
                <div class="tips">
                    <label><i></i><?php echo lang('text_title');?>:</label>
                    <input class="title-it" type="text">
                    <em class="error-tit"><?php echo lang('title_is_required');?></em>
                </div>
                <div class="tips">
                    <label class="mess"><i></i><?php echo lang('review');?>:</label>
                    <textarea cols="30" rows="10" class="review-it" id="reviewTxt" name=""></textarea> 
                </div>
                <div class="errtext">
                    <p class="tips">
                        <em class="error-tit"><i class="error-icon"></i><?php echo lang('content_is_required');?></em>
                        <span><i class="num-to">0</i>-<i class="num">1000</i> words</span>
                    </p>
                    <input type="button" value="<?php echo lang('submit_review');?>" class="sub icon-view" id="sub-btn">
                    <a class="view-a" href=""><?php echo lang('write_review_earn_money_tips');?></a>
                </div>
            </form>
            <div class="success hide">
                <p><?php echo lang('thank_you_for_review_tips');?></p>
                <a class="icon-view ok" id="askOk" href="javascript:;">OK</a>
            </div>
        </div>
        <!-- hover write end -->
        <div class="mark">
            <div class="recent-tit">
                <h3><?php echo lang('recent_reviews');?></h3>
                <input type="hidden" id="eachbuyer_logState" value="<?php if(isset($user) && $user!=false){ echo 1;}else {echo 0;}?>" >
                <input type="hidden" id="eachbuery_buyTimes" value="1" >
                <input type="hidden" id="eachbuery_review" value="0" >
                <!-- p>Sort by<select><option>Most helpful</option><option>Oldest to newest</option><option>Newest to oldest</option><option>High to low rating</option><option>Low to high rating</option></select></p-->
            </div>
            <?php 
            if(isset($review_list) && count($review_list)){
            ?>
            <ul class="recent-list clear" linkUrl="" dislikeUrl="">
            	<?php 
            	foreach ($review_list as $review_k=>$review_v){
            	?>
            	<li indexid="<?php echo $review_v['review_id']?>">
                    <div class="pic-lt">
                        <h5><?php echo $review_v['review_title']?></h5>
                        <p><i class="star<?php echo $review_v['review_score']?>"></i><span>by <?php echo $review_v['user_name']?></span></p>
                        <p><?php echo date("F d.Y",strtotime($review_v['review_time_lastmodified']));?></p>
                    </div>
                    <div class="cont-ct">
                        <p><?php echo $review_v['review_content']?></p>
                    </div>
                    <div class="helpful-rt">
                        <p><?php echo lang('was_this_review_helpful_tips');?></p>
                        <p class="praise">
                            <a class="like" href="javascript:;"><i processing="false" productId="<?php echo $review_v['product_id'];?>" class="icon-like <?php if($review_v['like'] == 0) echo 'unlike';?>"></i><em>(<?php echo $review_v['review_count_helpful']?>)</em></a>
                            <a class="dislike" href="javascript:;"><i processing="false" productId="<?php echo $review_v['product_id'];?>" class="icon-dis <?php if($review_v['unlike'] == 0) echo 'unlike';?>"></i><em>(<?php echo $review_v['review_count_nothelpful']?>)</em></a>
                        </p>
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
                <?php echo lang('no_review_tips');?><a class="init-vie" href="javascript:void(0);" rel="nofollow" id="markId" url="<?php echo genURL('review_create/index',false,array('product_id'=>$product_base_info['product_id']));?>"><?php echo lang('write_a_review');?></a>
            </div>
            <?php
            }
            ?>
        </div>
        <!-- mark end -->
    </div>
