/** 
 * 公共方法，改动或追加比较多
 * @class 	allPage
 * @author  gongshan
 * @date	2015/05/25
 * @dependon jquery-1.4.1.min.js or later、 ec.lib.js
 */
ec.pkg('ec.accountSettings');
var bool=true;
ec.accountSettings={
	init:function(){
		ec.accountSettings.showPop('editUserName','editUserNameBox');
		ec.accountSettings.showPop('editEamil','editEamilBox');
		ec.accountSettings.showPop('editPassword','editPasswordBox');
	},
	showPop:function(editId,showId){
		$('#'+editId).click(function(){
			ec.utils.closeBox();
			var html=$('#'+showId).html();
			$('#'+editId).parent().append(html);
			ec.utils.shade();
			//用户名
			if(editId=='editUserName'){
				ec.form.tips.label('#nickName');
				$('#nickName').on('blur',function(){
					$(this).parent().removeClass('error').find('.tips').remove();
					 ec.utils.username();
				})
				$('#updateName').on('click',function(){
					if(ec.utils.username()){
						$.ajax({
							url: "/account_settings/ajaxModifyUsername",
							data: {user_name:$('#nickName').val()},
							type: 'POST',
							dataType: 'json',
							success: function(json){
								if (json.status=='200') {
									location.reload();	
								}else{
									if(json.status == 1007){
										ec.utils.showPop('loginPop');					
										ec.login.init();
									}else{
										//alert(json.msg);
									}
								}
							}
						})
					}
				})
			}
			//邮箱
			if(editId=='editEamil'){
				ec.form.tips.label('#email');
				$('#email').on('blur',function(){
					$(this).parent().removeClass('error').find('.tips').remove();
					 ec.utils.email();
				})
				$('#updateEmail').on('click',function(){
					if(ec.utils.email()){
						$.ajax({
							url: "/account_settings/ajaxModifyEmail",
							data: {email:$('#email').val()},
							type: 'POST',
							dataType: 'json',
							success: function(json){
								if (json.status=='200') {
									location.reload();	
								}else{
									if(json.status == 1007){
										ec.utils.showPop('loginPop');					
										ec.login.init();
									}else{
										//alert(json.msg);
									}
								}			
							}
						})
					}
				})
				
			}
			//密码
			if(editId=='editPassword'){
				ec.form.tips.label('#currentPassword');
				ec.form.tips.label('#password');
				ec.form.tips.label('#confirmPassword');	
				$('#currentPassword').on('blur',function(){
					$(this).parent().removeClass('error').find('.tips').remove();
					 ec.utils.currentPassword();
				})
				$('#password,#confirmPassword').on('blur',function(){
					$(this).parent().removeClass('error').find('.tips').remove();
					 ec.utils.newPassword($(this));
				})
				$('#updatePassword').on('click',function(){
					ec.accountSettings.updatePassword();
					if($('.error .tips').length<1){
						$.ajax({
							url: "/account_settings/ajaxModifyPassword",
							data: {password:$('#password').val(),confirm_password:$('#confirmPassword').val(),current_password:$('#currentPassword').val()},
							type: 'POST',
							dataType: 'json',
							success: function(json){
								if (json.status=='200') {
									location.reload();	
								}else{
									if(json.status == 1007){
										ec.utils.showPop('loginPop');					
										ec.login.init();
									}else{
										//alert(json.msg);
									}
								}			
							}
						})
					}
				})
			}
			ec.utils.Cancel();
		})	
	},
	updatePassword:function(){
		ec.utils.currentPassword();
		ec.utils.newPassword($('#password'));
		ec.utils.newPassword($('#confirmPassword'));
	}
}
ec.ready(function () {
	ec.accountSettings.init();	
})



