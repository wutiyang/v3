	<!--<div class="nav-categray nav-categray-show">-->
	<div class="nav-categray " id="navCategray">
	<div class="cate-title"><a href="javascript:;">All Categories<i class="icon-allCate-arrow"></i></a></div>
	<div  class="cate-list">
	<ul class="big-list clearfix" id="categrayAll">
	<?php foreach ($cate_tree as $val){?>
	<li id="menu_15291" class="list">
		<div class="li"><a href="<?php echo genURL($val['category_url'],true) ?>"><?php echo $val['category_description_name']?></a><span>(<?php echo $val['category_pid_count']?>)</span><i class="icon-arr-right"></i></div>
			<div class="sub-list clearfix">
			<div class="sub-border"><div class="sub-border-top"></div></div>
			<div class="popup-logo"></div>
			<div class="sub-padding">
			<?php 
			if(isset($val['col_one'])){
			?>
			<?php foreach ($val['col_one'] as $ks=>$vs){?>
			<!--一列-->
			<ul class="column">
			<li data-id="15905" class="itemMenuName level1 item1"><a href=""><?php echo $vs['category_description_name']?></a></li>   
			<?php 
			if(!empty($vs['children'])){
				foreach ($vs['children'] as $kt=>$vt){
			?>
			<li data-id="15905" class="itemMenuName level2 item1"><a href=""><?php echo $vt['category_description_name']?></a></li>
			<?php
				}
			}
			?>
			</ul>
			<?php 
			}
			?>
			<!--一列 over-->
			<?php 
			if(isset($val['col_two'])){
			?>
			<ul class="column">
			<li data-id="15905" class="itemMenuName level1 item1">
			<a href="">Home &amp; Garden</a></li>   
			<li data-id="15905" class="itemMenuName level2 item1">
			<a href="">Home &amp; Garden</a></li>   
			</ul>
			<?php			
			}
			?>
			<?php 
			if(isset($val['col_three'])){
			?>
			<ul class="column">
			<li data-id="15905" class="itemMenuName level1 item1">
			<a href="">Home &amp; Garden</a></li>   
			<li data-id="15905" class="itemMenuName level2 item1">
			<a href="">Home &amp; Garden</a></li>   
			</ul>
			<?php			
			}
			?>
			</div>
			</div>
			<?php } ?>			
	</li>
	<?php } ?>
	</ul>
	</div>
	</div>