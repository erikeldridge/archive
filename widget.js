var widget = document.getElementById('widget'),
	token = null,
	url = 'index.php',
	launchPopup = function () {
			var popup = window.open(url+'?auth', 'auth', 'toolbar=0,scrollbars=1,location=1,statusbar=1,menubar=0,resizable=1,width=800,height=650,left=450,top=250'),
			interval = setInterval(function(){
				token = YAHOO.util.Cookie.get("yosAccessToken");
				if(token){
					clearInterval(interval);
					var authButton = widget.getElementsByTagName('a')[0],
						authConf = widget.getElementsByTagName('span')[0];
					authButton.style.display = 'none';
					authConf.style.display = 'block';
					popup.close();
				}
			}, 1000);
		},
	postComment = function () {
		if(!token){
			return;
		}
		var href = encodeURIComponent(document.location.href),
			title = ' left a comment at '+href,
			text = widget.getElementsByTagName('textarea')[0].value,
			description = encodeURIComponent(text.substr(0,9)+'...'),//title will be the first 10 char from comment w/ trailing elipses
			params = 'link='+href+'&title='+title+'&description='+description+'&token='+encodeURIComponent(token),
			callback = {
				success: function(o){
					if (o.responseText){
						console.log(o.responseText);
					}
				},
				failure: function(o){}
			};
		YAHOO.util.Connect.asyncRequest('POST', url+'?submit', callback, params);
	},
	buildWidget = function () {
		var html =''
			+'<span style="display:'+(token ? 'block' : 'none')+'">publishing updates to Yahoo!</span>'
			+'<a style="display:'+(token ? 'none' : 'block')+'" href="#" onclick="launchPopup(); return false;">'
				+'<img src="http://l.yimg.com/a/i/ydn/social/updt-spurp.png" style="border-width:0px;"/>'
			+'</a>'
			+'<form>'
				+'<textarea></textarea><br/>'
				+'<button onclick="postComment(); return false">Post Comment</button>'
			+'</form>';
		widget.innerHTML = html;
		widget.style.display = 'block';
	},
	loader = new YAHOO.util.YUILoader({
	    require: ["connection", "cookie"],
	    loadOptional: true,
	    onSuccess: function() {
			token = YAHOO.util.Cookie.get("yosAccessToken");
			buildWidget();
	    },
	    timeout: 100000,
	    combine: true
	});
loader.insert();

