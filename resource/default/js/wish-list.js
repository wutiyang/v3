ec.ready(function () {
	var addToCartBox=$('#addToCartBox'),
		delCartBox=$('#delCartBox');

	$('.del').on('click',function(){
		var me=$(this),
			parent=me.closest('.list-pro'),
			listInfo=me.closest('.list-info'),
			index=parent.attr('index');
		listInfo.append(delCartBox.html());
		ec.utils.shade();
		$('#delete').on('click',function(){
			$.ajax({
				url: "/wishlist/ajaxCancel",
				data: {id:index},
				type: 'POST',
				dataType: 'json',
				success: function(json){
					if (json.status=="200") {
						ec.utils.closeBox();
						parent.remove();
					}
					if(json.status=="1007"){
						ec.utils.closeBox();
						ec.utils.showPop('loginPop');	
						ec.login.init();
					}
				}
			});
			
		})
		ec.utils.Cancel();
	})
})


