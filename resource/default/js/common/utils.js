ec.pkg('ec.utils');
ec.utils={
	username:function(){
		var $user_name = $('#nickName'),
			username=$.trim($user_name.val()),
			ubool=true;
		if(username == '' || username.length < 1){
			ec.utils.addError($user_name,langtips.empty);
			return false;
		}
		if(!/^\w+$/.test(username)){
			ec.utils.addError($user_name,langtips.username.charmap);
			return false;	
		}
		if(username.length<3 || username.length>34){
			ec.utils.addError($user_name,langtips.username.len);
			return false;	
		}
		ec.utils.removeError();
        $.ajax({
            url: "/login/checkUserNameAvailable",
            data: {user_name:username},
            type: 'POST',
            async:false,  
            dataType: 'json',
            success: function(json){
                if (json.status!="200") {
                    ec.utils.addError($user_name,langtips.username.exist);
					ubool=false;
                }
            }
        });
		return ubool;
	},
	email:function(){
		var $email = $('#email'),
				email=$.trim($email.val());
				bool=true;
			var emailReg=/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
			if(email=='' || email.length < 1){
				ec.utils.addError($email,langtips.empty);
				bool=false;
				return false;
			}
			if(!email.match(emailReg)){
				ec.utils.addError($email,langtips.email.valid);
				return false;
				bool=false;
			}
			$.ajax({
				url: "/login/checkEmailAvailable",
				data: {email:email},
				type: 'POST',
				async:false,  
				dataType: 'json',
				success: function(json){
					if (json.status!="200") {
						ec.utils.addError($email,langtips.email.exist);
						bool=false;
					}
				}	
			})
			return bool;
	},
	currentPassword:function(){
		var $currentPassword=$('#currentPassword'),
			currentPassword=$.trim($currentPassword.val());
		if(currentPassword == '' ){
				ec.utils.addError($currentPassword,langtips.empty);
				return false;
			}
			if(currentPassword.length<6){
				ec.utils.addError($currentPassword,langtips.password.len);
				return false;
			}
			$.ajax({
				url: "/login/checkPasswordAvailable",
				data: {username:$('#userName').val(),password:currentPassword},
				type: 'POST',
				dataType: 'json',
				success: function(json){
					if (json.status!="200") {
						ec.utils.addError($currentPassword,langtips.password.currentPassword);
						return false;
					}
				}
			});
	},
	newPassword:function(newPassword){
		var	$password=$('#password'),
			password=$.trim($password.val());
			$confirm_password=$('#confirmPassword');
			confirm_password=$.trim($confirm_password.val());
			text=$.trim(newPassword.val());
			if(text== '' || text.length<1){
				ec.utils.addError(newPassword,langtips.empty);
				return false;
			}
			if(text.length<6){
				ec.utils.addError(newPassword,langtips.password.len);
				return false;
			}
			if(password!=confirm_password){
				$password.closest('.info').addClass('error');
				ec.utils.addError($confirm_password,langtips.password.disagree);
				return false;
			}else{
				$password.closest('.info').removeClass('error');
				$confirm_password.closest('.info').removeClass('error').find('.tips').remove();	
			}
	},
	removeError:function(){
		var error=$('.error');
			error.find('.tips').remove();
			error.removeClass('error');
	},
	removeTips:function(me){
		me.closest('.info').removeClass('error').find('.tips').remove();
	},
	addError:function(me,msg){
		var info=me.closest('.info');
		if(info.find('.tips').length<1){
			info.addClass('error').append('<div class="tips">'+msg+'</div>');
		}
	},
	closeBox:function(){
		$('#Pop,#shade').remove();
	},
	Cancel:function(){
		$('.cancel,#close').on('click',function(){
			ec.utils.closeBox();	
		})
	},
	shade:function(){
		$('body').append('<div id="shade"><iframe width="100%" height="100%" frameborder=0></iframe></div>');
		var Pop=$('#Pop'),
			left=Pop.offset().left,
			top=Pop.offset().top;
			$('body').append(Pop.remove().show().css({'top':top,'left':left,'margin-left':'0px'}));
	},
	showPop:function(obj){
		var by=$('body'),
			html=$('#'+obj).html();
		by.append('<div id="shade"><iframe width="100%" height="100%" frameborder=0></iframe></div>');
		by.append(html);
		var Pop=$('#Pop'),
			wid=Pop.width()/2,
			hei=Pop.height()/2;
		Pop.css({'position':'fixed','top':'50%','left':"50%",'margin-top':'-'+hei+'px','margin-left':'-'+wid+'px','width':'auto','z-index':'99'});
	},
	loading:function(){
		var by=$('body'),
			html='<div class="loading" id="Pop" style="position:fixed"></div>';
		by.append('<div id="shade"><iframe width="100%" height="100%" frameborder=0></iframe></div>');
		by.append(html);
	}
}