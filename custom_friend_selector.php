<?php
require('config.inc');
require('yosdk/Yahoo.inc');
$session = YahooSession::requireSession(KEY, SECRET);
$yql = 'select guid, nickname from social.profile where guid in (select guid from social.connections(0) where owner_guid = me)';
$profiles = $session->query($yql)->query->results->profile;
//reformat results as an array of names keyed by guid
foreach($profiles as $profile){
	$connections[$profile->guid] = $profile->nickname;
}

if($_POST['submit']){
	print_r($_POST);
}
?>

<style>
form{
	font-family:arial;
	font-size:12px;
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
	margin-top:1.5ex;
}
#suggestions li{
	list-style-type:none;
	padding:1ex 0.5ex;
	position:relative;
}
.selected{
	padding:0.5ex 20px 0.5ex 0.5ex;
	border: 1px solid #BBD8FB;
	background-color:#F3F7FD;
	float:left;
	margin:0.5ex;
	background-image:url('http://example.erikeldridge.com/yap/remove.png');
	background-position: right center;
	background-repeat:no-repeat;
}
.suggested{
	border: 1px solid white;/* prevent jumpy display changes when hover adds border */
}
.suggested img{
	position:absolute;
	top:0px;
	right:0px;
}
li.suggested:hover{
	background-color:#F3F7FD;
	border: 1px solid #BBD8FB;
	background-image:url('http://example.erikeldridge.com/yap/add.png');
	background-position: right center;
	background-repeat:no-repeat;
}
</style>

<div id="form">
	<yml:form params="custom_friend_selector_demo.php" method="POST">
		<div id="selector">
			<span id="selected"></span>
			<div id="wrapper">
				<input/>
				<ul id="suggestions"></ul>
			</div>
			<span style="color:white">a</span><!-- kludge: empty elements are not seen by JS in FF3 -->
		</div>
		<input type="hidden" id="guids" name="guids"/><!-- storage location for selected guids -->
		<input type="submit" name="submit"/>
	</yml:form>
</div>

<script>
var connections = <?= json_encode($connections) ?>,
	form = document.getElementById('form'),
	selector = document.getElementById('selector'),
	selected = document.getElementById('selected'),
	input = selector.getElementsByTagName('input')[0],
	suggestions = document.getElementById('suggestions'),
	guids = document.getElementById('guids'),
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
			html += '<li class="suggested" id="' + id + '">' + name;
			// html += '<img src="add_10.png" align="right"/>';
			html += '</li>';
		}
		suggestions.innerHTML = html;
		//if there are any suggestions, frame the display w/ a border	
		if(html){	
			suggestions.style.borderColor = '#000';
			suggestions.style.borderStyle = 'solid';
			suggestions.style.borderWidth = '1px';
		}else{
			suggestions.style.border = '';
		}
	},
	handleClick = function(event){
		var event = event || window.event,
			div,
			text,
			className = event.target.className,
			guid = event.target.id.substr(5);
		if(className && 'suggested' === className){
			//append item to selected display
			div = document.createElement('div');
			div.className = 'selected';
			div.id = event.target.id;
			text = document.createTextNode(event.target.firstChild.data);
			div.appendChild(text);
			selected.appendChild(div);
			//remove item from suggestions display
			event.target.parentNode.removeChild(event.target);
			//append guid to selected data
			guids.value += ',' + guid;//always add comma because it makes adding/removing guids easy. form handler will need to trim.
			//clear input & suggestions
			input.value = '';
			suggestions.innerHTML = '';
			suggestions.style.border = '';
		}else if(className && 'selected' === className){
			//remove item from suggestions display
			selected.removeChild(event.target);
			//remove guid from selected guids
			guids.value = guids.value.replace(',' + guid, ' ');
		}
		//reset focus on input
		input.focus();
	};
	form.addEventListener('keyup', handleKeyUp, false);
	form.addEventListener('click', handleClick, false);
</script>

