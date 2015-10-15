ec.pkg('ec.login');

ec.login={
	init:function(){
		var $userName=$('#userName'),
			$nickName=$('#nickName'),
			$email=$('#email'),
			$currentPassword=$('#currentPassword'),
			$agree=$('#agree'),
			$logFormError=$('#logFormError'),
			$remember=$('#remember'),
			$sign=$('#sign'),
			$register=$('#register'),
			$updataEamil=$('#updataEamil'),
			$SIGN=$('#SIGN'),
			$REGISTER=$('#REGISTER'),
			$agree=$('#agree'),
			$register=$('#register'),
			$password=$('#password'),
			$confirmPassword=$('#confirmPassword'),
			radios=$("input[type=radio]");
	  	$("input[type=checkbox]").click(function () {
	        if ($(this).prop("checked")) {
	            $(this).addClass("check");
	        } else {
	            $(this).removeClass("check");
	        }
	    });
	    $("li.potion-info input[type=checkbox]").click(function () {
	        if ($(this).prop("checked")) {
	            $(this).addClass("checked");
	        } else {
	            $(this).removeClass("checked");
	        }
	    });
	    $("dl.potion-info input[type=checkbox]").on('change',function () {
	        $(this).toggleClass('check');
	    });
	    $("input[type=radio]").click(function () {
	        if ($(this).prop("checked")) {
	            $(this).addClass("radioed");
	        } else {
	            $(this).removeClass("radioed");
	        }
	    });

	    var _this = $('#facebook'),
	    	strLen = _this.text().length,
            num = Math.ceil(strLen/22);
        if(num <= 1){
            _this.css('line-height','35px');
        }else{
        	_this.css('line-height','18px');
        }
		function checkUser(){
			var	userName=$.trim($userName.val());
				if(userName == '' || userName.length < 1){
					ec.utils.addError($userName,langtips.empty);
					return false;
				}
		}
		$('.earn').on('click',function(){
			ec.utils.showPop('rewardsTips');
			ec.utils.Cancel();
		})
		$userName.on('blur',function(){
			ec.utils.removeTips($(this));
			checkUser();
		})
		$nickName.on('blur',function(){
			 ec.utils.removeTips($(this));
			 ec.utils.username();
		})
		$email.on('blur',function(){
			ec.utils.removeTips($(this));
			ec.utils.email();
		})
		$currentPassword.on('blur',function(){
			ec.utils.removeTips($(this));
			ec.utils.currentPassword();
		})
		$('#password,#confirmPassword').on('blur',function(){
			ec.utils.removeTips($(this));
			ec.utils.newPassword($(this));
		})
		$agree.on('click',function(){
			ec.utils.removeTips($(this));
			if(!$(this).is(':checked')){
				ec.utils.addError($(this),langtips.clause);
			}
		})
		$sign.on('click',function(){
			checkUser();
			ec.utils.currentPassword();
			var form=$('#loginForm'),
				errorLength=form.find('.error').length,
				bool=$remember.is(':checked') ? true :false;
				$logFormError.find('tips').remove();
			var url=window.location.href,
				urlSplit = url.split('/'),
				loadUrl = urlSplit[urlSplit.length-1];
			if(errorLength<=0){
				$.ajax({
					url: "/login/ajaxAuthenticate",
					data: {user_name:$userName.val(),password:$currentPassword.val(),remember:bool,loadUrl:loadUrl},
					type: 'POST',
					async:false,  
					dataType: 'json',
					success: function(json){
						if (json.status!="200") {
							ec.utils.addError($logFormError,json.msg);	
							return false;
						}
						if($('#refer').val() != ''){
							window.location.href=$('#refer').val();
						}else{
							var timestamp = Date.parse(new Date()); 
							window.location.href=form.attr('action')+'?time='+timestamp;
						}
					}	
				})
			}
		})
		$register.on('click',function(){
			ec.utils.username();
			ec.utils.email();	
			ec.utils.newPassword($('#password'));	
			ec.utils.newPassword($('#confirmPassword'));
			var form=$('#resgisterForm'),
				errorLength=form.find('.error').length;
			if(errorLength<=0){
				form.submit();
			}else{
				return false;
			}
		})
		$updataEamil.click(function(){
			ec.utils.email();		
			var form=$('#updateEamilForm'),
				errorLength=form.find('.error').length;
			if(errorLength<=0){
				form.submit();
			}
		})
		
		$SIGN.on('click',function(){
			checkUser();
			ec.utils.currentPassword();
			var form=$('#loginForm'),
				errorLength=form.find('.error').length,
				bool=$remember.is(':checked') ? true :false;
				$logFormError.find('tips').remove();
			var url=window.location.href,
				urlSplit = url.split('/'),
				loadUrl = urlSplit[urlSplit.length-1];
			if(errorLength<=0){
				$.ajax({
					url: "/login/ajaxAuthenticate",
					data: {user_name:$userName.val(),password:$currentPassword.val(),remember:bool,loadUrl:loadUrl},
					type: 'POST',
					async:false, 
					dataType: 'json',
					success: function(json){
						if (json.status!="200") {
							ec.utils.addError($logFormError,json.msg);	
							return false;
						}else{
							ec.utils.closeBox();
                            if(json.redirect_url)
                                location.href = json.redirect_url;
                            else
							    location.reload();
						}
						
					}	
				})
			}
		})
		$REGISTER.on('click',function(){
			ec.utils.username();
			ec.utils.email();	
			ec.utils.newPassword($('#password'));	
			ec.utils.newPassword($('#confirmPassword'));
			var form=$('#resgisterForm'),
				errorLength=form.find('.error').length,
				URL=window.location.href,
				bool=$register.is(':checked') ? true :false;
			var url=window.location.href,
				urlSplit = url.split('/'),
				loadUrl = urlSplit[urlSplit.length-1];
			if(errorLength<=0){
				$.ajax({
					url: "/login/ajaxRegister",
					data: {user_name:$nickName.val(),email:$email.val(),password:$password.val(),confirm_password:$confirmPassword.val(),agreement:true,subscribe:$('#newsletter').val(),reg_from:$('#reg_from').val(),loadUrl:loadUrl},
					type: 'POST',
					async:false, 
					dataType: 'json',
					success: function(json){
						if (json.status!="200") {
							ec.utils.addError($logFormError,json.msg);	
							return false;
						}else{
							ec.utils.closeBox();
                            if(json.redirect_url)
                                location.href = json.redirect_url;
                            else
							    location.reload();
						}
					}	
				})
			}
		})
		ec.utils.Cancel();
		ec.login.loginTab();
	},
	loginTab:function(){
		//登录弹出层table切换
		var shopTab=$('.shop-tab a');
		shopTab.eq(0).addClass('on');
		shopTab.on('click',function(){
			var index=$(this).index();
			$(this).addClass('on').siblings().removeClass('on');
			$('.shop-menu form').eq($(this).index()).show().siblings().hide();
		})	
	}
}
ec.ready(function () {
	ec.login.init();
})
