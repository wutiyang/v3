ec.ready(function(){
    var tabTit=null,writeRev=null,tomitSub=null,num=null,numTo=null,reviewIt=null,titIt=null,nav=null,size=null,sizeTab=null,writeView=null,askView=null,policies=null,icon=null,write=null,ask=null,del=null,polTab=null;
    var navTop=$('.tit-tab').offset().top,
        scrollTop=$(document).scrollTop();
    var checkedObj = $('input[name="ques"]:checked').val();
    var writeReview={
        bindEvent: function () {
            nav = $('#tit li');
            size = $('.size li');
            sizeTab = $('.size-tab table');
            writeView = $('#writeView');
            askView = $('#askView');
            policies = $('.pol-list li');
            polTab = $('.pol-info div');
            icon = $('.pop-icon');
            write = $('.recent .write');
            ask = $('.Customer-QA .ask');
            tabTit   = $('.tab-tit a');
            writeRev = $('.write-rev');
            tomitSub = $('.sub');
            reviewIt = $('.review-it');
            del = $('.del');
            titIt=$('.title-it');
            num=$('.num').text();
            numTo=$('.num-to').text();
            nav.eq(0).addClass('on');
            size.eq(0).addClass('on');
            policies.eq(0).addClass('on');
            tabTit.eq(0).addClass('on');
            $('.popBox .cont').eq(0).show();
            writeRev.eq(1).hide();
            writeReview.bindEventClick();
            writeReview.alertBox();
            writeReview.tabFun(policies);
            writeReview.tabFun(size);
            writeReview.inputEach();
            writeReview.titKeydown();
            writeReview.setScroll();
            writeReview.initialize();
            writeReview.star();
        },
        setScroll:function(){
            $(window).scroll(function(){
                var curr_id='';
                var curr_top=$(document).height();
                var top=$(document).scrollTop();
                var detailBox=$('#detail-box'),
                    detailH=detailBox.height(),
                    detailTop=detailBox.offset().top;
                $('#tit').removeClass("tit-fix");
                if(top>navTop){
                    var curr_index=$("#"+curr_id).index();
                    $('#tit').addClass("tit-fix");

                }
                var d1=$(".detail-tab").eq(0);
                if(top<d1.height()+d1.offset().top-80){
                    $("#tit li").eq(0).addClass('tit-tab-on').siblings().removeClass("tit-tab-on");
                }
                if(top>d1.height()+d1.offset().top-80){
                    $("#tit li").eq(1).addClass('tit-tab-on').siblings().removeClass("tit-tab-on");
                }
                var d2=$(".detail-tab").eq(1)
                if(top>d2.height()+d2.offset().top-80){
                    $("#tit li").eq(2).addClass('tit-tab-on').siblings().removeClass("tit-tab-on");
                }
                var d3=$(".detail-tab").eq(2)
                if(top>d3.height()+d3.offset().top-80){
                    $("#tit li").eq(3).addClass('tit-tab-on').siblings().removeClass("tit-tab-on");
                }
                if(top>detailH+detailTop-100){
                   $("#tit").removeClass('tit-fix');
                   $("#tit li").removeClass('tit-tab-on');
                }   
            })
        },
        alertBox:function(){
            $('#writeView,#askView,.init-vie').on('click',function(){
                $(this).parents('.recent').find('.show').show();
                $(this).parents('.Customer-QA').find('.show').show();
                $(".index-box").show();
                del.on('click',function(){
                    write.hide();
                    ask.hide();
                    $(".index-box").hide();
                });
            })
        },
        tabFun:function(obj){
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
                    len=revVal.length;
                if(len<10 && len>=1){
                    $(this).parent().next().find('em').html('The number of word entered should be between 10 and 1000.');
                }
                if(len>=2000){
                    $(this).val(revVal.substring(0,2000));
                }
                
            })
            tomitSub.on('click',function(){
                var titleIt=$(this).parents('.remark').find('.tips >.title-it').val(),
                    reviewIt=$(this).parents('.remark').find('.review-it').val(),
                    titleItH=$(this).parents('.remark').find('.tips >.title-it'),
                    reviewItH=$(this).parents('.remark').find('.review-it'),
                    titleLen=titleIt.length,
                    reviewLen=reviewIt.length,
                    htmlLen=$(this).siblings().find('.num-to').html();
                if(titleLen!="" && reviewLen!=""){
                    $(this).parents('.remark').hide();
                    $(this).parents('.remark').next('.success').show();
                }else if(titleLen!="" && reviewLen==""){
                    $(this).parents('.remark').find('.tips').addClass('error');
                    event.preventDefault();
                }else if(titleLen=="" && reviewLen!=""){
                    $(this).parents('.remark').find('.tips').addClass('error');
                    event.preventDefault();
                }else{
                    $(this).parents('.remark').find('.tips').addClass('error');
                    event.preventDefault();
                }
                if(titleLen>40 || (reviewLen<10 && reviewLen>=1) || reviewLen>2000){
                    $(this).parents('.remark').show();
                    $(this).parents('.remark').next('.success').hide();
                    return false;
                }

                
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
            
            $('#askOk').on('click',function(){
                writeReview.vray();
                $('.show').hide();
                $(".index-box").hide();
            })
            $('#writeOk').on('click',function(){
                writeReview.vrayRadio();
                $('.show').hide();
                $(".index-box").hide();
            })

        },
        initialize:function(){
            if($('.recent-list li').length>0){
                $('.recent-list li').closest('.mark').find('.init').hide();
                $('#writeView').unbind("click");
                writeView.addClass("icon-view-dis");
            }
            if($('.faqs li').length>0){
                $('.faqs li').closest('.question').find('.init').hide();
                $('#askView').unbind("click");
                askView.addClass("icon-view-dis");
            }
        },
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
            })
        },
        vray:function(){
            $.ajax({
                url: baseurl+"product.php",
                data: {"rating":$('.starts').attr('class'),"title":$('.title-it').val(),"content":$('.review-it').val()},
                type: 'POST',
                dataType: 'json',
                success: function(data){
					var data=data.items[0];
                    var str="<li indexId="+data.indexId+"><div class='pic-lt'><h5>"+data.title+"</h5><p><i class='star"+data.rating+"'></i><span>by Mswstudent</span></p><p>"+data.time+"</p></div><div class='cont-ct'><p>"+data.content+"</p></div><div class='helpful-rt'><p>Was this review helpful?</p><p class='praise'><a class='like' href='javascript:;'><i class='icon-like unlike'></i>(<em>10</em>)</a><a class='dislike' href='javascript:;'><i class='icon-dis unlike'></i>(<em>0</em>)</a></p></div></li>";
                    $('.recent-list').append(str);
                    writeReview.bindEvent();
                    writeReview.praise();
                }
            });
        },
        praise:function(){
            $('.like i,.dislike i').on('click',function(){
                var me=$(this),
                    className=me.attr('class'),
                    link=$('.like'),
                    linkI=link.find('i'),
                    linkEm=link.find('em'),
                    dislike=$('.dislike');
                    dislikeI=dislike.find('i'),
                    dislikeEm=dislike.find('em'),
                    numlike=parseInt(linkEm.text()),
                    numdislike=parseInt(dislikeEm.text());
               var indexId=me.closest('li').attr('indexId');
               var recentList=$('.recent-list'),
                   linkUrl=recentList.attr('linkUrl'),
                   dislikeUrl=recentList.attr('dislikeUrl'),
                   url=linkUrl;
                var bool=true;
                if(className.indexOf('icon-like')!=-1){
                    url=linkUrl;
                    if(dislikeI.attr('class').indexOf('unlike')==-1){
                       numdislike-=1;
                    }
                    if(linkI.attr('class').indexOf('unlike')!=-1){
                        numlike+=1;  
                   }else{
                        bool=false;
                        numlike-=1;
                   }   
                }else{
                   url=dislikeUrl;
                   if(linkI.attr('class').indexOf('unlike')==-1){
                       numlike-=1;
                    }
                    if(dislikeI.attr('class').indexOf('unlike')!=-1){
                        bool=true;
                        numdislike+=1;  
                   }else{
                        bool=false;
                        numdislike-=1;
                   }   
                } 
              
            
                 $.ajax({
                    url : baseurl+"succee.php",//替换成url
                    data : {"indexId":indexId,"bool" : bool},
                    loading: true,
                    dataType: 'json',
                    success: function(json){
                        me.toggleClass('unlike').parent().siblings().find('i').addClass('unlike');
                        linkEm.html(numlike); 
                        dislikeEm.html(numdislike);
                    }
                })
            })


        },
        vrayRadio:function(){
            $.ajax({
                url: baseurl+"detail.php",
                data: {"radio":checkedObj,"question":$('.ask .title-it').val(),"additional":$('.ask .review-it').val()},
                type: 'POST',
                dataType: 'json',
                success: function(data){
                    var data=data.items[0];
                    var askStr="<li class='que-list'><div class='ask-que'><i class='q'></i><p class=''ask-tit'><strong>"+data.question+"</strong></p><p class='pose'>"+data.additional+"</p><p class='by'>"+data.time+"</p></div></li>";
                    $('.faqs').append(askStr);
                     writeReview.bindEvent();
                }
            });
        },
        blurFun:function (inputs){
            for(var i=0;i<inputs.length;i++){
                inputs[i].onkeydown=function(){
                    $(this).parent().removeClass('error');
                }
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
})
