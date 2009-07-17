//init widget
var widget = document.getElementById('widget'),
	token = YAHOO.util.Cookie.get("yosAccessToken"),
	url = 'index.php',
	launchPopup = function(event){
			window.open(url+'?auth', 'auth', 'toolbar=0,scrollbars=1,location=1,statusbar=1,menubar=0,resizable=1,width=800,height=650,left=450,top=250');
		},
	postComment = function(){
		var href = encodeURIComponent(document.location.href),
			text = widget.getElementsByTagName('textarea')[0].value,
			title = ' left a comment at '+href,
			body = encodeURIComponent(text.substr(0,9)+'...'),//title will be the first 10 char from comment w/ trailing elipses
			params = 'link='+href+'&title='+title+'&body='+body,
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
	html = null;

//build widget html
if(token){
	html+=''
	+'<span>publishing updates to Yahoo!</span>';
}else{
	html+=''
	+'<a href="" onclick="launchPopup()">'
		+'<img src="http://l.yimg.com/a/i/ydn/social/updt-spurp.png" style="border-width:0px;"/>'
	+'</a>';
}
html+=''
	+'<form>'
		+'<textarea></textarea><br/>'
		+'<button onclick="postComment(); return false">Post Comment</button>'
	+'</form>';
widget.innerHTML = html;
widget.style.display = 'block';