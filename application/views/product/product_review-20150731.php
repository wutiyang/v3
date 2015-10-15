<div class="recent detail-tab" id="detail-tab2">
        <div class="recent-grade">
            <i class="star<?php echo $star_num?>"></i>
            <span><b><?php echo $average_star_num?></b>
            <?php if(isset($review_nums) && $review_nums>0){ ?>
            (<?php echo $review_nums?>)
            <?php } ?>
            </span>
            <a class="icon-view" id="writeView" href="javascript:;">Write a Review</a>And Get Rewords!
            <?php 
            if(isset($review_nums) && $review_nums>0){ 
			?>
            <a class="view-all" href="<?php echo genURL("review/".$product_base_info['product_url'])?>">See All <?php echo $review_nums?> Reviews >></a>
            <?php 
			} 
			?>
        </div>
        <!-- grade end -->
        <!-- hover write -->
        <div class="write show">
            <h3>Write a Review<i class="del"></i></h3>
            <form class="remark" action="" method="post">
                <div class="rat">
                    <label><i></i>Rating:</label>
                    <span><i class="star10"></i><i class="star5 rating"></i></span>
                </div>
                <div class="tips">
                    <label><i></i>Title</label>
                    <input class="title-it" type="text">
                    <em class="error-tit">Title is required</em>
                </div>
                <div class="tips">
                    <label class="mess"><i></i>Review:</label>
                    <textarea cols="30" rows="10" class="review-it" id="reviewTxt" name=""></textarea> 
                </div>
                <div class="errtext">
                    <p class="tips">
                        <em class="error-tit"><i class="error-icon"></i>content is required</em>
                        <span><i class="num-to">0</i>-<i class="num">1000</i> words</span>
                    </p>
                    <input type="button" value="Submit Review" class="sub icon-view" id="sub-btn">
                    <a class="view-a" href="">Write Reviews, and Easily Make Money. Join Our Make Money Program</a>
                </div>
            </form>
            <div class="success hide">
                <p>Thank you for your  review！<br>Hope  other customer  will be helped by your review.</p>
                <a class="icon-view ok" id="askOk" href="javascript:;">OK</a>
            </div>
        </div>
        <!-- hover write end -->
        <div class="mark">
            <div class="recent-tit">
                <h3>Recent Reviews</h3>
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
                        <p>Was this review helpful?</p>
                        <p class="praise">
                            <a class="like" href="javascript:;"><i class="icon-like"></i><em>(<?php echo $review_v['review_count_helpful']?>)</em></a>
                            <a class="dislike" href="javascript:;"><i class="icon-dis"></i><em>(<?php echo $review_v['review_count_nothelpful']?>)</em></a>
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
                There are no review yet,be the first to<a class="init-vie" href="javascript:;" id="markId">Write a review</a>
            </div>
            <?php
            }
            ?>
        </div>
        <!-- mark end -->
    </div>