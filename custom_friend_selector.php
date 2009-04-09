<?php
// require('config.inc');
// require('yosdk/Yahoo.inc');
// $session = YahooSession::requireSession(KEY, SECRET, NULL, '');//kludge: passing in empty string for callback to prevent unverified domain error
// $yql = 'select guid, nickname from social.profile where guid in (select guid from social.connections(0) where owner_guid = me)';
// $profiles = $session->query($yql)->query->results->profile;
// //reformat results as an array of names keyed by guid
// foreach($profiles as $profile){
// 	$connections[$profile->guid] = $profile->nickname;
// }
$connections = array(
	'123asd'=>'huebert',
	'456gdf'=>'erik',
	'xcv789'=>'hhjoe'
);
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
	position:relative;
}
.selected img{
	position:absolute;
	top:5px;
	right:5px;
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
	background-image:url('add_15.png');
	background-position:center right;
	background-repeat:no-repeat;
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
			<span style="color:white">a</span><!-- kludge: empty elements are not seen by JS in FF3 -->
		</div>
		<input type="hidden" id="guids"/><!-- storage location for selected guids -->
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
			img.src = 'remove_10.png';
			div.appendChild(img);
			selected.appendChild(div);
			//append guid to selected data
			guids.value += ',' + guid;//always add comma because it makes adding/removing guids easy. form handler will need to trim.
			//clear input & suggestions
			input.value = '';
			suggestions.innerHTML = '';
			suggestions.style.border = '';
		}else if(className && 'remove' === className){
			//remove item from suggestions display
			selected.removeChild(event.target.parentNode);
			//remove guid from selected guids
			guids.value = guids.value.replace(',' + guid, ' ');
		}
		//reset focus on input
		input.focus();
	};
	form.onkeyup = handleKeyUp;
	form.onclick = handleClick;
</script>

