/**
 * pager.js
 * @update		2010-11-3
 * @version		0.2
 */
(function($) {
	var isNumeric=function(str){
		var re=/^\d*$/;
		return re.test(str);
	};
	var lang = {
		"us" : {"first" : "First", "prev" : "Prev", "next" : "Next", "last" : "Last", "site" : "Page"},
		"ru" : {"first" : "Первый", "prev" : "Предыдущая", "next" : "Следующая", "last" : "Последний", "site" : "Страница"},
		"it" : {"first" : "Primo", "prev" : "Precedente", "next" : "Successivo", "last" : "Ultimo", "site" : "Страница"},
		"fr" : {"first" : "Première", "prev" : "Précédent", "next" : "Suivant", "last" : "Dernier", "site" : "Page"},
		"es" : {"first" : "Primera", "prev" : "Previo", "next" : "Siguiente", "last" : "Última", "site" : "Página"},
		"de" : {"first" : "Erste", "prev" : "Zurück", "next" : "Vor", "last" : "Letzte", "site" : "Seite"},
		"br" : {"first" : "Primeiro", "prev" : "Anterior", "next" : "Próximo", "last" : "Último", "site" : "Página"}
	}
	$.fn.pager=function(options) {
			function renderPager(options, obj) {
				this.render = function() {
					if (options.pageNumber > options.pageCount) {
						//自动跳到第一页
						//this.callBack(1);
						return;
					}
					var $pager = $('<div class="ec_pager"></div>');
					var _item=options.item;
					for(var i=0;i<_item.length;i++)	{
						$pager.append(this.handler(_item[i]));
					}
					return $pager;
				}
				//render end
				this.handler=function(label){

					switch(label)
					{
						case "recordCount":
							var text=options.text["recordCount"];
							text=text.replace(/{#recordCount}/g,options.recordCount);
							return $('<span class="recordCount">' +text + '</span>');
						case "first":
						case "prev":
						case "next":
						case "last":
							return this.renderButton(label);
						case "pageCount":
							var text=options.text["pageCount"];
							text=text.replace(/{#pageCount}/g,options.pageCount);
							return $('<span class="ec_pager_count">' +text + '</span>');
						case "qpage":
							return this.renderQPages();
						case "pageSizer":
							return this.renderPageSizer();
						case "quickPager":
							return this.renderQuickPager();
						default:
							return '<em class="text">'+label+'</em>';

					}
				}
				this.renderButton = function(buttonLabel) {
					var destPage = 1;
					var buttonText=options.text[buttonLabel];
					switch (buttonLabel) {
					case "first":
						destPage = 1;
						break;
					case "prev":
						destPage = options.pageNumber - 1;
						break;
					case "next":
						destPage = options.pageNumber + 1;
						break;
					case "last":
						destPage = options.pageCount;
						break;
					case "pageCount":
						destPage = options.pageCount;
						break;
					}
					var exp=new RegExp('{#'+buttonLabel+'}',"gi");
					buttonText=buttonText.replace(exp,destPage);

					var $Button = $('<a class="p_'+buttonLabel+'" href="javascript:;">' + buttonText + '</a>');
					if (buttonLabel == "first" || buttonLabel == "prev") {
						options.pageNumber <= 1 ? $Button.addClass('p_btn_un') : $Button.on("click", {E: this},
						function(v) {
							v.data.E.callBack(destPage);
						});
					} else {
						options.pageNumber >= options.pageCount ? $Button.addClass('p_btn_un') : $Button.on("click", {E: this},
						function(v) {
							v.data.E.callBack(destPage);
						});
					}
					return $Button;
				}
				//renderButton end

				this.renderQPages = function() {
					var text=options.text["qpage"];
					var container=$('<span class="ec_page_num"></span>');
					var tmp = parseInt(options.qpageSize / 2);
					var startPoint = 1;
					var endPoint = options.qpageSize;

					if (options.pageNumber > tmp && options.pageCount > options.qpageSize) {
						startPoint = options.pageNumber - tmp;
						if(startPoint<=1) startPoint = 2;
						endPoint = options.pageNumber + tmp;

						var $Button = $('<a href="javascript:;">1</a>').on("click", {E: this}, function(v) {
							v.data.E.callBack(1);
						});
						container.append($Button);
						container.append('&nbsp;...&nbsp;');
					}
					if (endPoint > options.pageCount) {
						startPoint = options.pageCount - tmp*2;
						endPoint = (options.pageCount > options.qpageSize && (options.pageCount-options.pageNumber > tmp)) 
									? (options.pageCount -1)
									: options.pageCount;
					}
					if (startPoint < 1) {
						startPoint = 1;
					} // loop thru visible pages and render buttons
					for (var page = startPoint; page <= endPoint; page++) {
						var currentButton = $('<a href="javascript:;">' + text.replace(/{#qpage}/g,page) + '</a>');
						(page == options.pageNumber)
							? currentButton.addClass('current')
							: currentButton.on("click", {E: this}, function(v) {
								v.data.E.callBack(this.firstChild.data);
							});
						currentButton.appendTo(container);
					}
					if(options.pageCount > options.qpageSize && (options.pageCount-options.pageNumber > tmp)) {
						container.append('&nbsp;...&nbsp;');
						var $Button = $('<a href="javascript:;">' + text.replace(/{#qpage}/g,options.pageCount) + '</a>');
						(options.pageNumber >= options.pageCount)
							? $Button.addClass('p_btn_un')
							: $Button.on("click", {E: this}, function(v) {
								v.data.E.callBack(options.pageCount);
							});
						container.append($Button);
					}
					return container;
				} //renderQPages end

				this.renderQuickPager = function() {
					if(options.pageCount<=1)return null;
					var input=null;
					var t1 = $('<span class="ec_page_quick"></span>');
					if (options.pageCount <= 10) {
						var html = '<select class="vam">';
						for (var i = 1; i <= options.pageCount; i++) {
							html += '<option value="' + i + '"';
							if (i == options.pageNumber) {
								html += ' selected';
							}
							html += '>' + i + '</option>';
						}
						html += '</select>';
						input = $(html);
						input.on("change", {E: this}, function(v) {
							v.data.E.callBack($(this).attr("value"));
						});
					} else {
						input = $('<span class="padding_left_10 ec_pager_quick_text">Total of <em class="red">' + options.pageCount + '</em> pages &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span id="chatpage" class="ec_page_chat"><input id="quickPager" class="ec_page_pagenum vam" value="' + options.pageNumber + '" style="width:'+(options.pageNumber.toString().length+1)*10+'px;"><a class="ec_page_enter current vam" href="javascript:void(0)">GO</a></span>');
						input.find("#quickPager").on("keypress", {E: this},function(e) {
							var E = e.data.E;
							if (e.keyCode == 13) {
								var p = $(this).attr("value");
								if (!isNumeric(p)) {
									alert("Please enter a number！");
									return false;
								}
								if (parseInt(p) > options.pageCount) {
									alert("Maximum number of pages to " + options.pageCount + "！");
									return false;
								}
								E.callBack(p);
								return false;
							}
						});
						input.find("a.ec_page_enter").on("click", {E:this},function(e) {
							var E=e.data.E;
							var p = input.find("#quickPager").attr("value");
							if (!isNumeric(p)) {
								alert("Please enter a number！");
								return false;
							}
							if (parseInt(p) > options.pageCount) {
								alert("Maximum number of pages to " + options.pageCount + "！");
								return false;
							}
							E.callBack(p);
							return false;
						});
					}
					t1.append(input);
					return t1;
				}
				//renderQuickPage end

				this.renderPageSizer = function() {

					var t1;
					if (options.rowList) {
						var text=options.text["pageSizer"];
						text=text.replace(/{#pageSizer}/g,'</span><div id="select" class="fl"></div><span class="fl">');
						t1 = $('<span class="text pageSizer"><span class="fl">'+text+'</span></span>');
						var rowListHtml = '<select name="pageSize">';
						for (var i = 0; i < options.rowList.length; i++) {
							rowListHtml += '<option value="' + options.rowList[i] + '"';
							if (options.rowList[i] == options.pageSize) {
								rowListHtml += ' selected';
							}
							rowListHtml += '>' + options.rowList[i] + '</option>';
						}
						rowListHtml += "</select>";
						var input2 = $(rowListHtml);
						input2.bind("change", {
							E: this
						},
						function(v) {
							if(options.pageSize==this.value)return;
							options.pageSize=this.value;
							v.data.E.callBack(options.pageNumber);
						});
						$("#select", t1).append(input2);
					}
					return t1;
				}
				//renderPageSizer end
				this.callBack = function(page) {
					if(typeof(page) != "number") page = parseInt(page);
					if (page) options.pageNumber = page;
					if (typeof(options.callBack) == "function") options.callBack(options);
				}

				obj.empty().append(this.render());
			}
			//renderPager end*/

			var _lang = lang[ec.lang.globle];
			var _default = {
				pageNumber: 1,
				pageCount: 1,
				pageSize:null,
				recordCount:0,
				qpageSize:9,
				rowList:null,//分页大小数组
				text:{//显示文字
						recordCount:"Count:{#recordCount}",
						first: '&lt;&nbsp;' + _lang.first,
						prev: '&lt;&nbsp;' + _lang.prev,
						qpage:"{#qpage}",
						pageCount:"{#pageCount}",
						next: _lang.next + '&nbsp;&gt;',
						last: _lang.last + '&nbsp;&gt;',
						pageSizer:'pageSizer : {#pageSizer}'
					},
				item:["recordCount","first","prev","qpage","next","quickPager"]//显示样式
			};
			options.text  = $.extend({}, _default.text, options.text);
			options  = $.extend({}, _default, options);
			options.pageNumber = parseInt(options.pageNumber);
			options.pageCount = parseInt(options.pageCount);
			return this.each(function() {
				new renderPager(options, $(this));
			});
		}
})(jQuery);
