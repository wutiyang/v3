/**
* @authors gongshan
* @date    2015-06-06
*/
ec.pkg('ec.index.tab');
//隐藏首页分类上的小箭头
gid('navCategray').className = 'nav-categray nav-categray-show';
ol.load.define("ec.ui.slider" , [
	{mark:"ec.ui.slider", uri : "../plugs/ec.slider/slider.js?20140530" , type :"js"}
]);
// 加载倒计时插件
ec.index.countDown = function (){
	ec.load('ec.ui.countdown', {
		loadType:"lazy",
		onload : function () {
			ec.ui.countdown('.p-countdown',{
				"html" : "<span class='day'>{#day}</span><i class='day'>days</i> <span>{#hours}</span><i>:</i><span>{#minutes}</span><i>:</i><span>{#seconds}</span>",
				"zeroDayHide" : true,
				"callback" : function (json) {
					//计时结束时要执行的方法,比如按钮置灰
					$(this).parent().addClass('timeend');
				}
			});
		}
	});
};

//内容动画效果
ec.load("ec.ui.slider", {
	loadType : "lazy",
	onload : function() {
		$("#focus").slider({
			width: 720, //必须
			height: 410, //必须
			style : 1, //1显示分页，2只显示左右箭头,3两者都显示, 0都不显示
			pause : 3000, //间隔时间
			auto : true, //是否自动开始
			sliderType : 'filter' //up:向上，left:向左，filter：渐变
		});
	}
});
ec.ready(function () {
	ec.index.countDown();
	var list=$('.list-main');
		list.eq(0).show();
	
	$('.list-tab li').click(function(){
		var index=$(this).index();
        $(this).addClass('on').siblings().removeClass('on');
    	list.eq($(this).index()).show().siblings().hide();
	})
});
