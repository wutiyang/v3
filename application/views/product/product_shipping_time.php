<div class="shopping">
                    <span class="shopping-name"><?php echo lang('shipping_time_title');?>：</span>
                    <span class="shopping-time"><?php echo lang('shipping_time_3_7_days');?></span>
                    <span class="shopping-time hide"><?php echo lang('shipping_time_6_10_days');?></span>
                    <span class="shopping-time hide"><?php echo lang('shipping_time_10_20_days');?></span>
                    <div class="expedited select-box" id="Expedited">
                        <div class="select-block">
                            <div class="selected">
                                <a title="" href="javascript:;" rel="nofollow">
                                    <i class="account-icon"></i><span><?php echo lang('shipping_time_expedited_text');?></span>
                                </a>
                                <i class="icon-select-arrow"></i>
                            </div>
                            <div class="drop-box">
                                <ul class="drop-content drop-list">
                                    <li class="hide"><a href="javascript:;"><span><?php echo lang('shipping_time_expedited_text');?></span></a></li>
                                    <li><a href="javascript:;"><span><?php echo lang('shipping_time_Standard_text');?></span></a></li>
                                    <li><a href="javascript:;"><span><?php echo lang('shipping_time_freeshipping_text');?></span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
					<span class="shops-to <?php if(empty($product_warehouse)) echo 'hide';?>"><i class="<?php echo $product_warehouse_class;?>"></i><?php echo lang('shipping_time_ships_only');?> <?php echo $product_warehouse?></span>
                    
            </div>
            <div class="free">
                <a href="javascript:;" class="icon-shipping"><i></i><?php echo lang('shipping_time_free_shipping');?></a>
                <a href="javascript:;" class="icon-safe"><i></i><?php echo lang('shipping_time_buy_safe');?></a>
                <a href="javascript:;" class="icon-hips"><i></i><?php echo lang('shipping_time_hips_in_18h');?></a>
            </div>