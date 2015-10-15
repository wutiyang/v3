/** 
 * 公共方法，改动或追加比较多
 * @class 	allPage
 * @author  gongshan
 * @date	2015/06/04
 * @dependon jquery-1.4.1.min.js or later、 ec.lib.js
 */
ol.debug = (ol.debug || false);//调试模式
log('debug:' + ol.debug);
					
ol.load.define("ec.ui.box" , [
	{mark:"ec.ui.box", uri: "../plugs/ec.box/box.js?20140530",type: "js", depend:true}
]);

ol.load.define("ec.ui.tip" , [
	{mark:"ec.ui.tip", uri: "../plugs/ec.tip/tip.js?20140530",type: "js", depend:true}
]);

ol.load.define("ec.ui.countdown" , [
	{mark:"ec.ui.countdown", uri: "../plugs/ec.countdown/countdown.js?20140710",type: "js"}
]);


ec.load('ec.ui.box');


/* 系统级错误提示 */
ec.checkApiStauts = function(json, obj) {
	var errorCode = json.errorCode;
	var tips = langtips;
	var showMsg = function() {
		var msg = json.detail || json.msg || tips[msg];
		if(obj){
			obj.html(msg).show();
		} else {
			ec.ui.box('<span class="red">'+ msg + '</span>',{width:250, height:40, showCancel: false}).open();
		}
	};

	if(errorCode !== 0) {
		if(gid('ol_load')) $('#ol_load').hide();
		logger.warn('errorCode:' + errorCode);

		showMsg();
		return false;
	}
	return true;
};

//icon 提示
ec.ui.tip = function (selector, opt) {
	ec.load('ec.ui.tip',{
		loadType:"lazy",
		onload : function () {
			if(!selector) return;
			var $ele = $(selector);
			$ele.each(function (i, n) {
				$(n).hover(function () {
					$(n).tip(opt);
				}, function (){
					$('#ecTips').hide();
				});
			});
		}
	});
};
/**
 * 防止重复操作，设置操作间隔时间
 * @param  {Object}     thix    当前操作对象
 * @param  {int}        s       间隔时间，毫秒
 * @return {[bool]}     true || false
 */
ec.ui.eventTimeLimits = function (thix, s) {
    var $thix = $(thix);
    var ms = s || 3000; //间隔时间 毫秒
    var isBtn = $thix.hasClass('btn');
    if($thix.data('isDisabled')) return false;
    $thix.data('isDisabled', 'true');
    if(isBtn) $thix.addClass('time_out');
    setTimeout(function () {
        $thix.removeData('isDisabled');
        if(isBtn) $thix.removeClass('time_out');
    }, ms);
    return true;
};

/*select*/
ec.ui.select=function(){
    var selectBox=$('.select-box');
        selected=selectBox.find('.selected'),
        dropBox=selectBox.find('.drop-box'),
		className='select-current';
	$('body').click(function(){
		$('.select-current').removeClass(className);
	})
    selected.on('click',function(e){
		e.stopPropagation();
        selectBox.removeClass(className);
        $(this).closest('.select-box').addClass(className);
    })
    dropBox.find('li').on('click',function(e){
		e.stopPropagation();
        var me=$(this),
            parent=me.closest('.select-block'),
            selected=parent.find('.selected span'),
            value=selected.text(),
            text=me.text(),
			attrId=me.attr('attrid'),
            drop=parent.find('.drop-box'),
			SKU=me.closest('.attr').attr('sku');
        if(text!=value){
			selected.removeClass('default');
			if(attrId=='default'){
				selected.addClass('default');
			}
            selected.text(text).attr('attrid',attrId);
            drop.find('li').removeClass('hide');
            me.addClass('hide');
            me.closest('.select-box').removeClass(className)
        }
		// if(SKU=='')
			if(me.closest('.select-box').attr('class').indexOf('select-div')!=-1)
				ec.ui.getAttrOption(me,attrId);
		
    })
}
 /**
     * 选择sku,获取相应的sku组合数据
     * @param  {Object}     thix   this
     * @param  {int}        pid    当前商品pid
     * @param  {Boolean}    isMain 是否为主商品
     * @return {[type]}     null
     */

