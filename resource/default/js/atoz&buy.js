(function(){
	var list=$('.list-main');
		list.eq(0).show();
	
	$('.list-tab li').click(function(){
		var index=$(this).index();
        $(this).addClass('on').siblings().removeClass('on');
    	list.eq($(this).index()).show().siblings().hide();
	})
    
})()