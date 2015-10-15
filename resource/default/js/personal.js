
ec.pkg('ec.newsletter');
ec.newsletter={
	init:function(){
		ec.newsletter.unsubscribe();
		ec.newsletter.unsubBtn();
		ec.footerInit('footerSubmit2');
	},
	unsubscribe:function(){
		$('#unsubscribe').click(function(){
			$.ajax({
				url: "newsletter/unsubscribe",
				type: 'POST',
				dataType: 'json',
				success: function(json){
					if(json.status=="200"){
						$('.per-btn').removeClass('hide');
						$('.subscribe').addClass('hide');
					}
									
				}
			});
		})
	},
	unsubBtn:function(){
		$('#unsubBtn').click(function(){
			ec.utils.showPop('delete');
			$('#btnUnsubscribe').on('click',function(){
				ec.newsletter.btnUnsubscribe();	
				ec.utils.closeBox();
			})
		})
	},
	btnUnsubscribe:function(){
		var unsubscribe_mail=$('input[name="unsubscribe_mail"]').val(),
			hash=$('input[name="hash"]').val();
		$.ajax({
				url: "/newsletter/unsubscribe",
				type: 'POST',
				data: {'unsubscribe_mail':unsubscribe_mail,'hash':hash},
				dataType: 'json',
				success: function(json){
					if (json.status=='200') {
						console.log($('#takeBd'))
						$('#takeBd').removeClass('hide');
						$('#takeFt').hide();
						// ec.utils.closeBox();
					}
				}
			});
		
	}

}
ec.ready(function () {
	ec.newsletter.init();
})

