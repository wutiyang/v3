(function(){
    var tabTit=null,writeRev=null,tomitSub=null,reviewIt=null,titIt=null;
    var writeReview={
        bindEvent: function () {
            tabTit   = $('.tab-tit a');
            writeRev = $('.write-rev');
            tomitSub = $('.tomit .sub');
            reviewIt = $('.review-it');
            titIt=$('.title-it');
            writeRev.eq(1).hide();
            writeReview.bindEventClick();
            writeReview.inputEach();
            writeReview.titKeydown();
            writeReview.star();
        },
        inputEach: function(){
            $(':text,textarea').each(function(){
                writeReview.blurFun($(this));
            })
        },
        titKeydown:function(){
            titIt.on('blur',function(){
                    var titVal = $.trim($(this).val()),
                        len=titVal.length;
                    if(len>=50){
                        $(this).siblings('.error-part').show();
                        $(this).parent().find('.error-tit').show().text(langtips.question.infos);
                    }
                    if( len==0){
                        $(this).parent().addClass('error');
						var em= $(this).parent().find('em');
                    	em.html(langtips.empty).show();
                    }   
                })
        },
        bindEventClick:function(){
            tabTit.on('click',function(){
                var index = $(this).index();
                $(this).addClass('on').siblings().removeClass('on');
                writeRev.eq(index).show().siblings().hide();
            });
            tomitSub.on('click',function(){
                var titleIt=$(this).parents('.remark').find('.tips >.title-it').val(),
                    reviewIt=$(this).parents('.remark').find('.review-it').val(),
                    titleItH=$(this).parents('.remark').find('.tips >.title-it'),
                    reviewItH=$(this).parents('.remark').find('.review-it'),
                    titleLen=titleIt.length,
                    reviewLen=reviewIt.length,
                    start=$(this).parents('.remark').find('.stars span').attr('class'),
                    startLen=$(this).parents('.remark').find('.stars span').attr('class').length,
                    startClass=$(this).parents('.remark').find('.stars span').attr('class').charAt(startLen-1);

                var productId=$(this).parents('.write-list').attr('product_id'),
                	orderId=$(this).parents('.write-list').attr('order_id'),
                	productSku=$(this).parents('.write-list').attr('product_sku'),
                    row=$('.view-rev span').html();

                if(titleLen!="" && reviewLen!="" && startLen>6){
                    $(this).parents('.remark').hide();
                    $(this).parents('.list-info').find('.success').show();
                }else if(titleLen!="" && reviewLen=="" && startLen<=6){
                    titleItH.find('.tips').removeClass('error');
                    $(this).parents('.remark').find('.tips2').addClass('error');
                    $(this).parents('.remark').find('.tips2 .error-tit').show();
                    $(this).parents('.remark').find('.rat em').show();
                    return false;
                }else if(titleLen=="" && reviewLen!="" && startLen<=6){
                    reviewItH.find('.tips2').removeClass('error');
                    $(this).parents('.remark').find('.tips').addClass('error');
                    $(this).parents('.remark').find('.tips .error-tit').show();
                    $(this).parents('.remark').find('.rat em').show();
                    return false;
                }else if(titleLen!=="" && reviewLen!="" && startLen<=6){
                    $(this).parents('.remark').find('.tips,.tips2').removeClass('error');
                    $(this).parents('.remark').find('.tips .error-tit').hide();
                    $(this).parents('.remark').find('.rat em').show();
                    return false;
                }else if(titleLen=="" && reviewLen=="" && startLen>6){
                    $(this).parents('.remark').find('.tips,.tips2').addClass('error');
                    $(this).parents('.remark').find('.error-tit').show();
                    $(this).parents('.remark').find('.rat em').hide();
                    return false;
                }else if(titleLen=="" && reviewLen=="" && startLen<=6){
                    $(this).parents('.remark').find('.tips,.tips2').addClass('error');
                    $(this).parents('.remark').find('.error-tit').show();
                    event.preventDefault();
                }
                if(titleLen>50 || (reviewLen<10 && reviewLen>=1) || reviewLen>2000 || startLen<=6){
                    $(this).parents('.remark').show();
                    $(this).parents('.list-info').find('.success').hide();
                    return false;
                }
				var reviewSpan=$('.view-rev span'),
					reviewNum=parseInt(reviewSpan.html());
                $.ajax({
                    url : "/review_create/ajaxAdd",
                    data : {"score" : startClass,"title":titleIt,"content":reviewIt,"product_id":productId,"product_sku":productSku,"order_id":orderId},
                    type: 'post',
                    dataType: 'json',
                    success: function(data){
                    	if(data.status == 200){
                    		reviewSpan.html(++reviewNum);
                    	}else{
                    		if(data.status == 1007){
                    			ec.utils.showPop('loginPop');					
								ec.login.init();
                    		}else{
                    			alert(data.msg);
                    		}
                    	}
                    }
                })
            });
        },
        blurFun:function (inputs){
            for(var i=0;i<inputs.length;i++){
                inputs[i].onblur=function(){
                    var Oval = $(this).val();
                    if(Oval==''){
                        $(this).parent().addClass('error');
                        $(this).parent().find('.error-tit').show();
						var em= $(this).parent('.tips2').find('em');
                    	em.html(langtips.empty).show();
                    }else{
                        $(this).parent().removeClass('error');
                        $(this).parent().find('.error-tit').hide();
							   var revVal=$(this).val(),
								len=revVal.length,
								em= $(this).parent('.tips2').find('em');
							if(len<10 && len>=1){
								$(this).parent('.tips2').addClass('error');
								em.html(langtips.question.review).show();
							}else if(len<1){
								$(this).parent().addClass('error');
								em.html(langtips.empty).hide();
								
							}else{
								$(this).parent('.tips2').removeClass('error');
								em.hide();
							}
							
                    }
				
                }

            }
            
        },
        //星评
        star:function(){
            $('.starts i').on('click',function(){
                var me=$(this),
                    index=me.index()+1,
                    parent=me.parent();
                    parent.attr('class','starts');
                    parent.addClass('starts'+index);
                $('.starts').parents('.tips').removeClass('error');
                $(this).parents('.rat').find('em').hide();
            })
        }

    }
    writeReview.bindEvent();
})();
