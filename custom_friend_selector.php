<?php
require('config.inc');
require('yosdk/Yahoo.inc');
$session = YahooSession::requireSession(KEY, SECRET, NULL, '');
$yql = 'select guid, nickname from social.profile where guid in (select guid from social.connections(0) where owner_guid = me)';
$profiles = $session->query($yql)->query->results->profile;
foreach($profiles as $profile){
	$connections[$profile->guid] = $profile->nickname;
}
?>

<style>
form{
	font-family:arial;
	font-size:12px;
}
ul{
	padding:0;
	margin-top:1.5ex;
}
ul li{
	list-style-type:none;
	padding:1ex 0.5ex;
}
#selector{
	border: 1px solid black;
	height:2.5em;
}
#selector input {
	border:none;
	font-size:inherit;
	width: 20em;
	padding:0.5ex 0.5ex 0.5ex 1ex;
	margin-top:0.5ex;
}
#wrapper{
	width: 20em;
	float:left;
}
#suggestions{
	padding:0.5ex;
}
.selected{
	padding:0.5ex 0.5ex 0.5ex 0.5ex;
	border: 1px solid #BBD8FB;
	background-color:#F3F7FD;
	float:left;
	margin:0.5ex;
}
.selected img{
	margin-left:0.5ex;
}
.suggested{
	border: 1px solid white;
}
li.suggested:hover{
	background-color:#F3F7FD;
	border: 1px solid #BBD8FB;
}
</style>

<div id="form">
	<form>
		<div id="selector">
			<span id="selected"></span>
			<div id="wrapper">
				<input/>
				<ul id="suggestions"></ul>
			</div>
			<span style="color:white">a</span>
		</div>
		<input type="hidden" id="guids"/>
	</form>
</div>
<script>
var connections = <?= json_encode($connections) ?>,
	form = document.getElementById('form'),
	selector = document.getElementById('selector'),
	selected = document.getElementById('selected'),
	input = selector.getElementsByTagName('input')[0],
	suggestions = document.getElementById('suggestions'),
	guids = document.getElementById('guids'),
	matches = null,
	handleKeyUp = function(event){
		var event = event || window.event,
			value = event.target.value,
			matches = [],
			html = '',
			nonBlankValue = ('' !== value),
			match,
			notSelected,
			i,
			id,
			name,
			guid;
		//build suggestion list
		for(guid in connections) if(connections.hasOwnProperty(guid)){
			match = (0 === connections[guid].indexOf(value));
			notSelected = (-1 === guids.value.indexOf(guid));
			if(nonBlankValue && match && notSelected){
				matches.push(guid);
			}
		}
		// build suggestion display
		for(i = 0; i < matches.length; i++){
			id = 'guid_' + matches[i];
			name = connections[matches[i]];
			html += '<li class="suggested" id="' + id + '">' + name + '</li>';
		}
		suggestions.innerHTML = html;
		if(html){			
			suggestions.style.border = '1px solid black';
		}else{
			suggestions.style.border = '';
		}
	},
	handleClick = function(event){
		var event = event || window.event,
			div,
			text,
			img,
			className = event.target.className,
			guid = event.target.id.substr(5);
		if(className && 'suggested' === className){
			//remove item from suggestions display
			event.target.parentNode.removeChild(event.target);
			//append item to selected display
			div = document.createElement('div');
			div.className = 'selected';
			text = document.createTextNode(event.target.firstChild.data);
			div.appendChild(text);
			img = document.createElement('img');
			img.id = event.target.id;
			img.className = 'remove';
			img.src = 'http://gfx1.hotmail.com/mail/uxp/w3/m3/pr05/cp/DeleteContact.png';
			div.appendChild(img);
			selected.appendChild(div);
			//append guid to selected data
			guids.value += ','+guid;
			//clear input & suggestions
			input.value = '';
			suggestions.innerHTML = '';
			suggestions.style.border = '';
			//reset focus on input
			input.focus();
		}else if(className && 'remove' === className){
			//remove item from suggestions display
			selected.removeChild(event.target.parentNode);
			//remove guid from selected guids
			guids.value = guids.value.replace(',' + guid, ' ');
			input.focus();
		}
		console.log(event.target.tagName);
	};
	form.onkeyup = handleKeyUp;
	form.onclick = handleClick;
</script>

