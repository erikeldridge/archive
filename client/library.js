/*
Copyright (c) 2009, Erik Eldridge. All rights reserved.
Code licensed under the BSD License:
http://test.erikeldridge.com/foxbatexample/license.txt
*/

var sdk = function () {
	var hash = null,
		requests = {},
		request = function (params, userCallback) {
			var iframe = document.createElement('iframe'),
				id = null,
				
				//this should be populated by service
				url = 'http://test.erikeldridge.com/foxbatexample',
				chunks = [],
				collected = 0,
				iframeCallback = function (obj) {
					var index = obj['index'];
					chunks[index] = obj['chunk'];
					collected++;
					
					//if collected equals total (coercion intended), we're done
					if (collected == obj['total']) {

						//do something w/ data
						userCallback(chunks.join(''));

						//clean up
						requests[id] = null;
						total = null;
						collected = null;
						chunks = null;
						document.body.removeChild(iframe);
					}
				};
				
			//generate a unique identifier for each request
			do {
				var now = new Date().getTime().toString(),
					rand = Math.random().toString().substr(3, 5);
				id = now + rand;
			} while (request[id]);
			
			//init iframe channel
			iframe.style.display = 'none';
			iframe.src = 'iframe.html';
			iframe.id = id;
			document.body.appendChild(iframe);
			
			//get hash from ui
			if (!hash) {
				var scriptNodes = document.getElementsByTagName('script');
				for (var i = 0; i < scriptNodes.length; i++) {
					if (-1 !== scriptNodes[i].src.indexOf('library.js')) {
						hash = scriptNodes[i].innerHTML;
					}
				}
				if (!hash) {
					throw('library js, request() fn, hash must be defined');
				}
			}
			
			//build out request params
			url += '/server/?id=' + id + '&hash=' + hash;
			for (var key in params) {
				url += '&'+key+'='+params[key];
			}
			iframe.src = url;
			
			return {
				'id' : id,
				'iframeCallback' : iframeCallback
			};
		};

	return {
		'makeRequest' : function (params, userCallback) {
			var req = request(params, userCallback);
			requests[req.id] = req;
		},
		'requests' : requests
	};
}();