<?php include dirname(__FILE__).'/common/head.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo $current_page=='success'&&SSL_ENABLED?RESOURCE_URL_SSL:RESOURCE_URL ?>css/subscription.css">

<body class="lan-us">
<div class="main distribution">
    <div class="take-box">
        <div class="take-logo">
            <a title="Eachbuyer.com" href="/"><img title="Eachbuyer.com" alt="Eachbuyer.com" src="<?php echo RESOURCE_URL; ?>images/common/logo.png"></a>
        </div>
        <!-- distribution table start -->
        <div class="distribution-tab">
            <table class="myorder-table order-summary" id="cartSummary">
                <thead>
                <tr>
                    <th><?php echo lang('item');?></th>
                    <th><?php echo lang('item_price');?></th>
                    <th><?php echo lang('pid');?></th>
                </tr>
                </thead>
                <tbody>
                <?php if(isset($products) && !empty($products)) foreach($products as $val){ ?>
                <tr>
                    <td>
                        <img
                                src="<?php echo PRODUCT_IMAGES_URL . $val['product_image'] ?>"
                                alt="<?php echo $val['product_description_name'] ?>"
                            >
                        <div><?php echo $val['product_description_name'] ?></div>
                    </td>
                    <td class="t2"> <?php echo $product_currency.$val['product_currency'];echo number_format($val['product_discount_price'], 2, '.', ','); ?></td>
                    <td class="t3"><?php echo $val['product_id']?></td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <!-- distribution table end -->
        <div class="take-bd" id="takeBd">
            <div class="left take-per-have" >
                <div class="per-sub clearfix" >
                    <h2>These products Only ships to most European countries as below:</h2>
                </div>
                <ul>
                    <li>Andorra ,Austria</li>
                    <li>Belgium</li>
                    <li>Cyprus ,Croatia ,Czech ,Canary Islands</li>
                    <li>Denmark</li>
                    <li>Estonia</li>
                    <li>France ,Finland</li>
                    <li>Germany ,Guernsey ,Gibraltar ,Greenland ,Greece</li>
                    <li>Hungary</li>
                    <li>Italy ,Ireland Iceland</li>
                    <li>Jersey</li>
                    <li>Liechtenstein ,Luxembourg</li>
                    <li>Malta ,Monaco</li>
                    <li>Norway ,Netherlands</li>
                    <li>Portugal ,Poland</li>
                    <li>Spain ,Sweden ,Switzerland ,Slovakia</li>
                    <li>United Kingdom</li>
                </ul>
                <h3>Sorry for the inconvenience</h3>
            </div>
        </div>
    </div>
</div>
<script type="text/html" id="emailTips">
    <div class="per-confirm" id="Pop">
        <a href="javascript:;" title="Close" class="close" id="close">关闭</a>
        <h3>To earn more rewards,you can</h3>
        <div class="btn-form">
            <a class="btn34-gray btn-text" href="">Go Shopping</a>
            <span class="or">OR</span>
            <a class="btn34-org btn-text" href="">Write a Review</a>
        </div>
    </div>
</script>
<script type="text/javascript" src="<?php echo$realmName ?>resource/js/lang/us.js"></script>
<script type="text/javascript" src="<?php echo$realmName ?>resource/js/common/ec.base.js"></script>
<script type="text/javascript" src="<?php echo$realmName ?>resource/js/common/utils.js"></script>
<script type="text/javascript" src="<?php echo$realmName ?>resource/js/personal.js"></script>
</body>
</html>
