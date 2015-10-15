<?php
if(isset($children_category['children'])){
$children_count = count($children_category['children']);
if($children_count){
?>
<div id="dImgZoom" class="fl img_zoom">
		<div class="img_zoom_thumb relative">
			<div class="img_zoom_thumb_list relative">
				<ul class="imgZoomLi">
					<?php 
						foreach ($children_category['children'] as $chkey=>$chvalue){if($chvalue['category_pid_count'] == 0) continue;
					?>
					<li class="item <?php if($chkey==0){ echo 'current';}?>">
						<a href="<?php echo genURL($chvalue['category_url'],true)?>" title="<?php echo $chvalue['category_description_name']?>">
							<?php 
								if($chvalue['category_image']){
							?>
							<img src="<?php echo RESOURCE_URL?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" data-lazysrc="<?php echo COMMON_IMAGE_URL.$chvalue['category_image']?>" width="140" height="143" alt="<?php echo $chvalue['category_description_name']?>">
							<?php									
								}else{
							?>
							<img src="<?php echo RESOURCE_URL?>images/common/default.png?v=<?php echo STATIC_FILE_VERSION ?>" alt="<?php echo $chvalue['category_description_name']?>">
							<?php
								}
							?>
							<div class="org-border">
                            	<div class="shadow"></div>
                            	<div class="title"><?php echo $chvalue['category_description_name']?></div>
                            </div>
                        </a>
						<em class="arrow"></em>
					</li>
					<?php		
						}
					?>
				</ul>
			</div>
			<a href="javascript:;" class="arrow_left btn_arrow arrow_disabled"><span class="icon_arraw_left"></span> </a>
			<a href="javascript:;" class="arrow_right btn_arrow"><span class="icon_arraw_right"></span> </a>
		</div>
	</div>
<?php
	}
}
?>
