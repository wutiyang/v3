<?php if(isset($pagination)){ ?>


<?php
$current = $pagination['current_page'];
$href = $pagination['href'];
$default_href = $pagination['default_href'];
$total = $pagination['total_page'];
?>

<?php if($current == 1){ ?>
	<a class="p_previous_un" href="javascript:void(0)">&lt;&nbsp;<?php echo lang('back_up_page') ?></a>
<?php }elseif($current == 2){ ?>
	<a class="p_previous" href="<?php echo $default_href; ?>">&lt;&nbsp;<?php echo lang('back_up_page') ?></a>
<?php }else{ ?>
	<a class="p_previous" href="<?php echo sprintf($href,($current-1)); ?>">&lt;&nbsp;<?php echo lang('back_up_page') ?></a>
<?php } ?>

<?php if($total <= 10){ ?>
	<?php for($i=1;$i<=$total;$i++){ ?>
		<?php if($current == $i){ ?>
			<a class="current" href="javascript:void(0)"><?php echo $i?></a>
		<?php }else{ ?>
			<a<?php echo ($current-1==$i?' rel="prev"':'').($current+1==$i?' rel="next"':'') ?> href="<?php if($i == 1) echo $default_href;else echo sprintf($href,$i) ?>"><?php echo $i ?></a>
		<?php } ?>
	<?php } ?>
<?php }elseif($current <=4){ ?>
	<?php for($i=1;$i<=9;$i++){ ?>
		<?php if($current == $i){ ?>
			<a class="current" href="javascript:void(0)"><?php echo $i?></a>
		<?php }else{ ?>
			<a<?php echo ($current-1==$i?' rel="prev"':'').($current+1==$i?' rel="next"':'') ?> href="<?php if($i == 1) echo $default_href;else echo sprintf($href,$i) ?>"><?php echo $i ?></a>
		<?php } ?>
	<?php } ?>
	...
	<a href="<?php echo sprintf($href,$total) ?>"><?php echo $total?></a>
<?php }elseif($current + 5 > $total){ ?>
	<a href="<?php echo sprintf($href,1) ?>">1</a>
	...
	<?php for($i=$total-9;$i<=$total;$i++){ ?>
		<?php if($i == 2) continue; ?>
		<?php if($current == $i){ ?>
			<a class="current" href="javascript:void(0)"><?php echo $i?></a>
		<?php }else{ ?>
			<a<?php echo ($current-1==$i?' rel="prev"':'').($current+1==$i?' rel="next"':'') ?> href="<?php echo sprintf($href,$i) ?>"><?php echo $i ?></a>
		<?php } ?>
	<?php } ?>
<?php }elseif($current + 5 == $total){ ?>
	<a href="<?php echo sprintf($href,1) ?>">1</a>
	...
	<?php for($i=$current-3;$i<=$current+5;$i++){ ?>
		<?php if($current == $i){ ?>
			<a class="current" href="javascript:void(0)"><?php echo $i?></a>
		<?php }else{ ?>
			<a<?php echo ($current-1==$i?' rel="prev"':'').($current+1==$i?' rel="next"':'') ?> href="<?php echo sprintf($href,$i) ?>"><?php echo $i ?></a>
		<?php } ?>
	<?php } ?>
<?php }else{ ?>
	<?php for($i=$current-3;$i<=$current+5;$i++){ ?>
		<?php if($i == $total-1) continue; ?>
		<?php if($current == $i){ ?>
			<a class="current" href="javascript:void(0)"><?php echo $i?></a>
		<?php }else{ ?>
			<a<?php echo ($current-1==$i?' rel="prev"':'').($current+1==$i?' rel="next"':'') ?> href="<?php echo sprintf($href,$i) ?>"><?php echo $i ?></a>
		<?php } ?>
	<?php } ?>
	...
	<a href="<?php echo sprintf($href,$total) ?>"><?php echo $total?></a>
<?php } ?>

<?php if($current == $total){ ?>
	<a class="p_next_un" href="javascript:void(0)"><?php echo lang('page_next') ?>&nbsp;></a>
<?php }else{ ?>
	<a class="p_next" href="<?php echo sprintf($href,($current+1)) ?>"><?php echo lang('page_next') ?>&nbsp;></a>
<?php } ?>



<?php } ?>
