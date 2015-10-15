/**
* @authors gongshan
* @date    2015-06-11
*/
ec.pkg('ec.categories');

ec.categories.imgZoom = function () {
	var imgZoomLi=$('.imgZoomLi'),
		arrowRight=$('.arrow_right'),
		arrowLeft=$('.arrow_left'),
		li=imgZoomLi.find('li'),
		w=li.innerWidth(),
		len=li.length-7,
		count=0;
	imgZoomLi.css('width',w*li.length);
	function scrollDiv(){
		imgZoomLi.animate({'left':'-'+w*count+'px'});
	}
	$('.arrow_right').click(function(){
		count++;
		$('.btn_arrow').removeClass('arrow_disabled')
		if(count>len-1){
			
			$(this).addClass('arrow_disabled');	
			count=len;
		}
		var current=$('.imgZoomLi .current'),
		index=current.index()+1,
		subCate=$('#dImgZoomBox .sub-cate');
		if(index==count){
			current.removeClass('current').next().addClass('current');
			subCate.hide();
			subCate.eq(index).show();
		}	
		scrollDiv(count);
	})
	$('.arrow_left').click(function(){
		count--;
		$('.btn_arrow').removeClass('arrow_disabled');
		if(count<1){
			
			$(this).addClass('arrow_disabled');
			count=0;
		}
		var current=$('.imgZoomLi .current'),
		index=current.index()-1,
		subCate=$('#dImgZoomBox .sub-cate');
		if(count+7-index==1){
			current.removeClass('current').prev().addClass('current');
			subCate.hide();
			subCate.eq(index).show();
		}	
		scrollDiv(count);
	})
	$('#dImgZoom li').hover(function(){
		$(this).addClass('current').siblings().removeClass('current');	
		$('#dImgZoomBox .sub-cate').eq($(this).index()).css({'visibility':'visible'}).siblings().css({'visibility':'hidden'});
	})
   
};
ec.categories.priceSection =function(){
	//自定义价格区间
	var inputList=$('.intr-last').find('input[type=text]'),
		reg = /^[0-9]*$/;
	inputList.keyup(function(){
		var me=$(this);
			numVal=$.trim(me.val()),
			len=numVal.length;
		if(len>=8){
	           $(this).val(numVal.substring(0,8));
	       }
	})
	inputList.blur(function(){
		var me=$(this),	
			numVal=$.trim(me.val()),
			info=me.closest('.info');
		info.removeClass('error');
		var length=me.closest('form').find('.error').length;
		if(length<=0)
			info.closest('.intr-last').find('.tips').remove();
		if(!reg.test(numVal)){
			if(info.find('.tips').length<1){
				info.addClass('error');
				info.closest('.intr-last').find('.tips').remove();
				info.closest('.intr-last').append('<div class="tips">'+langtips.price+'</div>');
			}
			return false;
		}		
	})
	$('.go').on('click',function(){
		var form=$(this).closest('form');
		form.find('.front,.back').blur();
		var length=form.find('.error').length,
			front=form.find('.front').val().replace(/(^\s*)|(\s*$)/g, ""),
			back=form.find('.back').val().replace(/(^\s*)|(\s*$)/g, ""),
			current_url = $("#current_url_tim").val(),
			url_c='';
		if(front.length<=0 && back.length<=0){
            location.href=current_url;
			return false;
		}
		if(front.length>0 && back.length>0){
			url_c=',';
		}
		if(length>0){
			return false;
		}else{
			var param_type = $("#param_type").val(),
				url_con = "?";
			if(param_type!=1){
				url_con = "&";
			} 
			console.log(current_url)
			location.href=current_url+url_con+"search_price_range="+front+url_c+back;
		}
		return false;
    })
     inputList.blur();
}
ec.categories.init = function () {
	//展开收起
	function slidetoggle(ob,parent){
		var height=ob.height();
		ob.height('auto');
		ob.each(function(i,e){
			var _this=ob.eq(i),
				h=_this.height();
			if(h>height){
				_this.height(height);
				_this.closest(parent).find('.more').show();
				_this.closest(parent).find('.btn-span').click(function(){
					var ht=$(this).attr('class').indexOf('more')!=-1 ? h :height;
						$(this).hide().siblings('a').show();
						_this.animate({'height':+ht},500);
				})
			}
		})
	}
	//搜素条件
	if(gid('searchCondition') || gid('searchRes') || gid('cateZone') || gid('brandCon')){
		var	ddList=$('.search-condition .attr-item'),
			viewList=$('#showRes'),
			cateZone=$('.zone-list'),
			brandHome=$('.home-list'),
			cateZoneLen=$('.zone-list li').length,
			homeListLen=$('.home-list li').length;
		slidetoggle(ddList,'dl'); 
		slidetoggle(viewList,'.result');
		if(cateZoneLen<=16){
			// $('.all-brand').find('.more').show();
		}else if(cateZoneLen>16){
			slidetoggle(cateZone,'.all-brand');
		}
		if(homeListLen>12){
			brandHome.css('height','513px');
			$('.lists').find('.more').show();
			slidetoggle(brandHome,'.lists');    
		}else{
			slidetoggle(brandHome,'.lists');
		}
		ddList.find('li').click(function(){
			$(this).toggleClass('selected')
		})
			//初始化checkbox的选中状态
		$('label input:checked').parent().addClass('checked');
		$('label').click(function(){
			var me=$(this);
			me.removeClass('checked');	
			if(me.find('input').is(':checked')){
				me.addClass('checked');		
			}
			
		})
	}
	//类目
	if(gid('dImgZoom')){
		ec.categories.imgZoom();
		//显示第一个三级类目
		$('#dImgZoomBox .sub-cate:eq(0)').css({'visibility':'visible'});
		//类目展开收起
		var list=$('#dImgZoomBox .list-con ul');
			slidetoggle(list,'.list-con');
	}
	//多语言价格换行
	$('.p-price span').each(function(){
        var priceW=$(this).innerWidth();
        if(priceW>=70){
            $(this).css({'display':'block'});
            $('.deals .primary-list .p-price').css({'padding-bottom':'6px'});
        }
    })
	var Price = function(obj,classA,classB){
			var flag=0;
			obj.on('click',function(){
				flag=1;
				var parentLi = $(this).parents('li'),
					parentA = $(this).parents('span');
				parentLi.siblings().removeClass('on');
				parentLi.addClass('prices').find('i').hide();
				parentA.addClass(classA);
			})
		}
	var PriceUp=new Price($('.icon-top'),'icon-up','icon-delow');
	var PriceDown=new Price($('.icon-down'),'icon-delow','icon-up');


	var Accordion = function(el, multiple) {
			this.el = el || {};
			this.multiple = multiple || false;
			var adds = this.el.find('.add');
			el.find('.list-par').addClass('active');
			adds.on('click', {el: this.el, multiple: this.multiple}, this.dropdown)

			$('.collapse a').on('click',function(){
				// $(this).find('em').toggleClass('in');
				$(this).toggleClass('in');
			})
		}

	Accordion.prototype.dropdown = function(e) {
		var $el = e.data.el,
			$this = $(this),
			$next = $this.next(),
			$parent =$this.parent();
		$next.slideToggle();
		$parent.toggleClass('active');
	}	

	var accordion = new Accordion($('#accordion'), false);
	ec.categories.priceSection();
};

ec.ready(function () {
 	ec.categories.init();
});
