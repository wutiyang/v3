/**
跨域AJAX请求类
Author:		Lulu
Last Edit:	2012-10-15
Version:	0.1

flash参考自:
http://blog.s135.com/ajaxcdr/
**/

(function(){


	var _getFlash = function() {
		//var flash = (navigator.appName.indexOf ("Microsoft") !=-1)?window["storage"]:document["storage"];
		return document.getElementById("ol_cdr");
	},
	writeFlash = function() {
		var swfName = ol.libPath + "/base/ajaxcdr.swf"; 
		   
		if (window.ActiveXObject)
		{
			// browser supports ActiveX
			// Create object element with 
			// download URL for IE OCX
			document.write('<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"');
			document.write(' codebase="http://download.macromedia.com');
			document.write('/pub/shockwave/cabs/flash/swflash.cab#version=8,5,0,0"');
			document.write(' height="0" width="0" id="ol_cdr">');
			document.write('<param name="allowScriptAccess" value="always" />');
			document.write(' <param name="movie" value="' + swfName + '">');
			document.write(' <param name="quality" value="high">');
			document.write(' <param name="swliveconnect" value="true">');
			document.write('<\/object>');
		}
		else
		{
			// browser supports Netscape Plugin API
			document.write('<embed src="'+swfName+'" quality="high" width="0" height="0" id="ol_cdr" align="middle" allowScriptAccess="always" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer_cn" />');
		}
	}

	//FlashHelper.init();

	var _req= function(options) {
		var fs = _getFlash(),
			contentType = "application/x-www-form-urlencoded";

		if(options.data)
		{
			var q = jQuery.param(options.data);
			options.url += (options.url.indexOf('?') >= 0 ? '&' : '?') + q;
		}
		
		options = $.extend({
			dataType : "json"
		} , options);

		var funName = "cdrCallback"+new Date().getTime();
		window[funName] = function(success , data){
			if(options.afterSendFunction)options.afterSendFunction();

			try{
				delete window[funName];
			}catch(e){
				window[funName] = null;
			}
			//请求失败
			if(!success)return;

			switch(options.dataType.toLowerCase())
			{
				case "json":
					data = jQuery.parseJSON(data);
					break;
			}
			if(options.successFunction)options.successFunction(data);

		}//callback end
		
		if(options.beforeSendFunction)options.beforeSendFunction();
		if (options.method == "GET"){
			//GET请求方式
			fs._request(options.url , "GET", "" , contentType , "" , funName);
		} else {
			//POST请求方式
			var body = $(options.form).formSerialize();
			fs._request(options.url, "POST" , body , contentType , "" ,funName);
		}
	}

	ol.cdr = {};
	ol.cdr.get = ol.cdr.load = function(options) {
		options.type = "GET";
		_req(options);
	}	

	ol.cdr.submit = ol.cdr.post = function(options) {
		options.type = "POST";
		_req(options);
		
	}

	ol.cdr._ready = function(d){
		_getFlash()._setDebug(ol.debug);
		log("CDR" , "ready");

	}

	ol.cdr._log = function(msg){
		log("CDR" , msg);
	}

	writeFlash();
})();


