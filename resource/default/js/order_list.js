/** 
 * @author  gongshan
 * @date	2015/05/25
 */
ec.pkg('ec.orderList');
ec.orderList.init = function () {
	$('#more').on('click',function(){
		$page = $('#page').val();
		if($page == -1){
			return false;
		}
		$.ajax({
			url: "/rewards/ajaxRewardsList",
			type: 'get',
			data: {page:$page},
			dataType: 'json',
			success: function(json){
				var status=json.status;
				if(status=='1007'){
					ec.utils.showPop('loginPop');					
					ec.login.init();
					return false;	
				}
				if(status=='200'){
					var str='';
					$.each(json.data,function(i,n){
						var status=n.status=='0' ?  'minus' : 'add';
						str+='<tr><td class="t1">'+n.date+'</td><td class="t2"><i class="'+status+'"></i>'+n.amount+'</td><td class="t3">'+n.description+'</td></tr>';
					})
					$('#page').val(json.page);
					$(str).insertBefore($('.rewards-tab .last'));
				}
			}
		});	
	})
	$('.cancelBtn').on('click',function(){
		ec.utils.showPop('shippedTips');
		$('.ship-confirm').css('width','334px');
		$('#shippedOk').on('click',function(){
			$.ajax({
				url: "/order_view/ajaxCancel",
				type: 'post',
				data: {order_id:$('#order_id').val()},
				dataType: 'json',
				success: function(json){
					var status=json.status;
					if(status=='1007'){
						ec.utils.showPop('loginPop');					
						ec.login.init();
						return false;
					}
					if(status=='200'){
//						$('.order-info').remove();
//						location.reload();
						location.reload();
					}
				}
			});
		})
		
		$('#shippedClose').on('click',function(){
			ec.utils.closeBox();
		})
	})
}
ec.ready(function () {
	ec.orderList.init();
})