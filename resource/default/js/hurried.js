(function(){
	var icon=$('.pop-icon'),
		goods=$('.goods-list');
		goods.eq(0).show();
		$('.thumbnail li').eq(0).show().addClass('on');
	$('.thumbnail li').each(function(){
        var _this = $(this).find('p');
        var strLen = _this.text().length,
            num = Math.ceil(strLen/15);
        if(num <= 1){
            _this.css('line-height','28px')
        }
    })
    $('.thumbnail li').click(function(){
        var index=$(this).index();
        $(this).addClass('on').siblings().removeClass('on');
        icon.stop().animate({'left':10*index+'%'},300);
        goods.eq($(this).index()).show().siblings().hide();
    })
    $('.p-price .p-price-o').each(function(){
        var priceW=$(this).innerWidth();
        if(priceW>=70){
            $(this).parent().css({'line-height':'17px'});
            $(this).css({'float':'none'});
            $(this).next().css({'float':'none','display':'block'});
        }
    })
	//吸顶
	$(window).scroll(function(){
        var curr_top=$(document).height();
        var top=$(document).scrollTop(),
        	navTop=$('.box').offset().top;
        var primaryBox=$('.primary-list'),
            primaryH=primaryBox.height(),
            primaryTop=primaryBox.offset().top;
        $('.main-list').removeClass('thumbnail-fix');
        if(top>navTop){
            $('.main-list').addClass('thumbnail-fix');
        }
        if(top>(primaryH+primaryTop)-131){
           $(".main-list").removeClass('thumbnail-fix');
        }
        $('.thumbnail li').click(function(){
	    	$(document).scrollTop(navTop);
		})
    })
    
})()