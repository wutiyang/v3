/**
* @authors zhuzhengwei
* @date    2014-11-1
*/
ec.pkg('ec.details');

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
//声明product对象
ec.pkg('ec.product');
ec.product.addToWish = function(ele, prcId, opt) {
    var $thix = $(ele);
    var okClass = 'wish-red';
    var _default = {
        width : 200,
        showLoginBox : true
    };
    opt = $.extend(_default, opt);
    //防止恶意点击
    if(!prcId || !ec.ui.eventTimeLimits($thix)) return;
    $.ajax({
        url : baseurl+"currentPassword.php",
        data : {"prcId" : prcId},
        loading: true,
        dataType: 'json',
        success: function(json){
            if(json.success) {
                $thix.toggleClass(okClass);
            } 
        }
    })

};
/*select*/
ec.details.select=function(){
    var selectBox=$('.select-box');
        selected=selectBox.find('.selected'),
        dropBox=selectBox.find('.drop-box'),
		className='select-current';
	$('body').click(function(){
		$('.select-current').removeClass(className);
	})
    selected.click(function(e){
		e.stopPropagation();
        selectBox.removeClass(className);
        $(this).closest('.select-box').addClass(className);
    })
    dropBox.find('li').click(function(e){
		e.stopPropagation()
        var me=$(this),
            parent=me.closest('.select-block'),
            selected=parent.find('.selected span'),
            value=selected.text(),
            text=me.text(),
			attrId=me.attr('attrId'),
            drop=parent.find('.drop-box');
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
    })
}
ec.details.countPrice=function(){
    var Frequently=$('#Frequently'),
        priceList=Frequently.find('.current').closest('li').find('.p-price i'),
        savePriceList=Frequently.find('.current').closest('li').find('input:hidden');
        count=0,
        saveCount=0;
        countPrice=$('#countPrice');
        if(priceList.length>0){
            priceList.each(function(i,n){
                saveCount+=parseFloat(savePriceList.eq(i).val());
                count+= parseFloat(priceList.eq(i).text());
            })
        }
        saveCount=saveCount-count;
        countPrice.find('.count i').html(count);
        countPrice.find('.save i').html(saveCount);
		//搭配商品属性未选择提示
   		ec.details.listToCart();
}
ec.details.imgZoom=function(){
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
       imgSroll($con.eq(i),$con.attr('size'))
    })
}
ec.details.picZoom = function () {
    var picZoom=$('#picZoom'),
        imgZoomLi=picZoom.find('.imgZoomLi'),
        arrowTop=picZoom.find('.arrow_top'),
        arrowArrow=picZoom.find('.btn-arrow'),
        arrowBottom=picZoom.find('.arrow_bottom'),
        li=picZoom.find('li'),
        w=li.width()+9,
        len=li.length-6,
        count=0;
    
    function scrollDiv(){
        imgZoomLi.animate({'top':'-'+w*count+'px'});
    }
    arrowTop.click(function(){
        count++;
        arrowArrow.removeClass('arrow-disabled')
        if(count>len-1){
            $(this).addClass('arrow-disabled'); 
            count=len;
        }
        var current=$('.imgZoomLi .current'),
        index=current.index()+1;
        if(index==count){
            current.removeClass('current').next().click();

        }   
        scrollDiv(count);
    })
    arrowBottom.click(function(){
        count--;
        arrowArrow.removeClass('arrow-disabled');
        if(count<1){
            
            $(this).addClass('arrow-disabled');
            count=0;
        } 
        var current=$('.imgZoomLi .current'),
        index=current.index()-1,
        subCate=$('#dImgZoomBox .sub-cate');
        if(count+6-index==1){
            current.removeClass('current').prev().click();
        }   
        scrollDiv(count);
    })
    picZoom.find('li').click(function(){
        var proOriginal =$('.pro-original'),
            src=$(this).find('img').attr('src'),
			sku=$(this).find('img').closest('li').attr('data-sku'),
            index=$(this).index();
        $(this).addClass('current').siblings().removeClass('current');  
        proOriginal.find('img').attr({'src':src,'data-sku':sku});
    })
	$('#bigPic').click(function(){
		var sku=$(this).attr('data-sku');
		var src=$(this).attr('src');
		$('.box_zoom_content').find('img').attr('src',src).attr('data-sku',sku);
		$('<div class="shade shadebg" id="shade"></div>').insertBefore('.box_zoom_main');
		$('#boxZoomMain').show();
	})
	$('.box_zoom_arr_r').click(function(){
		var me=$('.box_zoom_content').find('img');
			var index=me.attr('data-sku');
			var imgZoomLi=$('.imgZoomLi').find('[data-sku='+index+']').next();
			me.attr({'src':imgZoomLi.find('img').attr('src'),'data-sku':imgZoomLi.attr('data-sku')})
	})
	$('.box_zoom_arr_l').click(function(){
		var me=$('.box_zoom_content').find('img');
			var index=me.attr('data-sku');
			var imgZoomLi=$('.imgZoomLi').find('[data-sku='+index+']').prev();
			me.attr({'src':imgZoomLi.find('img').attr('src'),'data-sku':imgZoomLi.attr('data-sku')})
	})
	$('.box_close').click(function(){
		$('#boxZoomMain,#shade').hide();
	})
   
};
//数字输入验证
ec.ui.number = function (selector, options){
    var defaultOpt = {
        max : null,
        min : null,
        showButton : true,
        isDisabled : true,
        minusBtn : '<i class="icon-minus"></i>',
        plusBtn : '<i class="icon-plus"></i>'
    };
    var thix = $(selector);
    var options = $.extend(defaultOpt, options);
    var _checkNumber = function(e) { //非法字符过滤
        var currentKey = e.which;
        var val = parseInt(this.value, 10);
        var thisVal = (val < 1) ? 1 : val;
        var limit = _getLimit(e);
        if((currentKey < 37 || currentKey > 40) && currentKey != 8 && currentKey != 46) {
            if(thisVal > limit.max || thisVal < limit.min) {
                e.preventDefault();
                return false;
            } else {
                if((currentKey<48 || currentKey>57) && (currentKey <96 || currentKey>105) && currentKey!=9) {
                    e.preventDefault();
                    return false;
                }
            }
        }

    };
    var _changClass = function (ele) {
        if(!options.isDisabled) return;
        var $thix = $(ele);
        var inputVal = parseInt($thix.val().trim(), 10);
        var $minBtn = $thix.prev('.icon_minus');
        var $maxBtn = $thix.next('.icon_plus');
        var limit = _getLimit($thix);
        $minBtn.toggleClass('disabled', (inputVal <= limit.min));
        $maxBtn.toggleClass('disabled', (inputVal >= limit.max));
    };
    var _getLimit = function (ele) {
        var $thix = $(ele);
        var max = $thix.data("max");
        var min = $thix.data("min");

        max = (max ? parseInt(max , 10) : options.max);
        min = (min ? parseInt(min , 10) : options.min);
        return {"max" : max, "min" : min};
    };


    thix.each(function () {
        if($(this).data('isuinumber')) return true;
        $(this).data('isuinumber', 'true');

        var opt = $.extend({}, options);
        var inputObj = $(this).css('ime-mode','disabled');

        if(opt.showButton) {
            //减少
            var minusBtn = $(opt.minusBtn).click(function(){
                var val= inputObj.val() || 0;
                var thisVal = parseInt(val , 10) -1;
                var limit = _getLimit(inputObj);

                if(typeof(limit.min) == "number" && thisVal < limit.min) {
                    _changClass(inputObj);
                    return;
                }
                inputObj.val(thisVal).trigger("blur");
            }),
            //增加
            plusBtn = $(opt.plusBtn).click(function(){
                var val= inputObj.val() || 0;
                var thisVal = parseInt(val , 10) +1;
                var limit = _getLimit(inputObj);

                if(typeof(limit.max) == "number" && thisVal > limit.max) {
                    _changClass(inputObj);
                    return;
                }
                inputObj.val(thisVal).trigger("blur");
            });
            $('#inputNum').append(minusBtn).append(plusBtn);
            _changClass(inputObj);
        }
        inputObj.data("ovalue" , inputObj.val() || 0)
            .keydown(_checkNumber)
            .keyup(function () {
                var $thix = $(this);
                var thisVal = parseInt(this.value || 0);
                var limit = _getLimit(this);
                if(typeof(limit.min) == "number" && thisVal < limit.min) {
                    this.value  = limit.min ;
                    $thix.select();
                }else if(typeof(limit.max) == "number" && thisVal > limit.max) {
                    this.value  = limit.max ;
                    $thix.select();
                }
                if(opt.onkeyup && typeof opt.onkeyup === 'function'){
                    opt.onkeyup.call(this);
                }
                _changClass(this);
            })
            .blur(function () {
                if(typeof opt.onchange === "function") {
                    var oldVal = inputObj.data("ovalue"),
                        newVal = this.value || 0,
                        diff = parseInt(newVal , 10) -  parseInt(oldVal , 10);
                    if(diff == 0)return;
                    opt.onchange.call(this , newVal , diff);
                    inputObj.data("ovalue" , newVal);
                }
                _changClass(this);
            });
    });
};
//数量输入验证
ec.details.checkNumber = function(obj) {
    var $obj = $('#goodsNumInput');
    ec.ui.number($obj, {
        max : 9999,
        min : 1
    });
};
ec.details.addCartTips=function(){
    var attrSelected=$('#attr .select-block'),
        addToCartTip=$('#addToCartTip').find('.tip-p'),
        btnBox=addToCartTip.closest('.btn-box');
        btnBox.removeClass('dab-btn'),
        count=0;
        addToCartTip.html('');
        btnBox.removeClass('dab-btn');
        if(attrSelected.length<=0) return;
            attrSelected.each(function(i,n){
                var me=attrSelected.eq(i)
                    span=me.find('.selected span'),
                    title=me.attr('title'),
                    text=span.html();
                    if(text.length<=0){
                        count++
                        addToCartTip.append('<p><i></i>'+title+'</p>');
                    }
            }) 
        if(count>0)
            btnBox.addClass('dab-btn');
}
ec.details.addToCart=function(){
	 //love
    $('#Love').click(function(){
        ec.product.addToWish(this,$('#prcId').val())
    })
	//购买数量
    ec.details.checkNumber();
	//expedited模块
    var expedited=$('#Expedited');
    expedited.find('.drop-box li').click(function(e){
        var shoppingTime=$('.shopping-time'),
            index=$(this).index();
            shoppingTime.eq(index).show().siblings('.shopping-time').hide();
    })
	//按钮是否显示以及提示语
    ec.details.addCartTips();
    $('.drop-box li').click(function(){
         ec.details.addCartTips();
    })
	
	$('#addToCart').click(function(e){
		if($('.dab-btn').length>0) return false; 
			var attrSelected=$('#attr .select-block'),
			listCount=$('#pid').val(),
			bool=true;
			attrSelected.each(function(i,n){
                var me=attrSelected.eq(i)
                    span=me.find('.selected span'),
					attrId=span.attr('attrid');
					listCount+='-'+attrId;

            }) 
			listCount+='-'+$('#goodsNumInput').val();
		bool=true;
		 $.ajax({
			url : baseurl+"currentPassword.php",
			data : {"listCount" : listCount},//listCount:pid1 attrid1 attrid2
			loading: true,
			dataType: 'json',
			success: function(json){
				if(!json.success) {
					bool=false;
				} 
			}
		})	
		return bool;
	})	
}
ec.details.listToCart=function(){
    var Frequently=$('#Frequently'),
        listToCart=$('#listToCart');
       
    listToCart.click(function(){
	var	selectedList=Frequently.find('.current').closest('li');
        bool=true,
		listCount=[];
        selectedList.each(function(i,n){
			var me=selectedList.eq(i);
		   var items=me.attr('pid');
		   var selectSpan=me.find('.selected span');
		   var selectList=[];
		   selectSpan.each(function(y,k){
			   if(bool==false)return;
				if(selectSpan.eq(y).attr('attrid')=='default'){
					listToCart.parent().append($('#listToCartBox').html());
					$('body').append('<div id="shade"><iframe width="100%" height="100%" frameborder=0></iframe></div>');
					var Pop=$('#Pop'),
						left=Pop.offset().left,
						top=Pop.offset().top;
					$('body').append(Pop.remove().show().css({'top':top,'left':left,'margin-left':'0px'}));
					$('.cancel').on('click',function(){
						$('#Pop,#shade').remove();
					})
					
					bool=false;
				}else{
					
					items+='-'+selectSpan.eq(y).attr('attrid');
				}
				
		   })
		   listCount.push(items);
        })
		 $.ajax({
			url : baseurl+"currentPassword.php",
			data : {"listCount" : listCount},
			loading: true,
			dataType: 'json',
			success: function(json){
				if(!json.success) {
					bool=false;
				} 
			}
		})
        return bool;
    })  
}
ec.details.toolBox=function(){
	$('#toolBox').remove();
	var $toTop = $('#goTopNew');
	if(!$toTop[0]) return;
	$toTop.click(function(){
		ec.ui.scrollTo(0);
	})
}
ec.details.init = function () {
    //图片
    ec.details.picZoom();
    //select插件
    ec.details.select();
    //计算价格
    ec.details.countPrice();
    $('#Frequently .icon-ipt').click(function(){
        $(this).parent().toggleClass('current');
        ec.details.countPrice();
    })
	ec.details.addToCart();	     
    /*图片滚动*/
    ec.details.imgZoom();
    ec.details.toolBox();
};

ec.ready(function () {
    ec.details.init();
});