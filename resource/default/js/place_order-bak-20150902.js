ec.pkg('ec.placeOrder');
var region_data = {"ES":["A Coru\u00f1a","Alava","Albacete","Alicante","Almeria","Asturias","Avila","Badajoz","Baleares","Barcelona","Burgos","Caceres","Cadiz","Cantabria","Castellon","Ceuta","Ciudad Real","Cordoba","Cuenca","Girona","Granada","Guadalajara","Guipuzcoa","Huelva","Huesca","Jaen","La Rioja","Las Palmas","Leon","Lleida","Lugo","Madrid","Malaga","Melilla","Murcia","Navarra","Ourense","Palencia","Pontevedra","Salamanca","Santa Cruz de Tenerife","Segovia","Sevilla","Soria","Tarragona","Teruel","Toledo","Valencia","Valladolid","Vizcaya","Zamora","Zaragoza"],"CH":["Aargau","Appenzell Ausserrhoden","Appenzell Innerrhoden","Basel-Landschaft","Basel-Stadt","Bern","Freiburg","Genf","Glarus","Graub\u00fcnden","Jura","Luzern","Neuenburg","Nidwalden","Obwalden","Schaffhausen","Schwyz","Solothurn","St. Gallen","Tessin","Thurgau","Uri","Waadt","Wallis","Zug","Z\u00fcrich"],"LV":["Aglonas novads","Aizkraukles novads","Aizputes novads","Akn\u012bstes novads","Alojas novads","Alsungas novads","Al\u016bksnes novads","Amatas novads","Apes novads","Auces novads","Bab\u012btes novads","Baldones novads","Baltinavas novads","Balvu novads","Bauskas novads","Bever\u012bnas novads","Broc\u0113nu novads","Burtnieku novads","Carnikavas novads","Cesvaines novads","Ciblas novads","C\u0113su novads","Dagdas novads","Daugavpils","Daugavpils novads","Dobeles novads","Dundagas novads","Durbes novads","Engures novads","Garkalnes novads","Grobi\u0146as novads","Gulbenes novads","Iecavas novads","Ik\u0161\u0137iles novads","Il\u016bkstes novads","In\u010dukalna novads","Jaunjelgavas novads","Jaunpiebalgas novads","Jaunpils novads","Jelgava","Jelgavas novads","J\u0113kabpils","J\u0113kabpils novads","J\u016brmala","Kandavas novads","Kokneses novads","Krimuldas novads","Krustpils novads","Kr\u0101slavas novads","Kuld\u012bgas novads","K\u0101rsavas novads","Lielv\u0101rdes novads","Liep\u0101ja","Liep\u0101jas novads","Limba\u017eu novads","Lub\u0101nas novads","Ludzas novads","L\u012bgatnes novads","L\u012bv\u0101nu novads","Madonas novads","Mazsalacas novads","M\u0101lpils novads","M\u0101rupes novads","Nauk\u0161\u0113nu novads","Neretas novads","N\u012bcas novads","Ogres novads","Olaines novads","Ozolnieku novads","Prei\u013cu novads","Priekules novads","Prieku\u013cu novads","P\u0101rgaujas novads","P\u0101vilostas novads","P\u013cavi\u0146u novads","Raunas novads","Riebi\u0146u novads","Rojas novads","Ropa\u017eu novads","Rucavas novads","Rug\u0101ju novads","Rund\u0101les novads","R\u0113zekne","R\u0113zeknes novads","R\u012bga","R\u012bgas novads","R\u016bjienas novads","Salacgr\u012bvas novads","Salas novads","Salaspils novads","Saldus novads","Saulkrastu novads","Siguldas novads","Skrundas novads","Skr\u012bveru novads","Smiltenes novads","Stopi\u0146u novads","Stren\u010du novads","S\u0113jas novads","Talsu novads","Tukuma novads","T\u0113rvetes novads","Vai\u0146odes novads","Valkas novads","Valmiera","Valmieras novads","Varak\u013c\u0101nu novads","Vecpiebalgas novads","Vecumnieku novads","Ventspils","Ventspils novads","Vies\u012btes novads","Vi\u013cakas novads","Vi\u013c\u0101nu novads","V\u0101rkavas novads","Zilupes novads","\u0100da\u017eu novads","\u0112rg\u013cu novads","\u0136eguma novads","\u0136ekavas novads"],"FI":["Ahvenanmaa","Etel\u00e4-Karjala","Etel\u00e4-Pohjanmaa","Etel\u00e4-Savo","It\u00e4-Uusimaa","Kainuu","Kanta-H\u00e4me","Keski-Pohjanmaa","Keski-Suomi","Kymenlaakso","Lappi","Pirkanmaa","Pohjanmaa","Pohjois-Karjala","Pohjois-Pohjanmaa","Pohjois-Savo","P\u00e4ij\u00e4t-H\u00e4me","Satakunta","Uusimaa","Varsinais-Suomi"],"FR":["Ain","Aisne","Allier","Alpes-Maritimes","Alpes-de-Haute-Provence","Ardennes","Ard\u00e8che","Ari\u00e8ge","Aube","Aude","Aveyron","Bas-Rhin","Bouches-du-Rh\u00f4ne","Calvados","Cantal","Charente","Charente-Maritime","Cher","Corr\u00e8ze","Corse-du-Sud","Creuse","C\u00f4te-d'Or","C\u00f4tes-d'Armor","Deux-S\u00e8vres","Dordogne","Doubs","Dr\u00f4me","Essonne","Eure","Eure-et-Loir","Finist\u00e8re","Gard","Gers","Gironde","Haut-Rhin","Haute-Corse","Haute-Garonne","Haute-Loire","Haute-Marne","Haute-Savoie","Haute-Sa\u00f4ne","Haute-Vienne","Hautes-Alpes","Hautes-Pyr\u00e9n\u00e9es","Hauts-de-Seine","H\u00e9rault","Ille-et-Vilaine","Indre","Indre-et-Loire","Is\u00e8re","Jura","Landes","Loir-et-Cher","Loire","Loire-Atlantique","Loiret","Lot","Lot-et-Garonne","Loz\u00e8re","Maine-et-Loire","Manche","Marne","Mayenne","Meurthe-et-Moselle","Meuse","Morbihan","Moselle","Ni\u00e8vre","Nord","Oise","Orne","Paris","Pas-de-Calais","Puy-de-D\u00f4me","Pyr\u00e9n\u00e9es-Atlantiques","Pyr\u00e9n\u00e9es-Orientales","Rh\u00f4ne","Sarthe","Savoie","Sa\u00f4ne-et-Loire","Seine-Maritime","Seine-Saint-Denis","Seine-et-Marne","Somme","Tarn","Tarn-et-Garonne","Territoire-de-Belfort","Val-d'Oise","Val-de-Marne","Var","Vaucluse","Vend\u00e9e","Vienne","Vosges","Yonne","Yvelines"],"US":["Alabama","Alaska","American Samoa","Arizona","Arkansas","Armed Forces Africa","Armed Forces Americas","Armed Forces Canada","Armed Forces Europe","Armed Forces Middle East","Armed Forces Pacific","California","Colorado","Connecticut","Delaware","District of Columbia","Federated States Of Micronesia","Florida","Georgia","Guam","Hawaii","Idaho","Illinois","Indiana","Iowa","Kansas","Kentucky","Louisiana","Maine","Marshall Islands","Maryland","Massachusetts","Michigan","Minnesota","Mississippi","Missouri","Montana","Nebraska","Nevada","New Hampshire","New Jersey","New Mexico","New York","North Carolina","North Dakota","Northern Mariana Islands","Ohio","Oklahoma","Oregon","Palau","Pennsylvania","Puerto Rico","Rhode Island","South Carolina","South Dakota","Tennessee","Texas","Utah","Vermont","Virgin Islands","Virginia","Washington","West Virginia","Wisconsin","Wyoming"],"RO":["Alba","Arad","Arge\u015f","Bac\u0103u","Bihor","Bistri\u0163a-N\u0103s\u0103ud","Boto\u015fani","Bra\u015fov","Br\u0103ila","Bucure\u015fti","Buz\u0103u","Cara\u015f-Severin","Cluj","Constan\u0163a","Covasna","C\u0103l\u0103ra\u015fi","Dolj","D\u00e2mbovi\u0163a","Gala\u0163i","Giurgiu","Gorj","Harghita","Hunedoara","Ialomi\u0163a","Ia\u015fi","Ilfov","Maramure\u015f","Mehedin\u0163i","Mure\u015f","Neam\u0163","Olt","Prahova","Satu-Mare","Sibiu","Suceava","S\u0103laj","Teleorman","Timi\u015f","Tulcea","Vaslui","Vrancea","V\u00e2lcea"],"CA":["Alberta","British Columbia","Manitoba","New Brunswick","Newfoundland and Labrador","Northwest Territories","Nova Scotia","Nunavut","Ontario","Prince Edward Island","Quebec","Saskatchewan","Yukon Territory"],"LT":["Alytaus Apskritis","Kauno Apskritis","Klaip\u0117dos Apskritis","Marijampol\u0117s Apskritis","Panev\u0117\u017eio Apskritis","Taurag\u0117s Apskritis","Tel\u0161i\u0173 Apskritis","Utenos Apskritis","Vilniaus Apskritis","\u0160iauli\u0173 Apskritis"],"DE":["Baden-W\u00fcrttemberg","Bayern","Berlin","Brandenburg","Bremen","Hamburg","Hessen","Mecklenburg-Vorpommern","Niedersachsen","Nordrhein-Westfalen","Rheinland-Pfalz","Saarland","Sachsen","Sachsen-Anhalt","Schleswig-Holstein","Th\u00fcringen"],"AT":["Burgenland","K\u00e4rnten","Nieder\u00f6sterreich","Ober\u00f6sterreich","Salzburg","Steiermark","Tirol","Voralberg","Wien"],"EE":["Harjumaa","Hiiumaa","Ida-Virumaa","J\u00e4rvamaa","J\u00f5gevamaa","L\u00e4\u00e4ne-Virumaa","L\u00e4\u00e4nemaa","P\u00e4rnumaa","P\u00f5lvamaa","Raplamaa","Saaremaa","Tartumaa","Valgamaa","Viljandimaa","V\u00f5rumaa"]};

