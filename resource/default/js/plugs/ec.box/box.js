/*
* ol.ui.box plugin
* Version 1.2 (2012-7-13)
* @requires jQuery v1.2.6 or later
*
* Example at: http://www.open-lib.com/
*
* Copyright (c) 2009-2011 Open-Lib.Com
* Dual licensed under the MIT and GPL licenses:
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl.html
*
* Read the related post and contact the author at http://www.open-lib.com/
*
* This version is far from perfect and doesn't manage it's own state, therefore contributions are more than welcome!
*
* Usage: var box=new ol.ui.box("something...",{boxid:"div1"});
*		box.open();
*		box.close();
*
* Tested in IE6 IE7 Firefox. Any browser strangeness, please report.
*/

ol.pkg('ol.ui');

(function($) {

	var box = function(content,options){
		this.win = $(window);
		this.doc = $(document);

		//当前操作类型
		this._type=null;
		//遮罩jq对象
		this._mask=null;
		//事件
		this._events={};
		//对话框jq对象
		this._box=null;
		//box的内容窗口的jq对象
		this._b_content=null;
		//box的按钮栏jq对象
		this._b_button=null;
		this.content=content;
		//是否已经填充内容
		this._initedContent=false;

		//内部临时变量
		this._onbox=false;
		this._isOpen=false;


		//初始化对象
		var _getDoc=function(){
			return document.compatMode == 'CSS1Compat' ? document.documentElement : document.body;
		};
		this._getWinSize=function(){
			var doc = _getDoc();
			return {
					width: Math.max( doc.scrollWidth, doc.clientWidth || 0 ) - 1,
					height: Math.max( doc.scrollHeight, doc.clientHeight || 0 ) - 1
			};
		};
		this.types = {
			dialog : {
				html :  '<div class="ol_box">' +
						'	<h5 class="box_header">' +
						'		<a href="javascript:;" onclick="return false;" class="box_close">x</a><span class="box_title"></span>' +
						'	</h5>' +
						'	<div class="box_content"></div>' +
						'	<div class="box_button">' +
						'		<a class="box_ok" href="javascript:;"><span></span></a>' +
						'		<a class="box_cancel" href="javascript:;"><span></span></a>' +
						'	</div>' +
						'</div>',
				initContent:function(self){
					self.setContent(self.content);
				},
				setContent:function(self,content,callback){
					self.content=content||self.content;
					self.setContent(self.content);
					if(typeof(callback)=="function")callback();
				}
			},
			ajax : {
				initContent:function(self){},
				setContent:function(self,content,callback){
					self._b_button.hide();
					self.setContent('<div class="box-loading"></div>');
					if (self._b_content.height()<90)self._b_content.height(Math.max(90, self.options.height));
					if (self._box.width()<200) self._box.width(Math.max(200, self.options.width));

					//ajax
					var ajaxurl = content||self.content;
					if(typeof(ajaxurl)!="string")
					{
						alert("please set ajax url.");
						return;
					}
					self.content=ajaxurl;
					if (self.options.cache == false) {
						if (ajaxurl.indexOf('?') == -1) {
							ajaxurl += "?_t="+Math.random();
						} else {
							ajaxurl += "&_t="+Math.random();
						}
					}
					$.get(ajaxurl, function(data) {
						if(self.options.showButton)self._b_button.show();
						self.setContent(data);
						if(typeof(callback)=="function")callback();
					});
				}
			},//ajax end
			iframe : {
				initContent:function(self){},
				setContent:function(self,content,callback){
					var url = content||self.content;
					if(typeof(url)!="string") {
						alert("please set iframe url.");
						return;
					}

					self.content=url;
					var name="box-iframe-"+(new Date()).getTime();
					self.setContent('<iframe class="boxIframe" width="100%" height="100%" frameborder="0" name="'+name+'"></iframe><script>window["'+name+'"].location.href="'+url+'";</script>');
					if(typeof(callback)=="function")callback();
				},
				closeEvent:function(self){
					if(!self.options.remember) {
						self.find("iframe").each(function(){
							//this.contentWindow.document.write('');//清空iframe的内容
							this.contentWindow.close();//避免iframe内存泄漏
							jQuery(this).remove();//删除iframe
						});
					}
				}
			}//frame end
		};

		this.langText = {
				"ok" : {"us" : "ok", "zh" : "确定"},
				"cancel" : {"us" : "cancel", "zh" : "取消"},
				"close" : {"us" : "close", "zh" : "关闭"}
		};
		this.options  = $.extend({}, this.defaults, options);

		this.init();
	};
	box.prototype={
		init:function(){
			this.initConfig();
			if(this.options.mask)this.initMask();
			this.initBox();
			this.initEvent();
		},
		initBox:function(){
			//移除已经存在的box
			$("#"+this.options.boxid).remove();
			this._box = $(this._type.html).css({
				visibility:"hidden",
				position: 'absolute',
				top:0,
				left:0,
				zIndex: this.options.zIndex
			});
			this._b_header=this._box.find(".box_header");
			this._b_button=this._box.find(".box_button");
			this._b_content=this._box.find(".box_content");
			this.renderBox(this.options);
			this._box.appendTo('body');
			this._type.initContent(this);
		},
		initConfig:function(){
			var type = this.types;
			switch(this.options.type)
			{
				case "ajax":
					this._type=type.ajax;
					break;
				case "iframe":
					this._type=type.iframe;
					break;
				default:
					this._type=type.dialog;
					break;
			}
			this._type=$.extend({},type.dialog, this._type);
		},
		initEvent:function(){
			var thix=this;
			if (thix.options.draggable && thix.options.showTitle) {
				thix._box.find('.box_header').mousedown(function(event){
					var h  = this;
						o  = document,
						ox = parseInt(thix._box.css("left"),10),
						oy = parseInt(thix._box.css("top"),10),
						mx = event.clientX,
						my = event.clientY,

						size = thix._getWinSize(),
						box_w = thix._box.outerWidth(true),
						box_h = thix._box.outerHeight(true);

					if(h.setCapture)h.setCapture();

					var mousemove=function(event){
						if (window.getSelection) {
							window.getSelection().removeAllRanges();
						} else {
							document.selection.empty();
						}
						var left = Math.max(ox+event.clientX-mx, 0),
							top = Math.max(oy+event.clientY-my, 0);

						left = Math.min(left , size.width - box_w);
						top = Math.min(top , size.height - box_h);

						thix._box.css({left: left, top: top});
					};
					var mouseup=function(){
						if(h.releaseCapture)h.releaseCapture();
						thix.doc.unbind('mousemove',mousemove);
						thix.doc.unbind('mouseup',mouseup);
					};
					thix.doc.mousemove(mousemove).mouseup(mouseup);
				});
			}else{
				thix._box.find('.box_header').css("cursor","default");
			}//if end
		},
		//渲染box
		renderBox:function(options){
			var css={zIndex:options.zIndex,position:"absolute"};
			if (options.boxid)this._box.attr('id', options.boxid);
			if (options.boxclass)this._box.addClass(options.boxclass);

			if (!options.showTitle) {
				this._box.find('.box_header').hide();
			}else{
				if (options.title != '') {
					this.setTitle(options.title);
				}
			}
			if (!options.showClose) {
				this._box.find('.box_close').hide();
			}else{
				this._box.find('.box_close').show();
			}
			if (!options.showButton) {
				this._b_button.hide();
			}else{
				if (!options.showCancel) {
					this._b_button.find('.box_cancel').hide();
				}
				if (!options.showOk) {
					this._b_button.find(".box_ok").hide();
				}
			}
			options.okBtnName = options.okBtnName || this.langText.ok[options.lang];
			options.cancelBtnName = options.cancelBtnName || this.langText.cancel[options.lang];
			this._b_button.find(".box_ok span").html(options.okBtnName);
			this._b_button.find(".box_cancel span").html(options.cancelBtnName);
			this._b_header.find(".box_close").attr('title', this.langText.close[options.lang]);
			this._box.css(css);
		},
		setTitle:function(title){
			this._box.find(".box_title").html(title);
			return this;
		},
		//设置BOX的内容
		setContent:function(content){
			if(typeof(content)=="undefined"||content==null)return;
			this._initedContent=true;
			this._b_content.empty().html(content);
			var thix=this;
			//调整窗口大小
			if(this.options.width>0){
				this._box.css("width",this.options.width);
			}else{
				this._box.css("width",null);
			}
			if(this.options.height>0){
				var css= {
					"height" : this.options.height
				};
				this.options.autoHeight || (css["overflow-y"] = "auto");
				this._b_content.css(css);

			}else{
				this._b_content.css("height","auto");
			}
			//调整位置
			this.setPosition();
			//绑定按钮事件
			this._box.find(".box_close, .box_cancel, .box_ok").unbind('click').click(function(){thix.close();});
			if (typeof(this.options.onok) == "function") {
				this._box.find(".box_ok").unbind('click').click(function(){thix.options.onok.call(this,thix);});
			}
			if (typeof(this.options.oncancel) == "function") {
				this._box.find(".box_cancel").unbind('click').click(function(){thix.options.oncancel.call(this,thix);});
			}
			//绑定键盘事件
			this._box.find(".box_close, .box_cancel, .box_ok").unbind('keypress').bind("keypress",function(e){
				e = e||window.event;
				var key = e.which||e.charCode||e.keyCode;
				switch(key)
				{
					case 27://esc
						thix.close();
						return false;
					case 32://空格
					case 13://回车
						$(document.activeElement).trigger("click");
						return false;
				}
			});
		},
		//打开box事件
		openEvent:function(){
			if(this._isOpen)return;
			var thix=this;
			//自动更新位置
			if(this.options.autoPosition)
			{
				if(this.options.position!="center")
				{
					var timer;
					this._events["scroll"]=function(){
							clearTimeout(timer);
							timer=setTimeout(function(){
								thix.setPosition();
							},300);
					};
					this.win.scroll(this._events["scroll"]);
				}
				thix._events["resize"]=function(){
					thix.setPosition();
				};
				this.win.resize(thix._events["resize"]);

			}//if end
			if(this.options.timeout>0)
			{
				this._events["timeout"]=setTimeout(function(){
					thix.close();
				},this.options.timeout);
			}//if end
			//点击空白处事件
			this._onbox=true;
			if(this.options.clickOut)
			{
				this._events["box_click"]=function(event){
					thix._onbox=true;
				};
				this._events["document_click"]=function(event){
					if (event.button!=0) return true;
					if(thix._onbox===false)
					{
						thix.options.clickOut(thix);
					}
					thix._onbox=false;
				};
				this._box.on("click",this._events["box_click"]);
				this.doc.on("click",this._events["document_click"]);
			}//if end

			if(this.options.mask)
			{
				this.showMask();
			}
			if(this.options.onopen) this.options.onopen(this);
			if (this.options.focus) {
				$(this.options.focus).focus();
			}
			this._isOpen=true;
		},
		//关闭box事件
		closeEvent:function(){
			clearTimeout(this._events["timeout"]);
			if(this._events["scroll"])this.win.off("scroll",this._events["scroll"]);
			if(this._events["resize"])this.win.off("resize",this._events["resize"]);
			if(this._events["box_click"])this._box.off("click",this._events["box_click"]);
			if(this._events["document_click"])this.doc.off("click",this._events["document_click"]);

			if(this.options.mask)
			{
				this.hideMask();
			}
			if(this.options.onclose)this.options.onclose(this);
			//设置关闭后的焦点
			if (this.options.blur) {
				$(this.options.blur).focus();
			}
			this._isOpen=false;
			if(this._type.closeEvent)this._type.closeEvent(this);
		},
		//设置位置
		setPosition:function(){
			if(this.options.position=="center")	{
				var w_t=this.win.scrollTop();
				var w_l=this.win.scrollLeft();
				var w_h=this.win.height();
				var w_w=this.win.width();
				var box_w=this._box.outerWidth(true);
				var box_c_h=this._b_content.outerHeight(true);
				var box_h_h=this._box.find(".box_header:first").outerHeight(true);
				var box_h_b=this._box.find(".box_bottom:first").outerHeight(true);
				var box_h=box_c_h+box_h_h+box_h_b;
				var offset={
					x : Math.ceil((w_w-box_w)/2),
					y : Math.ceil((w_h-box_h)/2)
				};
				var css={
					position:"fixed"
				};

				//窗口可视面积比box大
				if(offset.x<0)
				{
					//css.width=w_w;//重新调整大小
					offset.x=0;
				}
				if(offset.y<0)
				{
					//css.height=w_h;
					offset.y=0;
				}
				css.top=offset.y;
				css.left=offset.x;

				if(ol.isIE6)
				{
					css.position="absolute";

					var h=$("html");
					if(!h.css("background-image")||h.css("background-image")=="none")h.css("background-image","url(about:blank)");

					this._box[0].style.setExpression('left', '(document.documentElement || document.body).scrollLeft+'+css.left+'+"px"');
					this._box[0].style.setExpression('top', '(document.documentElement || document.body).scrollTop+'+css.top+'+"px"');
					delete css["top"];
					delete css["left"];
				}
				this._box.css(css);
				return;
			}else if($.type(this.options.position) === "object"){
					var top=this.options.position.top||0;
					var left=this.options.position.left||0;
					if(this.options.position.ref)
					{
						var ref=$(this.options.position.ref);
						var offset=ref.offset();
						top+=offset.top;
						top+=ref.outerHeight(true);
						left+=offset.left;
					}
					this._box.css({
						top:top,
						left:left
					});
			}
		},
		//构造窗口显示内容
		renderContent:function(content,callback){
			if(typeof(content)!="undefined"&&content!=null)this._initedContent=false;
			if(!this._initedContent||!this.options.remember)
			{
				this._type.setContent(this,content,callback);
			}else{
				this.setPosition();
				this.openEvent();
			}
		},
		open:function(content,options){
			if(this._isOpen)
			{
				this.closeEvent();
			}
			this.options  = $.extend({},this.options, options);
			var thix=this;
			this.renderContent(content,function(){
				thix.openEvent();
			});
			this._box.stop().addClass('scaleIn').css({
				"visibility":"visible"
			});

			return this;
		},
		close:function(){
			this.closeEvent();
			this._box.removeClass('scaleIn').css("visibility","hidden");
		},
		isOpen:function(){
			return this._isOpen;
		},
		find:function(selector){
			return this._b_content.find(selector);
		},
		getBox:function(){
			return this._box;
		},
		//遮罩
		initMask:function(){
			if(document.getElementById('olMask')) {
				this._mask = $("#olMask");
				return;
			}
			this._mask=$("<div class='ol_mask hide' id='olMask'></div>").css({
				zIndex: this.options.zIndex
			}).appendTo('body');
			var thix=this;
			this._events["masker_resize"]=function(){
				thix._mask.css(thix._getWinSize());
			};
		},
		showMask:function(){
			var css = this._getWinSize();
			css.opacity = this.options.maskOpacity;
			this._mask.css(css).show();
			this.win.on("resize",this._events["masker_resize"]);
		},
		hideMask:function(){
			this._mask.hide();
			this.win.off("resize",this._events["masker_resize"]);
		},
		defaults : {
			boxid: "olBox",
			boxclass: "",
			type: 'dialog',
			title: '',
			width: 0,
			height: 0,
			showTitle: true,
			showButton: true,
			showCancel: true,
			showOk: true,
			showClose: true,
			okBtnName: '',
			cancelBtnName: '',
			timeout: 0,
			draggable: false,
			mask: true,
			maskOpacity : .3,
			zIndex: 500,
			remember:false,
			position: 'center',
			clickOut: null,//点击box外调用的function
			onclose: null,
			onopen: null,
			oncancel: null,
			onok: null,
			blur:null,//关闭窗口后的焦点
			focus:null,//打开窗口中默认的焦点
			autoHeight : true,//自动高度(当内容高度大于设定的高度)
			autoPosition : true,//自动修正位置（当scroll resize时）
			//For type=ajax
			cache: false,
			lang : 'us'
		}
	};
	ol.ui.box = function (content, options) {
		return new box(content, options);
	};


})(jQuery);