ec.ui.getAttrOption = function (thix,attrId) {
	var arry=[],
	idList=[];
	var selectTr=thix.closest('tr'),
		selectValue=selectTr.attr('value'),
		attr=thix.closest('.attr'),
		bool=attr.attr('bool'),
		data=eval(attr.attr('pidDate')),
		other=attr.find('tr[value!='+selectValue+']');
		other.find('li[attrid!=default]').addClass('disabled');
	var selectModule=thix.closest('.selectModule'),
		elidePrice=selectModule.find('.elidePrice'),
		salePrice=selectModule.find('.salePrice');
		if(thix.attr('class').indexOf('disabled')!=-1){
			other.find('li[attrid=default]').click();
		}
		if(thix.attr('attrid').indexOf('default')!=-1){
			other.find('li').removeClass('disabled');	
		}
		var lenth=attr.find('tr .selected span[attrid=default]').length;
		var attridList='';
		if(lenth==0){
			var attridSelect=attr.find('tr .selected');
			$.each(attridSelect,function(i,n){
				attridList+=attridSelect.eq(i).find('span').attr('attrid')+'-';
			})		
		}
		$.each(data,function(i,n){//i=sku
			$.each(n,function(y,k){
				if(y=='attr'){
					var s='';				
					$.each(k,function(x,m){//m=212
						s+=m+'-';
						if(m==attrId){
							arry.push(i);	
						}
					})
					if(attridList==s){
						attr.attr('sku',i);
					}
				}
			
			})
		})
		$.each(arry,function(i,n){
			$.each(data[n],function(y,k){
				if(y=='attr')
				$.each(k,function(x,m){//m=212
					idList.push(m);
				})
			})	
		})
		$.each(idList,function(i,n){
			other.find('li[attrid='+n+']').removeClass('disabled');	
		})
		if(lenth==0){
			var sku=attr.attr('sku');
			//sku下架判断
			if(data[sku].sku_status!=1){
				//禁用add to cart
				$("#addToCart").addClass("icon-view-dis").attr('href',"javascript:;");
			}else{
				var cartButton = $("#addToCart").attr('data-url');
				$("#addToCart").removeClass("icon-view-dis").attr('href',cartButton );
			}
			elidePrice.html(data[sku].elidePrice);
			salePrice.html(data[sku].salePrice);
			if(bool){
				var	shopsTo=selectModule.find('.shops-to'),
					warehouse=shopsTo.find('.warehouse'),
					warehouseName=shopsTo.find('i'),
					warehouseClass=data[sku].product_sku_warehouse_class;
				if(warehouseClass=='' || warehouseClass.length<=0){
					shopsTo.hide();	
				}else{
					warehouse.html(data[sku].product_sku_warehouse);
					warehouseName.attr('class',warehouseClass);	
					shopsTo.show();
				}
				$('#picZoom .imgZoomLi li[data-sku='+sku+']').click();
			}
		}
};

/**
 * 图片延时加载
 * @param  {String | jQuery Object} selector jQuery选择器
 * @return {[type]}          无
 */
ec.ui.lazyLoad = function(selector){
	var _window = $(window),
		_doc =  ol.isIE ? document.body : document.documentElement,
		_clientHeight, //可见区域高度
		_scrollTopSrart = 0, //网页卷上去的高度
		_scrollTopEnd = 0, //网页卷上去的高度+网页可见区域高度
		_imgList = [],
		_timer,

		_renderImg = function (img) {
			var top = img.offset().top;
			var pos = top + img.height();
			if((top >= _scrollTopSrart && top <= _scrollTopEnd) || (pos >= _scrollTopSrart && pos <= _scrollTopEnd))
			{
				img.attr("src" , img.attr("data-lazysrc"));
				img.removeAttr("data-lazysrc");
				return true;
			}
			return false;
		},

		_bindEvent = function () {
			var scrollEvent = function(){
				clearTimeout(_timer);
				_timer = setTimeout(function(){

					_scrollTopSrart = _window.scrollTop();
					_scrollTopEnd = _scrollTopSrart + _clientHeight;

					var img;

					for(var i = 0 ; i < _imgList.length ; i ++)	{
						img = _imgList[i];
						if(_renderImg(img)) {
							_imgList.splice(i , 1);
							i--;
						}
					}

					if(!_imgList || _imgList.length == 0)
					{
						window.onscroll = null;
						window.onresize = null;
					}

				} , 100);

			},

			resizeEvent = function(event) {
				_clientHeight = _doc.clientHeight;
			};

			window.onscroll = function (){ scrollEvent(); };
			window.onresize = function (){ resizeEvent(); };

			_clientHeight = _doc.clientHeight;
			_scrollTopSrart = _window.scrollTop();
			_scrollTopEnd = _scrollTopSrart + _clientHeight;

		};

	_bindEvent();//绑定事件

	$(selector).each(function(){
		var thix = $(this);
		if(thix.attr("data-lazysrc")) {

			if(!_renderImg(thix)) {
				if(!thix.attr("src")) {
					thix.attr("src" , "resource/images/common/default.png");
				}
				_imgList.push(thix);
			}
		}

	});
};


