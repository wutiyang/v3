ec.ready(function () {
	var Price = function(obj,classA,classB){
			var flag=0;
			obj.on('click',function(){
				flag=1;
				var parentLi = $(this).parents('li'),
					parentA = $(this).parents('span');
				parentLi.siblings().removeClass('on');
				parentLi.addClass('prices').find('i').hide();
				parentA.addClass(classA);
				/*if(parentA.hasClass(classA) && flag==1){
					parentA.addClass(classB);
				}*/
			})
		}
	var PriceUp=new Price($('.icon-top'),'icon-up','icon-delow');
	var PriceDown=new Price($('.icon-down'),'icon-delow','icon-up');


	var Accordion = function(el, multiple) {
			this.el = el || {};
			this.multiple = multiple || false;
			var adds = this.el.find('.add');
			el.find('li').addClass('active');

			adds.on('click', {el: this.el, multiple: this.multiple}, this.dropdown)

			$('.collapse em').on('click',function(){
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









});