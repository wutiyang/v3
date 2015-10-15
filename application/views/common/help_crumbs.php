<div class="wrap breadcrumbs">
    <i class="icon-home">&nbsp;</i>
    <a href="<?php echo genURL('/')?>"><?php echo lang('home');?></a>
    <i class="icon-arr-right">&nbsp;</i>
    <a href="<?php echo genURL('help.html')?>"><?php echo lang('help');?></a>
    <?php 
    if($name!='help'){
    ?>
    <i class="icon-arr-right">&nbsp;</i>
    <span><?php echo $name;?></span>
    <?php
    }
    ?>
    
</div>