/**
 * [货币转换]
 * @return {[type]} [description]
 */
ec.ui.searchCountry = function () {
	if(!gid('countryList')) return;
	var $countryList = $('#countryList'),
		$list = $countryList.html(),
		countryList = $('#allCurrency').val().trim().split(',');
	ec.currency = function (currencyType) {
		// cookie 保留货币种类
		var url=window.location.host;
		ec.cookie.set('currency',currencyType.toUpperCase(),{expires:7, path:'/',domain:'.eachbuyer.com',secure:false});
		var urlOf= url.indexOf('#');
		var url = url.substring(0,urlOf);
		var url = window.location.href.replace(/currency=([A-Z]{3})(&|$)/i,'');
		window.location.href = url;
	};

	/* 自动查找货币列表 */
	$('#currencyKeywords').on('keyup', function () {
		var val = $(this).val().trim();
		var html = '';
		var text = '';
		if(!val) {
			$countryList.html($list);
			return;
		}
		for(var i = 0; i < countryList.length; i += 1) {
			text = countryList[i];
			if(text.indexOf(val.toUpperCase()) > -1) {
				html = '<a href="javascript:;" onclick="ec.currency(\''+ text +'\');" rel="nofollow" class="tab_'+ text +'">';
				html += '<i class="icon_country tab-'+ text +'"></i><span>'+ text +'</span>';
				html += '</a>';
			}
		}
		$countryList.html(html);
	});
};


//nav部份
ec.navInit = function () {
    if(!gid('pageNav')) return;
    var setTimeoutHover = null;
    //分类展示效果
    var widths = [0, 275, 510, 760, 980]; //宽度设置
    $('#categrayAll .list').hover(function() {
        if(setTimeoutHover !== null) clearTimeout(setTimeoutHover);
        var $thix = $(this);
        setTimeoutHover = setTimeout(function () {
            var $thisSubList = $thix.children('.sub-list');
            var $col = $thisSubList.find('.column');
            var colLen = $col.size();
            var w = widths[colLen];

            if(colLen < 1) return;
            $thisSubList.width(w).find('.sub-padding').width(w - 40);
            $thix.addClass('li-hover');
        }, 200);
    }, function() {
    	if(setTimeoutHover !== null) clearTimeout(setTimeoutHover);
        var $thix = $(this);
        var $thisSubList = $(this).children('.sub-list');
        setTimeout(function (){
            $thix.removeClass('li-hover');
            $thisSubList.width(0);
        }, 50);
    });

	//搜索关键字检测
	var $keywords = $('#keywords');
	var keywordsVal = $keywords.data('value');
	$('#searchForm').submit(function(){
		keywordsVal = $.trim($keywords.val()) || $.trim(keywordsVal);
		if(!keywordsVal) {
			return false;
		}
		$keywords.val( keywordsVal );
	});
	ec.form.tips.label($keywords);
	//搜索下拉框
	var $searchList = $('.search-list'),
		liArr = [],
		currentTxt,
		time=null,
		bout=false,
		flag=0,
  		ajax;
	$keywords.on('keyup',function(e){
    	flag++;
		if (e.keyCode != 40 && e.keyCode != 38 && e.keyCode !=13) {
            currentTxt = $.trim($("#keywords").val());
            if(bout==true){
            	ajax.abort();
            }
    		clearTimeout(time);
    		var keywordsVal = $keywords.data('value');
    		keywordsVal = $.trim($keywords.val()) || $.trim(keywordsVal);
			if(!keywordsVal) {
				return false;
			}
    		resultOption(currentTxt);
        }
        
    })
    $keywords.focus(function(){
    	$("#status").val(1);
    	$searchList.show();
    })
    $keywords.blur(function(){
    	$("#status").val(0);
    	setTimeout(function(){
    		$searchList.hide();
    	},1000)
    	
    })
    var resultOption = function(val){
    	bout=true;
    	var number=flag;
    	var time = setTimeout(function(){
    		if(number<flag){
				return false;
			}
			ajax = $.ajax({
	      		url:"/search/getKeyWords",
	      		data:{keywords:val,number:flag},
	      		type: 'GET',
				dataType: 'json',
				success:function(data){
					var number=data.number,
						text=data.data;
					$('#status').val(number);
					liArr=[];
					$.each(text,function(i,obj){
			            liArr.push('<li><a href="/search/?keywords='+obj+'">'+obj+'</a></li>');
			  		})
			  		$searchList.html(liArr.join('')).show();
			  		resultCallback();
				}
	      	})
      	},100);
      	bout=false;	
    };
    var resultCallback = function(data){
        var index = -1;	
        document.documentElement.onkeydown = function (e) {
            e = window.event || e;
            var lisize = $(".search-list li");
            if (e.keyCode == 40) { 
                if (++index == lisize.length) {
                    index = -1;
                    $("#keywords").val(currentTxt);
                    $(".search-list li").removeClass("hover");
                }
                else {
                    $("#keywords").val($(lisize[index]).text());
                    $(lisize[index]).siblings().removeClass("hover").end().addClass("hover");
                }
            }
            if (e.keyCode == 38) {
                if (--index == -1) {
                    $("#keywords").val(currentTxt);
                    $(".search-list li").removeClass("hover");
                }
                else if (index == -2) {
                    index = lisize.length - 1;
                    $("#keywords").val($(lisize[index]).text());
                    $(lisize[index]).siblings().removeClass("hover").end().addClass("hover");
                }
                else {
                    $("#keywords").val($(lisize[index]).text());
                    $(lisize[index]).siblings().removeClass("hover").end().addClass("hover");
                }
            }
        }
	}
};
	
