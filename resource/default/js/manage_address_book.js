/** 
 * 公共方法，改动或追加比较多
 * @class 	allPage
 * @author  gongshan
 * @date	2015/05/25
 * @dependon jquery-1.4.1.min.js or later、 ec.lib.js
 */
ec.pkg('ec.manageAddressBook');

//var region_data = {"ES":["A Coru\u00f1a","Alava","Albacete","Alicante","Almeria","Asturias","Avila","Badajoz","Baleares","Barcelona","Burgos","Caceres","Cadiz","Cantabria","Castellon","Ceuta","Ciudad Real","Cordoba","Cuenca","Girona","Granada","Guadalajara","Guipuzcoa","Huelva","Huesca","Jaen","La Rioja","Las Palmas","Leon","Lleida","Lugo","Madrid","Malaga","Melilla","Murcia","Navarra","Ourense","Palencia","Pontevedra","Salamanca","Santa Cruz de Tenerife","Segovia","Sevilla","Soria","Tarragona","Teruel","Toledo","Valencia","Valladolid","Vizcaya","Zamora","Zaragoza"],"CH":["Aargau","Appenzell Ausserrhoden","Appenzell Innerrhoden","Basel-Landschaft","Basel-Stadt","Bern","Freiburg","Genf","Glarus","Graub\u00fcnden","Jura","Luzern","Neuenburg","Nidwalden","Obwalden","Schaffhausen","Schwyz","Solothurn","St. Gallen","Tessin","Thurgau","Uri","Waadt","Wallis","Zug","Z\u00fcrich"],"LV":["Aglonas novads","Aizkraukles novads","Aizputes novads","Akn\u012bstes novads","Alojas novads","Alsungas novads","Al\u016bksnes novads","Amatas novads","Apes novads","Auces novads","Bab\u012btes novads","Baldones novads","Baltinavas novads","Balvu novads","Bauskas novads","Bever\u012bnas novads","Broc\u0113nu novads","Burtnieku novads","Carnikavas novads","Cesvaines novads","Ciblas novads","C\u0113su novads","Dagdas novads","Daugavpils","Daugavpils novads","Dobeles novads","Dundagas novads","Durbes novads","Engures novads","Garkalnes novads","Grobi\u0146as novads","Gulbenes novads","Iecavas novads","Ik\u0161\u0137iles novads","Il\u016bkstes novads","In\u010dukalna novads","Jaunjelgavas novads","Jaunpiebalgas novads","Jaunpils novads","Jelgava","Jelgavas novads","J\u0113kabpils","J\u0113kabpils novads","J\u016brmala","Kandavas novads","Kokneses novads","Krimuldas novads","Krustpils novads","Kr\u0101slavas novads","Kuld\u012bgas novads","K\u0101rsavas novads","Lielv\u0101rdes novads","Liep\u0101ja","Liep\u0101jas novads","Limba\u017eu novads","Lub\u0101nas novads","Ludzas novads","L\u012bgatnes novads","L\u012bv\u0101nu novads","Madonas novads","Mazsalacas novads","M\u0101lpils novads","M\u0101rupes novads","Nauk\u0161\u0113nu novads","Neretas novads","N\u012bcas novads","Ogres novads","Olaines novads","Ozolnieku novads","Prei\u013cu novads","Priekules novads","Prieku\u013cu novads","P\u0101rgaujas novads","P\u0101vilostas novads","P\u013cavi\u0146u novads","Raunas novads","Riebi\u0146u novads","Rojas novads","Ropa\u017eu novads","Rucavas novads","Rug\u0101ju novads","Rund\u0101les novads","R\u0113zekne","R\u0113zeknes novads","R\u012bga","R\u012bgas novads","R\u016bjienas novads","Salacgr\u012bvas novads","Salas novads","Salaspils novads","Saldus novads","Saulkrastu novads","Siguldas novads","Skrundas novads","Skr\u012bveru novads","Smiltenes novads","Stopi\u0146u novads","Stren\u010du novads","S\u0113jas novads","Talsu novads","Tukuma novads","T\u0113rvetes novads","Vai\u0146odes novads","Valkas novads","Valmiera","Valmieras novads","Varak\u013c\u0101nu novads","Vecpiebalgas novads","Vecumnieku novads","Ventspils","Ventspils novads","Vies\u012btes novads","Vi\u013cakas novads","Vi\u013c\u0101nu novads","V\u0101rkavas novads","Zilupes novads","\u0100da\u017eu novads","\u0112rg\u013cu novads","\u0136eguma novads","\u0136ekavas novads"],"FI":["Ahvenanmaa","Etel\u00e4-Karjala","Etel\u00e4-Pohjanmaa","Etel\u00e4-Savo","It\u00e4-Uusimaa","Kainuu","Kanta-H\u00e4me","Keski-Pohjanmaa","Keski-Suomi","Kymenlaakso","Lappi","Pirkanmaa","Pohjanmaa","Pohjois-Karjala","Pohjois-Pohjanmaa","Pohjois-Savo","P\u00e4ij\u00e4t-H\u00e4me","Satakunta","Uusimaa","Varsinais-Suomi"],"FR":["Ain","Aisne","Allier","Alpes-Maritimes","Alpes-de-Haute-Provence","Ardennes","Ard\u00e8che","Ari\u00e8ge","Aube","Aude","Aveyron","Bas-Rhin","Bouches-du-Rh\u00f4ne","Calvados","Cantal","Charente","Charente-Maritime","Cher","Corr\u00e8ze","Corse-du-Sud","Creuse","C\u00f4te-d'Or","C\u00f4tes-d'Armor","Deux-S\u00e8vres","Dordogne","Doubs","Dr\u00f4me","Essonne","Eure","Eure-et-Loir","Finist\u00e8re","Gard","Gers","Gironde","Haut-Rhin","Haute-Corse","Haute-Garonne","Haute-Loire","Haute-Marne","Haute-Savoie","Haute-Sa\u00f4ne","Haute-Vienne","Hautes-Alpes","Hautes-Pyr\u00e9n\u00e9es","Hauts-de-Seine","H\u00e9rault","Ille-et-Vilaine","Indre","Indre-et-Loire","Is\u00e8re","Jura","Landes","Loir-et-Cher","Loire","Loire-Atlantique","Loiret","Lot","Lot-et-Garonne","Loz\u00e8re","Maine-et-Loire","Manche","Marne","Mayenne","Meurthe-et-Moselle","Meuse","Morbihan","Moselle","Ni\u00e8vre","Nord","Oise","Orne","Paris","Pas-de-Calais","Puy-de-D\u00f4me","Pyr\u00e9n\u00e9es-Atlantiques","Pyr\u00e9n\u00e9es-Orientales","Rh\u00f4ne","Sarthe","Savoie","Sa\u00f4ne-et-Loire","Seine-Maritime","Seine-Saint-Denis","Seine-et-Marne","Somme","Tarn","Tarn-et-Garonne","Territoire-de-Belfort","Val-d'Oise","Val-de-Marne","Var","Vaucluse","Vend\u00e9e","Vienne","Vosges","Yonne","Yvelines"],"US":["Alabama","Alaska","American Samoa","Arizona","Arkansas","Armed Forces Africa","Armed Forces Americas","Armed Forces Canada","Armed Forces Europe","Armed Forces Middle East","Armed Forces Pacific","California","Colorado","Connecticut","Delaware","District of Columbia","Federated States Of Micronesia","Florida","Georgia","Guam","Hawaii","Idaho","Illinois","Indiana","Iowa","Kansas","Kentucky","Louisiana","Maine","Marshall Islands","Maryland","Massachusetts","Michigan","Minnesota","Mississippi","Missouri","Montana","Nebraska","Nevada","New Hampshire","New Jersey","New Mexico","New York","North Carolina","North Dakota","Northern Mariana Islands","Ohio","Oklahoma","Oregon","Palau","Pennsylvania","Puerto Rico","Rhode Island","South Carolina","South Dakota","Tennessee","Texas","Utah","Vermont","Virgin Islands","Virginia","Washington","West Virginia","Wisconsin","Wyoming"],"RO":["Alba","Arad","Arge\u015f","Bac\u0103u","Bihor","Bistri\u0163a-N\u0103s\u0103ud","Boto\u015fani","Bra\u015fov","Br\u0103ila","Bucure\u015fti","Buz\u0103u","Cara\u015f-Severin","Cluj","Constan\u0163a","Covasna","C\u0103l\u0103ra\u015fi","Dolj","D\u00e2mbovi\u0163a","Gala\u0163i","Giurgiu","Gorj","Harghita","Hunedoara","Ialomi\u0163a","Ia\u015fi","Ilfov","Maramure\u015f","Mehedin\u0163i","Mure\u015f","Neam\u0163","Olt","Prahova","Satu-Mare","Sibiu","Suceava","S\u0103laj","Teleorman","Timi\u015f","Tulcea","Vaslui","Vrancea","V\u00e2lcea"],"CA":["Alberta","British Columbia","Manitoba","New Brunswick","Newfoundland and Labrador","Northwest Territories","Nova Scotia","Nunavut","Ontario","Prince Edward Island","Quebec","Saskatchewan","Yukon Territory"],"LT":["Alytaus Apskritis","Kauno Apskritis","Klaip\u0117dos Apskritis","Marijampol\u0117s Apskritis","Panev\u0117\u017eio Apskritis","Taurag\u0117s Apskritis","Tel\u0161i\u0173 Apskritis","Utenos Apskritis","Vilniaus Apskritis","\u0160iauli\u0173 Apskritis"],"DE":["Baden-W\u00fcrttemberg","Bayern","Berlin","Brandenburg","Bremen","Hamburg","Hessen","Mecklenburg-Vorpommern","Niedersachsen","Nordrhein-Westfalen","Rheinland-Pfalz","Saarland","Sachsen","Sachsen-Anhalt","Schleswig-Holstein","Th\u00fcringen"],"AT":["Burgenland","K\u00e4rnten","Nieder\u00f6sterreich","Ober\u00f6sterreich","Salzburg","Steiermark","Tirol","Voralberg","Wien"],"EE":["Harjumaa","Hiiumaa","Ida-Virumaa","J\u00e4rvamaa","J\u00f5gevamaa","L\u00e4\u00e4ne-Virumaa","L\u00e4\u00e4nemaa","P\u00e4rnumaa","P\u00f5lvamaa","Raplamaa","Saaremaa","Tartumaa","Valgamaa","Viljandimaa","V\u00f5rumaa"]};
	
