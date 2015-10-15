/**
 * 搜索自动完成插件
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

	function autocomplete(obj, options) {
		if(!options || !options.url || (typeof obj !== 'object' && !obj.match(/^\s*#([\w:\-\.]+)\s*$/igm))) {
			log('Parameter Error');
			return;
		}

		this.cache = {};
		this.enabled = false;
		this._setTime = null;
		this.opt = $.extend({}, this.defaults, options);

		this.obj = $(obj);
		var $parent = this.obj.parent();
		this.selectedIndex = -1;

		this.searchList = $('<div class="'+ this.opt.className +'"></div>').appendTo($parent);
		this.init();
	}

	autocomplete.prototype = {
		init : function () {
			var self = this;
			if (window.opera) {
		    	self.obj.keypress(function(e) { self.onKeyPress(e); });
		    } else {
		    	self.obj.keydown(function(e) { self.onKeyPress(e); });
		    }
			self.obj.on('keyup', function (e){
				switch (e.keyCode) {
					case 27: //KEY_ESC:
					case 38: //KEY_UP:
					case 40: //KEY_DOWN:
					  return;
				}
				clearTimeout(self._setTime);
				self._setTime = setTimeout(function (){self.getData()}, 200);
			}).on('blur', function () {
				setTimeout(function () {self.hide();}, 150);
			});
		},
		getData : function () {
			var self = this;
			var key = self.obj.val().trim();
			var data = self.cache[key];
			var $searchList = self.searchList;
			//缓存数据
			if(!data) {
				self.getAjax();
			} else {
				if(data.length < 1) {
					self.hide();
					return;
				}
				$searchList.html('<ul>'+ data.join('') +'</ul>');
				self.show();
				self.bindEvent();
			}
			//this.setPosition();
		},
		getAjax : function () {
			var self = this;
			var keywords = this.key = self.obj.val().trim();
			var $searchList = self.searchList;
			var opt = self.opt;
			//$searchList.html('<ul><li>loading...</li></ul>').show();
			$.ajax({
				type: opt.method,
				url: opt.url,
				data: (opt.form) ? opt.form.serialize() : {act:"auto_search", "is_new":true, term : keywords},
				cache :false,
				dataType: "json",
				success: function(json){

					if(!json) {
						log('load fail');
						return;
					}

					var list = [];
					var html = '';
					var len = json.length;
					if(len > 0) {
						for(var i =0; i<len; i+=1) {
							list.push('<li><a href="javascript:;">'+json[i]+'</a></li>');
						}
						html = '<ul>'+ list.join('') + '</ul>';
						$searchList.html(html);
						if(len > 10) $searchList.addClass('max_height');
						self.show();
						//缓存数据
						if(!self.cache[keywords]) {
							self.cache[keywords] = list;
						}
					} else {
						self.cache[keywords] = [];
						self.hide();
					}
					self.bindEvent();
				},
				error : function (XMLHttpRequest, textStatus, errorThrown) {
					log('error:' + XMLHttpRequest.status + ' ---- ' + errorThrown);
				}

			});
		},
		setPosition : function () {
			/*
			var offset = this.obj.position();
			var css = {
					"left" : offset.left + 'px',
					"top" : offset.top + this.obj.outerHeight(true),
					"position" : "absolute",
					"width" : this.obj.outerWidth(true)-2
			};
			this.searchList.css(css);
			*/
		},
		show : function () {
			this.enabled = true;
			this.searchList.show();
			//this.setPosition();
		},
		hide : function () {
			this.enabled = false;
      		this.selectedIndex = -1;
			this.searchList.hide();
		},
		bindEvent : function () {
			if (!this.enabled) { return; }
			var self = this;
			var opt = self.opt;
			var $searchList = self.searchList;
			$('li',$searchList).on('click', function () {
				self.obj.val($(this).text());
				if(opt.callback) {
					opt.callback.call(self);
				}
			});
		},
		onKeyPress: function(e) {
	      	if (!this.enabled) { return; }
		      	// return will exit the function
		      	// and event will not be prevented
		      	switch (e.keyCode) {
		        	case 27: //KEY_ESC:
		          	this.hide();
		          	break;
		        case 38: //KEY_UP:
		          	this.moveUp();
		         	break;
		        case 40: //KEY_DOWN:
		          	this.moveDown();
		          	break;
		        default:
		          	return;
	      	}
	      	e.stopImmediatePropagation();
	      	e.preventDefault();
	    },
		moveUp: function() {
	      	if (this.selectedIndex === -1) { return; }
	      	if (this.selectedIndex === 0) {
	        	$('li', this.searchList).eq(0).removeClass('current');
	        	this.selectedIndex = -1;
	        	this.obj.val(this.key);
	        	return;
	      	}

	      	this.onSelect(this.selectedIndex-1);
	    },
	    moveDown: function() {
	      	if (!this.cache[this.key] || this.selectedIndex === (this.cache[this.key].length-1)) {
	      		return;
	      	}
	       	this.onSelect(this.selectedIndex+1);
	    },
	    onSelect: function(i) {
	      	var self = this;
	      	var val = this.cache[this.key][i];
	      	this.selectedIndex = i;
	      	self.obj.val($(val).text());
	      	$('li', this.searchList).removeClass('current').eq(i).addClass('current');
	    },
	    defaults : {
			method : 'get',
			form : null,
			url : '',
			className : 'ol_autocomplete',
			callback : null
		}
	};


	ec.ui.autocomplete = function (obj, options) {
		return new autocomplete(obj, options);
	};
})(jQuery);
