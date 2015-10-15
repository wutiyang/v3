/**
 * 图片幻灯片
 * Last Update:2012-8-2
 */
 (function($) {
	$.fn.slider = function(options){
		var defaults = {
			auto:			false, 	//　自动播放
			speed: 			250, 	//　速度; 越小越快
			pause:			4000, 	//　此4000代表自动播放的间隔，单位：毫秒
			style:			1, 		//　1为显示分页按钮，2为只显示前后两个按钮, 3两种都显示, 0不显示分页
			width :			0, 		//　必须
			height :		0, 		//　必须
			sliderType :	"filter",  //  滚动方向，left || up || filter
			btnPrevClassName : 'button_slider_prev',
			btnNextClassName : 'button_slider_next',
			minWidth : 1000 //页面最小宽度
		},
		options = $.extend(defaults, options),
		startIndex = 1,

		obj = $(this),
		$ul = $("ul", obj),
		$li = $("li", $ul),
		$cloneLi = $li.clone(),
		len = $li.length, //获取焦点图个数
		$page,

		sliderHeight = $li.eq(0).height(),
		sliderWidth = $li.eq(0).width(),
		//显示图片函数，根据接收的index值显示相应的内容
		showPics = function (i, fn) {
			switch(options.sliderType) {
				case 'up' : //向上滚动
					var nowTop = -i * sliderHeight;
					$ul.stop(true,false).animate({"top":nowTop}, options.speed, fn);
					break;
				case 'filter' : //滤镜效果
					$li.eq(_preIndex).fadeOut(options.speed).end().eq(i).fadeIn(options.speed);
					break;
				case 'left' : //向左滚动
				default :
					var nowLeft = -i * sliderWidth;
					$ul.stop(true,false).animate({"left":nowLeft}, options.speed, fn);
					break;
			}
			if (!options.style || options.style == 2) return;
			if(i >= len) {i = 0;}
			$page.removeClass("current").eq(i).addClass('current');

		},
		_preIndex = 0;
		//重置宽度
		if(options.width == '100%') {
			$(window).resize(function () {
				var w = $(window).width();
				if(w < options.minWidth) w = options.minWidth;
				obj.width(w);
			});
			options.width = $(window).width();
			if(options.width < options.minWidth) options.width = options.minWidth;
		}

		obj.width(options.width);
		obj.height(options.height);

		if (len <= 1) return;

		this.each(function() {
			var index = 0, picTimer, btn = '',
				pre = options.btnPrevClassName,
				next = options.btnNextClassName,
				$pre ,
				$next ,
				preAndNext,
				_setTimeout = function () {
					clearInterval(picTimer);
					if(!options.auto) return;
					picTimer = setInterval(function() {
						var liLen = len;
						if(options.sliderType == 'up' || options.sliderType == 'left') {
							liLen = len +1;
						}
						_preIndex = index;
						index += 1;
						if(index == liLen) {
							index = startIndex;
							$ul.css({"left":0, "top" :0});
						}
						showPics(index);
					}, options.pause);
				};

			//style==0时不显示分页样式
			if(!!options.style){
				//分页按钮
				for(var i=0; i < len; i++) {
					if(i == 0) {
						btn += '<span class="current"></span>';
					} else {
						btn += '<span></span>';
					}
				}
				$page = $(btn);

				//为分页按钮添加鼠标滑入事件，以显示相应的内容
				$page.mouseenter(function() {
					_preIndex = index;
					index = $(this).index();
					showPics(index);

				});

				//上一页
				$pre = $('<a class="'+ pre +'" href="javascript:;"><span>next</span></a>').appendTo(obj);
				$pre.click(function() {
					_preIndex = index;
					index -= 1;
					if(index < startIndex) {
						switch (options.sliderType) {
							case 'up':
								showPics(index, function () {
									index = len;
									$ul.css({"top" : 0 - index * sliderHeight});
								});
								break;
							case 'left':
								showPics(index, function () {
									index = len;
									$ul.css({"left" : 0 - index * sliderWidth});
								});
								break;
							case 'filter':
								index = len-1;
								showPics(index);
								break;
						}

					} else {
						showPics(index);
					}
				});

				//下一页按钮
				$next = $('<a class="'+ next +'" href="javascript:;"><span>next</span></a>').appendTo(obj);
				$next.click(function() {
					_preIndex = index;
					index += 1;
					var liLen = len;
					if(options.sliderType == 'up' || options.sliderType == 'left') {
						liLen = len +1;
					}
					if(index >= liLen) {
						//log(index);
						//log(len);
						index = startIndex;
						$ul.css({"left":0, "top" :0});
					}
					showPics(index);
				});


				//$(".btnBg", obj).css({"opacity":0.5, "width": options.width + 'px'});

				//上一页、下一页按钮透明度处理
				var _preAndNextHover = function () {
					$(obj).hover(function () {
						$pre.addClass(pre+'_high');
						$next.addClass(next+'_high');
					}, function () {
						$pre.removeClass(pre+'_high');
						$next.removeClass(next+'_high');
					});
				};

				//添加分页，前进，后退按钮
				$('<div class="ec_slider_nav"></div>').append($page).add($pre,$next).appendTo(obj);
			}

			//$ul.add($li).add($cloneLi).css({"width" : options.width});


			//三种展示效果，样式分别设置
			switch(options.sliderType) {
				case 'up' :
					$ul.append($cloneLi);
					$li = $li.add($cloneLi);
					$ul.css({"height" : sliderWidth * len * 2});
					obj.addClass('ec_slider_top');
					break;
				case 'filter' : //滤镜效果
					startIndex = 0;
					obj.addClass('ec_slider_filter');
					$ul.css({"width": options.width,"height": options.height});
					$li.css({"width":options.width}).eq(0).show();
					break;
				case 'left' :
				default	:
					$ul.append($cloneLi);
					$li = $li.add($cloneLi);
					$ul.css({"width": sliderWidth * len * 2});
					obj.addClass('ec_slider_left');
					break;
			}

			//鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
			obj.hover(function() {
				clearInterval(picTimer);
			},_setTimeout);

			if(options.auto) { //是否自动播放
				_setTimeout();
			}
			//三种分页显示方式
			switch (parseInt(options.style)){
				case 1:
					$pre.add($next).hide();
					break;
				case 2:
					$page.hide();
					_preAndNextHover();
					break;
				case 3:
					_preAndNextHover();
					break;
			}
		});
	}
})(jQuery);