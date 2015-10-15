ol.lang = {globle : 'us'};
var langtips={
	sys_error : "System Error!",
	ajaxLogin_error : "Invalid login or password!",
	addToWish_error : "This item was already in Wish List.",
	empty:'This field is a required field.',
	//不为数字
	price:'Price should be number.',
	username:{
		//用户名已经存在
		exist:'This customer Nickname already exists.',
		//3-34
		len:'Your Nickname cannot exceed 34 characters',
		//昵称只能由字母、数字和下划线
		charmap:'Nickname only can be composed of letters, figure and underline.',
		//不超过50
		extent:'The number of characters entered cannot exceed 50.'
	},
	email:{
		//请输入一个有效的地址
		valid:'Please enter a valid email address. For example johndoe@domain.com.',
		//已经存在
		exist:"There is already an account with this email address."
	},
	password:{
		//当前密码错误
		currentPassword:'Invalid current password.',
		//密码长度
		len:'Please enter 6 or more characters. Leading or trailing spaces will be ignored.',
		//密码不一致
		disagree:'Please make sure your passwords match.'
	},
	address:{
		//4-100
		extent:'The number of characters entered should be between 4 and 100.',
		Invalid:'Invalid address.'
	},
	phone:{
		//小于5
		extent:'The number of characters entered cannot be less than 5.',
		Invalid:'Invalid phone number.',
		Invalid2:"This field only supports numbers and '-'."
	},
	city:{
		extent:'The number of characters entered cannot exceed 50.',
		Invalid:'Invalid city.'
	},
	cpf:{
		extent:'The number of characters entered cannot be less than 11.',
		Invalid:'This field only supports “.”,"/","-",numbers and letters.'
	},
	writeReview:{
		tit:"Titre est requis.",
		review:"Contenu est requis."
	},
	question:{
		//50以内
		"infos":"The number of characters entered cannot exceed 50.",
		"review":"The number of characters entered should be between 15 and 1000."
	},
	//login 是否阅读条款
	clause:"Please make sure you agree to our Terms and Conditions.",
	//1-9
	numgt0:"Numbers only"
	
}