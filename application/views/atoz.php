<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/atoz&buy.css?v=<?php echo STATIC_FILE_VERSION ?>">
<div class="wrap breadcrumbs">
    <i class="icon-home">&nbsp;</i>
    <a href="<?php echo genURL('/')?>"><?php echo lang('home');?></a>
    <i class="icon-arr-right">&nbsp;</i>
    <span><?php echo $key;?></span>
</div>
<div class="main" id="Atoz">
    <!-- list-main end -->
    <?php include dirname(__FILE__).'/index/banner.php'; ?>
    <div class="list-tit">
        <ul class="list-tab">
        	<?php foreach ($charactersList as $char):?>
            <li <?php if($key == $char) echo 'class="on"';?>><a href="/<?php echo $char;?>.html"><?php echo $char;?></a></li>
            <?php endforeach;?>
            <li <?php if($key == '0-9') echo 'class="on"';?>><a href="/0-9.html">0-9</a></li>
        </ul>
    </div>
    <div class="listBox">
        <div class="list-main">
            <ul class="list-info">
            	<?php foreach ($atozArr as $atoz):?>
                <li><a href="<?php echo $atoz['url'];?>"><?php echo $atoz['name'];?></a></li>
            	<?php endforeach;?>
            </ul>
            <div class="show-pager">
            	<?php include dirname(__FILE__).'/common/pagination.php'; ?>
            </div>
        </div>
    </div>
    
    
</div>
<!--main end-->
<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>/js/atoz.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>

</body>
</html>