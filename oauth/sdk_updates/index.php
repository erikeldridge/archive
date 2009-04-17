<head>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<h1>Publishing my activities on Yahoo!</h1>
Suppose this is an account settings page.  
<ul>
	<li><input type="checkbox"/>Setting A</li>
	<li><input type="checkbox"/>Setting B</li>
	.<br/>
	.<br/>
	.<br/>
	<li><input type="checkbox" id="yahoo_updates"/>Keep My Friends Updated On Yahoo!</li>
	.<br/>
	.<br/>
	.<br/>
	<li><input type="checkbox"/>Setting X</li>
	<li><input type="checkbox"/>Setting Y</li>
</ul>
<script>
	var launchPopup = function(event){
			event = event || window.event;
			var url = 'http://example.erikeldridge.com/oauth/using_sdk/popup.php';
			if(event.target.checked){
				window.open(url, 'auth', 'toolbar=0,scrollbars=1,location=1,statusbar=1,menubar=0,resizable=1,width=800,height=650,left = 450,top = 250');
			}
		};
	document.getElementById('yahoo_updates').onclick = launchPopup;
</script>