ec.manageAddressBook={
	init:function(){
		ec.manageAddressBook.addAddress();
		ec.manageAddressBook.editEvent();
		ec.manageAddressBook.deleteEvent();
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
			$("#region").hide();
			$("#region").val('');
		}else{
			$("#region").show();
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
			ec.manageAddressBook.utilsNull(name);
			var nameStr = $.trim(firstname.val()) + $.trim(lastname.val());
			var nameLen = nameStr.replace(/[^\x00-\xff]/g, 'xx').length;	//获取字符长度
			if(nameLen>50 ){
				ec.utils.addError(lastname,langtips.username.extent);
				return false;
			}
	},
	addressUtils:function($address){
		var $address1=$('#street_1');	
			ec.manageAddressBook.utilsNull($address);	
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
		ec.manageAddressBook.utilsNull(mobile);
		if( $.trim(mobile.val()).length >= 5 ){
			var telReg = /^[\d\s-+\(\)\（\）]+$/;
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
			bool=ec.manageAddressBook.utilsNull(country)? false:true;
		if(bo){
			ec.manageAddressBook.loadRegion(country.val());
		}
	},
	provinceUtils:function(){
		var province=$('#province');
		province.parent().removeClass('error').find('.tips').remove();
		if(province.is(':visible')){
			if($.trim(province.val()) == '' || $.trim(province.val()).length<=0){
				ec.utils.addError(province,langtips.empty);
				return false;
			}else{
				$('#region').val(province.val());
			}	
		}else{
			province = $('#region');
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
		ec.manageAddressBook.utilsNull(city);
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
			ec.manageAddressBook.utilsNull(cpf);	
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
		ec.manageAddressBook.nameUtils($firstName);
		ec.manageAddressBook.nameUtils($lastName);
		ec.manageAddressBook.addressUtils($address);
		ec.manageAddressBook.phoneUtils();
		ec.manageAddressBook.countryUtils(false);
		ec.manageAddressBook.cityUtils();
		ec.manageAddressBook.provinceUtils();
		ec.manageAddressBook.utilsNull($('#zipcode'));
		ec.manageAddressBook.cpfUtils();
		
	},
	eventBlur:function(){
		$('.address-table input').on('blur',function(){
			$(this).parent().removeClass('error').find('.tips').remove();
		})
		$('#firstName,#lastName').on('blur',function(){
			ec.manageAddressBook.nameUtils($(this));
		})
		$('#mobile').on('blur',function(){
			ec.manageAddressBook.phoneUtils();
		})
		$('#street_1').on('blur',function(){
			ec.manageAddressBook.addressUtils($(this));
		})
		$('#zipcode').on('blur',function(){
			ec.manageAddressBook.utilsNull($(this));
		})	
		$('#region').on('blur',function(){
			ec.manageAddressBook.provinceUtils();
		})
		$('#province').on('change',function(){
			ec.manageAddressBook.provinceUtils();
		})
		$('#country').on('change',function(){
			ec.manageAddressBook.countryUtils(true);
		})
		$('#City').on('blur',function(){
			ec.manageAddressBook.cityUtils();
		})
		$('#citySelect').on('change',function(){
			ec.manageAddressBook.cityUtils();
		})	
		$('#cpf').on('blur',function(){
			ec.manageAddressBook.cpfUtils();
		})		
	},
	saveEvent:function(){
		$('button[title=Save]').on('click',function(){
			var Pop=$('#Pop'),
				form=Pop.find('form');
			var action=	Pop.attr('index')=='indexAdd' ? form.attr('actionAdd') : form.attr('actionEdit');
			ec.utils.removeError();
			ec.manageAddressBook.validate();
			var length=Pop.find('.error').length;
			if(length<1){
				$('#addressForm').attr('action',action).submit();
			}
		})	
	},
	editEvent:function(){
		$('button[title=Edit]').click(function(){
				var me=$(this),
				parent=me.closest('.box-module'),
				index=parent.attr('index');
				ec.manageAddressBook.createModule($('#addressPop'),parent);
				$('#Pop').attr('index',index);
				ec.manageAddressBook.defaultEvent();
				$.ajax({
					url: "/manage_address_book/ajaxAddrInfo",
					data: {id:index},
					type: 'POST',
					dataType: 'json',
					success: function(json){
						if (json.status == 200) {
							if(json.data[index]){
								var address = json.data[index];
								$('#addressId').val(address.address_id);
								$('#firstName').val(address.address_firstname);
								$('#lastName').val(address.address_lastname);
//								$('#company').val(address.company);
								$('#street_1').val(address.address_address);
								if( address.country == "CN" ){
									$('#citySelect').val(address.address_city);
								}else{
									$('#City').val(address.address_city);
								}
								$('#country').val(address.address_country);
								$('#zipcode').val(address.address_zipcode);
								$('#mobile').val(address.address_phone);
								$('#cpf').val(address.address_cpfcnpj);
								if(address.address_default=='1'){
									$('#default').attr('checked','checked').hide().next('label').hide();
									$('#defaultValue').val('1');
								}
								ec.manageAddressBook.loadRegion(address.address_country, true);
								if(region_data[address.address_country]){
									$('#province').val(address.address_province);
								}else{
									$('#region').val(address.address_province);
								}
								ec.manageAddressBook.eventBlur();
								ec.manageAddressBook.saveEvent();
							}
						}else{
							if(json.status == 1007){
								ec.utils.showPop('loginPop');					
								ec.login.init();
							}else{
								//alert(json.msg);
							}
						}
						
					}
				});
		})
	},
	addAddress:function(){
		$('#addAddress p').click(function(){
			ec.manageAddressBook.createModule($('#addressPop'),$(this).parent());
			$('#Pop').attr('index','indexAdd');
			ec.manageAddressBook.eventBlur();
			ec.manageAddressBook.saveEvent();
			ec.manageAddressBook.defaultEvent();
		})
	},
	createModule:function(htmlId,parent){
		var html=htmlId.html();
			parent.append(html);
			ec.utils.shade();
			ec.utils.Cancel();
	},
	deleteEvent:function(){
		$('button[title=Delete]').click(function(){
			var me=$(this),
				parent=me.closest('.box-module'),
				index=parent.attr('index');
				ec.manageAddressBook.createModule($('#delete'),parent);
				$('.delete-box').attr('index',index);
				$('#deleteOk').on('click',function(){
					ec.manageAddressBook.deleteOk();	
				})
		})
	},
	deleteOk:function(){
		var index=$('.delete-box').attr('index');
		$.ajax({
				url: "/manage_address_book/ajaxDel",
				data: {id:index},
				type: 'POST',
				dataType: 'json',
				success: function(json){
					if (json.status=='200') {
						ec.utils.closeBox();
						$('div[index='+index+']').remove();
					}else{
						if(json.status == 1007){
							ec.utils.showPop('loginPop');					
							ec.login.init();
						}else{
							//alert(json.msg);
						}
					}
				}
			});
		
	}
}
ec.ready(function () {
	ec.manageAddressBook.init();
})
