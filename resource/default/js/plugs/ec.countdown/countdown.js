/**
 * 倒计时插件
 * @class 	allPage
 * @author  zhuzhengwei
 * @date	2014/05/08
 * @lastModified  2014/07/10
 * @dependon jquery-1.4.1.min.js or later、 ec.lib.js
 *
 * @param  {String | jQuery Object} selector jQuery选择器
 * @param  {Object} opt      相关参数
 * @return {[type]}          无
 */
(function ($) {
	ec.ui.countdown = function (ele, options) {
		// Default options
		var defaults = {
			//"onlyDay" : {"day":1, "html" : "<span><em>{#day}</em>DAY</span>"}, 
			"onlyDay" : null, //小于指定天数时只显示天数，不显示时间，参考上面的设置参数
			"zeroDayHide" : false, //0天是否隐藏
			"html" : "<span>{#day}</span><em>:</em>{#dayText}<span>{#hours}</span><em>:</em>HR<span>{#minutes}</span><em>:</em>MIN<span>{#seconds}</span>SECS",
			"endTimesName" : "data-endtime", //获取剩余时间的属性名（单位：秒）
			"callback" : null
		};
		if(!ele) return;
		var opt = $.extend(defaults, options);
		var dayLangs = {
			"us": {	"days" : "Days","day" : "Day"},
			"de": {	"days" : "Tage","day" : "Tage"},
			"es": {	"days" : "Días","day" : "Día"},
			"it": {	"days" : "Giorni","day" : "Giorno"},
			"fr": {	"days" : "Jours","day" : "Jour"},
			"br": {	"days" : "Dias","day" : "Dia"},
			"ru": {	"days5" : "ДНЕЙ","days" : "ДНЯ","day" : "ДЕНЬ"}
		};
		var render = function(obj) {
			var html = '';
			var diffSecs = obj.data('diffSecs');
			var lang = ec.lang.globle;
			var dayText = '';
			diffSecs--;

			if(!diffSecs || diffSecs <= 0) {
				diffSecs = 0;
			}

			var diff = {
				day : Math.floor(diffSecs / (24*60*60)),
				hour : (opt.html.indexOf("{#day}") >= 0) 
						? Math.floor(diffSecs / 60 / 60 ) % 24 
						: Math.floor(diffSecs / 60 / 60 ),
				minute : Math.floor(diffSecs / 60) % 60,
				second : diffSecs % 60
			};

			if(opt.onlyDay && diff.day > opt.onlyDay.day && opt.onlyDay){
				html = opt.onlyDay.html.replace(/{#day}/g, diff.day);
			} else {
				if(opt.zeroDayHide && diff.day < 1) {
					obj.parent().addClass('hide_day');
				}
				html = opt.html.replace(/{#day}/g, diff.day).replace(/{#hours}/g, diff.hour > 9 ? diff.hour : "0" + diff.hour).replace(
					/{#minutes}/g, diff.minute > 9 ? diff.minute : "0" + diff.minute).replace(/{#seconds}/g, diff.second > 9 ? diff.second : "0" + diff.second);
			}
			dayText = (diff.day > 1) ? dayLangs[lang]['days'] : dayLangs[lang]['day'];
			if(lang == 'ru' && diff.day >= 5) {
				dayText = dayLangs[lang]['days5'];
			}
			html = html.replace(/{#dayText}/g, dayText);
			obj.html(html);
			obj.data('diffSecs', diffSecs);
			return (diffSecs <=0) ? false : true;
		};

		$(ele).each(function (i, n) {
			var $thix = $(n);
			var $parentObj = $thix.parent();
			var endTimes = $thix.attr(opt.endTimesName);
			var timer = null;
			var init = function (){
				var diffSecs = endTimes <= 0 ? 0 : endTimes;
				timer = $thix.data("countdown");
				if(timer) clearInterval(timer);
				if (!render($thix)){
					if (opt.callback && ec.util.isFunction(opt.callback)){ 
						opt.callback.call($thix, opt);
					}
					return;
				}

				timer = setInterval(function() {
					if (!render($thix)) {
						if (opt.callback && ec.util.isFunction(opt.callback)){ 
							opt.callback.call($thix, opt);
						}
						clearInterval(timer);
					}
				}, 1000);

				$thix.data("countdown", timer);
			};
			if($.isNumeric(endTimes)) {
				$parentObj.css('visibility', 'visible');
				$thix.data('diffSecs', endTimes);
				init();
			}
		});

	};
})(jQuery);
