<?php include dirname(__FILE__).'/common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_URL ?>css/help.css?v=<?php echo STATIC_FILE_VERSION ?>">
<?php include dirname(__FILE__).'/common/help_crumbs.php'; ?>
<div class="main help">
	<?php include dirname(__FILE__).'/common/help_menu.php'; ?>
    <div class="content">
        <h2 class="help-tit"><?php echo lang('frequently_asked_questions');?></h2>
        <ul class="faq">
            <li class="active">
                <a href="javascript:;">1.  <?php echo lang('question1');?><i class="fewer"><span class="top"></span></i></a>
                <div class="faq-info">
                    <p><?php echo lang('answer1');?></p>
                </div>
            </li>
            <li>
                <a href="javascript:;">2.  <?php echo lang('question2');?><i class="fewer"><span class="top"></span></i></a>
                <div class="faq-info">
                    <p><?php echo lang('answer2');?></p>
                </div>
            </li>
            <li>
                <a href="javascript:;">3.  <?php echo lang('question3');?><i class="fewer"><span class="top"></span></i></a>
                <div class="faq-info">
                    <p><?php echo lang('answer3');?></p>
                </div>
            </li>
            <li>
                <a href="javascript:;">4.  <?php echo lang('question4');?><i class="fewer"><span class="top"></span></i></a>
                <div class="faq-info">
                    <p><?php echo lang('answer4');?></p>
                </div>
            </li>
            <li>
                <a href="javascript:;">5.  <?php echo lang('question5');?><i class="fewer"><span class="top"></span></i></a>
                <div class="faq-info">
                    <p><?php echo lang('answer5');?></p>
                </div>
            </li>
            <li>
                <a href="javascript:;">6.  <?php echo lang('question6');?><i class="fewer"><span class="top"></span></i></a>
                <div class="faq-info">
                    <p><?php echo lang('answer6');?></p>
                </div>
            </li>
            <li>
                <a href="javascript:;">7.  <?php echo lang('question7');?><i class="fewer"><span class="top"></span></i></a>
                <div class="faq-info">
                    <p><?php echo lang('answer7');?></p>
                </div>
            </li>
            <li>
                <a href="javascript:;">8.  <?php echo lang('question8');?><i class="fewer"><span class="top"></span></i></a>
                <div class="faq-info">
                    <p><?php echo lang('answer8');?></p>
                </div>
            </li>
            <li>
                <a href="javascript:;">9.  <?php echo lang('question9');?><i class="fewer"><span class="top"></span></i></a>
                <div class="faq-info">
                    <p><?php echo lang('answer9');?></p>
                </div>
            </li>
            <li>
                <a href="javascript:;">10.  <?php echo lang('question10');?><i class="fewer"><span class="top"></span></i></a>
                <div class="faq-info">
                    <p><?php echo lang('answer10');?></p>
                </div>
            </li>
            <li>
                <a href="javascript:;">11.  <?php echo lang('question11');?><i class="fewer"><span class="top"></span></i></a>
                <div class="faq-info">
                    <p><?php echo lang('answer11');?></p>
                </div>
            </li>
            <li>
                <a href="javascript:;">12.  <?php echo lang('question12');?><i class="fewer"><span class="top"></span></i></a>
                <div class="faq-info">
                    <p><?php echo lang('answer12');?></p>
                </div>
            </li>
            <li>
                <a href="javascript:;">13.  <?php echo lang('question13');?><i class="fewer"><span class="top"></span></i></a>
                <div class="faq-info faq-tab">
                    <table>
                        <tr>
                            <td><?php echo lang('shipping_method');?></td>
                            <td><?php echo lang('tracking_number');?></td>
                            <td><?php echo lang('typical_shipping_time');?></td>
                            <td><?php echo lang('longest_shipping_time_in_record');?></td>
                            <td><?php echo lang('ask_for_resend_or_refund');?></td>
                        </tr>
                        <tr>
                            <td><?php echo lang('airmail');?></td>
                            <td><?php echo lang('no');?></td>
                            <td>10-20 <?php echo lang('working_days');?></td>
                            <td>40 <?php echo lang('days');?></td>
                            <td>40 <?php echo lang('days');?></td>
                        </tr>
                        <tr>
                            <td><?php echo lang('airmail');?> + <?php echo lang('tracking_number');?></td>
                            <td><?php echo lang('yes');?></td>
                            <td>10-20 <?php echo lang('working_days');?></td>
                            <td>40 <?php echo lang('days');?></td>
                            <td>40 <?php echo lang('days');?></td>
                        </tr>
                        <tr>
                            <td><?php echo lang('standard');?></td>
                            <td><?php echo lang('no');?></td>
                            <td>6-10 <?php echo lang('working_days');?></td>
                            <td>25 <?php echo lang('days');?></td>
                            <td>25 <?php echo lang('days');?></td>
                        </tr>
                        <tr>
                            <td><?php echo lang('standard');?> + <?php echo lang('tracking_number');?></td>
                            <td><?php echo lang('yes');?></td>
                            <td>6-10 <?php echo lang('working_days');?></td>
                            <td>25 <?php echo lang('days');?></td>
                            <td>25 <?php echo lang('days');?></td>
                        </tr>
                        <tr>
                            <td><?php echo lang('express');?></td>
                            <td><?php echo lang('yes');?></td>
                            <td>3-7 <?php echo lang('working_days');?></td>
                            <td>12 <?php echo lang('days');?></td>
                            <td>15 <?php echo lang('days');?></td>
                        </tr>
                    </table>
                    <p><?php echo lang('answer13');?></p>
                </div>
            </li>
        </ul>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        //处理问题的展开与关闭
        $("li").click(function(){
            if ($(this).hasClass("active")){
                $(this).removeClass("active");
            } else {
                $(this).addClass("active");
            }
        });
    });
    
</script>

<?php include dirname(__FILE__).'/common/footer.php'; ?>
<script type="text/javascript" src="<?php echo RESOURCE_URL ?>js/help.js?v=<?php echo STATIC_FILE_VERSION ?>"></script>
</body>
</html>