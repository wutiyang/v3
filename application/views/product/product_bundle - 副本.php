<div class="con">
            <ul>
                <li class="master selectModule" pid="<?php echo $product_base_info['product_id']?>">
                    <div class="pro-list-block">
                        <div class="p-pic">
                            <a title="<?php echo $product_base_info['product_description_name']?>" href="<?php echo genURL($product_base_info['product_url'])?>"><img alt="<?php echo $product_base_info['product_description_name']?>" src="<?php echo PRODUCT_IMAGE_URL."160x160/".$product_base_info['product_image']?>" width="160" height="160"></a>
                        </div>
                        <div class="p-name current">
                            <i class="icon-ipt"></i><a title="<?php echo $product_base_info['product_description_name']?>" href="<?php echo genURL($product_base_info['product_url'])?>"><?php echo $product_base_info['product_description_name']?></a>
                        </div>
                        <div class="p-price">
                            <i class="elidePrice hide"><?php echo $product_extend_price['product_discount_price']?></i>
                            <?php echo $product_extend_price['product_currency']?><i class="salePrice"><?php echo $product_extend_price['product_discount_price']?></i>  
                        </div>
                        <div class="select-1 select-box">
                            <div class="select-block">
                                <div class="selected" title="Select Color">
                                    <a rel="nofollow" href="javascript:;">
                                        <i class="account-icon"></i>
                                        <span attrid="default" class="default">Select Color</span>
                                    </a>
                                    <i class="icon-select-arrow"></i>
                                </div>
                                <div class="drop-box">
                                    <ul class="drop-content drop-list">
                                        <li class="hide" attrid="default"><a href="javascript:;"><span>Select Color</span></a></li>
                                        <li attrid="color1"><a href="javascript:;"><span>red</span></a></li>
                                        <li attrid="color2"><a href="javascript:;"><span>blue</span></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="select-1 select-box">
                            <div class="select-block">
                                <div class="selected" title="Select Color">
                                    <a rel="nofollow" href="javascript:;">
                                        <i class="account-icon"></i>
                                        <span attrid="default"  class="default">Select Color</span>
                                    </a>
                                    <i class="icon-select-arrow"></i>
                                </div>
                                <div class="drop-box">
                                    <ul class="drop-content drop-list">
                                        <li class="hide" attrid="default"><a href="javascript:;"><span>Select Color</span></a></li>
                                        <li attrid="size1"><a href="javascript:;"><span>red</span></a></li>
                                        <li attrid="size2"><a href="javascript:;"><span>blue</span></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="add"></li>
                <?php 
                foreach ($bundle_product_list as $bundle_key=>$bundle_val){
                ?>
                <li pid="<?php echo $bundle_val['product_id']?>">
                    <div class="pro-list-block">
                        <div class="p-pic">
                            <a title="<?php echo $bundle_val['product_description_name']?>" href="<?php echo genURL($bundle_val['product_url'])?>"><img alt="" src="<?php echo PRODUCT_IMAGE_URL."160x160/".$bundle_val['product_image']?>" width="160" height="160"></a>
                        </div>
                        <div class="p-name current">
                            <i class="icon-ipt"></i><a title="<?php echo $bundle_val['product_description_name']?>" href="<?php echo genURL($bundle_val['product_url'])?>"><?php echo $bundle_val['product_description_name']?></a>
                        </div>
                        <div class="p-price">
                            <input type="hidden" value="<?php echo $bundle_val['product_price_market']?>">
                            <?php echo $bundle_val['product_currency']?><i><?php echo $bundle_val['product_discount_price']?></i> 
                        </div>
                        <div class="select-1 select-box">
                            <div class="select-block">
                                <div class="selected" title="Select Color">
                                    <a rel="nofollow" href="javascript:;">
                                        <i class="account-icon"></i>
                                        <span attrid="default"  class="default">Select Color</span>
                                    </a>
                                    <i class="icon-select-arrow"></i>
                                </div>
                                <div class="drop-box">
                                    <ul class="drop-content drop-list">
                                        <li class="hide" attrid="default"><a href="javascript:;"><span>Select Color</span></a></li>
                                        <li attrid="color1"><a href="javascript:;"><span>red</span></a></li>
                                        <li attrid="color2"><a href="javascript:;"><span>blue</span></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="select-1 select-box">
                            <div class="select-block">
                                <div class="selected" title="Select Color">
                                    <a rel="nofollow" href="javascript:;">
                                        <i class="account-icon"></i>
                                        <span attrid="default"  class="default">Select Color</span>
                                    </a>
                                    <i class="icon-select-arrow"></i>
                                </div>
                                <div class="drop-box">
                                    <ul class="drop-content drop-list">
                                        <li class="hide" attrid="default"><a href="javascript:;"><span>Select Color</span></a></li>
                                        <li attrid="size1"><a href="javascript:;"><span>red</span></a></li>
                                        <li attrid="size2"><a href="javascript:;"><span>blue</span></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <?php
                }
                ?>
                <li class="list-tocart" id="countPrice">
                    <strong>Total Price<span class="count">$<i>144.00</i></span></strong>
                    <a href="baidu.com"  class="btnorg35" id="listToCart">Add Selected Items to Cart</a>
                    <strong>Save<span class="save">$<i>14.00</i>!</span></strong>
                </li>
            </ul>
        </div>