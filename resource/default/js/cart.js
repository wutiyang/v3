ec.pkg('ec.cart');
var cartTable=$('.cart-table'),
	eachbuyerLogState=parseInt($('#eachbuyer_logState').val());
ec.cart={
	init:function(){
		//select²å¼þ
    	ec.ui.select();
    	ec.cart.selectBlock();	
		ec.cart.select100();	
		ec.cart.del();
		ec.cart.wishlist();
		ec.cart.imgZoom();
		ec.cart.scrollRight();
		ec.cart.addToCart();
		ec.cart.checkOut();
		ec.cart.isNull();
	},
	del:function(){
		$('.del').on('click',function(){
			var me=$(this),
				table=me.closest('table'),
				parent=me.closest('.parent'),
				pid=parent.attr('pid'),
				sku=parent.attr('sku'),
				cid=parent.attr('cid'),
				td0=parent.find('td').eq(0),
				html=$('#deleteBox').html(),
				url=me.attr('href');
				td0.append(html);
				me.attr('href','javascript:;')
				 $.ajax({
					url : "/cart/delcartproduct",
					type:'POST',
					data : {"pid" : pid+'-'+sku+'-'+cid},
					dataType: 'json',
					success: function(json){
						setTimeout(function(){
						//	parent.remove();
						//	ec.cart.ajaxEvent(json,me);	
						//	ec.cart.isNull();
							window.location=url;
						},800);
					}
				})		
		})
	},
	wishlist:function(){
		$('.move').on('click',function(){
				var me=$(this),
					table=me.closest('table'),
					parent=me.closest('.parent'),
					pid=parent.attr('pid'),
					sku=parent.attr('sku'),
					cid=parent.attr('cid'),
					url=me.attr('href');
					me.attr('href','javascript:;')
				$.ajax({
					url : "/cart/carttowishlist",
					type:'POST',
					data : {"pid" : pid+'-'+sku+'-'+cid},
					dataType: 'json',
					success: function(json){
						if(json.status=="1007"){
							ec.utils.showPop('loginPop');					
							ec.login.init();	
						}else{
							var	td0=parent.find('td').eq(0),
							html=$('#wishlistBox').html();
							td0.append(html);
							setTimeout(function(){
								ec.cart.isNull();
								window.location=url;
							},800);	
						}
					}
				})
				
		})
	},
	select100:function(){
		var selected=cartTable.find('.select-box .selected span'),
			dropBox=cartTable.find('.drop-box ul');
			$.each(selected,function(y,n){
				var html='';
				var attrid=selected.eq(y).attr('attrid');
				for(var i=1;i<101;i++){
					html+='<li attrid="'+i+'"';
					if(attrid==i){
						html+='class="hide"';
					}
					html+='><a href="javascript:;">'+i+'</a></li>';	
				}	
				dropBox.eq(y).html(html);
			})	
		dropBox.find('li').on('click',function(){
			var me=$(this),
				tr=me.closest('tr'),
				pid=tr.attr('pid'),
				sku=tr.attr('sku'),
				cid=tr.attr('cid'),
				attrid=me.attr('attrid'),
				parameter=pid+'-'+sku+'-'+cid+'-'+attrid;
			$.ajax({
				url : "cart/updatequantity",
				type:'get',
				data : {"parameter":parameter},
				dataType: 'json',
				success: function(json){
					ec.cart.ajaxEvent(json,me);
				}
			})
		})
	},
	selectBlock:function(){
		var index=99,
			parentIndex=999;
		$('.t3 .selected').click(function(){
			$(this).closest('.t3').css({'z-index':index++});
	        $(this).closest('.cart-table tr').css({'position':'relative','z-index':parentIndex++});
		})
	},
	ajaxEvent:function(items,me){
		var $count=$('#count'),
            $cartTotal=$('#cartTotal'),
			$subtotal=$('#subtotal'),
			$savings=$('#savings'),
			$reward=$('#reward'),
			$count2=$('#count2'),
			$subtotal2=$('#subtotal2'),
			data=JSON.parse(items.data),
			html='',
			promotion='',
			links=me.closest('.t3').find('.links'),
			saveing=me.closest('tr').find('.save em').html(),
			strs='';
			$.each(links,function(i,n){
				strs+='<div class="links">'+links.eq(i).html()+'</div>';
			})
			$.each(data.list,function(i,n){
				var str='',
					bkclass = '',
					warehouse_class='';
				//判断是否参与促销	
				salePromotion=n.view_title;
				if(salePromotion){
					html+='<tr class="reduction">'+
								'<td colspan="4">'+
											'<div class="front"><strong>'+n.view_title+'</strong><span></span></div>'+
											'<div class="back">';
											if(n.is_off){
												html+='<a class="dis-act" href="javascript:;">'+n.view_short_title+'</a>';
											}else if(n.discount_url=="javascript:;"){
												html+='<a class="dis-eff" href="javascript:;">'+n.view_short_title+'</a>';
											}else{
												html+='<a href="'+n.discount_url+'">'+n.view_short_title+'</a>';
											}
											
											html+='<p class="hide">'+n.view_short_title+'</p>'+
											'</div>'+
								'</td>'+
							'</tr>';
					bkclass='bkgray';
				}
				$.each(n.product_list,function(o,k){
					product_sku_warehouse_class=k.warehouse_class;
					warehouse_class=product_sku_warehouse_class=='' ? 'hide' : product_sku_warehouse_class;
					$.each(k.attr,function(x,y){
						str+='<span>'+y.attr_name+': '+y.attr_value_name+'</span>';
					})
	                var sell_out = '',
	                	savePri = '';
	                if(k.sell_out){
	                    sell_out = '<div class="sale-out on"><i class="tips"></i><span class="span-red">'+k.sell_out+'</span></div>';
	                }
	                if(n.is_off){
	                	savePri = '<p class="save-price">'+k.off_price+'</p>';
	                }
	                html+='<tr class="parent '+bkclass+'" sku="'+k.sku+'" pid="'+k.pid+'" cid="'+k.cid+'">';
					html+='<td class="t1">'+
							'<div class="cart-img"><a href="'+k.src+'"><img width="73" height="73" alt="'+k.title+'" src="'+k.pic+'"></div></a>'+
							'<div class="cart-info">'+
								'<a href="'+k.src+'">'+k.title+'</a>'+
								'<p class="cart-msg">'+str+'</p>'+
								'<span class="shops-to"><i class="'+warehouse_class+'"></i><span class="warehouse">'+k.warehouse+'</span></span>'+
							'</div>'+
                            sell_out+
						'</td>'+
						'<td class="t2">'+
							'<p class="price">'+k.salePrice+'</p>'+
							'<p class="discount">'+k.originalPrice+'</p>'+
							'<p class="save"><em>'+saveing+'</em><span>'+k.savePrice+'</span></p>'+
						'</td>'+
						'<td class="t3">'+
                        	'<div class="select-box">'+
                                '<div class="select-block">'+
                                    '<div class="selected">'+
                                        '<a title="" href="javascript:;" rel="nofollow">'+
                                            '<i class="account-icon"></i>'+
                                            '<span attrid="'+k.quantity+'" class="">'+k.quantity+'</span>'+
                                        '</a>'+
                                        '<i class="icon-select-arrow"></i>'+
                                     '</div>'+
                                     '<div class="drop-box">'+
                                        '<ul class="drop-content drop-list"></ul>'+
                                     '</div>'+
                                  '</div>'+
                              '</div>'+
                              strs+
						'</td>'+
						'<td class="t4">'+k.price+savePri+'</td>'+
					'</tr>';
				})
			})
		$('#cartTable .parent').remove();
		$('#cartTable .reduction').remove();
		$('#cartTable').children('tbody').prepend(html);
		ec.ui.lazyLoad($('body .main').find("img"));
		$count.html(data.count);
        $count2.html(data.count);
        $cartTotal.html(data.count);
		$subtotal.html(data.subtotal);
		$subtotal2.html(data.subtotal);
		$savings.html(data.savings);
		$reward.html(data.reward);
		//select插件
    	ec.ui.select();
    	ec.cart.selectBlock();	
		ec.cart.select100();	
		ec.cart.del();
		ec.cart.wishlist();		
	},
	scrollRight:function(){
		 $(window).scroll(function(){
			var curr_top=parseInt($(document).height()),
				bodyWidth=parseInt($(document).width());
			var top=parseInt($(document).scrollTop()),
				left=parseInt($(document).scrollLeft()),
				footer=parseInt($('.footer').height()),
				winW=bodyWidth-left;
			//console.log(top+' '+ (curr_top-1000))
			if(top>curr_top-800){
				$('#cartRight').css({'position':'relative'})
			}else{
				$('#cartRight').css({'position':'fixed'})
			}
			if(bodyWidth-left>0){
				$('#cartRight').addClass('cart-right-fix');
			}
			if(left<=0){
				$('#cartRight').removeClass('cart-right-fix');
			}
		 })
	},
	addToCart:function(){
		$('.add').click(function(e){
			var me=$(this),
				selectModule=me.closest('.selectModule'),
				attr=selectModule.find('.attr'),
				argumentAll=attr.attr('pid'),
				SKU=attr.attr('sku');
				argumentAll+='-'+1;
				
				if(SKU!=''){
					argumentAll+='-'+SKU;
					//¹ºÎï³µ½Ó¿ÚÎ´Ìá¹©
					 $.ajax({
						url :  "/cart/wishlisttocart",
						data : {"argumentAll" : argumentAll},
						type : 'POST',
						dataType: 'json',
						success: function(json){
							window.location.reload();
						}
					})
				}
			return true;
		})	
	},
	checkOut:function(){
		$('#checkOut').click(function(){
			var bool=true;
			if(eachbuyerLogState==0){
				//没有登录
				ec.utils.showPop('loginPop');					
				ec.login.init();
				bool=false;						
			}
			return bool;
		})	
	},
	isNull:function(){
		var cartTable=$('#cartTable'),
			cartRight=$('#cartRight'),
			length=cartTable.find('.parent').length;
		if(length<=0){
			cartTable.hide();
			cartRight.hide();
		}else{
			cartTable.show();
			cartRight.show();
		} 
	}
}
ec.cart.imgZoom=function(){
    function imgSroll(con){
        var  li=con.find('.selectModule'),
            number=Math.ceil(li.length/5),
            margin=10,
            num=0,
            btnArrow=con.find('.btn_arrow');
			con.find('ul').eq(0).width((li.outerWidth()+margin)*li.length);
			function scolldiv(ulw,w,num){
				var leftdis = w*num;
				if(leftdis>ulw)
					leftdis=ulw;
				con.find('ul').stop().animate({left:'-'+leftdis});
			}
		btnArrow.click(function(){
			var con=$(this).closest('.con'),
				w=(li.outerWidth()+margin)*con.attr('size'),
				ulw=con.find('ul').eq(0).width()-w,
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
    var $con=$('.module-con .con'),
		$selectModule=$con.find('.selectModule'),
		h=345,
		size=parseInt($con.attr('size')),
		count=$con.find('.selectModule').length;
		$.each($selectModule,function(i,n){
			var height=$selectModule.eq(i).height();
			if(h<height)
				h=height;
				
		})
		$selectModule.height(h);
		$con.height(h+14);
       imgSroll($con);
	 	if(count<=size)
			$con.find('.btn_arrow').addClass('btn_arrow arrow_disabled');

}
$(document).ready(function(){
	ec.cart.init();
});


