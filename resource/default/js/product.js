/**
* @authors gongshan
* @date    2017-07-1
*/
ec.pkg('ec.details');

//声明product对象
ec.pkg('ec.product');
ec.details.Breadcrumbs =function(){
    $('.breadcrumbs a').each(function(){
        var _this = $(this).text(),
            strLen = _this.length;
        if(strLen >= 30){
            var vals=_this.substring(0,30)+"...";
            $(this).text(vals);
        }
    })
};
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
    //收藏商品
    $.ajax({
        url : "/product/addWish",
        data : {"prcId" : prcId,"time":new Date()},
        type: 'get',
        loading: true,
        dataType: 'json',
        success: function(json){
            if(json.status=="0") {
				ec.utils.showPop('loginPop');
				ec.login.init();
            }
            //添加成功后
            else if(json.status=="200"){
                $thix.addClass(okClass);
            }
        }
    })

};
ec.product.removeToWish = function(ele, prcId, opt) {
    var $thix = $(ele);
    var okClass = 'wish-red';
    var _default = {
        width : 200,
        showLoginBox : true
    };
    opt = $.extend(_default, opt);
    //防止恶意点击
    if(!prcId || !ec.ui.eventTimeLimits($thix)) return;
    //收藏商品
    $.ajax({
        url : "wishlist/ajaxCancel",
        data : {"id" : prcId},
        type: 'post',
        loading: true,
        dataType: 'json',
        success: function(json){
            if(json.status=="0") {
                ec.utils.showPop('loginPop');
                ec.login.init();
            }
            else if(json.status=="200"){
                $thix.removeClass(okClass);
            }
        }
    })

};

//数字输入验证
ec.product.number = function (selector, options){
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
            $('#inputNum').append(plusBtn).append(minusBtn);
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
ec.product.checkNumber = function() {
    var $obj = $('#goodsNumInput');
    ec.product.number($obj, {
        max : 9999,
        min : 1
    });
};


ec.details.countPrice=function(){
    if(!gid('countPrice')) return;
    var Frequently=$('#Frequently'),
        priceList=Frequently.find('.current').closest('li').find('.salePrice'),
        savePriceList=Frequently.find('.current').closest('li').find('.elidePrice'),
        count=0,
        saveCount=0,
        countPrices=$('#countPrice');
    if(priceList.length>0){
        priceList.each(function(i,n){
            saveCount+=parseFloat(savePriceList.eq(i).html());
            count+= parseFloat(priceList.eq(i).html());
        })
    }
    saveCount=saveCount-count;
    countPrices.find('.count i').html(count.toFixed(2));
    countPrices.find('.save i').html(saveCount.toFixed(2));
		
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
			image=$(this).find('img').closest('li').attr('data-image'),
            index=$(this).index();
        $(this).addClass('current').siblings().removeClass('current');  
        proOriginal.find('img').attr({'src':src,'data-sku':sku,'data-image':image});
    })
    picZoom.find('li').eq(0).click();
	$('#bigPic').click(function(){
        var sku=$(this).attr('data-sku');
		var image=$(this).attr('data-image');
		$('.box_zoom_content').find('img').attr('src',image).attr('data-sku',sku);
		$('<div class="shade shadebg" id="shade"></div>').insertBefore('.box_zoom_main');
		$('#boxZoomMain').show();
        $('#checkoutMask').show();
	})
    $('.zoom-arr').click(function(){
        var _this=$(this),
            className=_this.attr('class'),
            zoomContent=$('.box_zoom_content').find('img'),
            index=zoomContent.attr('data-sku'),
            bool=className.indexOf('box_zoom_arr_r')!=-1,
            imgZoomLi=$('.imgZoomLi').find('.current'),
            arrowTop=$('.arrow_top'),
            arrowBottom=$('.arrow_bottom');
        imgZoomLi= bool  ? imgZoomLi.next() : imgZoomLi.prev();
        zoomContent.attr({'src':imgZoomLi.attr('data-image'),'data-sku':imgZoomLi.attr('data-sku')});
        imgZoomLi.click();
        bool ? arrowTop.click() : arrowBottom.click();
            
    })
	$('.box_close').click(function(){
		$('#boxZoomMain,#shade').hide();
        $('#checkoutMask').hide();
	})
   
};

