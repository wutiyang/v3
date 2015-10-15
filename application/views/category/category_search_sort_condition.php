<div class="primary-top clear">
                <div class="primary-introduction">
                    <ul>
                        <li class="intr-list <?php if($sort==1) echo 'on'?>">
                        	<?php
                            $all_param['sort'] = 1;
                        	?>
                        	<a href="<?php echo genURL($url,true,$all_param)?>" rel="nofollow"><?php echo lang('most_popular');?></a>
                        </li>
                        <li class="intr-list <?php if($sort==2) echo 'on'?>">
                        	<?php
                            $all_param['sort'] = 2;
                        	?>
                        	<a href="<?php echo genURL($url,true,$all_param)?>" rel="nofollow"><?php echo lang('new_arrivals');?></a>
                        </li>
                        <!--
                        上下箭头请在span标签上加class
                        	箭头向上：icon-up
                            箭头向下：icon-delow
                        -->
                        <li class="intr-list <?php if($sort==3 || $sort==4) echo 'prices'?>">
                        	<?php
                            $all_param['sort'] = $sort==3?4:3;
                            $icon_class = '';
                            $cion_str = '';
                                if($sort == 3)
                                    $icon_class = 'icon-up';
                                else if($sort == 4)
                                    $icon_class = 'icon-delow';
                                else
                                    $icon_str = '<i class="icon-top"></i><i class="icon-down"></i>';
                        	?>
                        	<a href="<?php echo genURL($url,true,$all_param)?>" rel="nofollow"><?php echo lang('price');?><span class="<?php echo $icon_class;?>"><?php echo $cion_str;?></span></a>
                        </li>
                        <li>
                            <input id="current_url_tim" name="current_url" value="<?php echo $current_url?>" type="hidden">
                            <input id="param_type" type="hidden" value="<?php if($param_isempty) echo 1;else echo 0;?>" >
                            <div class="intr-last">
                                <form  method="post" name="priceForm">
                                    <span class="price info"><?php echo $new_currency;?><input class="price-text front" value="<?php $search_price_range = isset($basicParam['search_price_range'])?$basicParam['search_price_range']:',';$search_price_range = explode(',',$search_price_range);if(count($search_price_range) == 1)echo 0;else echo $search_price_range[0];?>" type="text" name="price-front"></span>
                                    <strong>to</strong>
                                    <span class="price info"><?php echo $new_currency;?><input class="price-text back" value="<?php if(count($search_price_range) == 1)echo $search_price_range[0];else echo $search_price_range[1];?>" type="text" name="price-back"></span>
                                    <input class="go" type="submit" value="Go">
                                </form>
                            </div>
                        </li>
                    </ul>
                    
                </div>
                <?php if(isset($pagination)){ ?>
                <div class="page-top">
                    <?php if($pagination['current_page'] == 1){ ?>
						<a class="p-prev-un" href="javascript:void(0)"><i class="icon-arraw-left"></i></a>
					<?php }elseif($pagination['current_page'] == 2){ ?>
						<a class="p-prev" href="<?php echo $pagination['default_href']; ?>"><i class="icon-arraw-left"></i></a>
					<?php }else{ ?>
						<a class="p-prev" href="<?php echo sprintf($pagination['href'],($pagination['current_page']-1)); ?>"><i class="icon-arraw-left"></i></a>
					<?php } ?>
                    <a class="p-page" href=""><span class="current"><?php echo $pagination['current_page']?></span>/<span><?php echo $pagination['total_page']?></span></a>
                    <?php if($pagination['current_page'] == $pagination['total_page']){?>
                    	<a class="p-next" href="javascript:void(0)" title="Next"><i class="icon-arraw-right"></i></a>
                    <?php }else{ ?>
                    	<a class="p-next" href="<?php echo sprintf($pagination['href'],($pagination['current_page']+1))?>" title="Next"><i class="icon-arraw-right"></i></a>
                    <?php }?>
                    
                </div>
                <?php } ?>
            </div>
