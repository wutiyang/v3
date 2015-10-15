$(function(){
	ol.load.define("ec.ui.slider" , [
		{mark:"ec.ui.slider", uri : "../plugs/ec.slider/slider.js?20140530" , type :"js"}
	]);
	//内容动画效果
	ec.load("ec.ui.slider", {
		loadType : "lazy",
		onload : function() {
			$("#focus").slider({
				width: 1200, //必须
				height: 200, //必须
				style : 1, //1显示分页，2只显示左右箭头,3两者都显示, 0都不显示
				pause : 3000, //间隔时间
				auto : true, //是否自动开始
				sliderType : 'filter' //up:向上，left:向左，filter：渐变
			});
		}
	});
	$(".nav-list").hover(function(){
		var thix=$(this),
			i=$(this).index(),
			left=parseInt($(this).position().left);
		$(".posiabox").show();
		twoUl = $(".posiabox .subnav").eq(i),
		twoLi = twoUl.find('li').length;
		if(twoLi!=0){
			$(this).parent().css({'height':'36px'});
			$(this).css({'height':'36px'});
		}else{
			$(this).parent().css({'height':'30px'});
			$(this).css({'height':'30px'});
		}
		if(twoUl.is(":hidden")){
			twoUl.show().css({"left":left+40,"top":"48px"});
		}else{
			twoUl.stop().animate({'left':left},200)
		}
		
		$('.subnav').hover(function(){
			var i=$(this).index(),
				primary=$(".nav-list").eq(i);
			primary.parent().css({'height':'36px'});
			primary.css({'height':'36px'});
			$(this).show();
		},function(){
			$(this).hide();
			thix.parent().css({'height':'30px'});
			thix.css({'height':'30px'});
		})
	},function(){
		twoUl.hide();
		$(this).parent().css({'height':'30px'});
		$(this).css({'height':'30px'});
	})

	$(".more,.less").click(function(){
		if($(this).hasClass("more")){
			$(this).removeClass('more').addClass('less');
		}else{
			$(this).removeClass('less').addClass('more');
		}
		$(this).parent().find("li").each(function(){ 
			if($(this).attr('scale') == 'true'){
				$(this).toggle();
			}
		}); 
	});

	$('.p-price .p-price-o').each(function(){
        var priceW=$(this).innerWidth();
        if(priceW>=70){
            $(this).css({'float':'none'});
            $(this).next().css({'float':'none','display':'block'});
        }
    })
    $(".zone-info").each(function(i){
	    var divH = $(this).height();
	    var $p = $("p", $(this)).eq(0);
	    while ($p.outerHeight() > divH) {
	        $p.text($p.text().replace(/(\s)*([a-zA-Z0-9]+|\W)(\.\.\.)?$/, "..."));
	    };
	});
})