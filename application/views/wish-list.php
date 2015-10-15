<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="resource/css/order_list.css">
<link rel="stylesheet" type="text/css" href="resource/css/wish-list.css">
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
<div class="main" id="wish-list">
	<?php include dirname(__FILE__).'/account/nav.php'; ?>
	<!-- content start -->
	<div class="content">
        <div class="wish-tit clear">
            <h3 class="points-tit">My wish list</h3>
            <div class="head-sns">
                <a href="" class="share">share</a>
                <a href="" class="img email" title="E-mail"></a>
                <a href="" class="img fb" title="facebook"></a>
                <a href="" class="img tw" title="twitter"></a>
            </div>
        </div>
        <div class="list">
            <!--index为id号-->
            <div class="list-pro clear" index="1">
                <a href="" class="list-img"><img src="resource/images/wish-list/pic.jpg"></a>
                <div class="list-info">
                    <div class="intro">
                        <h5><a href="">Tattoos Top Tattoo Machine Complete Set Tattoo Kit 3 Machine Guns InkSet Tattoo Kit 3 Machine Guns Ink Machine Guns InkSet Tattoo Kit 3 Machine Guns Ink</a></h5>
                        <span>April 15, 2015</span>
                    </div>
                    <p class="size">Size:L</p>
                    <div class="tocart-btn">
                        <input type="button" class="add" value="Add to Cart">
                        <input type="button" class="del" value="Delete">
                    </div>
                </div>
            </div>
            <div class="list-pro clear" index="2">
                <a href="" class="list-img"><img src="resource/images/wish-list/pic.jpg"></a>
                <div class="list-info">
                    <div class="intro">
                        <h5><a href="">Tattoos Top Tattoo Machine Complete Set Tattoo Kit 3 Machine Guns InkSet Tattoo Kit 3 Machine Guns Ink Machine Guns InkSet Tattoo Kit 3 Machine Guns Ink</a></h5>
                        <span>April 15, 2015</span>
                    </div>
                    <p class="size">Size:L</p>
                    <div class="tocart-btn">
                        <input type="button" class="add" value="Add to Cart">
                        <input type="button" class="del" value="Delete">
                    </div>
                </div>
                
            </div>
            <div class="list-pro clear" index="3">
                <a href="" class="list-img"><img src="resource/images/wish-list/pic.jpg"></a>
                <div class="list-info">
                    <div class="intro">
                        <h5><a href="">Tattoos Top Tattoo Machine Complete Set Tattoo Kit 3 Machine Guns InkSet Tattoo Kit 3 Machine Guns Ink Machine Guns InkSet Tattoo Kit 3 Machine Guns Ink</a></h5>
                        <span>April 15, 2015</span>
                    </div>
                    <p class="size">Size:L</p>
                    <div class="tocart-btn">
                        <input type="button" class="add" value="Add to Cart">
                        <input type="button" class="del" value="Delete">
                    </div>
                </div>
                
            </div>
            <div class="list-pro clear" index="4">
                <a href="" class="list-img"><img src="resource/images/wish-list/pic.jpg"></a>
                <div class="list-info">
                    <div class="intro">
                        <h5><a href="">Tattoos Top Tattoo Machine Complete Set Tattoo Kit 3 Machine Guns InkSet Tattoo Kit 3 Machine Guns Ink Machine Guns InkSet Tattoo Kit 3 Machine Guns Ink</a></h5>
                        <span>April 15, 2015</span>
                    </div>
                    <p class="size">Size:L</p>
                    <div class="tocart-btn">
                        <input type="button" class="add" value="Add to Cart">
                        <input type="button" class="del" value="Delete">
                    </div>
                </div>
                
            </div>
        </div>

<script type="text/html" id="addToCartBox">
<div class="ab-confirm" id="Pop">
    <h3><i></i>Added to Cart</h3>
    <div>
        <a href="/cart" class="view" id="view">View Cart</a>
        <a href="javascript:void(0)" class="cancel">Close</a>
    </div>
</div>
</script>
<script type="text/html" id="delCartBox">
 <div class="del-confirm" id="Pop">
    <h4>Are you sure you want to delete this item?</h4>
    <div>
        <button id="delete" class="btn34-org" title="Delete" type="button">
            <span class="btn-right"><span class="btn-text">Delete</span></span>
        </button>
        <button id="delete" class="btn34-gray cancel" title="Cancel" type="button">
            <span class="btn-right"><span class="btn-text">Cancel</span></span>
        </button>
    </div>
 </div>
</script>

		<!-- 分页 start -->
		<div class="show-pager">
        	<a href="javascript:;" class="p-previous-un">Previous</a><a href="javascript:void(0)" class="current">1</a><a href="" rel="next">2</a><a href="">3</a>......<a href="">6</a><a href="" class="p-next">Next</a>
		</div>
		<!-- 分页 end -->

	</div>
	<!-- content end -->
</div>
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/javascript" src="resource/js/common/utils.js"></script>
<script type="text/javascript" src="resource/js/wish-list.js"></script>
</body>
</html>
