<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="resource/css/order_list.css">
<link rel="stylesheet" type="text/css" href="resource/css/write-review.css">
<div class="wrap breadcrumbs">
    <i class="icon-home">&nbsp;</i>
    <a href="">Home</a>
    <i class="icon-arr-right">&nbsp;</i>
    <a href="">My Account</a>
    <i class="icon-arr-right">&nbsp;</i>
    <span><a href="">My Order</a></span>
    <i class="icon-arr-right">&nbsp;</i>
    <span><a href="">Manage Reviews</a></span>
</div>
<div class="main" id="review">
	<?php include dirname(__FILE__).'/account/nav.php'; ?>
	<!-- content start -->
	<div class="content">
        <h3 class="review-tit">Manage Review</h3>
        <div class="tab-tit">
            <a href="javascript:void(0)" class="on">Write a Review</a>
            <a href="javascript:void(0)" class="view-rev">My Reviews<span>(11)</span></a>
        </div>
        <div class="wrapper">
            <!-- write a review start -->
            <div class="write-rev">
                <div class="write-list clear">
                    <img src="resource/images/wish-list/pic.jpg" class="list-img">
                    <div class="list-info">
                        <div class="intro">
                            <div>
                                <h5>Tattoos Top Tattoo Machine Complete Set Tattoo Kit 3 Machine Guns InkSet Tattoo Kit 3 Machine Guns Ink</h5>
                                <h5>USD:<em>$89.99</em></h5>
                            </div>
                            <p>April 15, 2015</p>
                        </div>
                        <p class="size">Size:<em>L</em>Color:<span>Blue</span></p>
                        <form class="remark" id="myForm" action="" method="post">
                            <p class="tips">
                                <label class="hide"><i></i>Title</label>
                                <input type="text" class="title-it" id="">
                                <em class="error-tit">Title is required</em>
                            </p>
                            <p class="tips2">
                                <label class="mess"><i></i>Review:</label>
                                <textarea cols="30" rows="10" class="review-it" id="reviewTxt" name=""></textarea> 
                                <em class="error-tit"><i class="error-icon"></i>content is required</em>
                            </p>
                            <p class="errtext">
                                <label>&nbsp;</label>
                                <span class="tomit">
                                    <input type="button" value="Submit Review" class="sub" id="sub-btn">
                                    <em><i class="num-to">0</i>-<i class="num">1000</i> words</em>
                                </span>
                            </p>
                        </form>
                        <!-- success start -->
                        <div class="success hide">
                            <p>Thank you for your  review !</p>
                            <p>Hope  other customer  will be helped by your review.</p>
                        </div>
                        <!-- success end -->
                    </div>
                </div>
                <div class="write-list clear">
                    <img src="resource/images/wish-list/pic.jpg" class="list-img">
                    <div class="list-info">
                        <div class="intro">
                            <div>    
                                <h5>Tattoos Top Tattoo Machine Complete Set Tattoo Kit 3 Machine Guns InkSet Tattoo Kit 3 Machine Guns Ink</h5>
                                <h5>USD:<em>$89.99</em></h5>
                            </div>    
                            <p>April 15, 2015</p>
                        </div>
                        <p class="size">Size:<em>L</em>Color:<span>Blue</span></p>
                        <form class="remark"  action="" method="get">
                            <p class="tips">
                                <label><i></i>Title</label>
                                <input type="text" class="title-it" id="titleIt" name="title">
                                <em class="error-tit">Title is required</em>
                            </p>
                            <p class="tips2">
                                <label class="mess"><i></i>Review:</label>
                                <textarea cols="30" rows="10" class="review-it" name="textarea"></textarea> 
                                <em class="error-tit"><i class="error-icon"></i>content is required</em>
                            </p>
                            <p class="errtext">
                                <label></label>
                                <span class="tomit">
                                    <input type="button" class="sub" id="sub" value="Submit Review">
                                    <em class=""><i class="num-to">0</i>-<i class="num">1000</i> words</em>
                                </span>
                            </p>
                        </form>
                        <!-- success start -->
                        <div class="success hide">
                            <p>Thank you for your  review !</p>
                            <p>Hope  other customer  will be helped by your review.</p>
                        </div>
                        <!-- success end -->
                    </div>
                </div>
            </div>
            <!-- write a review end -->
            <!-- review start -->
            <div class="write-rev">
                <div class="write-list clear">
                    <a href="" class="list-img"><img src="resource/images/wish-list/pic.jpg"></a>
                    <div class="list-info">
                        <div class="intro">
                            <div>
                                <h5>Tattoos Top Tattoo Machine Complete Set Tattoo Kit 3 Machine Guns InkSet Tattoo Kit 3 Machine Guns Ink</h5>
                            </div>
                            <p>April 15, 2015</p>
                        </div>
                        <p class="size">Size:<em>L</em></p>
                        <div class="evaluate">
                            <p>“Full of love, very satisfied!”</p>
                        </div>
                    </div>
                </div>
                <div class="write-list clear">
                    <a href="" class="list-img"><img src="resource/images/wish-list/pic.jpg"></a>
                    <div class="list-info">
                        <div class="intro">
                            <div>    
                                <h5>Tattoos Top Tattoo Machine Complete Set Tattoo Kit 3 Machine Guns InkSet Tattoo Kit 3 Machine Guns Ink</h5>
                            </div>    
                            <p>April 15, 2015</p>
                        </div>
                        <p class="size">Size:<em>L</em></p>
                        <div class="evaluate">
                            <p>“Full of love, very satisfied!”</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- review end -->
            
        </div>
        

		<!-- 分页 start -->
		<div class="show-pager">
        	<a href="javascript:;" class="p-previous-un">Previous</a><a href="javascript:void(0)" class="current">1</a><a href="" rel="next">2</a><a href="">3</a>......<a href="">6</a><a href="" class="p-next">Next</a>
		</div>
		<!-- 分页 end -->

	</div>
	<!-- content end -->
</div>
<script type="text/javascript" src="resource/js/write-review.js"></script>
<?php include dirname(__FILE__).'/common/footer.php'; ?>
</body>
</html>
