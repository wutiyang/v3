<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="resource/css/order_list.css">
<link rel="stylesheet" type="text/css" href="resource/css/points-level.css">
<div class="wrap breadcrumbs">
    <i class="icon-home">&nbsp;</i>
    <a href="">Home</a>
    <i class="icon-arr-right">&nbsp;</i>
    <a href="">My Account</a>
    <i class="icon-arr-right">&nbsp;</i>
    <span><a href="">My Rewards & Level</a></span>
</div>
<div class="main" id="points-level">
	<div class="sidebar">
        <div class="sub-nav">
            <h3 class="title">MY ACCOUNT</h3>
            <ul>
                <li class="current">My Orders</li>
                <li><a href="">My Rewards & Level</a></li>
                <li><a href="">My Tickets</a></li>
                <li><a href="">Manage Reviews</a></li>
                <li><a href="">My WishList</a></li>
                <li><a href="">Manage Address Book</a></li>
                <li><a href="">Account Settings</a></li>
                <li><a href="">Newsletter Subscriptions</a></li>
                <li><a href="">Make Money Program</a></li>
            </ul>
        </div>
        <div class="activity">
        	<a href=""><img src="resource/images/account/img.jpg"></a>
        </div>
    </div>
	<!-- content start -->
	<div class="content">
		<h3 class="points-tit">My Points & Level</h3>
		<div class="points">
			<h4><span>Available points:</span><em>35</em></h4>
			<p>Points Awaiting Validation:<span>236</span></p>
			<p>Total Accumulated Points:<span>35</span></p>
			<div class="level">
				<h4><span>Your VIP Level:</span><em>Bronze</em></h4>
				<div class="level-icon">
					<img src="resource/images/point&level/red-diamond.png" alt="">
					<img src="resource/images/point&level/blue-diamond.png" alt="">
					<img src="resource/images/point&level/green-diamond.png" alt="">
					<img src="resource/images/point&level/gray-diamond.png" alt="">
                    <img src="resource/images/point&level/diamond.png" alt="">
				</div>
			</div>
		</div>
        <h4 class="points-his">Your Shopping Points History</h4>
        <table class="his-tab">
            <thead>
                <th>Point Type</th>
                <th>Points Status</th>
                <th>Action</th>
                <th>Update Time</th>
            </thead>
            <tbody>
                <tr>
                    <td>Order points(2015050800876161)</td>
                    <td class="t2">pending</td>
                    <td class="t3">+4</td>
                    <td class="t4">2015-05-08 12:32:34</td>
                </tr>
            </tbody>
        </table>



		<!-- 分页 start -->
		<div class="show-pager">
        	<a href="javascript:;" class="p-previous-un">Previous</a><a href="javascript:void(0)" class="current">1</a><a href="" rel="next">2</a><a href="">3</a>......<a href="">6</a><a href="" class="p-next">Next</a>
		</div>
		<!-- 分页 end -->

	</div>
	<!-- content end -->
</div>

<?php include dirname(__FILE__).'/common/footer.php'; ?>
</body>
</html>
