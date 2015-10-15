<div class="sidebar">
		<ul class="aside-list" id="accordion">
            <li class="list-par">
                <a href="javascript:;" class="level1"><?php echo lang('help_center');?></a>
                <ul class="collapse">
                    <li <?php if($name=='about_us') echo 'class="on"';?>><a href="<?php echo genURL('about_us.html')?>"><?php echo lang('about_us');?></a></li>
                    <li <?php if($name=='contact_us') echo 'class="on"';?>><a href="<?php echo genURL('contact_us.html')?>"><?php echo lang('contact_us');?></a></li>
                    <li <?php if($name=='payment_method') echo 'class="on"';?>><a href="<?php echo genURL('payment_method.html')?>"><?php echo lang('payment_method');?></a></li>
                    <li <?php if($name=='shipping_method_guide') echo 'class="on"';?>><a href="<?php echo genURL('shipping_method_guide.html')?>"><?php echo lang('shipping_method_guide');?></a></li>
                    <li <?php if($name=='faq') echo 'class="on"';?>><a href="<?php echo genURL('faq.html')?>"><?php echo lang('faq');?></a></li>
                    <li <?php if($name=='terms_and_conditions') echo 'class="on"';?>><a href="<?php echo genURL('terms_and_conditions.html')?>"><?php echo lang('terms_and_conditions');?></a></li>
                    <li <?php if($name=='return_policy') echo 'class="on"';?>><a href="<?php echo genURL('return_policy.html')?>"><?php echo lang('return_policy');?></a></li>
                    <li <?php if($name=='privacy_policy') echo 'class="on"';?>><a href="<?php echo genURL('privacy_policy.html')?>"><?php echo lang('privacy_policy');?></a></li>
                </ul>
            </li>
            <li class="list-par">
                <a href="javascript:;" class="level1"><?php echo lang('make_money_with_us');?></a>
                <ul class="collapse">
                    <li <?php if($name=='affiliate_program') echo 'class="on"';?>><a href="<?php echo genURL('affiliate_program.html')?>"><?php echo lang('affiliate_program');?></a></li>
                </ul>
            </li>
            <li class="list-par">
                <a href="<?php echo genURL('wholesale.html')?>" class="level1"><?php echo lang('wholesale');?></a>
            </li>
        </ul>
	</div>