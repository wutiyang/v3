ec.pkg('ec.newsletter');
ec.newsletter={
	init:function(){
		ec.newsletter.unsubscribe();
	},
	unsubscribe:function(){
		$('#unsubscribe').click(function(){
			$('#takeFt').hide();
			$('#takeBd').show();
		})
	}
}
ec.ready(function () {
	ec.newsletter.init();
})
