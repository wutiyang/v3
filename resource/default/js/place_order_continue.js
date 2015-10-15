(function(){
	var orderId=$('#order_id').val();

	function payment(){
		$('#payment .method-list li').on('click',function(){
			var payment=$(this).attr('paymentid'),
				paymentId=$('#payment_id').val(payment);
			$(this).addClass('on').siblings().removeClass('on');
			if(paymentId==''){
				$('#place').addClass('icon-view-dis');
				$('#place').attr('disabled');
			}
		})
	}
	payment();
	
	$("#place").on('click',function(e){
		e.stopPropagation();
		payment();
		var payment_id = $('#payment_id').val();
		location.href='/repay/process?order_id='+orderId+'&payment_id='+payment_id;
		/*$.ajax({
			url: "/repay/process/",
			data: {'order_id':orderId,'payment_id':payment_id},
			//type: 'post',
			type: 'get',
			dataType: 'json',
			success: function(json){
				
			}	
		})*/
		
	})
			
})()
