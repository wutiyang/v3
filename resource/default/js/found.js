ec.pkg('ec.found');
ec.found.imgZoom=function(){
    function imgSroll(con,size){
        var  li=con.find('li'),
            number=Math.ceil(li.length/5),
            margin=10,
            num=0,
            btnArrow=con.find('.btn_arrow');
			con.find('ul').width((li.outerWidth()+margin)*li.length);
			function scolldiv(ulw,w,num){
				var leftdis = w*num;
				if(leftdis>ulw)
					leftdis=ulw;
				con.find('ul').stop().animate({left:'-'+leftdis});
			}
		btnArrow.click(function(){
			var con=$(this).closest('.con'),
				w=(li.outerWidth()+margin)*con.attr('size'),
				ulw=con.find('ul').width()-w,
				className=$(this).attr('class');
				if(className.indexOf('btn_arrow arrow_disabled')!=-1) return;
			btnArrow.removeClass('arrow_disabled');
			if(className.indexOf('arrow_right')!=-1){
				num++;
				if(num>=number-1){
					 $(this).addClass('arrow_disabled');
				}
				if(num >= number){
					num = number-1;
					return false;
				}
				scolldiv(ulw,w,num);
				
			}else{
				num--;
				if(num<=0){
					$(this).addClass('arrow_disabled');
				}
				if(num < 0){
					num = 0;
					return false;
				}
				scolldiv(ulw,w,num);
			}
		})
    }
    var $con=$('.module-con .con');
    $con.each(function(i,e){
		var me=$(this),
			size=parseInt(me.attr('size')),
			count=me.find('li').length;
       imgSroll($con.eq(i),size);
	 	if(count<=size)
			me.find('.btn_arrow').addClass('btn_arrow arrow_disabled');
    })
}
ec.ready(function () {
	ec.found.imgZoom();
})