function mergeJsonObject(jsonbject1, jsonbject2)  
{  
   var resultJsonObject={};  
   for(var attr in jsonbject1){  
      resultJsonObject[attr]=jsonbject1[attr];  
   }  
   for(var attr in jsonbject2){  
      resultJsonObject[attr]=jsonbject2[attr];  
   }  
  
   return resultJsonObject;  
}; 
function strToObj(str){
	str = str.replace(/&/g,"','");
	str = str.replace(/=/g,"':'");
	str = "({'"+str +"'})";
	obj = eval(str);
	return obj;
} 
ec.placeOrder={
	init:function(){
		ec.placeOrder.addAddress();
		ec.placeOrder.editEvent();
		ec.placeOrder.addressLi();
		ec.placeOrder.changeCountry();
		ec.placeOrder.listEvent(true);
		if($("#place_order_type").val()=='paypal_ec_nologin'){
			ec.placeOrder.paypalEcAddress();
		};
		var rememberCheckbox=$('.remember').find('[type="checkbox"]');
		$.each(rememberCheckbox,function(i,n){
			var remove=$(this),
					frmCheck=remove.closest('.frm-check'),
					couponcheckbox=frmCheck.find('.coupon-checkbox');
				frmCheck.find('.code').val('');
			var frmCheck=rememberCheckbox.eq(i).closest('.frm').closest('.frm').find('.frm-check');
				rememberCheckbox.eq(i).is(":checked") ? frmCheck.removeClass('hide')	: frmCheck.addClass('hide');
		})
		rememberCheckbox.on('click',function(){
			var me=$(this),
				coupon=me.closest('.coupon'),
				frmCheck=coupon.find('.frm-check'),
				couponCheckbox=frmCheck.find('.coupon-checkbox');
			if(me.is(":checked")){
				me.attr('checked','checked'); 
				frmCheck.removeClass('hide');
			}else{
				frmCheck.addClass('hide');
				me.removeAttr('checked');
				couponCheckbox.show();
				frmCheck.removeClass('error');
				frmCheck.find('.code').val('');
				frmCheck.find('.coupon-info').addClass('hide');
				frmCheck.find('.removeLinks').hide();	
				frmCheck.find('.tips').remove();
			}	
		})
	},
	addressLi:function(){
		var $address=$('#address'),
			$addressli=$address.find('li[class!="add-ads"]');
		if($addressli.length<=0){
			ec.utils.showPop('addressPop');					
			ec.utils.Cancel();
			ec.placeOrder.eventBlur();
			$('#default').click().attr('disabled','disabled');
			$('#defaultValue').val('1');
		}
		ec.placeOrder.addressMore('more');
	},
	//配送地址,银行选中
	specialEffects:function(){
		$('#payment .method-list li').on('click',function(){
			$(this).addClass('on').siblings().removeClass('on');
		})
		$('#address .address li[class!=add-ads]').on('click',function(){
				$(this).addClass('on').siblings().removeClass('on');
		})
		$('.remember').find('[type="text"]').blur(function(){
			var me=$(this),
				text=me.val(),
				frmCheck=me.closest('.frm-check');
				frmCheck.removeClass('error');
				frmCheck.find('.tips').remove();
				if(text == '' || text.length < 1){
					ec.utils.addError(me,langtips.empty);
					frmCheck.find('.coupon-info').hide();
					frmCheck.find('.removeLinks').hide();
				}
				if( me.attr('id')=='rewardsKey'){
					if(isNaN(text) || parseFloat(text)<=0 ){
						ec.utils.addError(me,langtips.numgt0);
					}
				}
		})
	},
	closePop:function(){
			$('#cancel').on('click',function(){
				$('#close').click();
			})
	},
	changeCountry:function(){
		$('#changeCountry').click(function(){
			ec.utils.showPop('changeConfirm');
			ec.utils.Cancel();
			ec.ui.select();
			ec.placeOrder.closePop();
			ec.placeOrder.listEvent(false);
		})
	},	
	utilsNull:function(km){
		if($.trim(km.val()) == ''){
			ec.utils.addError(km,langtips.empty);
			return false;
		}else{
			return true;	
		}
	},
	loadRegion:function(country, isEdit){
		var html = '<option value=""></option>';
		if(region_data[country]){
			$("#province").empty();
			$.each(region_data[country],function(idx,region){
				html += "<option value='"+region+"'>"+region+"</option>";
			});
			$("#province").append(html);
			$("#province").show();
			$("#region").hide().prop('disabled',true);
			$("#region").val('');
		}else{
			$("#region").show().prop('disabled',false);
			$("#province").hide();
			$("#province").val('');
		}
		if(country=='BR'){
			$('.cpf-box').show();
		}else{
			$('.cpf-box').hide();
			$(".cpf-box").val('');
		}
		if(country == 'CN'){
			$('#citySelect').show().prop('disabled',false);
			$('#City').hide().prop('disabled',true);
			$('#City').val('');
		} else {
			$('#citySelect').hide().prop('disabled',true);
			$('#City').show().prop('disabled',false);
			if(!isEdit){
				//$('#City').val('');
			}
		}
	},
	nameUtils:function(name){
		var firstname=$('#firstName'),
			lastname=$('#lastName');
			ec.placeOrder.utilsNull(name);
			var nameStr = $.trim(firstname.val()) + $.trim(lastname.val());
			var nameLen = nameStr.replace(/[^\x00-\xff]/g, 'xx').length;	//获取字符长度
			if(nameLen>50 ){
				ec.utils.addError(lastname,langtips.username.extent);
				return false;
			}
	},
	addressUtils:function($address){
		var $address1=$('#street_1');
			ec.placeOrder.utilsNull($address);	
		var addressStrLen = $.trim($address1.val()).length;
			if( addressStrLen < 4 || addressStrLen > 100 ){
				ec.utils.addError($address1,langtips.address.extent);
				return false;
			}else{
				$address1.parent().removeClass('error').find('.tips').remove();
			}
		var addressReg = /([\D])\1{2,}|undefined|none/;
		if( addressReg.test($.trim($address.val())) || /^(.)\1+$/.test($.trim($address.val()))){
				ec.utils.addError($address,langtips.address.Invalid);
				return false;
		}
	},
	phoneUtils:function(){
		var mobile=$('#mobile');
		ec.placeOrder.utilsNull(mobile);
		if( $.trim(mobile.val()).length >= 5 ){
			var telReg = /^[\d\s-+\(\)\(\)]+$/;
			var telArr = $.trim(mobile.val()).split(''),isSameNum = true;
			
			if( !telReg.test( $.trim(mobile.val()) )){
				ec.utils.addError(mobile,langtips.phone.Invalid2);
				return false;
			}
			if(/^(.)\1+$/.test(mobile.val())){
				ec.utils.addError(mobile,langtips.phone.Invalid);
				return false;
			}
			
		}else{
			ec.utils.addError(mobile,langtips.phone.extent);
			return false;
		}
	},
	countryUtils:function(bo){
		var country=$('#country'),
			bool=false;
		$('#region,#province,#country').parent().removeClass('error').find('.tips').remove();
			bool=ec.placeOrder.utilsNull(country)? false:true;
		if(bo){
			ec.placeOrder.loadRegion(country.val());
		}
	},
	provinceUtils:function(){
		var province=$('#province');
		province.parent().removeClass('error').find('.tips').remove();
		if(province.is(':visible')){
			if($.trim(province.val()) == '' || $.trim(province.val()).length<=0){
				ec.utils.addError(province,langtips.empty);
				return false;
			}	
		}		
	},
	cityUtils:function(){
		var city=$('#City'),
			country=$('#country');
		if($.trim(country.val()) == 'CN'){
			$('#citySelect').parent().removeClass('error').find('.tips').remove();
			city=$('#citySelect');
		}
		ec.placeOrder.utilsNull(city);
		if( $.trim(city.val()).length > 50){
			ec.utils.addError(city,langtips.city.extent);
			return false;	
		}
		var cityReg = /^(.)\1+$/;
		if( cityReg.test($.trim(city.val())) ){
			ec.utils.addError(city,langtips.city.Invalid);
			return false;
		}
	},
	cpfUtils:function(){
		var country=$('#country'),
			cpf=$('#cpf');
		if( $.trim(country.val()) == 'BR'){
			ec.placeOrder.utilsNull(cpf);	
			if( $.trim(cpf.val()).length < 11 ){
				ec.utils.addError(cpf,langtips.cpf.extent);
				return false;
			}else{
				var cpfReg = /^[\.\/\-a-zA-Z0-9]+$/;
				if( !cpfReg.test($.trim(cpf.val())) ){
					ec.utils.addError(cpf,langtips.cpf.Invalid);
					return false;
				}
			}
		}
	},
	validate:function(){
		var $firstName = $('#firstName'),
		$lastName = $('#lastName'),
		$address = $('#street_1');
		ec.placeOrder.nameUtils($firstName);
		ec.placeOrder.nameUtils($lastName);
		ec.placeOrder.addressUtils($address);
		ec.placeOrder.phoneUtils();
		ec.placeOrder.countryUtils(false);
		ec.placeOrder.cityUtils();
		ec.placeOrder.provinceUtils();
		ec.placeOrder.utilsNull($('#zipcode'));
		ec.placeOrder.cpfUtils();
		
	},
	defaultEvent:function(){
		$('#default').on('click',function(){
				var me=$(this),
					$defaultValue=$('#defaultValue');
				if(me.is(":checked")){
					$defaultValue.attr('value','1');	
				}else{
					$defaultValue.attr('value','0');	
				}
			})
	},
	eventBlur:function(){
		$('.address-table input').on('blur',function(){
			$(this).parent().removeClass('error').find('.tips').remove();
		})
		$('#firstName,#lastName').on('blur',function(){
			ec.placeOrder.nameUtils($(this));
		})
		$('#mobile').on('blur',function(){
			ec.placeOrder.phoneUtils();
		})
		$('#street_1').on('blur',function(){
			ec.placeOrder.addressUtils($(this));
		})
		$('#zipcode').on('blur',function(){
			ec.placeOrder.utilsNull($(this));
		})	
		$('#region').on('blur',function(){
			ec.placeOrder.provinceUtils();
		})
		$('#province').on('change',function(){
			ec.placeOrder.provinceUtils();
		})
		$('#country').on('change',function(){
			ec.placeOrder.countryUtils(true);
		})
		$('#City').on('blur',function(){
			ec.placeOrder.cityUtils();
		})
		$('#citySelect').on('change',function(){
			ec.placeOrder.cityUtils();
		})	
		$('#cpf').on('blur',function(){
			ec.placeOrder.cpfUtils();
		})		
	},
	//地址展开收起
	addressMore:function(more){ 
		var $address=$('#address'),
			morelinks=$('#more,#less');
		morelinks.hide();
		$address.find('li:gt(2)[class!="add-ads"]').hide();
		$address.find('li[class!="add-ads"]').length<=3 ? morelinks.hide() : $('#'+more+'').show();	
		morelinks.on('click',function(){
			var me=$(this),
				li=$address.find('li:gt(2)[class!="add-ads"]');
			me.attr('id')=='more' ? li.show() : li.hide();
			$(this).hide().siblings('a').show();
		})
	},
	editEvent:function(){
		$('.edit a').on('click',function(e){
			e.stopPropagation();
			var me=$(this),
				parent=me.closest('li'),
				index=parent.attr('index');
				ec.utils.showPop('addressPop');	
			$('#edit_address_title_name').show();
			$('#add_address_title_name').hide();
			ec.placeOrder.baseRepayAddress(index);
		})
	},
	paypalEcAddress:function(){
		ec.utils.showPop('addressPop');	
		ec.placeOrder.baseRepayAddress(0);
	},
	baseRepayAddress:function(index){
		ec.utils.Cancel();	
		ec.placeOrder.defaultEvent();
		ec.placeOrder.eventBlur();
		ec.placeOrder.listEvent(false);
		ec.placeOrder.closePop();
		$('#Pop').attr('index',index);
		var address=addressList['address_'+index];
		if(address){
			$('#address_id').val(address.address_id);
			$('#firstName').val(address.first_name);
			$('#lastName').val(address.last_name);
			$('#street_1').val(address.address);
			if( address.country == "CN" ){
				$('#citySelect').val(address.city);
			}else{
				$('#City').val(address.city);
			}
			$('#country').val(address.country);
			$('#zipcode').val(address.zipcode);
			$('#mobile').val(address.mobile);
			$('#cpf').val(address.cpf);
			if(address.defaults=='1'){
				$('#default').attr('checked','checked').hide().next('label').hide();
				$('#defaultValue').val('1');
			}
			ec.placeOrder.loadRegion(address.country, true);
			if(region_data[address.country]){
				$('#province').val(address.region);
			}else{
				$('#region').val(address.region);
			}
		}
	},
	addAddress:function(){
		$('#addAddress').click(function(){
			ec.utils.showPop('addressPop');					
			ec.utils.Cancel();	
			$('#Pop').attr('index','indexAdd');
			ec.placeOrder.eventBlur();
			ec.placeOrder.listEvent(false);
			ec.placeOrder.defaultEvent();
			ec.placeOrder.closePop();
		})
	},
	dataList:function(json){
		var json = json.data;
			var $address=$('#address'),
				addressStr='',
				eb_edit=$('#eb_edit').val(),
				eb_preferred=$('#eb_address').val();
            $address.find('li[class!=add-ads]').remove();
			addressList=json.address;
			$.each(addressList,function(i,n){
				var eb_default='',
					eb_str='';
				if(n.defaults!="0"){
					eb_str='<div class="preferred">'+eb_preferred+'</div>';	
				}
				if(n.checked!="0"){
					eb_default='class="on"';
				}
				addressStr+='<li index="'+n.address_id+'" '+eb_default+'>'+
					'<div class="ads-user"><i></i><strong>'+n.first_name+' '+n.last_name+'</strong></div>'+
					'<div class="ads-info"><p>'+n.address+'</p><p>'+n.city+' '+n.region+' '+n.zipcode+'</p><p>'+n.country_name+'</p></div>'+
					'<div class="ads-phone">Phone:'+n.mobile+'</div>'+
					eb_str+
					'<p class="edit"><a href="javascript:;">'+eb_edit+'</a></p>'+
				'</li>';
			})
			$address.find('ul').prepend(addressStr);
			ec.placeOrder.editEvent();
			ec.placeOrder.addressMore('more');                
			
			var $shpping=$('#shipping'),
				disShip=$shpping.find('.dis-ship'),
				shipPotion=$shpping.find('.ship-potion'),
				radioChecked=$shpping.find('.radio:checked'),
				isRadio=radioChecked.closest('li').find('[type=checkbox]'),
				track=radioChecked.closest('li').find('[type=checkbox]').is(':checked') ? 1 : 0 ;
			if(track==1){
				isRadio.addClass('checks');
				isRadio.on('click',function(){
					$(this).addClass('checks');
				})
			}
			radioChecked.addClass('radioed');
			if($('input.radio[type=radio]').prop('checked')){
				$(this).addClass('radioed');
			}else{
				$(this).removeClass('radioed');
			}
			if(isRadio.prop("checked")){
				$(this).addClass('checks');
			}else{
				$(this).removeClass('checks');
			}
			$shpping.find('li[class!=potion-info]').remove();
			var shipping=json.shipping;
			disShip.hide();
			if(shipping.status=="200"){
				var shoppingData=shipping.data,
					shoppingStr='';
				shipPotion.show();
				$.each(shoppingData,function(i,n){
					var disabled= n.available=='0' ? 'disabled' : '',
						tips=n.tips=='' ? '' : '<dl class="potion-info indent">'+n.tips+'</dl>',
						trak='',
						selected = n.selected=='0' ? '' : 'checked="checked"';
					if(n.track!= -1 && (i !=3)){
						var trackDisabled = n.track=="0" ? '' : 'checked="checked"';
						if (n.available=='0') {
							trak+='<dl class="potion-info gray9">';
						}else{
							trak+='<dl class="potion-info">';
						}
									trak+= '<dt class="remember">'+
										'<input type="checkbox" '+trackDisabled+' '+disabled+' id="checkbox'+i+'" class="check">'+
										'<label class="track" for="checkbox'+i+'">'+n.trackTitle+'</label>'+
									'</dt>'+
									'<dd>'+n.trackPrice+'</dd>'+
								'</dl>';
						
					}
					
					shoppingStr+='<li>';
					if (n.available=='0') {
						shoppingStr+='<dl class="gray9">';
					}else{
						shoppingStr+='<dl>';
					}
								// shoppingStr+=	'<dl>'+
									shoppingStr+='<dt class="remember">'+
											'<input type="radio" name="options" id="'+n.id+'" c class="radio" '+selected+'  '+disabled+'>'+
											'<label for="'+n.id+'">'+n.title+'</label>'+
										'</dt>'+
										'<dd>'+n.day+'</dd>'+
										'<dd>'+n.price+'</dd>'+
									'</dl>'+	
									trak+											
									tips+
								'</li>';

				})
				$shpping.find('.ship-potion').prepend(shoppingStr);
				$('#Other').show();
			}else{
				shipPotion.hide();
				$('#Other').hide ();
				disShip.show().find('span').html(shipping.msg);
			}	
			
				
				
			var $insurance=$('#insurance'),
				$itemsFirst=$('#itemsFirst'),
				$shippingInsurance=$('#shippingInsurance');
			$shippingInsurance.html(json.shippingInsurance);
			json.insurance==1 ? $insurance.attr('checked') : $insurance.removeAttr('checked');
			json.itemsFirst==1 ? $itemsFirst.attr('checked') : $itemsFirst.removeAttr('checked');
				
				
			var $payment=$('#payment'),
				paymentDisShip=$payment.find('.payment'),
				payment=json.payment,
				paymentStr='',
				country=$('#countryId'),
				currency=$('#currencyId');
			country.html(payment.country);
			country.attr('countryid',payment.countryId);
			currency.html(payment.currency);
			currency.attr('currencyid',payment.currencyId);
			paymentDisShip.hide();
			if(payment.data=='' || payment.data.length<=0){
				paymentDisShip.show().find('span').html(payment.msg);
				$payment.find('.method-list').html('');	
			}else{				
				$.each(payment.data,function(i,n){
					 var checked=n.checked==1? 'on' : ""
					paymentStr+='<li class="remember rem-list '+checked+'" paymentid="'+n.id+'">'+
						'<img class="pay-img" src="/resource/default/images/common/default.png" data-lazysrc="'+n.picname+'">'+
						'<input class="method" id="'+n.id+'" type="checkbox"  disabled="disabled"><label for="'+n.id+'"></label>'+
					'</li>';
				})
				$payment.find('.method-list').html(paymentStr);	
			}
			
			
			var $cartSummary=$('#cartSummary'),
				tbody=$cartSummary.find('tbody'),
				cartSummary=json.list,
				cartSummaryStr='';
			tbody.find('tr:last').siblings().remove();
			$.each(cartSummary,function(i,n){
				var attr=n.attr,
					attrStr='';
				if(attr.length>0){
					$.each(attr,function(k,y){
						attrStr+=y.name+': <span>'+y.value+'</span>';	
					})
				}
				cartSummaryStr+='<tr pid="'+n.pid+'">'+
					'<td>'+
						'<img src="/resource/default/images/common/default.png" data-lazysrc="'+n.pic+'" alt="'+n.title+'">'+
						'<div>'+n.title+'</div>'+
						'<p>'+attrStr+'</p>'+
					'</td>'+
					'<td class="t2">'+n.itemPrice+'</td>'+
					'<td class="t3">'+n.quantity+'</td>'+
					'<td class="t4">'+n.price+'</td>'+
				'</tr>';
			})
			tbody.prepend(cartSummaryStr);		
			 	
			var $rewards=$('#rewards'),
				rewards=json.rewards,
				rewardsMsg=$rewards.find('.coupon-info'),
				rewardsRemove=$rewards.find('.removeLinks'),
				rewardsCheckbox=$rewards.find('.coupon-checkbox'),
				rewards1=$('#rewards1'),
				rewardsfrmcheck=$rewards.find('.frm-check');
			$('#rewardsPrice').html(json.AvailableBalanceprice);
			rewardsMsg.addClass('hide');
			rewardsRemove.hide();
			if(rewards==''){
				// rewards1.removeAttr('checked');
			}else{
				rewards1.attr('checked','checked');	
				rewardsfrmcheck.removeClass('hide');
				if(rewards.status=="200"){
					rewardsMsg.show().find('#rewardsPrice').html(rewards.rewardsPrice);	
					$rewards.find('.frm-check').removeClass('error').find('.tips').remove();
					rewardsRemove.show();
					rewardsCheckbox.hide();
				}else{
					ec.utils.addError($rewards.find('input'),rewards.msg);
					rewardsCheckbox.removeClass('hide');
				}	
			}
			var $coupon=$('#coupon'),
				couponKey=$('#couponKey'),
				couponId=$('#couponId'),
				coupon=json.coupon,
				couponMsg=$coupon.find('.coupon-info'),
				couponRemove=$coupon.find('.removeLinks'),
				couponCheckbox=$coupon.find('.coupon-checkbox'),
				coupon1=$('#rewards2'),
				couponfrmcheck=$coupon.find('.frm-check');
				couponMsg.addClass('hide');
				couponRemove.hide();
			if(coupon==''){
				// coupon1.removeAttr('checked');
				// couponfrmcheck.addClass('hide');
			}else{
				coupon1.attr('checked','checked');	
				couponfrmcheck.removeClass('hide');
				if(coupon.status=="200"){
					if(coupon.msg==0){
						couponMsg.show();
						$('#couponPriceErrorTitle').hide();
					}else{
						couponMsg.show().find('#couponPrice').html(coupon.price);
						couponMsg.find('#couponId').html(couponKey.val());
					}
					$coupon.find('.frm-check').removeClass('error').find('.tips').remove();
					couponRemove.show();
					couponCheckbox.hide();
				}else{
					ec.utils.addError(couponKey,coupon.msg);
					couponCheckbox.removeClass('hide');
				}	
			}					
			$('#subtotal').html(json.subtotal);
			$('#shippingCharges').html(json.shippingCharges);
			$('#insurancePrice').html(json.insurancePrice);
			$('#rewardsBalance').html(json.rewardsBalance);
			$('#couponSavings').html(json.couponSavings);
			$('#discountSavings').html(json.discountSavings);
			$('#payPrice').html(json.payPrice);
			//add by wty
			$('#shippingInsurance').html('+'+json.shipping_insurance_txt);
			
			$('.remove').on('click',function(){
				var remove=$(this),
					frmCheck=remove.closest('.frm-check'),
					prevCheckbox=frmCheck.prev('.remember-check').find('.rewards'),
					couponcheckbox=frmCheck.find('.coupon-checkbox');
				remove.parent().hide();
				// frmCheck.hide();
				prevCheckbox.removeClass('check');
				frmCheck.find('.code').val('');
				frmCheck.find('.coupon-info').hide();
				couponcheckbox.show();
				prevCheckbox.addClass('check').attr('checked');
				ec.placeOrder.ajaxEvent($(this));								
			})
			ec.placeOrder.listEvent(false);
			ec.ui.lazyLoad($('body .main').find("img"));
				
	},
	checkSum:function(){
		$('#rewards1').on('change',function(){
			if(!$(this).prop('checked')){
				$(this).attr('checked');
				$(this).parent().next().find('.coupon-info').hide().find('#rewardsPrice').html('');
				$('#rewardsKey').val('');
				ec.placeOrder.ajaxEvent($(this));
			}
		})
		$('#rewards2').on('change',function(){
			if(!$(this).prop('checked')){
				$(this).attr('checked');
				$(this).parent().next().find('.coupon-info').hide().find('#rewardsPrice').html('');
				$('#couponKey').val('');
				ec.placeOrder.ajaxEvent($(this));
			}
		})
	},
	ajaxEvent:function(obj){
			var me=obj,
				id=me.attr('id'),
				$address=$('#address'),
				$on=$address.find('li[class="on"]'),
				addressId=$on.length<1 ? 0 : $on.attr('index');
			var $shpping=$('#shipping'),
				radioChecked=$shpping.find('.radio:checked'),
				radioCheckedId=radioChecked.attr('id'),
				track=radioChecked.closest('li').find('[type=checkbox]').is(':checked') ? 1 : 0 ;
			var $insurance=$('#insurance'),
				insurance=$insurance.is(':checked') ? 1 : 0,
				$itemsFirst=$('#itemsFirst'),
				itemsFirst=$itemsFirst.is(':checked') ? 1 : 0;
			var $payment=$('#payment'),
				paymentId=$payment.find('li[class*="on"]').attr('paymentid');
			var rewardsValue=$('#rewardsKey').val(),
				couponValue=$('#couponKey').val(),
				countryId=$('#countryId'),
				currencyId=$('#currencyId');
			if (!radioCheckedId && typeof(radioCheckedId)!="undefined" && radioCheckedId!=0) radioCheckedId = radioCheckedId.replace('shippingid','');// add by wty
			else radioCheckedId = radioCheckedId;
			if(id=='changeCountrySave'){
				var countrys=$('#selectCountry .selected span'),
					currencys=$('#selectCurrency .selected span');
				countryId.html(countrys.html());
				countryId.attr('countryid',countrys.attr('attrid'));
				currencyId.html(currencys.html());
				currencyId.attr('currencyid',currencys.attr('attrid'));
				//add by WTY 20150813
				ec.cookie.set('currency',currencyId.attr('currencyId'),{expires:7, path:'/',domain:'.eachbuyer.com',secure:false});
			}

			var parameter={
					"addressId":addressId,
					//"shipping":{"id":radioCheckedId,"track":track},
					"shippingid":radioCheckedId,
					"shippingtrack":track,
					"insurance":insurance,
					"itemsFirst":itemsFirst,
					"payment":paymentId,
					"rewards":rewardsValue,
					"coupon":couponValue,
					"country":countryId.attr('countryId'),
					"currency":currencyId.attr('currencyId')
				}
				if(id=='couponApply' || id=='rewardsApply'){
					me.prev().blur();
					var className=me.closest('.frm-check').attr('class');
					 if(className.indexOf('error')!=-1 )
					 	 return false;
				}
			var place_order_type = $("#place_order_type").val();
			var place=$('#place'),
				isbool=(id=='place'),
				action='';
				if(place_order_type=='paypal_ec_login'){
					action=isbool ?  "/place_order/eclogined_processOrder" : "/place_order/eclogined_ajaxFresh";
				}else if(place_order_type=='paypal_ec_nologin'){
					action=isbool ?  "/place_order/ecnologined_processOrder" : "/place_order/ecnologined_ajaxFresh";
				}else{
					action=isbool ?  "/place_order/processOrder" : "/place_order/ajaxFresh";
				}
				if(isbool && place.attr('class').indexOf('place-dis')!=-1){
					return false;
				}
			if(id=='useAddress'){
				var Pop=$('#Pop'),
					form=Pop.find('form'),
					index=Pop.attr('index');
				if(place_order_type=='paypal_ec_nologin'){
					action=	'/place_order/ajaxAddress_nologin';
				}else{	
					action= '/place_order/ajaxAddress';
				}
				ec.utils.removeError();
				ec.placeOrder.validate();
				var length=Pop.find('.error').length;
					if(length>0) return false;
					parameter= mergeJsonObject(parameter,strToObj(decodeURIComponent($('#addressForm').serialize().replace(/\+/g," ").replace(/\'/g, " "),true)));
			}

			$('#checkoutMask').show();
			$.ajax({
					url: action,
					data: parameter,
					type: 'post',
					dataType: 'json',
					success: function(json){
						var status=json.status,
							url=json.data.url;
						if(status=="1007"){
							ec.utils.showPop('placeTips');
							$('#startOver').on('click',function(){
								window.location=url;	
							})
						}else if(status!=200){
							window.location=url;
						}
						if(status=="200"){
							if(isbool){
								window.location=url;
								return false;	
							}else{
								if(json.data.bool==true){
									place.removeClass('icon-view-dis');
									place.removeAttr("disabled");
								}else{
									place.addClass('icon-view-dis');
									place.attr("disabled","disabled")

								}
								ec.placeOrder.dataList(json);
								ec.utils.closeBox();
							}
						}	
						$('#checkoutMask').hide();
					}	
			})
			
	},
	listEvent:function(tbool){
		var eventlist='#address li[class!="add-ads"],#shipping li[class!="potion-info"] input,#payment .method-list li,#changeCountrySave,#useAddress',
			eventlist2=tbool ?  eventlist+',#couponApply,#rewardsApply,#itemsFirst,#insurance' :eventlist;
			ec.placeOrder.specialEffects();
			ec.placeOrder.checkSum();

		$(eventlist2).on('click',function(e){
			e.stopPropagation();
			ec.placeOrder.ajaxEvent($(this));
		})

		if(tbool===true){
			$('#place').on('click',function(e){
				$('#Processing').show();
				$(this).hide();
				e.stopPropagation();
				ec.placeOrder.ajaxEvent($(this));
			})
		}
		
	}
}

ec.ready(function () {
	ec.placeOrder.init();
})
