<div class="sidebar">
        <div class="sub-nav">
            <h3 class="title"><?php echo lang('my_account_upper');?></h3>
            <ul>
                <li <?php if(isset($currentPage) && $currentPage == 'order') echo 'class="current"';?>><a href="<?php echo genURL('order_list');?>"><?php echo lang('my_order');?></a></li>
                <li <?php if(isset($currentPage) && $currentPage == 'rewards') echo 'class="current"';?>><a href="<?php echo genURL('rewards');?>"><?php echo lang('my_rewards');?><span class="icon-rw-new"><em><?php echo lang('new');?></em></span></a></li>
                <li <?php if(isset($currentPage) && $currentPage == 'review') echo 'class="current"';?>><a href="<?php echo genURL('review_create');?>"><?php echo lang('manage_review');?> </a></li>
                <li <?php if(isset($currentPage) && $currentPage == 'wishlist') echo 'class="current"';?>><a href="<?php echo genURL('wishlist');?>"><?php echo lang('my_wishlist');?></a></li>
                <li <?php if(isset($currentPage) && $currentPage == 'address') echo 'class="current"';?>><a href="<?php echo genURL('manage_address_book');?>"><?php echo lang('manage_address_book');?> </a></li>
                <li <?php if(isset($currentPage) && $currentPage == 'settings') echo 'class="current"';?>><a href="<?php echo genURL('account_settings');?>"><?php echo lang('account_setting');?></a></li>
                <li <?php if(isset($currentPage) && $currentPage == 'newsletter') echo 'class="current"';?>><a href="<?php echo genURL('newsletter');?>"><?php echo lang('newsletter_subscriptions');?></a></li>
                <!-- 
                <li><a href="">Make Money Program</a></li>
                <li><a href="">My Tickets</a></li>
                 -->
            </ul>
        </div>
        <div class="activity">
            <?php if(!empty($image_ad)):?>
        	<a href="<?php echo $image_ad['url'];?>"><img src="<?php echo COMMON_IMAGE_URL.$image_ad['img'];?>" alt="<?php echo $image_ad['alt'];?>"></a>
            <?php endif;?>
        </div>
    </div>