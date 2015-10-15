(function(){
    var tabTit=null,writeRev=null,tomitSub=null,num=null,numTo=null,reviewIt=null,titIt=null;
    var writeReview={
        bindEvent: function () {
            tabTit   = $('.tab-tit a');
            writeRev = $('.write-rev');
            tomitSub = $('.tomit .sub');
            reviewIt = $('.review-it');
            titIt=$('.title-it');
            num=$('.num').text();
            numTo=$('.num-to').text();
            writeRev.eq(1).hide();
            tabTit.eq(0).addClass('on');
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
            titIt.on('keyup',function(){
                    var titVal = $.trim($(this).val()),
                        len=titVal.length;
                    if(len>=40){
                        $(this).siblings('.error-part').show();
                        $(this).parent().find('.error-tit').show().text('The number of word entered cannot exceed 40.');
                        $(this).val(titVal.substring(0,40));
                    }
                    if( len==0){
                        $(this).siblings('span').show();
                    }   
                })
        },
        bindEventClick:function(){
            tabTit.on('click',function(){
                var index = $(this).index();
                $(this).addClass('on').siblings().removeClass('on');
                writeRev.eq(index).show().siblings().hide();
            });
            reviewIt.on('blur',function(){
                var revVal=$(this).val(),
                    len=revVal.length,
                    em= $(this).parent().next().find('em');
                if(len<10 && len>=1){
                    em.html(langtips.question.review).show();
                }else if(len<1){
                    $('.errtext .error-tit').parent('.tips').addClass('error');
                    em.html(langtips.empty).hide();
                }else{
                    $('.errtext .error-tit').parent('.tips').removeClass('error');
                    em.hide();
                }
                if(len>=1000){
                    $(this).val(revVal.substring(0,1000));
                }
                
            })
            tomitSub.on('click',function(){
                var titleIt=$(this).parents('.remark').find('.tips >.title-it').val(),
                    reviewIt=$(this).parents('.remark').find('.review-it').val(),
                    titleItH=$(this).parents('.remark').find('.tips >.title-it'),
                    reviewItH=$(this).parents('.remark').find('.review-it'),
                    titleLen=titleIt.length,
                    reviewLen=reviewIt.length,
                    start=$(this).parents('.remark').find('.stars span').attr('class'),
                    startLen=$(this).parents('.remark').find('.stars span').attr('class').length,
                    startClass=$(this).parents('.remark').find('.stars span').attr('class').charAt(startLen-1),
                    htmlLen=$(this).siblings().find('.num-to').html();

                var pid=$(this).parents('.write-list').attr('pid'),
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
                    event.preventDefault();
                }else if(titleLen=="" && reviewLen!="" && startLen<=6){
                    reviewItH.find('.tips2').removeClass('error');
                    $(this).parents('.remark').find('.tips').addClass('error');
                    $(this).parents('.remark').find('.tips .error-tit').show();
                    $(this).parents('.remark').find('.rat em').show();
                    return false;
                    event.preventDefault();
                }else if(titleLen!=="" && reviewLen!="" && startLen<=6){
                    $(this).parents('.remark').find('.tips,.tips2').removeClass('error');
                    $(this).parents('.remark').find('.tips .error-tit').hide();
                    $(this).parents('.remark').find('.rat em').show();
                    return false;
                    event.preventDefault();
                }else if(titleLen=="" && reviewLen=="" && startLen>6){
                    $(this).parents('.remark').find('.tips,.tips2').addClass('error');
                    $(this).parents('.remark').find('.error-tit').show();
                    $(this).parents('.remark').find('.rat em').hide();
                    return false;
                    event.preventDefault();
                }else{
                    $(this).parents('.remark').find('.tips,.tips2').addClass('error');
                    $(this).parents('.remark').find('.error-tit').show();
                    event.preventDefault();
                }
                if(titleLen>40 || (reviewLen<10 && reviewLen>=1) || reviewLen>2000 || startLen<=6){
                    $(this).parents('.remark').show();
                    $(this).parents('.list-info').find('.success').hide();
                    return false;
                }

                $.ajax({
                    url : baseurl+"write-review.php",
                    data : {"pid":pid,"rating" : startClass,"title":titleIt,"review":reviewIt},
                    type: 'POST',
                    dataType: 'json',
                    success: function(data){
                        var data=data.item[0],
                            rank=$('.review stars').find('span i').attr('class','star'+data.startClass),
                            rev=$('.review').find('evaluate span').html(reviewIt);
                        // var reviewInfo="<div class='write-list review clear'><a href='' class='list-img'><img src=''></a><div class='list-info'><div class='intro'><div><h5 class='intro-info'></h5></div><p class='time'>April 15, 2015</p></div><p class='size'>Size:<em></em></p><div class='rat'><label>Rating:</label><div class='stars'><i class='star5'></i><span><i class='star"+data.startClass+"'></i></span></div></div><div class='evaluate'><p>“"+data.reviewIt+"”</p></div></div></div>";
                        $('#reviewList').append(reviewInfo);

                        row++;
                    }
                })

            });

            reviewIt.keyup(function(){
                var textVal=$.trim($(this).val());
                if(textVal.length<=num){
                    $(this).parent().siblings('.errtext').find('.num-to').text(textVal.length)
                }else{
                    var vals=textVal.substring(0,num);
                    $(this).val(vals);
                }
            })

        },
        blurFun:function (inputs){
            for(var i=0;i<inputs.length;i++){
                inputs[i].onkeydown=function(){
                    $(this).parent().removeClass('error');
                    $(this).parent().find('.error-tit').hide();
                }
                inputs[i].onblur=function(){
                    var Oval = $(this).val();
                    if(Oval==''){
                        $(this).parent().addClass('error');
                        $(this).parent().find('.error-tit').show();
                    }else{
                        $(this).parent().removeClass('error');
                        $(this).parent().find('.error-tit').hide();
                    }
                }

            }
            
        },
        //星评
        star:function(){
             $('.starts i').mouseenter(function(){
                var parent=$(this).parent(),
                    className=parent.attr('class');
                if(className.length>6)
                    parent.attr('class','starts');
             })
            $('.starts i').on('click',function(){
                var me=$(this),丄
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