ec.details.addCartTips=function(){
    var attrSelected=$('.pro-wrapper .select-block'),
        addToCartTip=$('#addToCartTip').find('.tip-p'),
        btnBox=addToCartTip.closest('.btn-box'),
        count=0;
        btnBox.removeClass('dab-btn');
        addToCartTip.html('');
        btnBox.removeClass('dab-btn');
        if(attrSelected.length<=0) return;
            attrSelected.each(function(i,n){
                var me=attrSelected.eq(i),
                    span=me.find('.selected span'),
                    attr=span.attr('attrid'),
                    title=me.attr('title');
                    if(attr=='default'){
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
        if($('#Love').hasClass('wish-red')){
            ec.product.removeToWish(this,$('#pid').val())
        }else{
            ec.product.addToWish(this,$('#pid').val())
        }
    })
	//购买数量
    ec.product.checkNumber();
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
	
    //单商品加入购物车
	$('#addToCart').click(function(e){
		if($('.dab-btn').length>0) return false;
		var proWrapper=$('.pro-wrapper'),
			argumentAll=proWrapper.find('#pid').val(),
			bool=true,
			attr=proWrapper.find('.attr'),
			SKU=proWrapper.find('.SKU').val(),
			goodsNumInput=proWrapper.find('#goodsNumInput').val();
			argumentAll+='-'+goodsNumInput,
            url=$(this).attr('href');
		if(SKU==''){
			if(attr.length>0 && attr.attr('sku')!=''){
				var sku=attr.attr('sku');
				argumentAll+='-'+sku;
			}
		}else{
			argumentAll+='-'+SKU;	
		}
        dataLayer.push({
            "event": "addToCart",
            "ecommerce": {
                "add": {
                    "products": [{
                        'id' : proWrapper.find('#pid').val(),
                        'price' : proWrapper.find('#price').val(),
                        'quantity' : goodsNumInput
                    }]
                }
            }
        });

		//购物车接口未提供
		 $.ajax({
			url : "/product/addCart",
			cache: false,// 设置浏览器不缓存页面   
            async: true,// 所有的请求都是异步的  默认就是 
			data : {"argumentAll" : argumentAll},
			type : 'POST',
			loading: true,
			dataType: 'json',
			success: function(json){
				if(json.status=="200") {
					window.location.href=url;
				} 
			}
		})
		return false;
	})	
}
ec.details.listToCart=function(){
    var Frequently=$('#Frequently'),
        listToCart=$('#listToCart');
       
    listToCart.click(function(e){
	var	selectedList=Frequently.find('.current').closest('li'),
        bool=true,
		listCount=[],
        url=$(this).attr('href');
        selectedList.each(function(i,n){
			var me=selectedList.eq(i);
		    var items='';
		    var selectSpan=me.find('.selected span');
		    var selectList=[];
		    selectSpan.each(function(y,k){
			    if(bool==false)return;
				if(selectSpan.eq(y).attr('attrid')=='default'){
					listToCart.parent().append($('#listToCartBox').html());
					ec.utils.shade();
					ec.utils.Cancel();
					bool=false;
				}
		   })
		   if(bool){
			   var SKU=me.find('.SKU').val(),
			   	   attr=me.find('.attr'),
			   	   sku=attr.attr('sku'),
				   pid=attr.attr('pid');
				items+=pid+'-1';
				if(SKU!=''){
					items+='-'+SKU;
				}
				else{
					if(sku!='')
						items+='-'+sku;
				}
				listCount.push(items);
		   }  
        })

        if(bool){
            //购物车接口未提供
            $.ajax({
                url : "product/bundleInCart",
                cache: false,// 设置浏览器不缓存页面
                async: true,// 所有的请求都是异步的  默认就是
                data : {"argumentAll" : listCount},
                //type : 'get',
                type : 'post',
                loading: true,
                dataType: 'json',
                success: function(json){
                    if(json.status=="200") {
                        setTimeout(window.location.href = url, 1);
                    }
                }
            })
        }
        return false;
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
ec.details.hideHeight=function(){
    var proinfoLt=$('.pro-info-lt').outerHeight(),
        proinfoRt=$('.side').outerHeight(),
        also=$('.also');
    if(proinfoLt<proinfoRt){
        also.hide();
    }
}
ec.details.init = function () {
    
    ec.details.hideHeight();     
    //图片
    ec.details.picZoom();
    //select插件
    ec.ui.select();
    //计算价格
    ec.details.countPrice();
	//搭配商品属性未选择提示
    ec.details.listToCart();
    $('#Frequently .icon-ipt,#Frequently .drop-box li').click(function(){
		if($(this).attr('class').indexOf('icon-ipt')!=-1)
       		$(this).parent().toggleClass('current');
        ec.details.countPrice();
    })
	ec.details.addToCart();	
    /*图片滚动*/
    ec.details.imgZoom();
    ec.details.toolBox();
};

ec.ready(function () {
    ec.details.Breadcrumbs();
    ec.details.init();
    var tabTit=null,writeRev=null,num=null,nav=null,size=null,policies=null,icon=null;
    var titTab=$('.tit-tab');
		if(titTab.length>0){
		    var navTop=titTab.offset().top;
			scrollTop=$(document).scrollTop();
		}
    var checkedObj = $('input[name="ques"]:checked').val();
    var writeReview={
        bindEvent: function () {
            nav = $('#tit li');
            size = $('.size li');
            policies = $('.pol-list li');
            icon = $('.pop-icon');
            tabTit   = $('.tab-tit a');
            writeRev = $('.write-rev');
            num=$('.num').text();
            nav.eq(0).addClass('on');
            size.eq(0).addClass('on');
            policies.eq(0).addClass('on');
            tabTit.eq(0).addClass('on');
            $('.popBox .cont').eq(0).show();
            writeRev.eq(1).hide();
            writeReview.alertBox();
            writeReview.tabFun(policies);
            writeReview.tabFun(size);
         var titTab=$('.tit-tab');
			if(titTab.length>0){
                writeReview.setScroll();
			}
            writeReview.initialize();
            writeReview.praise();
            writeReview.selectFun();
        },
        // 吸顶
        setScroll:function(){
            $(window).scroll(function(){
                var curr_id='';
                var curr_top=$(document).height();
                var top=$(document).scrollTop();
                var detailBox=$('#detail-box'),
                    detailH=detailBox.height();
                var detailTop=detailBox.offset().top;
                $('#tit').removeClass("tit-fix");
                if(top>=navTop){
                    var curr_index=$("#"+curr_id).index();
                    $('#tit').addClass("tit-fix");
                }else{
                    $('#tit').removeClass("tit-fix");
                }
                var d1=$(".detail-tab").eq(0);
                if(top<d1.height()+d1.offset().top-45){
                    $("#tit li").eq(0).addClass('tit-tab-on').siblings().removeClass("tit-tab-on");
                }
                $(".detail-tab").each(function(i,n){
                  var d2=$(".detail-tab").eq(i)
                    if(top>d2.height()+d2.offset().top-45){
                        $("#tit li").eq(++i).addClass('tit-tab-on').siblings().removeClass("tit-tab-on");
                    }  
                })
                if(top>detailH+detailTop-60){
                   $("#tit").removeClass('tit-fix');
                   $("#tit li").removeClass('tit-tab-on');
                }   
            })
        },
        // 评论、提问弹出框
        alertBox:function(){
			$('#writeView,#markId').on('click',function(){
				var eachbuyerLogState=parseInt($('#eachbuyer_logState').val()),
					url=$(this).attr('url');
					if(eachbuyerLogState==0){
						//没有登录
							ec.utils.showPop('loginPop');					
							ec.login.init();						
					}else{
						$.ajax({
							url: "product/reviewAjax",
							data:{"prcId":$('#pid').val()},
							type: 'post',
							dataType: 'json',
							success: function(json){
								if(json.status=="200"){
									window.location=url;
								}
								if(json.status=="1001"){
									ec.utils.showPop('noBuy',json.msg);	
									ec.utils.Cancel();
								}
								if(json.status=="1002"){
									ec.utils.showPop('haveComments',json.msg);	
									ec.utils.Cancel();
								}
                                if(json.status=="1007"){
                                    //没有登录
                                    ec.utils.showPop('loginPop');                   
                                    ec.login.init();
                                }
							}
						})
					}	
					return false;	
						
			})
            $('#askView,#askOk').on('click',function(){
                var eachbuyerLogState=parseInt($('#eachbuyer_logState').val());
                    if(eachbuyerLogState==0){
                        //没有登录
                            ec.utils.showPop('loginPop');                   
                            ec.login.init();
                    }else{
                        ec.utils.showPop('questionPop');
                        ec.utils.Cancel();
                        var reviewTxt=$('#reviewTxt'),
                            questionTit=$('#question-tit');
                        writeReview.inputEach();
                        writeReview.bindEventClick();
                        writeReview.titKeydown();
						num=$('#Pop .num').text();
                }
            })
        },
        // Warranty & Return Policies小三角
        tabFun:function(obj){
            $('.tab-box table').eq(0).show();
            obj.hover(function(){
                var index=$(this).index();
                $(this).addClass('on').siblings().removeClass('on');
                icon.stop().animate({'left':25*index+'%'},300);
                $('.popBox .cont').eq($(this).index()).show().siblings().hide();
                $('.tab-box table').eq($(this).index()).show().siblings().hide();
            })
        },
        inputEach: function(){
            $(':text,textarea').each(function(){
                writeReview.blurFun($(this));
            })
        },
        titKeydown:function(){
            $('.title-it').on('blur',function(){
                    var titVal = $.trim($(this).val()),
                        len=titVal.length,
                        em=$(this).parent().find('.error-tit');
                    if(len<1){
                         em.show().text(langtips.empty);
                         em.parent().addClass('error');
                    }else if(len>50){
                        $(this).siblings('.error-part').show();
                        em.show().text(langtips.question.infos);
                        em.parent().addClass('error');
                    }else{
                        em.hide();
                        em.parent().removeClass('error');
                    }
                    if( len==0){
                        $(this).siblings('span').show();
                    }   
                })
        },

        //表单验证
        bindEventClick:function(){
            $('.tab-tit a').on('click',function(){
                var index = $(this).index();
                $(this).addClass('on').siblings().removeClass('on');
                writeRev.eq(index).show().siblings().hide();
            });
            $('.review-it').on('blur',function(){
                var revVal=$(this).val(),
                    len=revVal.length,
                    em= $(this).parent().next().find('em');
                if(len<10 && len>=1){
                    em.html(langtips.question.review).show();
                }else if(len<1){
                    $('.errtext .error-tit').parent('.tips').addClass('error');
                    em.html(langtips.empty).show();
                }else{
                    $('.errtext .error-tit').parent('.tips').removeClass('error');
                    em.hide();
                }
                if(len>=1000){
                    $(this).val(revVal.substring(0,1000));
                }
                
            })
            $('.sub').on('click',function(){
                writeReview.inputEach();
                var reviewTxt=$('#reviewTxt'),
                    questionTit=$('#question-tit');
                var titleIt=$(this).parents('.remark').find('.tips >.title-it').val(),
                    reviewIt=$(this).parents('.remark').find('.review-it').val(),
                    titleItH=$(this).parents('.remark').find('.tips >.title-it'),
                    reviewItH=$(this).parents('.remark').find('.review-it'),
                    titleLen=titleIt.length,
                    reviewLen=reviewIt.length,
                    htmlLen=$(this).siblings().find('.num-to').html(),
                    radioVal=$('input:radio[name="ques"]:checked').val();
                    if(radioVal==null) radioVal = 1;
                if(titleLen!="" && reviewLen!="" && radioVal!=null){
                    $(this).parents('.remark').hide();
                    $(this).parents('.remark').next('.success').show();
                }else if(titleLen!="" && reviewLen=="" && radioVal==null){
                    titleItH.find('.tips').removeClass('error');
                    $('.review-it').parents('.tips').addClass('error');
                    $('.errtext .error-tit').parent('.tips').addClass('error');
                    return false;
                }else if(titleLen=="" && reviewLen!="" && radioVal==null){
                    reviewItH.find('.tips').removeClass('error');
                    $('.title-it').parents('.tips').addClass('error');
                    return false;
                }else if(titleLen!=="" && reviewLen!="" && radioVal==null){
                    titleItH.find('.tips').removeClass('error');
                    reviewItH.find('.tips').removeClass('error');
                    return false;
                }else{
                    $(this).parents('.remark').find('.tips').addClass('error');
                }
                if(titleLen>40 || (reviewLen<10 && reviewLen>=1) || reviewLen>2000 || radioVal==null){
                    $(this).parents('.remark').show();
                    $(this).parents('.remark').next('.success').hide();
                    return false;
                }
            });
        
            $('.review-it').keyup(function(){
                var textVal=$.trim($(this).val());
                if(textVal.length<=num){
                    $(this).parent().siblings('.errtext').find('.num-to').text(textVal.length)
                }else{
                    var vals=textVal.substring(0,num);
                    $(this).val(vals);
                }
            })
            $('#writeOk').on('click',function(){
                writeReview.vrayRadio();
                ec.utils.closeBox();
                $('.show').hide();
                $(".index-box").hide();
            })

        },
        //评论、提问数据显示
        initialize:function(){
            if($('.recent-list li').length>0){
                $('.recent-list li').closest('.mark').find('.init').hide();
            }
            if($('.faqs li').length>0){
                $('.faqs li').closest('.question').find('.init').hide();
            }
        },
        //下拉菜单
        selectFun:function(){
            $(".divselect cite,.divselect .icon-plus").click(function () {
                if ($(".divselect ul").css("display") == "none") {
                    $(".divselect ul").show();
                } else {
                    $(".divselect ul").hide();
                }
                return false;
            })
            $(".divselect li").each(function () {
                $(this).click(function () {
                    $(".divselect cite").text($(this).text());
                    $(".inputselect").val($(this).text());
                    $("input").get(0).setAttribute("selectnumber",$(this).find("a").attr("selectid"))
                })                            
            })
            $(document).click(function () {
                $(".divselect ul").hide();
            })
        },
		//提问
        vrayRadio:function(){
            //"radio":checkedObj,
            var qnapid=$('#pid').val();
            $.ajax({
                url: "product/addqna",
                data: {'qnapid':qnapid,"title":$('.ask .title-it').val(),"content":$('.ask .review-it').val()},
                type: 'post',
                dataType: 'json',
                success: function(data){
                }
            });
        },
        //点赞
        praise:function(){
            $('.like i,.dislike i').on('click',function(){
                var me=$(this),
                    className=me.attr('class'),
                    processing=me.attr('processing'),
                    productId=me.attr('productId'),
                    link=me.parent().parent().find('.like'),
                    linkI=link.find('i'),
                    linkEm=link.find('em'),
                    dislike=me.parent().parent().find('.dislike'),
                    dislikeI=dislike.find('i'),
                    dislikeEm=dislike.find('em'),
                    numlikeText=linkEm.text(),
                    numdislikeText=dislikeEm.text(),
                    numlike=parseInt(linkEm.text().replace(/[\(\)]*/g, "")),
                    numdislike=parseInt(dislikeEm.text().replace(/[\(\)]*/g, ""));
               var indexId=me.closest('li').attr('indexId');

               if(processing == 'true'){
                    return false;
               }
               me.attr('processing','true');

                var bool=1;
                //点赞动作
                if(className.indexOf('icon-like')!=-1){
                    if(dislikeI.attr('class').indexOf('unlike')==-1){
                       numdislike-=1;
                    }
                    if(linkI.attr('class').indexOf('unlike')!=-1){
                        numlike+=1;  
                   }else{
                        numlike-=1;
                   }   
                }else{
                    //点踩动作
                    bool=2;
                   if(linkI.attr('class').indexOf('unlike')==-1){
                       numlike-=1;
                    }
                    if(dislikeI.attr('class').indexOf('unlike')!=-1){
                        numdislike+=1;  
                   }else{
                        numdislike-=1;
                   }   
                } 
                 $.ajax({
                    url : "/product/ebhelpful",//替换成url
                    data : {"indexId":indexId,"bool":bool,"productId":productId},
                    type:'post',
                    dataType: 'json',
                    success: function(json){

                        if(json.status == 1007){
                            ec.utils.showPop('loginPop');                   
                            ec.login.init();
                            me.attr('processing','false');
                        }else if(json.status == 200){
                            me.toggleClass('unlike').parent().siblings().find('i').addClass('unlike');
                            linkEm.html('('+numlike+')'); 
                            dislikeEm.html('('+numdislike+')');
                            me.attr('processing','false');
                            //console.log(json.msg);
                        }else{
                            me.attr('processing','false');
                            console.log(json.msg);
                        }
                        //alert(json);
                    	//me.toggleClass('unlike').parent().siblings().find('i').addClass('unlike');
                        //linkEm.html(numlike); 
                        //dislikeEm.html(numdislike);
                    }
                })
            })
        },
        blurFun:function (inputs){
            for(var i=0;i<inputs.length;i++){
                inputs[i].onblur=function(){
                    var Oval = $(this).val();
                    if(Oval==''){
                        $(this).parent().addClass('error');
                    }else{
                        $(this).parent().removeClass('error');
                    }
                    
                }

            }
            
        }

    }
    writeReview.bindEvent();
});
