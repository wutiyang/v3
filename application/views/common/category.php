	<!--<div class="nav-categray nav-categray-show">-->
	<div class="nav-categray " id="navCategray">
	<div class="cate-title"><a href="javascript:;"><?php echo lang('all_categories');?><i class="icon-allCate-arrow"></i></a></div>
	<div  class="cate-list">
	<ul class="big-list clearfix" id="categrayAll">
	<?php
    foreach ($cate_tree as $val){
        if($val['category_pid_count']) {
            ?>
            <li id="menu_<?php echo $val['category_id']; ?>" class="list">
                <div class="li"><a
                        href="<?php echo genURL($val['category_url'], true) ?>"><em><?php echo $val['category_description_name'] ?></em></a><span>(<?php echo $val['category_pid_count'] ?>)</span></div>
                <div class="sub-list clearfix">
                    <div class="sub-border">
                        <div class="sub-border-top"></div>
                    </div>
                    <div class="popup-logo"></div>
                    <div class="sub-padding">
                        <?php
                        if (isset($val['col_one'])) {
                            ?>
                            <ul class="column">
                                <?php
                                foreach ($val['col_one'] as $k => $v) {
                                    if ($v['category_pid_count']) {
                                        ?>
                                        <li data-id="<?php echo $v['category_id']; ?>"
                                            class="itemMenuName level1 item1"><a
                                                href="<?php echo genURL($v['category_url'], true) ?>"><?php echo $v['category_description_name'] ?></a>
                                        </li>
                                        <?php
                                        if (!empty($v['children'])) {
                                            foreach ($v['children'] as $kk => $vv) {
                                                if ($vv['category_pid_count']) {
                                                    ?>
                                                    <li data-id="<?php echo $vv['category_id']; ?>"
                                                        class="itemMenuName level2 item1"><a
                                                            href="<?php echo genURL($vv['category_url'], true) ?>"><?php echo $vv['category_description_name'] ?></a>
                                                    </li>
                                                <?php
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                <?php
                                }
                                ?>
                            </ul>
                        <?php } ?>
                        <?php
                        if (isset($val['col_two'])) {
                            ?>
                            <ul class="column">
                                <?php
                                foreach ($val['col_two'] as $kt => $vt) {
                                    ?>
                                    <?php
                                    if (isset($vt['category_url'])) {
                                        ?>
                                        <li data-id="15905" class="itemMenuName level1 item1"><a
                                                href="<?php echo genURL($vt['category_url'], true) ?>"><?php echo $vt['category_description_name'] ?></a>
                                        </li>
                                    <?php
                                    }
                                    ?>
                                    <?php
                                    if (!empty($vt['children'])) {
                                        foreach ($vt['children'] as $kkt => $vvt) {
                                            ?>
                                            <li data-id="15905" class="itemMenuName level2 item1"><a
                                                    href="<?php echo genURL($vvt['category_url'], true) ?>"><?php echo $vvt['category_description_name'] ?></a>
                                            </li>
                                        <?php
                                        }
                                    }
                                    ?>
                                <?php
                                }
                                ?>
                            </ul>
                        <?php } ?>
                        <?php
                        if (isset($val['col_three'])) {
                            ?>
                            <ul class="column">
                                <?php
                                foreach ($val['col_three'] as $ktr => $vtr) {
                                    ?>
                                    <?php
                                    if (isset($vtr['category_url'])) {
                                        ?>
                                        <li data-id="15905" class="itemMenuName level1 item1"><a
                                                href="<?php echo genURL($vtr['category_url'], true) ?>"><?php echo $vtr['category_description_name'] ?></a>
                                        </li>
                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if (!empty($vtr['children'])) {
                                        foreach ($vtr['children'] as $kktr => $vvtr) {
                                            ?>
                                            <li data-id="15905" class="itemMenuName level2 item1"><a
                                                    href="<?php echo genURL($vvtr['category_url'], true) ?>"><?php echo $vvtr['category_description_name'] ?></a>
                                            </li>
                                        <?php
                                        }
                                    }
                                    ?>
                                <?php
                                }
                                ?>
                            </ul>
                        <?php } ?>
                    </div>
                </div>
            </li>
        <?php
        }
    }
    ?>
	</ul>
	</div>
	</div>
