/**
 * 倒计时插件
 * @class 	allPage
 * @author  zhuzhengwei
 * @date	2014/05/08
 * @lastModified  2014/05/26
 * @dependon jquery-1.4.1.min.js or later、 ec.lib.js
 *
 * @param  {String | jQuery Object} selector jQuery选择器
 * @param  {Object} opt      相关参数
 * @return {[type]}          无
 */
(function ($) {
	var countdown = function (ele, options) {
		// Default options

		this.obj = $(ele);
		this.parentObj = this.obj.parent();
		this.timer = this.obj.data("countdown");
		this.timeIndex = 0;
		this.preTarget = null;

		this.opt = $.extend(this.defaults, options);
		this.diffMs = this.opt.now - new Date().getTime();
		this.diffSecs = 0;
		this.init();
	}
	countdown.prototype = {
		getNext : function() {
			var me = this;
			if (me.timeIndex >= me.opt.startTimes.length) {
				return false;
			}
			me.preTarget = me.opt.startTimes[me.timeIndex];
			if(ec.util.isString(me.preTarget)) {
				me.preTarget = ec.util.parseDate(me.preTarget, 'yyyy-MM-dd HH:mm:ss').getTime();
			}
			if($.type(me.preTarget) == 'date') {
				me.preTarget = new(me.preTarget).getTime();
			}
			me.timeIndex++;

			return true;
		},
		getDiffSec : function(){
			var me = this;
			me.diffSecs = Math.round((me.preTarget - new Date().getTime() - me.diffMs) / 1000);
			me.diffSecs = me.diffSecs <= 0 ? 0 : me.diffSecs;
			return me.diffSecs;
		},
		render : function() {
			var me = this;
			var html = '';
			var opt = me.opt;
			me.diffSecs--;

			if(me.diffSecs <= 0) {
				me.diffSecs = 0;
			}

			var diff = {
				day : Math.floor(me.diffSecs / (24*60*60)),
				hour : (me.opt.html.indexOf("{#day}") >= 0) 
						? Math.floor(me.diffSecs / 60 / 60 ) % 24 
						: Math.floor(me.diffSecs / 60 / 60 ),
				minute : Math.floor(me.diffSecs / 60) % 60,
				second : me.diffSecs % 60
			};

			if(opt.onlyDay && diff.day > opt.onlyDay.day && opt.onlyDay){
				html = opt.onlyDay.html.replace(/{#day}/g, diff.day);
			} else {
				if(opt.zeroDayHide && diff.day < 1) {
					me.parentObj.addClass('hide_day');
				}
				html = opt.html.replace(/{#day}/g, diff.day).replace(/{#hours}/g, diff.hour > 9 ? diff.hour : "0" + diff.hour).replace(
					/{#minutes}/g, diff.minute > 9 ? diff.minute : "0" + diff.minute).replace(/{#seconds}/g, diff.second > 9 ? diff.second : "0" + diff.second);
			}
			me.obj.html(html);

			return (me.diffSecs <=0) ? false : true;
		},

		init : function (){
			var me = this;
			if (!me.opt.startTimes) {
				me.opt.startTimes = [ me.opt.endTime ];
			}

			clearInterval(me.timer);
			while (me.getNext()) {
				if (me.getDiffSec() <= 0) { continue;}
				break;
			}

			if (!me.render()){
				if (me.opt.callback && ec.util.isFunction(me.opt.callback)){ 
					me.opt.callback.call(me.obj, me.opt);
				}
				return;
			}

			me.timer = setInterval(function() {
				if (!me.render()) {
					if (me.opt.callback && ec.util.isFunction(me.opt.callback)){ 
						me.opt.callback.call(me.obj, me.opt);
					}

					if (!me.getNext()) {
						clearInterval(me.timer);
					} else {
						me.getDiffSec();
					}
				}
			}, 1000);

			me.obj.data("countdown", me.timer);
		},
		defaults : {
			"onlyDay" : {"day":1, "html" : "<span><em>{#day}</em>DAY</span>"},
			"html" : "<span>{#day}</span><em>:</em>DAY<span>{#hours}</span><em>:</em>HR<span>{#minutes}</span><em>:</em>MIN<span>{#seconds}</span>SECS",
			"now" : new Date().getTime(),
			"startTimes" : [new Date().getTime()+1000*60*60*24],
			"endTime" : null,
			"callback" : null
		}
	};
	ec.ui.countdown = function (selector, opt) {
		return new countdown(selector, opt);
	};
})(jQuery);

