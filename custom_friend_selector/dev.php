<?php
/*
Purpose: 
Demonstrate how to create a friend selector that's caja-safe

Usage:
- upload this file to your server
- download Yahoo! PHP SDK (http://developer.yahoo.com/social/sdk/), if you don't have it already
- adjust Yahoo.inc include to reflect path to SDK
- create YAP application
- set the app url to this file on your server
- create config.inc file and define YAP key and secret using values from YAP app definition
- preview the YAP app
- click on the left side of the rectangle with the black border to set the focus on the input box
- start typing a connection's nickname.  If you don't know your connections' nicknames, visit your Yahoo! profile and view your connections.  Their nicknames appear below their pictures.
- Click on a name that appears below the input box to add its associated guid to the hidden "guids" input field in the form.  The clicked name will disappear form the dropdown and appear to the left of the input box
- To remove a name (that's been added), click on it to the left of the input box
- click the submit button to send the form data.  Note: the form submits to itself.  Assuming you added some names, you will see their guids printed at the top of the page.  Serving suggestions: you can plug these guids directly into a yml:name tag to get their names or a yml:profile-pic tag to see their pic, request data for a guid using Yahoo!'s social APIs (http://developer.yahoo.com/social/social), etc.
- play with this app live here: http://apps.yahoo.com/-HvSOMW30

License:
Software License Agreement (BSD License)
Copyright (c) 2009, Yahoo! Inc.
All rights reserved.

Redistribution and use of this software in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the
      following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
    * Neither the name of Yahoo! Inc. nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission of Yahoo! Inc.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
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
	$guids_csv = ltrim($_POST['guids'], ',');//note removal of leading comma
	$guids = explode(',', $guids_csv);
	var_dump($guids);
}
?>

<style>
form{
	font-family:arial;
	font-size:12px;
}
ul{
	margin:0;
	padding:0;
}
#submit{/*making space for the drop-down in the demo*/
	float:right;
}
#selector{
	border: 1px solid black;
	height:2.5em;
}
#selector input {
	border-width:0;
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
	<yml:if-env ua="ie">
	margin-top:0px;
	</yml:if-env>
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
	background-image:url('http://github.com/erikeldridge/example/raw/ae82c0c0124dbb0af99e19849a2d11bb19406028/custom_friend_selector/remove.png');
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
	background-image:url('http://github.com/erikeldridge/example/raw/ae82c0c0124dbb0af99e19849a2d11bb19406028/custom_friend_selector/add.png');
	background-position: right center;
	background-repeat:no-repeat;
}
</style>

<div id="form">
	<yml:form method="POST">
		<div id="selector">
			<span id="selected"></span>
			<div id="wrapper">
				<input/>
				<ul id="suggestions"></ul>
			</div>
			<span style="color:white">a</span><!-- kludge: empty elements are not seen by JS in FF3 -->
		</div>
		<input type="hidden" id="guids" name="guids"/><!-- storage location for selected guids -->
		<input type="submit" name="submit" id="submit" />
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
			guid,
			caseRespectiveReplace = function(string, value){
				var remainder = string,
					value = value.toLowerCase(),
					length = value.length,
					i = remainder.toLowerCase().indexOf(value),
					output = '',
					target = '';
				while(-1 !== i){
					output += target.bold() + remainder.substring(0,i);
					target = remainder.substr(i,length);
					remainder = remainder.substr(i+length);
					i = remainder.toLowerCase().indexOf(value);
				}
				output += target.bold() + remainder;
				return output;
			};
		//build suggestion list
		for(guid in connections) if(connections.hasOwnProperty(guid)){
			match = (-1 !== connections[guid].toLowerCase().indexOf(value.toLowerCase()));
			notSelected = (-1 === guids.value.indexOf(guid));
			if(nonBlankValue && match && notSelected){
				matches.push(guid);
			}
		}
		// build suggestion display
		for(i = 0; i < matches.length; i++){
			id = 'suggested_' + matches[i];
			name = connections[matches[i]];
			html += '<li class="suggested" id="' + id + '">';
			html += caseRespectiveReplace(name, value);
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
	handleKeyDown = function(event){
		var event = event || window.event;
		if(40 === event.keyCode){
			console.log(suggestions.childNodes[0]);			
		}
	},
	handleClick = function(event){
		var event = event || window.event,
			div,
			//kludge: using id instead of class for identifier because className is not readable by YAP in IE
			suggestedElement = (event.target.id && (0 === event.target.id.indexOf('suggested_'))),
			selectedElement = (event.target.id && (0 === event.target.id.indexOf('selected_'))),
			guid;
		if(suggestedElement){
			guid = event.target.id.substr(10);
			//append item to selected display
			div = document.createElement('div');
			div.className = 'selected';
			div.id = 'selected_' + guid;
			div.innerHTML = connections[guid];
			selected.appendChild(div);
			//remove item from suggestions display
			event.target.parentNode.removeChild(event.target);
			//append guid to selected data
			guids.value += ',' + guid;//always add comma because it makes adding/removing guids easy. form handler will need to trim.
			//clear input & suggestions
			input.value = '';
			suggestions.innerHTML = '';
			suggestions.style.border = '';
		}else if(selectedElement){
			guid = event.target.id.substr(9);
			//remove item from suggestions display
			selected.removeChild(event.target);
			//remove guid from selected guids
			guids.value = guids.value.replace(',' + guid, ' ');
		}
		//reset focus on input
		input.focus();
	};
	form.addEventListener('keydown', handleKeyDown, false);
	form.addEventListener('keyup', handleKeyUp, false);
	form.addEventListener('click', handleClick, false);
	input.focus();
</script>

