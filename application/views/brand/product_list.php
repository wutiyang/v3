
        <ul>
            <?php if(isset($product_list) && !empty($product_list)){
                foreach($product_list as $product){
                    if(!isset($product['product_description_name'])) $product['product_description_name'] = $product['product_name'];
                    ?>
                    <li>
                        <div class="p-pic">
                            <a href="<?php echo genURL($product['product_url']);?>" title="<?php echo $product['product_description_name'];?>"onclick='productEvent(<?php $productObj = array();$productObj['data'][] = array('price' => $product['product_discount_price'],'id' => $product['product_id']);$productObj['list'] = isset($category)?'Brand Category Page':'Brand Page'; echo json_encode($productObj);?>)'>
                                <img src="<?php echo RESOURCE_URL ?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo PRODUCT_IMAGEM_URL.$product['product_image'];?>" width="189" height="189">
                                <?php
                                if((int) $product['product_discount_number']!=0){ ?>
                                    <p class="icon-off"><i>
                                            <?php
                                            echo (int) $product['product_discount_number'];
                                            ?>
                                        </i></p>
                                <?php } ?>
                            </a>
                        </div>
                        <div class="p-name">
                            <a href="<?php echo genURL($product['product_url']);?>" title="<?php echo $product['product_description_name'];?>"><?php echo $product['product_description_name'];?></a>
                        </div>
                        <div class="p-price">
                            <span class="p-price-o"><?php echo  $product['product_currency'].number_format($product['product_price_market'],2,'.',',');?></span>
                            <span class="p-price-n"><?php echo  $product['product_currency'].number_format($product['product_discount_price'],2,'.',',');?></span>
                        </div>
                    </li>
                <?php
                }
            }?>

        </ul>