/* 返回顶部按钮  */
ec.ui.tools = function () {
	var $toTop = $('#gotop');
	if(!$toTop[0]) return;
	$(window).scroll(function(){
		t = $(document).scrollTop();
		if(t > 50){
			$toTop.fadeIn('slow');
		}else{
			$toTop.fadeOut('slow');
		}
	})
	$toTop.click(function(){
		ec.ui.scrollTo(0);
	})
};


/* footer 相关功能 */
ec.footerInit = function (footerSubmit) {
	if(!gid(footerSubmit)) return;
	var $footerSubmit=$('#'+footerSubmit),
		text=$footerSubmit.find('input[name="email"]'),
		$errorObj=$footerSubmit.find('.f-error'),
		button=$footerSubmit.find('button');
	ec.form.tips.label('#'+text.attr('id'));
	button.click(function(){
		var val = text.val().trim(),
			$errorMsg = $errorObj.children('p'),
			$errorEmpty = $errorObj.children('span'),
			$error=$errorObj.children('div'),
			emailReg = ec.form.regex.email;
		$errorMsg.hide();
		$errorEmpty.hide();
		$error.hide();
		if(!val){
			$errorEmpty.show()
			$errorMsg.hide();
			return false;
		}
		if(!val.match(emailReg)) {
			$errorEmpty.hide()
			$errorMsg.show();
			return false;
		}
		$.ajax({
			url:"/common/subscribe",
			data:{"email":val},
			type: 'POST',
			dataType: 'json',
			async:false,  
			success: function(json){
				if( json && json.status != "200" ){
					$error.show().html(json.msg);
				}else{
					window.location.href=json.data.url;
				}
			}
		});
	});
};
//header部份
ec.headerInit = function () {
	//货币转换
	ec.ui.searchCountry();
	//顶部下拉动画效果
	$('#topbar .select-block').on('mouseout', function () {
		$(this).find('input').blur();
	});
	//导航
	ec.navInit();
};

function  productEvent(json) {
	dataLayer.push({
		"event": "productClick",
		"ecommerce": {
			"click": {
				"actionField": {"list": json.list},
				"products": json.data
			}
		}
	});
}


/*加入、删除 购物车按钮*/
function addCartEvent(json){
	dataLayer.push({
		"event": "addToCart",
		"ecommerce": {
			"add": {
			"products": json.data
			}
		}
	});
}
function removeCartEvent(json){
	dataLayer.push({
		"event": "removeFromCart",
		"ecommerce": {
			"remove": { 
			"products":json.data
			}
		}
	});
}

/*banner页面*/
function onPromoClick(json) {
dataLayer.push({
	"event": "promotionClick",
	"ecommerce": {
		"promoClick": {
			"promotions": [
			{
				"id": json.data.id,
				"name": json.data.name
			}]
		}
	}
	});
}

/*购物流程*/
function onCheckout(json) {
var option=json.option.length<=0 ? $('#payment .method-list .on').attr('paymentid') : json.option;
dataLayer.push({
	"event": "checkout",
	"ecommerce": {
		"checkout": {
			"actionField": {"step": json.step, "option": option},
			"products": json.data
		}
	}
});
}
function onCheckoutOption(json) {
	dataLayer.push({
		'event': 'checkoutOption',
		'ecommerce': {
			'checkout_option': {
				'actionField': {'step':json.step, 'option': json.option}
			}
		}
	});
}

$(document).ready(function(){
	//图片延时加载
	ec.ui.lazyLoad($('body .main').find("img"));
	ec.headerInit();
	ec.footerInit('footerSubmit');
	//初始化右侧工具条
	ec.ui.tools();
	//select插件
    ec.ui.select();
});


