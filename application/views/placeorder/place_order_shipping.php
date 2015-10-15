<div class="shipping options" id="shipping">
		<h2 class="ship-tit"><i></i><?php echo lang('shipping_options');?></h2>
		<!-- <form action="" method="post"> -->
		<ul class="ship-potion <?php if($shipping_option_data['status']!=200) echo 'hide';?>">
		<?php 
		if(isset($shipping_option_data) && $shipping_option_data['status']==200){
		?>
				<?php
				$shipping_nums = 0; 
				foreach ($shipping_option_data['data'] as $shipping_key=>$shipping_val){
				?>
				<li>
					<dl <?php if(isset($shipping_val['available']) && $shipping_val['available']==0) echo 'class="gray9"';?>>
						<dt class="remember">
							<input class="radio" <?php if($shipping_nums==0 && $shipping_val['available']==1) echo 'checked';?> id="<?php echo 'shippingid'.$shipping_val['id']?>" <?php if($shipping_val['available']==0) echo "disabled";?> type="radio" name="options">
							<label for="<?php echo 'shippingid'.$shipping_val['id']?>"><?php echo $shipping_val['title']?></label>
						</dt>
						<dd><?php echo $shipping_val['day']?></dd>
						<dd><?php echo $shipping_val['price']?></dd>
					</dl>
					<?php 
					if(isset($shipping_val['track']) && in_array($shipping_val['id'], array(1,2,4,5))){
					?>
					<dl class="potion-info <?php if(isset($shipping_val['available']) && $shipping_val['available']==0) echo 'gray9';?>">
						<dt class="remember">
							<input class="check" id="checkbox<?php echo $shipping_key+1?>" type="checkbox" <?php if($shipping_val['available']==0) echo "disabled";?> <?php if($shipping_val['track']==1 && in_array($shipping_val['id'], array(4,5))) echo 'checked="checked"';?>>
							<label for="checkbox<?php echo $shipping_key+1?>" class="track"><?php echo $shipping_val['trackTitle']?></label>
						</dt>
						<dd id="shippingInsurance"><?php echo $shipping_val['trackPrice']?></dd>
					</dl>
					<?php	
					}
					?>
					<?php 
					if(isset($shipping_val['available']) && $shipping_val['available']==0){
					?>
					<dl class="potion-info indent"><?php echo lang('not_available_address_tips');?></dl>
					<?php	
					}
					?>
				</li>
				<?php
				if($shipping_val['available']==1)$shipping_nums++;
				}
				?>
			<?php	
			}
			?>
				<li class="potion-info" id="Other">
					<dl>
						<dt class="remember">
							<input class="check" id="insurance" type="checkbox">
							<label for="insurance"><?php echo lang('shipping_insurance');?></label>
						</dt>
						<dd>&nbsp;</dd>
						<dd id="shippingInsurance">+<?php echo $new_currency.' '.$insurance;?></dd>
					</dl>
					<dl>
						<dt class="remember">
							<input class="check" id="itemsFirst" type="checkbox">
							<label for="itemsFirst"><?php echo lang('ship_available_first_tips');?></label>
						</dt>
						<dd>&nbsp;</dd>
					</dl>
				</li>
		<!-- </form> -->
		</ul>
		<div class="dis-ship <?php if($shipping_option_data['status']==200) echo 'hide';?>">
			<i></i><span><?php echo $shipping_option_data['msg']?></span>
		</div>
	</div>